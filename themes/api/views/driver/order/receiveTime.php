<?php
/**
 * driver receive order time
 * @author aiguoxin
 * @version 2014-04-14 
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$order_id = isset($params['order_id']) ? trim($params['order_id']) : '';
$order_number = isset($params['order_number']) ? trim($params['order_number']) : '';
$driver_receive_time = isset($params['driver_receive_time']) ? trim($params['driver_receive_time']) : '';

if (strlen($order_id) > 11 && is_numeric($order_id)) {
	$order_number = $order_id;
}

if(empty($token) || (empty($driver_receive_time)&&$driver_receive_time !=0)){
    $ret=array('code'=>2 , 'message'=>'参数不正确!');
    echo json_encode($ret);return;
}

//解决order_id大于11位数，实际传的是order_number cry...
if(empty($order_id) && empty($order_number)){
    $ret=array('code'=>2 , 'message'=>'参数不正确!');
    echo json_encode($ret);return;
}

// 验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver===null||$driver->token===null||$driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}
if(!empty($order_number)){
	$order = Order::model()->getOrderByOrderNumberOnly($order_number);
	if(empty($order)){
		//can not find order
		$ret=array('code'=>2 , 'message'=>'找不到对应的订单');
		echo json_encode($ret);return;
	}else{
		$order_id = $order['order_id'];
	}
}


$flag = OrderExt::model()->updateDriverReceiveTime($order_id,$driver_receive_time);

//返回成功信息
$ret=array('code'=>0 , 'message'=>'成功');
echo json_encode($ret);return;
