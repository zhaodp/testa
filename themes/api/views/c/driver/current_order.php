<?php
/**
 * 获取司机当前订单，目前提供给洗车业务使用
 */

if(empty($params['token']) || empty($params['driver_id'])) {
    echo json_encode(array('code' => 2, 'message' => '参数有误'));
    return;
}

$isTokenValid = CustomerToken::model()->validateToken($params['token']);
if(!$isTokenValid) {
    echo json_encode(array('code' => 1, 'message' => '验证失败'));
    return ;
}

$response = array('code' => 0);

$sources = null;
if(isset($params['source'])) {
    if(Order::SOURCE_WASHCAR_CLIENT == $params['source']) {
        $sources = Order::$washcar_sources;
    }
}
$order = Order::model()->getOngoingOrder($params['driver_id'], $sources);
if(empty($order)) {
    echo json_encode($response);
    return;
}

$order_queue_map = OrderQueueMap::model()->getByOrderID($order->order_id);
$order_queue = OrderQueue::model()->getByID($order_queue_map->queue_id);
$booking_key = $order->phone . '_' . $order_queue->callid;
$booking_orders = QueueApiOrder::model()->get($booking_key, "orders");
if(empty($booking_orders)) {
    echo json_encode($response);
    return;
}

if(array_key_exists($order->order_id, $booking_orders)) {
    $response['order_id'] = $order->order_id;
    $response['order_state'] = $booking_orders[$order->order_id]["order_state"];
} else if(array_key_exists($order->order_number, $booking_orders)) {
    $response['order_id'] = $order->order_number;
    $response['order_state'] = $booking_orders[$order->order_number]["order_state"];
}
$response['booking_id'] = $order_queue->callid;

echo json_encode($response);
return;

