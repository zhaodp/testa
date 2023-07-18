<?php
/**
 * 确认订单开始
 * @author dayuer
 * @date   2012-07-10
 * 
 * @param 订单编号
 * @param GPS经纬度
 * @param GPS经纬度
 * @param 街道名称
 */
$order_id = $params['order_id'];
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
if ($driver){
	$driver_id = $ret->driver_id;
	$order = Order::model()->find('order_id=:order_id and driver_id=:dirver_id and status=:status', array (
				':order_id'=>$order_id, 
				':dirver_id'=>$driver_id, 
				':status'=>Order::ORDER_READY));

	if ($order){
		$attr = array (
			'location_start'=>$street,
			'start_time'=>time());
		$order->updateByPk($order_id, $attr);
		
		$position = new OrderPosition();
		$position->attributes = array (
			'order_id'=>$order_id, 
			'type'=>OrderPosition::POSITION_START, 
			'latitude'=>$latitude, 
			'longitude'=>$longitude, 
			'street'=>$street);
		$position->save();
		$message = array('code'=>0,'order_id'=>$order_id,'message'=>'代驾开始');
	} else {
		$message = array('code'=>3005,'message'=>'订单不存在，保存失败');
	}
} else {
	$message = array('code'=>3008,'message'=>'系统状态不正常，请检查状态');
}

echo json_encode($message);