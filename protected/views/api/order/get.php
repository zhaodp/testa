<?php
/**
 * 呼叫中心订单接受
 * @author Daniel
 * @date   2012-07-24
 * 
 * @param 司机登录验证串
 * @param GPS经纬度
 * @param GPS经纬度
 * @param 街道名称
 */

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
					
$criteria->condition = 'driver_id=:driver_id and status=:status and source=:source';
$criteria->order = 'created DESC';
$criteria->params = array (
	':driver_id'=> $ret->driver_id,
	':status'=>Order::ORDER_READY,
	':source'=>Order::SOURCE_CALLCENTER);
$order = Order::model()->find($criteria);

if ($order) {
	$criteria = new CDbCriteria();
					
	$criteria->condition = 'phone like :phone and name not like :disable';
	$criteria->params = array (
		':phone'=>'%' . $order->phone . '%', 
		':disable'=> '%停用%');
	
	$vip = UserVip::model()->find($criteria);
	
	$vipcard = ($vip) ? $vip->attributes['id'] : '';
	
	$order_id = $order->order_id;
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
	
	$sql = "SELECT leader_name, leader_phone FROM t_order_callcenter WHERE order_id <> leader_order_id AND order_id=:order_id";
	
	$command = Yii::app()->db->createCommand($sql);
	
	$leader = $command->query(array(':order_id'=>$order_id));
	
	if($leader){
		$isLead = 0;
		$leaderName = $leader['leader_name'];
		$leaderPhone = $leader['leader_phone'];
	} else {
		$isLead = 1;
		$leaderName = '';
		$leaderPhone = '';
	}
	
	$message = array(
				'code'=>0,
				'phone'=>$order->phone,
				'order_id'=>$order->order_id,
				'isLead'=>$isLead,
				'leaderName'=>$leaderName,
				'leaderPhone'=>$leaderPhone,
				'vipcard'=>$vipcard, 
				'name'=>$order->name,
				'booking_time'=>$order->booking_time,
				'location_start'=>$order->location_start,
				'message'=>'订单读取成功');
}else{
	$message = array('code'=>3021,'message'=>'订单不存在，读取失败');
}

echo json_encode($message);