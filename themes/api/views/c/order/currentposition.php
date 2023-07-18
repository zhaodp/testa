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

if(empty($order_id) || empty($token)) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
	echo json_encode($ret);return ;
}

$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
	$ret = array('code' => 1 , 'data' => '' , 'message' => '验证失败');
	echo json_encode($ret);return ;
}

// Get the order info

if (strlen($order_id) > 11 && is_numeric($order_id)) {
    // Single booking order
    $order_id = ROrder::model()->getOrder($order_id , 'order_id');
}

$position = OrderPosition::model()->getOrderCurrentPosition($order_id);

if(empty($position)) {
    $ret = array('code' => 2 , 'data' => '' , 'message' => '读取数据失败');
} else{
    $ret = array('code' => 0 , 'data' => $position , 'message' => '读取数据成功');
}
echo json_encode($ret); 
return;

