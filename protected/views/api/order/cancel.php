<?php
/**
 * 订单取消
 * @param 订单号
 * @param 订单取消原因
 * @param GPS经纬度
 * @param GPS经纬度
 * @param 街道名称
 * 
 */

$order_id = $params['order_id'];
$token = $params['token'];
$reason = $params['log'];
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

$driver_id = $ret->driver_id;

$order = Order::model()->find('order_id=:order_id and driver_id=:dirver_id and status=:status', array (
			':order_id'=>$order_id, 
			':dirver_id'=>$driver_id, 
			':status'=>Order::ORDER_READY));

if ($order){
	$attr = array (
		'cancel_desc'=>$reason, 
		'status'=>Order::ORDER_COMFIRM); //修改订单状态为等待确认
	if ($order->updateByPk($order_id, $attr)) {
		//记录日志
		$log = new OrderLog();
		$log_attr = array (
			'order_id'=>$order_id, 
			'description'=>$reason, 
			'operator'=>strtoupper($driver_id), 
			'created'=>time());
		$log->attributes = $log_attr;
		$log->save();
		
		$position = new OrderPosition();
		$position->attributes = array (
			'order_id'=>$order_id, 
			'type'=>OrderPosition::POSITION_CANCEL, 
			'latitude'=>$latitude, 
			'longitude'=>$longitude, 
			'street'=>$street);
		$position->save();
		
		$message = array('code'=>0,'order_id'=>$order_id,'message'=>'销单申请已成功提交');
	} else {
		$message = array('code'=>3007,'message'=>'此订单不能处理，请联系管理员');
	}
} else {
	$message = array('code'=>3007,'message'=>'此订单不能处理，请联系管理员');
}

echo json_encode($message);