<?php
/**
 * 客户端API：c.order.timeout 订单超时接口。
 * @param token
 * @param booking_id
 * @return json
 * @author AndyCong 2013-10-14
 * @version 1.0
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$booking_id = isset($params['booking_id']) ? trim($params['booking_id']) : '';
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

//处理(更新redis、放入队列取消订单)
$cancel_redis = CustomerApiOrder::model()->cancelQueueRedis($validate['phone'] , $booking_id);
$task = array(
    'method' => 'api_queue_cancel',
    'params' => array(
        'phone' => $validate['phone'],
        'booking_id' => $booking_id,
    ),
);
Queue::model()->putin($task , 'apporder');
$ret = array('code' => 0 , 'data' => array('booking_id' => 1) , 'message' => '处理成功');
echo json_encode($ret);return;