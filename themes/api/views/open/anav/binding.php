<?php
Yii::import('application.models.activity.BActivityBonus');
/**
 * 高德绑定优惠券接口
 * 调用的url:
 * @author bidong 2014-1-7
 * @param $params['phone']  $params['passwd']
 */

$ret = array(
    'code' => 1,
    'message' => '您已绑定手机号,活动只能参加一次');

if (empty($params['phone']) || empty($params['passwd'])) {
    $ret = array('code' => 2, 'message' => '手机号和验证码不能为空');
    echo json_encode($ret);
    return;
}

$phone = trim($params['phone']);
$passwd = empty($params['passwd']) ? '' : trim($params['passwd']);
$imei = empty($params['imei']) ? '' : trim($params['imei']);
$eventId = empty($params['eventId']) ? '' : trim($params['eventId']);
$model = empty($params['model']) ? '' : trim($params['model']);
$business = empty($params['business']) ? CustomerToken::EDJ_TOKEN_FROM : trim($params['business']);


//获取密码
$customerPass = RCustomerInfo::model()->getCustomerSmsPasswd($phone,$business);
//判断提示
if (empty($customerPass)) {
    $ret = array(
        'code' => 2,
        'message' => '请先获取验证码');
    echo json_encode($ret);
    return;
}

//密码是否相等
if (trim($customerPass->code) != trim($passwd)) {
    $ret = array(
        'code' => 2,
        'message' => '验证码输入不正确');
    echo json_encode($ret);
    return;
}

//密码过期
if ($customerPass->out_time < time()) {
    $ret = array(
        'code' => 2,
        'message' => '验证码已过期，请重新获取。');
    echo json_encode($ret);
    return;
}

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

//一个imei仅能参加一次
#$hasPhoneEventLog = $activity->usedTheActivity($user_phone);    //验证是否已经参加过此活动
$hasImeiEventLog = $activity->usedTheActivity($user_imei);    //验证是否已经参加过此活动

if($hasImeiEventLog){
//if($hasEventLog){
    $ret = array(
        'code' => 2,
        'message' => '该手机号已经参加过此活动。');
    echo json_encode($ret);
    return;
}

$params = array('event_id'=>$eventId, 'phone'=>$phone, 'imei'=>$imei,'model'=>$model);
$is_bind=BCustomerEventLog::model()->buildEventLog($params);   //添加用户参加活动记录

if($is_bind==true){
    $result = CustomerBonus::model()->bonusOldCode($phone, $eventId);
    if ($result) {
        //返回数据
        $ret = array(
            'code' => 0,
            'message' => '绑定成功');
    }
}else{
    $ret = array(
        'code' => 2,
        'message' => '该手机号已经参加过此活动。');
    echo json_encode($ret);
    return;
}


echo json_encode($ret);
return;

