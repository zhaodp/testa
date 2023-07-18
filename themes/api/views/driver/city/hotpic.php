<?php
/**
 * get city hot map which driver in
 * @author aiguoxin
 * @version 2014-04-22
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';

if(empty($token) || empty($driver_id)){
    $ret=array('code'=>2 , 'message'=>'参数不正确!');
    echo json_encode($ret);return;
}

// //验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver===null||$driver->token===null||$driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}

//get city_prefix by driver_id
$city_prefix = substr($driver_id, 0,2);
$pic = DriverPositionPic::model()->getLatestPic($city_prefix);

if(empty($pic)){
	$ret=array('code'=>1 , 'message'=>'no object found');
	echo json_encode($ret);return;
}
//返回成功信息
$ret=array('code'=>0 , 'message'=>$pic);
echo json_encode($ret);return;
