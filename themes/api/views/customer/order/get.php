<?php
	$ret = array( 'code'=>1,'message'=>'获取失败');
	echo json_encode($ret);return;
	
//获得用户订单列表 返回是否成功
//这个接口需要客户端配合检查是否还在使用，如果使用中，则需要优化，增加缓存,写清楚注释。add by sunhongjing
$token = $params['token'];
$orderId = $params['orderId'];
$validate = CustomerToken::model()->validateToken($token);

if ($validate){
	$order = Order::getByCustomerPhoneID($validate->phone, $orderId);
	
	$orderInfo = array(
		'order_id'=>$order['order_id'],
		'driver_id'=>$order['driver_id'],
		'booking_time'=>$order['booking_time'],
		'start_time'=>$order['start_time'],
		'end_time'=>$order['end_time'],
		'location_start'=>$order['location_start'],
		'location_end'=>$order['location_end'],
		'distance'=>$order['distance'],
		'income'=>$order['income'],
		'driver'=>$order['driver'],
		'vipcard'=>$order['vipcard'],
	);
	
	$ret = array(
		'code'=>0,
		'orderInfo'=>$orderInfo,
		'message'=>'获取成功');
} else {
	$ret = array(
		'code'=>1,
		'message'=>'获取失败');
}

echo json_encode($ret);