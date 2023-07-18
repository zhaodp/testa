<?php
/**
 * 记录订单位置移动
 * @author Daniel
 * @date   2012-07-16
 * 
 * @param 订单流水号
 * @param 司机登录验证串
 * @param GPS流转串
 */

$order_id = $params['order_id'];
$token = $params['token'];
$gpsString = $_FILES['gpsstring'];
//查找司机登录信息
$ret = DriverToken::validateToken($token);

if(!$ret){
	$message = array('code'=>1004,'message'=>'登录已过期');
	echo json_encode($message);
	Yii::app()->end();
}

//获取司机的信息
$driver = Driver::getProfile($ret->driver_id);

$order = Order::model()->find('order_id=:order_id', array(':order_id'=>$order_id));

if (!$order){
	$message = array('code'=>3010,'message'=>'订单不存在。');
	echo json_encode($message);
	Yii::app()->end();
}

$str = file_get_contents($gpsString['tmp_name']);

$gpsString = gzuncompress($str);

$gpsString = str_replace('}]"', '}]',str_replace('"[{', '[{',str_replace('\"', '"', $gpsString)));

$gpsArray = json_decode($gpsString, true);
foreach ($gpsArray['gps'] as $gps){
	$params = array (
		'order_id'=>$order_id,
		'driver_id'=>$ret->driver_id, 
		'imei'=>$driver->imei, 
		'latitude'=>$gps['a'], 
		'longitude'=>$gps['o'],
		'created'=>date(Yii::app()->params['formatDateTime'],$gps['c']));
	$orderTrack = new OrderTrack();
	
	$orderTrack->attributes = $params;
	$orderTrack->insert();
}
$message = array('code'=>0,'message'=>'保存成功。');
echo json_encode($message);