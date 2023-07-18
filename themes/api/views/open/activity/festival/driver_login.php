<?php
/**
 * 司机登录
 */
$driver = isset($params['driver']) ? trim($params['driver']) : '';
$password  = isset($params['password']) ? trim($params['password']) : '';

if(empty($driver) || empty($password)){
	$ret = array ('code'=>2,'message'=>'请输入正确的工号和密码');
	echo json_encode($ret);return;
}
$password = base64_decode($password);
$driver_model = Driver::model()->find('user=:user and password=:password', array(':user'=>$driver, ':password'=>$password));
if(!$driver_model){
    $ret = array ('code'=>2,'message'=>'请输入正确的工号和密码');
    echo json_encode($ret);return;
}
if($driver_model['mark'] == Employee::MARK_LEAVE){
    $ret = array ('code'=>2,'message'=>'本活动仅支持e代驾正常合作状态司机');
    echo json_encode($ret);return;
}

//判断是否报名,如果已报名,则进入报名成功界面
$driver_model = FestivalDriver::model()->getDriver($driver);
if(!$driver_model){//未报名
    $ret = array ('code'=>3,'message'=>'登录成功');
    echo json_encode($ret);return;
}
//已报名
$ret = array ('code'=>0,'message'=>'登录成功');
echo json_encode($ret);return;
