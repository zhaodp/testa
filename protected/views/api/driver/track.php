<?php
/**
 * 记录司机状态位置
 * @author Daniel
 * @date   2012-07-16
 * 
 * @param 司机状态
 * @param 司机登录验证串
 * @param GPS经纬度
 * @param GPS经纬度
 * @param 街道名称
 */

$status = $params['status'];
$token = $params['token'];
$latitude = $params['latitude'];
$longitude = $params['longitude'];
$street = $params['street'];

//查找司机登录信息
$ret = DriverToken::validateToken($token);

if(!$ret){
	$message = array('code'=>1004,'message'=>'登录已过期');
	echo json_encode($message);
	Yii::app()->end();
}

//获取司机的信息
$driver = Driver::getProfile($ret->driver_id);

$params = array (
	'driver_id'=>$ret->driver_id, 
	'imei'=>$driver->imei, 
	'status'=>$status, 
	'latitude'=>$latitude, 
	'longitude'=>$longitude, 
	'street'=>$street);
$driverTrack = new DriverTrack();

$driverTrack->attributes = $params;

if ($driverTrack->insert()) {
	
	$employee = Employee::model()->find('imei=:imei', array(':imei'=>$driver->imei));
	$attr = array(
		'state'=>$status,
		'longitude'=>$longitude,
		'latitude'=>$latitude,
		'report_time'=>date('Y-m-d H:i:s'),
		'update_time'=>date('Y-m-d H:i:s'));
	$employee->attributes = $attr;
	
	$employee->save();
	
	$message = array('code'=>0,'message'=>'保存成功');
} else {
	$message = array('code'=>1,'message'=>'保存失败');
}

echo json_encode($message);

