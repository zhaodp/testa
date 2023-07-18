<?php
/**
 * 确认接收订单预约
 * @author dayuer
 * @date   2012-07-10
 * 
 * @param 客户电话
 * @param 呼叫时间
 * @param 预约时间
 * @param 司机登录验证串
 * @param GPS经纬度
 * @param GPS经纬度
 * @param 街道名称
 */

$phone = $params['phone'];
$call_time = $params['call_time'];
$booking_time = $params['booking_time'];
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

$criteria = new CDbCriteria();
					
$criteria->condition = 'phone like :phone and name not like :disable';
$criteria->params = array (
	':phone'=>'%' . $phone . '%', 
	':disable'=> '%停用%');

$vip = UserVip::model()->find($criteria);

$vipcard = ($vip) ? $vip->attributes['id'] : '';

$params = array (
	'phone'=>$phone, 
	'driver'=>$driver->name, 
	'driver_id'=>$ret->driver_id, 
	'driver_phone'=>$driver->phone, 
	'imei'=>$driver->imei, 
	'source'=>Order::SOURCE_CLIENT, 
	'call_time'=>$call_time, 
	'order_date'=>date('Ymd', $call_time), 
	'booking_time'=>$booking_time, 
	'created'=>time(), 
	'description'=>'客户直接呼叫', 
	'status'=>Order::ORDER_READY);
$order = new Order();
$order->is_api = true;
$order->attributes = $params;

if ($order->save()) {
	$order_id = $order->getPrimaryKey();
	//保存接收订单时所在位置
	$position = new OrderPosition();
	$position->attributes = array (
		'order_id'=>$order_id, 
		'type'=>OrderPosition::POSITION_CONFIRM, 
		'latitude'=>$latitude, 
		'longitude'=>$longitude, 
		'street'=>$street);
	$position->save();
	
	$employee = Employee::model()->find('imei=:imei', array(':imei'=>$driver->imei));
	$attr = array(
		'state'=>Employee::EMPLOYEE_WORK,
		'longitude'=>$longitude,
		'latitude'=>$latitude,
		'report_time'=>date('Y-m-d H:i:s'),
		'update_time'=>date('Y-m-d H:i:s'));
	$employee->attributes = $attr;
		
	$employee->save();
	$message = array('code'=>0,'order_id'=>$order_id,'vipcard'=>$vipcard,'message'=>'订单保存成功');
}else{
	$message = array('code'=>3001,'message'=>'订单已经存在，保存失败');
}

echo json_encode($message);