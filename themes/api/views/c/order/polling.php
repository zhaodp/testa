<?php
/**
 * 客户端API：c.order.polling 拉取订单信息，检查是否接单成功,只需要读取redis的订单信息里是否有了driver_id即可
 * @param token
 * @param booking_id
 * @param $polling_start  请求时间
 * @param $polling_count  请求次数
 * @return json
 * @author AndyCong 2013-10-14
 * @version 1.0
 */
if(Yii::app()->params['order_architecture_refactor_on']) {
    $result = PollOrderService::getInstance()->pollUntreatedOrder($params);
    echo json_encode($result);
    return;
}

//验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$booking_id = isset($params['booking_id']) ? $params['booking_id'] : '';
$booking_type = isset($params['booking_type']) ? $params['booking_type'] : '';
$polling_start = isset($params['polling_start']) ? $params['polling_start'] : '';
$polling_count = isset($params['polling_count']) ? $params['polling_count'] : '';
$gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'google';
if (empty($booking_id) || empty($polling_start) || empty($polling_count)) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
	echo json_encode($ret);return ;
}

//验证token
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
	$ret = array('code' => 1 , 'data' => '' , 'message' => '验证失败');
	echo json_encode($ret);return ;
}

$data = array();
$data['eta'] = '';
$data['next'] = 10;
if ($polling_count != 1) {
	$data['next'] = 5;
}

switch ($booking_type) {
	case CustomerApiOrder::QUEUE_CHANNEL_SINGLE_DRIVER:
		$data['timeout'] = CustomerApiOrder::POLLING_SECOND_DRIVER;
		break;
	case CustomerApiOrder::QUEUE_CHANNEL_SINGLE_CHANGE:
		$data['timeout'] = CustomerApiOrder::POLLING_SECOND_CHANGE;
		break;
	case CustomerApiOrder::QUEUE_CHANNEL_BOOKING:
		$data['timeout'] = CustomerApiOrder::POLLING_SECOND_BOOKING;
		break;
	case CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER:
		$data['timeout'] = CustomerApiOrder::POLLING_SECOND_BOOKING;
		break;
	case CustomerApiOrder::QUEUE_CHANNEL_FIFTEEN_MIN_BOOKING:
		$data['timeout'] = CustomerApiOrder::POLLING_SECOND_FIFTEEN_MIN_BOOKING;
		break;
	case CustomerApiOrder::QUEUE_CHANNEL_400_OR_OTHER: //400订单
		$data['timeout'] = CustomerApiOrder::POLLING_SECOND_400_ORDER;
		break;
	default:
		$data['timeout'] = CustomerApiOrder::POLLING_SECOND_DRIVER;
		break;
}
//$data['timeout'] = 60;
$data['polling_count'] = $polling_count;
$data['text'] = '发送订单中...';
//获取redis中订单信息
//增加司机端拒绝返回参数要修改

$order = CustomerApiOrder::model()->getOrderByBookingID($validate['phone'] , $booking_id , $data['timeout']);
if ($order['driver_id'] == Push::DEFAULT_DRIVER_INFO) {
	$order['driver_id'] = '';
}
$data['driver_id'] = $order['driver_id'];
$data['order_id'] = $order['order_id'];
$data['polling_state'] = $order['polling_state'];
//下边的返回值需要有改动
//$data['number'] = 6;

//返回数据
$ret = array('code' => 0 , 'data' => $data , 'message' => '获取成功');
echo json_encode($ret);return ;
