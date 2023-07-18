<?php
/**
 * Get the driver positions for an order
 *
 * Driver positions are splitted into two parts,
 * the arrive part and the drive part.
 *
 * @author qiujianping@edaijia-staff.cn
 * @created 2014-06-09
 */

// Check the params
$token = isset($params['token']) ? trim($params['token']) : '';
$order_id = isset($params['order_id']) ? trim($params['order_id']):'';
$start_time = isset($params['start']) ? trim($params['start']):'';

if(empty($order_id) || empty($token)) {
	$ret = array('code' => 2 , 'message' => '参数有误');
	echo json_encode($ret);return ;
}

$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
	$ret = array('code' => 1 , 'message' => '验证失败');
	echo json_encode($ret);return ;
}

// Get the order info
// Get order start time and get order end time

if (strlen($order_id) > 11 && is_numeric($order_id)) {
    // Single booking order
    $order_id = ROrder::model()->getOrder($order_id , 'order_id');
}

$positions = OrderPosition::model()->getOrderPositions($order_id, $start_time);

if(empty($positions)) {
	$ret = array('code' => 2 , 'message' => '读取数据失败');
} else {
	$ret = array('code' => 0 , 'data' => $positions , 'message' => '读取数据成功');
}

echo json_encode($ret); 
return;

