<?php
Yii::import('application.models.activity.BActivityBonus');
/**
 * 高德活动 获取验证码
 * 调用的url:
 * @author bidong 2014-01-06
 * @param $params $params['phone']
 */
//用手机号码预登录 发送短信 返回是否成功
$phone = empty($params['phone']) ? '' : trim($params['phone']);
$business = empty($params['business']) ? CustomerToken::EDJ_TOKEN_FROM : trim($params['business']);

if (empty($phone)) {
    $ret = array('code' => 2, 'message' => '请填写手机号');
    echo json_encode($ret);
    return;
}

//增加对手机号格式的验证，add by sunhongjing 2014-01-04
$is_phone = Common::checkPhone($phone);
if (!$is_phone) {
    $ret = array('code' => 2, 'message' => '手机号有误，请更正');
    echo json_encode($ret);
    return;
}

$imei = empty($params['imei']) ? '' : trim($params['imei']);
$eventId = empty($params['eventId']) ? '' : trim($params['eventId']);

$activity = BActivityBonus::model()->getActivityByKey($eventId);
if(!$activity || !$activity->nowInActivity()){
    $ret = array(
        'code' => 2,
        't'=>time(),
        'message' => '现在不能参加此活动。');
    echo json_encode($ret);
    return;
}
//$user = array('phone'=>$phone, 'imei'=>$imei);
//$hasEventLog = $activity->usedTheActivity($user);    //验证是否已经参加过此活动

//$user_phone = array('phone'=>$phone);
$user_imei = array('imei'=>$imei);

//一个手机或者一个imei仅能参加一次
//$hasPhoneEventLog = $activity->usedTheActivity($user_phone);    //验证是否已经参加过此活动
$hasImeiEventLog = $activity->usedTheActivity($user_imei);    //验证是否已经参加过此活动

if($hasImeiEventLog){
//if($hasEventLog){
    $ret = array(
        'code' => 2,
        'message' => '该手机号已经参加过此活动。');
    echo json_encode($ret);
    return;
}

$customerPass = RCustomerInfo::model()->getCustomerSmsPasswd($phone,$business);
$pass = array();
$pass['business'] = $business;
if (!empty($customerPass)) {
    //如果有加验证  10次 和 一分钟发一次
    if ($customerPass->num > 10) {
        $ret = array(
            'code' => 2,
            'message' => '一天之内只能验证十次。');
        echo json_encode($ret);
        return;
    }

    $min_time = isset($customerPass->min_time) ? $customerPass->min_time : 0;
    //1分钟限制
    if ($min_time > time()) {
        $ret = array(
            'code' => 2,
            'message' => '一分钟之内只能请求一次验证码。');
        echo json_encode($ret);
        return;
    }

    //大于1分钟限制并且在10分钟之内
    if ($customerPass->out_time > time() && $min_time < time()) {
        $pass['date_time'] = $customerPass->date_time;
        $pass['num'] = $customerPass->num;
        $pass['code'] = $customerPass->code;
        $pass['out_time'] = $customerPass->out_time;
        $customerPass = RCustomerInfo::model()->setCustomerSmsPasswd($phone, $pass);
    }

    //不在十分钟限制之内
    if ($customerPass->out_time < time()) {
        $pass['date_time'] = $customerPass->date_time;
        $pass['num'] = $customerPass->num;
        $customerPass = RCustomerInfo::model()->setCustomerSmsPasswd($phone, $pass);
    }
} else {
    $customerPass = RCustomerInfo::model()->setCustomerSmsPasswd($phone,$pass);
}

$passwd = $customerPass->code;

$message = '您的验证码为：%s，请在5分钟内完成验证。感谢参加高德地图活动。';
$content = sprintf($message, $passwd);

$ret_sms = Sms::SendSMS($phone, $content, Sms::CHANNEL_ZCYZ);


//返回json数据
$ret = array(
    'code' => 0,
    'message' => '验证码已成功发送。');
echo json_encode($ret);
return;





