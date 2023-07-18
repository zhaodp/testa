<?php
if (!isset($params['order_id'])) {
	$ret = array(
	    'code'=>1,
	    'message'=>'操作失败'
	);
}
$token = $params['token'];
$queue_id = $params['order_id'];
//需要优化，增加缓存。add by sunhongjing
$validate = CustomerToken::model()->validateToken($token);
if ($validate){
	$order = OrderQueue::model()->getOrderQueueByID( $queue_id , $validate['phone'] );
	if (!$order) {
		$ret = array(
		    'code'=>1,
		    'message'=>'操作失败'
		);
	} else {
		if ($order['flag'] == OrderQueue::QUEUE_WAIT) {
			$cancel = OrderQueue::model()->cancelOrderQueueByID($queue_id);
			if ($cancel) {
				$ret = array(
				    'code'=>0,
				    'message'=>'订单取消成功'
				);
			} else {
				$ret = array(
				    'code'=>1,
				    'message'=>'订单取消失败'
				);
			}
		} elseif ($order['flag'] == OrderQueue::QUEUE_CANCEL) {
			$ret = array(
			    'code'=>1,
			    'message'=>'该订单已被取消'
			);
		} else {
			$ret = array(
			    'code'=>1,
			    'message'=>'订单已派出，请联系司机取消'
			);
		}
	}
	
} else {
	$ret = array(
		'code'=>2,
		'message'=>'验证失败'
	);
}
echo json_encode($ret);