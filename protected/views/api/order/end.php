<?php
/**
 * 确认代驾结束
 * @author dayuer
 * @date   2012-07-10
 * 
 * @param 订单号
 * @param 订单编号
 * @param 客户称谓
 * @param 代驾公里数 多少公里
 * @param 代驾开始前的等候 多少分种
 * @param 代驾过程中的等候 多少分钟
 * @param GPS经纬度
 * @param GPS经纬度
 * @param 街道名称
 */

$order_id = $params['order_id'];
$name = $params['name'];
$order_number = $params['order_number'];
$distance = $params['distance'];
$wait_before = $params['wait_before'];
$wait_on = $params['wait_on'];
$token = $params['token'];
$vipcard = $params['vipcard'];
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

$driver_id = $ret->driver_id;

if(!empty($vipcard)){
	$vip = UserVip::model()->find("id=:vipcode and name not like '%停用%'", array (':vipcard'=>$vipcard));
	
	if(!$vip){
		$message = array('code'=>3009,'message'=>'VIP卡号不存在');
		echo json_encode($message);
		Yii::app()->end();
	}
}



$order = Order::model()->find('order_id=:order_id and driver_id=:dirver_id and status=:status', array (
			':order_id'=>$order_id, 
			':dirver_id'=>$driver_id, 
			':status'=>Order::ORDER_READY));

if ($order){
	if (Order::model()->count('order_number=:order_number', array (':order_number'=>$order_number))){
		$message = array('code'=>3006,'message'=>'此订单号已存在，请使用其他订单号');
	} else {
		$beforeWaitingFee = 0;
		$onWaitingFee = 0;
		$distanceFee = 0;
		
		$beforeWaitingFee += floor($wait_before / Yii::app()->params['beforeWaitingStep']) * Yii::app()->params['beforeWaitingFee'];
		
		$onWaitingFee += floor($wait_on / Yii::app()->params['onWaitngStep']) * Yii::app()->params['onWaitingFee'];
		
		$distanceFee += (ceil(($distance / 1000 - Yii::app()->params['minDistance']) / Yii::app()->params['distanceStep'])) * Yii::app()->params['distanceFeeStep'];
		
		if (date('H', $order->booking_time) >= Yii::app()->params['minFeeHour'] 
			&& date('H', $order->booking_time) < Yii::app()->params['firstFeeHour']) 
			$distanceFee += Yii::app()->params['minFee'];
			
		if (date('H', $order->booking_time) >= Yii::app()->params['firstFeeHour'] 
			&& date('H', $order->booking_time) < Yii::app()->params['secondFeeHour']) 
			$distanceFee += Yii::app()->params['firstFee'];
			
		if (date('H', $order->booking_time) >= Yii::app()->params['secondFeeHour']) 
			$distanceFee += Yii::app()->params['secondFee'];
			
		if (date('H', $order->booking_time) >= Yii::app()->params['thirdFeeHour'] 
			&& date('H', $order->booking_time) < Yii::app()->params['minFeeHour']) 
			$distanceFee += Yii::app()->params['thirdFee'];
			
		$income = $distanceFee + $beforeWaitingFee + $onWaitingFee;
		
		//查询phone是否为有效用户，否则添加一个新的用户
		$customer = Customer::getCustomer($order->phone);
		if (!$customer) {
			$attr = array (
				'name'=>$name, 
				'phone'=>$order->phone, 
				'insert_time'=>date('Y-m-d h:i:s', time()));
			$customer = new Customer();
			$customer->attributes = $attr;
			$customer->save();
		}
		
		$orderStatus = ($order_number) ? Order::ORDER_COMPLATE : Order::ORDER_READY;
		$order_attr = array (
			'location_end'=>$street,
			'distance'=>ceil($distance / 1000),
			'name'=>$name,
			'vipcard'=>$vipcard,
			'user_id'=>$customer->id,
			'order_number'=>$order_number,
			'income'=>$income,
			'status'=>$orderStatus,
			'end_time'=>time());
		$order->attributes = $order_attr;
		if ($order->save()){
			if ($order_number){
				//记录日志
				$log = new OrderLog();
				$log_attr = array (
					'order_id'=>$order_id, 
					'description'=>'报单', 
					'operator'=>strtoupper($driver_id), 
					'created'=>time());
				$log->attributes = $log_attr;
				$log->save();
			}
			
			$position = new OrderPosition();
			$position->attributes = array (
				'order_id'=>$order_id, 
				'type'=>OrderPosition::POSITION_END, 
				'latitude'=>$latitude, 
				'longitude'=>$longitude, 
				'street'=>$street);
			$position->save();
			if ($order_number){
				$message = array(
					'code'=>0,
					'order_id'=>$order_id, 
					'name'=>$name,
					'order_number'=>$order_number, 
					'distance_fee'=>$distanceFee,
					'before_waiting_fee'=>$beforeWaitingFee,
					'on_waiting_fee'=>$onWaitingFee,
					'message'=>'已成功报单。您辛苦了。');
			} else {
				$message = array(
					'code'=>0,
					'order_id'=>$order_id,
					'name'=>$name,
					'order_number'=>'',
					'distance_fee'=>$distanceFee,
					'before_waiting_fee'=>$beforeWaitingFee,
					'on_waiting_fee'=>$onWaitingFee,
					'message'=>'订单完成，请及时报单。');
			}
			
			$employee = Employee::model()->find('imei=:imei', array(':imei'=>$driver->imei));
			$attr = array(
				'state'=>Employee::EMPLOYEE_IDLE,
				'longitude'=>$longitude,
				'latitude'=>$latitude,
				'report_time'=>date('Y-m-d H:i:s'),
				'update_time'=>date('Y-m-d H:i:s'));
			$employee->attributes = $attr;
			
			$employee->save();
		} else {
			$message = array('code'=>3007,'message'=>'此订单不能处理，请联系管理员');
		}
	}
} else {
	$message = array('code'=>3011,'message'=>'此订单不能处理，请联系管理员');
}


echo json_encode($message);
//操作直接报单
//给客户发送短信，代驾费用，等候费用，代驾时长，总等候时长，公里数