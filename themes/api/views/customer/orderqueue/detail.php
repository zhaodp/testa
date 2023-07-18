<?php
/**
 * 一键预约获取订单详情
 * @author AndhCong<congming@edaijia.cn>
 * @version 2013-03-28
 */

$token = $params['token'];
$queue_id = $params['order_id'];
$gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'google';
//需要优化，增加缓存。add by sunhongjing
$validate = CustomerToken::model()->validateToken($token);
if ($validate){
	$order = OrderQueue::model()->getOrderQueueByID( $queue_id , $validate['phone'] , $gps_type , $token);
	if (!$order) {
		$ret = array(
		    'code'=>1,
		    'message'=>'操作失败'
		);
	} else {
		$ret = array(
		    'code'=>0,
		    'order'=>$order,
		    'message'=>'获取成功'
		);
	}
	
} else {
	$ret = array(
		'code'=>2,
		'message'=>'验证失败'
	);
}
echo json_encode($ret);