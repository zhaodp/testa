<?php
/**
 * 客户端API：c.order.cancel 取消订单接口
 * @param token
 * @param booking_id
 * @return json
 * @author AndyCong 2013-10-14
 * @version 1.0
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$booking_id = isset($params['booking_id']) ? trim($params['booking_id']) : '';
$type = isset($params['type']) ? $params['type'] : CustomerApiOrder::CANCEL_QUEUE;
if (empty($booking_id)) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
	echo json_encode($ret);return ;
}

//验证token
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
	$ret = array('code' => 1 , 'data' => '' , 'message' => '验证失败');
	echo json_encode($ret);return ;
}

//处理
//取消redis
$cancel_redis = CustomerApiOrder::model()->cancelQueueRedis($validate['phone'] , $booking_id);
if (!$cancel_redis) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '司机处于服务中,该订单不能被取消');
	echo json_encode($ret);return ;
}

switch ($type) {
	case CustomerApiOrder::CANCEL_QUEUE:
		$task = array(
		    'method' => 'api_queue_cancel',
		    'params' => array(
		        'phone' => $validate['phone'],
		        'booking_id' => $booking_id,
		    ),
		);
		break;
	case CustomerApiOrder::CANCEL_ORDER:
		$task = array(
		    'method' => 'api_order_cancel',
		    'params' => array(
		        'phone' => $validate['phone'],
		        'booking_id' => $booking_id,
		    ),
		);
		break;
	default:
		$task = array(
		    'method' => 'api_queue_cancel',
		    'params' => array(
		        'phone' => $validate['phone'],
		        'booking_id' => $booking_id,
		    ),
		);
		break;
}
Queue::model()->putin($task , 'apporder');
$ret = array('code' => 0 , 'data' => array('booking_id' => $booking_id) , 'message' => '取消成功');
echo json_encode($ret);return;