<?php
/**
 * 客户端API：c.driver.position 获取当前订单司机的位置,从redis中取，并且验证订单情况，订单时间超过24小时则不返回位置了
 * @param string $token
 * @param int $booking_id
 * @param int $order_id
 * @param string $driver_id
 * @return json
 * @author AndyCong 2013-10-15
 */
//接收并验证参数
$token 		= isset($params['token']) ? $params['token'] : '';
$booking_id = isset($params['booking_id']) ? $params['booking_id'] : '';
$driver_id = isset($params['driver_id']) ? $params['driver_id'] : '';
$order_id 	= isset($params['order_id']) ? $params['order_id'] : '';
$gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'google';

if( empty($booking_id) || empty($driver_id) ){
	$ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
	echo json_encode($ret);return ;
}

//验证token
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
	$ret = array('code' => 1 , 'data' => '' , 'message' => '验证失败');
	echo json_encode($ret);return ;
}

//获取订单详情
$driver = CustomerApiOrder::model()->getDriverPosition($driver_id , $gps_type , $validate['phone'] , $booking_id);
if (empty($driver)) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '订单已超时');
	echo json_encode($ret);return ;
}

//如果返回的order_id为空 , 则将传进来的order_id赋值到driver信息中
if (empty($driver['order_id'])) {
	$driver['order_id'] = $order_id;
}

$data = array(
    'driver' => $driver,
    'next' => 10,
    'polling_count' => isset($params['polling_count']) ? $params['polling_count'] : 1,
    'timeout' => 600,
);

$ret = array('code' => 0 , 'data' => $data , 'message' => '成功',);
echo json_encode($ret);return;

