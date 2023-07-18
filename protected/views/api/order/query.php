<?php
$token = $params['token'];
$range = $params['range'];

//查找司机登录信息
$ret = DriverToken::validateToken($token);

if(!$ret){
	$message = array('code'=>1004,'message'=>'登录已过期');
	echo json_encode($message);
	Yii::app()->end();
}

$driver_id = $ret->driver_id;

$criteria = new CDbCriteria();
$criteria->select = 'order_id';

switch ($range) {
	case 'today' :
		$dateStart = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$dateEnd = time();
		break;
	case 'yestoday' :
		$dateStart = mktime(0, 0, 0, date("m"), date("d")-1, date("Y"));
		$dateEnd = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		break;
	case 'week' :
		$dateStart = mktime(0, 0, 0, date("m"), date("d")-date("w")+1, date("Y"));
		$dateEnd = time();
		break;
	case 'month' :
		$dateStart = mktime(0, 0, 0, date("m"), 1, date("Y"));
		$dateEnd = time();
		break;
}

$criteria->condition = 'driver_id=:driver_id and status=:status';
$criteria->params = array (
	':driver_id'=>$driver_id, 
	':status'=>Order::ORDER_READY);
$criteria->addBetweenCondition('booking_time', $dateStart, $dateEnd);
//未报单订单
$init_order = Order::model()->count($criteria);

$criteria->condition = 'driver_id=:driver_id';
$criteria->params = array (
	':driver_id'=>$driver_id);
$criteria->addBetweenCondition('booking_time', $dateStart, $dateEnd);
$criteria->addInCondition('status', array (
	Order::ORDER_COMPLATE, 
	Order::ORDER_NOT_COMFIRM));
//已完成订单
$finished_order = Order::model()->count($criteria);

$criteria->condition = 'driver_id=:driver_id and status=:status';
$criteria->params = array (
	':driver_id'=>$driver_id, 
	':status'=>Order::ORDER_COMFIRM);
$criteria->addBetweenCondition('booking_time', $dateStart, $dateEnd);
//销单待审核
$waiting_order = Order::model()->count($criteria);

$json = json_encode(array (
	'driver_id'=>$driver_id, 
	'finished'=>$finished_order, 
	'waiting'=>$waiting_order, 
	'init'=>$init_order));

echo $json;
