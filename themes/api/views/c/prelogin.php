<?php
/**
 * 客户端API：c.prelogin 用户获取验证码
 * 调用的url:
 * @author mengtianxue 2013-05-20
 *
 * @param $params $params['phone'] $params['udid'] $params['macaddress']
 *
 * @see
 * @since
 */
Yii::import('application.models.redis.*');
//用手机号码预登录 发送短信 返回是否成功
$phone = empty($params['phone']) ? '' : trim($params['phone']);
$udid = empty($params['udid']) ? '' : trim($params['udid']);
$macaddress = empty($params['macaddress']) ? '' : trim($params['macaddress']);
$device_type = empty($params['os']) ? 'unknown' : strtoupper(trim($params['os']));
$type = empty($params['type']) ? 0 : intval(trim($params['type']));
$business = empty($params['business']) ? CustomerToken::EDJ_TOKEN_FROM : trim($params['business']);


if (empty($phone) || empty($udid)) {
    $ret = array('code' => 2, 'message' => '参数错误');
    echo json_encode($ret);
    return;
}

//增加对手机号格式的验证，add by sunhongjing 2014-01-04
$is_phone = Common::checkPhone($phone);
if( ! $is_phone ){
	$ret = array('code' => 2, 'message' => '手机号有误，请更正');
    echo json_encode($ret);
    return;
}

//获取cache中的cacha
$customerLogic = new CustomerLogic();

//if ($type !== 0) {
//    $customerInfo = RCustomerInfo::model()->getByPhone($phone);
//    if (!empty($customerInfo) && $customerInfo->vip == 0) {
//        //记录token
//        $token = $customerLogic->setCustomerTokenCache($phone);
//
//        $tokenParams = array(
//            'phone' => $phone,
//            'udid' => $udid,
//            'macaddress' => $macaddress,
//            'device_type' => $device_type,
//            'authtoken' => $token
//        );
//
//        //task 添加或修改token
//        $task = array(
//            'method' => 'customer_login',
//            'params' => $tokenParams
//        );
//        Queue::model()->putin($task, 'task');
//        //返回数据
//        $ret = array(
//            'code' => 0,
//            'token' => $token,
//            'message' => '登录成功');
//
//        echo json_encode($ret);
//        return;
//    }
//}

$customerPass = RCustomerInfo::model()->getCustomerSmsPasswd($phone,$business);

$pass = array();
$pass['business'] = $business;
if (!empty($customerPass)) {
    //如果有加验证  10次 和 一分钟发一次
    if ($customerPass->num > 50) {
        $ret = array(
            'code' => 2,
            'message' => '一天之内只能登录十次。');
        echo json_encode($ret);
        return;
    }

    $min_time = isset($customerPass->min_time) ? $customerPass->min_time : 0;
    //1分钟限制
    if ($min_time > time()) {
        $ret = array(
            'code' => 2,
            'message' => '一分钟之内只能请求一次预登录验证码。');
        echo json_encode($ret);
        return;
    }

    //大于1分钟限制并且在10分钟之内
    if($customerPass->out_time > time() && $min_time < time()){
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
}else{
    $customerPass = RCustomerInfo::model()->setCustomerSmsPasswd($phone, $pass);
}

$passwd = $customerPass->code;

$message = '您的验证码为：%s，验证码将在十分钟后失效。';
$content = sprintf($message, $passwd);

$ret_sms = Sms::SendSMS($phone, $content, Sms::CHANNEL_ZCYZ);
if (empty($ret_sms)) {
    $ret = array(
        'code' => 2,
        'message' => '系统延迟，请稍后再试。');
    echo json_encode($ret);
    return;
}

//一键登录相关 bidong 2014-1-18
$quickPwd=Common::makeRandCode(2);
$quickSms=$quickPwd."，直接发送短信以绑定手机号，10分钟内发送有效【e代驾】";
$quickLoginData = array('channel' => '106911892930', 'sms' =>$quickSms ,'limit'=>'60');
//保存一键注册短信内容
$customerLogic->setQuickLoginSms($phone,$quickSms,$business);
//一键登录相关 bidong 2014-1-18

//返回json数据
$ret = array(
    'code' => 0,
    'data'=>$quickLoginData,
    'message' => '验证码已成功发送。');
echo json_encode($ret);
return;





