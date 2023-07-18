<?php
Yii::import('application.models.activity.BActivityBonus');
/**
 * 可口可乐活动绑定优惠券接口
 * @author cuiluzhe 2014-9-18
 */
$phone      = isset($params['phone']) ? $params['phone'] : '';
$bonusCode  = isset($params['bonusCode']) ? $params['bonusCode'] : '';
$eventId    = isset($params['eventId']) ? $params['eventId'] : '';

if( empty($phone) || empty($bonusCode) || empty($eventId) ){
        $ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
        echo json_encode($ret);return ;
}


$activity = BActivityBonus::model()->getActivityByKey($eventId); //验证活动是否激活、开始或结束
if(!$activity || !$activity->nowInActivity()){
    $ret = array('code' => 2,'t'=>time(),'message' => '现在不能参加此活动。');
    echo json_encode($ret);
    return;
}

$user_phone = array('phone'=>$phone);
$hasPhoneEventLog = $activity->usedTheActivity($user_phone);    //验证是否已经参加过此活动
if($hasPhoneEventLog){
    $ret = array('code' => 2,'message' => '该手机号已经参加过此活动。');
    echo json_encode($ret);
    return;
}

$extra = $activity->getExtraIni();
if(!isset($extra) || empty($extra)){ //校验活动是否配置优惠劵信息
    $ret = array('code' => 2,'message' => '缺少配置信息。');
    echo json_encode($ret);
    return; 
}

$bonusId = $extra['bonusSn'];
//验证后台用户配置的优惠劵id是否正确
$bonusLibrary = BonusLibrary::model()->checkBonus($bonusId,$bonusCode); 
if(!$bonusLibrary){
    $ret = array('code' => 2,'message' => '该活动不存在此优惠码。');
    echo json_encode($ret);
    return; 
}




$params = array('event_id'=>$eventId, 'phone'=>$phone);
/**$is_bind=BCustomerEventLog::model()->buildEventLog($params);   //添加用户参加活动记录
if($is_bind==true){
    $ret = BonusLibrary::model()->BonusBinding($bonus_sn, $phone);
    echo json_encode($ret); 
}else{
    $ret = array('code' => 2,'message' => '该手机号已经参加过此活动。');
    echo json_encode($ret);
    return;
}**/
$ret = BonusLibrary::model()->BonusBinding($bonusCode, $phone);
if($ret['code'] == 0){
    $is_bind=BCustomerEventLog::model()->buildEventLog($params); 
}
echo json_encode($ret);
return;

