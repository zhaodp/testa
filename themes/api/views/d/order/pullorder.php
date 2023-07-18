<?php
/**
 * 拉取订单(目前只拉取400订单)
 * @author AndyCong<congming@edaijia-staff.cn>
 * @version 2014-01-22
 */


$token = isset ( $params ['token'] ) ? trim ( $params ['token'] ) : '';
if (empty ( $token )) {
	$ret = array (
			'code' => 2,
			'message' => '参数有误' 
	);
	echo json_encode ( $ret );
	return;
}

// 验证token
$driver = DriverStatus::model ()->getByToken ( $token );
if (! $driver) {
	$ret = array (
			'code' => 1,
			'message' => '请重新登录' 
	);
	echo json_encode ( $ret );
	return;
}

$order_id = isset ( $params ['order_id'] ) ? trim ( $params ['order_id'] ) : '';

//验证order id是否在请求参数中
if (! empty ( $order_id )) {
	$data = ROrder::model()->getMessage($order_id);
	//在redis中验证订单数据的完整性，content，content，push_msg_id，push_distinct_id，queue_id是必须存在的数据
	if (!empty($data['content'])&&
		!empty($data['content'])&&
		!empty($data['push_msg_id'])&&
		!empty($data['push_distinct_id'])&&
		!empty($data['queue_id']))
	{
            EdjLog::info('SmsPushPullOrder|'.$order_id.'|'.$data['content']);
	//timestamp，timeout如果缺失，则使用服务器当前时间和默认超时时间
	$timestamp = isset( $data['timestamp'])?$data['timestamp']:time();
	$timeout = isset( $data['timeout'])?$data['timeout']:120;
	$content = json_decode($data['content']);
	$order_info = array (
			'timestamp' => $timestamp,
			'content' =>$content,
			'timeout' =>$timeout,
			'type' =>$data['type'],
			'push_msg_id' =>$data['push_msg_id'],
			'push_distinct_id' =>$data['push_distinct_id'],
			'queue_id' =>$data['queue_id'],
	);
	$order_message = array(
			'code' => 0,
			'data' => $order_info,
			'message' => '获取成功'
	);
	echo json_encode($order_message);
	return;
	}
	else {
		EdjLog::error('SMS order or data miss in Redis,order_id:'.$order_id);
		
		$ret = array (
				'code' => 2,
				'message' => '订单获取失败'
		);
		echo json_encode ( $ret );
		return ;
	}
	
}

 else {
	// 获取订单信息
	$data = Order::model ()->pullOrderInfo ( $driver->driver_id );
	$ret = array (
			'code' => 0,
			'data' => $data,
			'message' => '获取成功' 
	);
	echo json_encode ( $ret );
	return;
}
?>
