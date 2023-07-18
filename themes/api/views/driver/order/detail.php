<?php
//需要写清楚注释 add by sunhongjing at 2013-5-19
/*
 * modify  zhanglimin 2013-06-07
 * 切换验证token方式
 */

$driver = DriverStatus::model()->getByToken($params['token']);
if (empty($driver) || $driver->token === null || $driver->token !== $params['token']) {
    $ret = array(
        'code' => 1,
        'message' => '请重新登录'
    );
    echo json_encode($ret);
    return;
}

$order = Order::model()->getOrderById($params['order_id']);
if (empty($order)) {
    $ret = array(
        'code' => 1,
        'message' => '请重新登录'
    );
    echo json_encode($ret);
    return;
}

if (strtoupper($order['driver_id']) != strtoupper($driver->driver_id)) {
    $ret = array(
        'code' => 1,
        'message' => '请重新登录'
    );
    echo json_encode($ret);
    return;
}

$orderLog = OrderLog::model()->getOrderLogByOrderId($params['order_id']);
$log = array();
foreach ($orderLog as $key => $value) {
    $value['created'] = date('Y-m-d H:i', $value['created']);
    $log[$key] = $value;
}
unset($order['user_id']);
unset($order['car_id']);
unset($order['driver']);
unset($order['imei']);
unset($order['driver_id']);
unset($order['driver_phone']);
unset($order['city_id']);
unset($order['call_type']);
unset($order['order_date']);
unset($order['reach_time']);
unset($order['reach_distance']);
$order['phone'] = substr_replace($order['phone'], '****', 3, 4);
$order['call_time'] = date('Y-m-d H:i', $order['call_time']);
$order['booking_time'] = date('Y-m-d H:i', $order['booking_time']);
$order['start_time'] = date('Y-m-d H:i', $order['start_time']);
$order['end_time'] = date('Y-m-d H:i', $order['end_time']);


//$favorable = Order::model()->getOrderFavorable($order['phone'], $order['booking_time'], $order['source'], $params['order_id']);
//$order['type'] = $favorable['code'];
//if ($favorable['code'] != 0) {
//    $order['card'] = $favorable['card'];
//    $order['money'] = $favorable['money'];
//}

$favorable = Order::model()->getOrderFavorable($order['phone'], $order['booking_time'], $order['source'], $params['order_id']);
if ($favorable) {
    $order['type'] = $favorable['code'];
    $order['card'] = $favorable['card'];
    $order['money'] = $favorable['money'] + $favorable['user_money'];
}


$orderExt = OrderExt::model()->getPrimary($order['order_id']);
if (!empty($orderExt)) {
    $order['waiting_time'] = $orderExt['wait_time'];
    $order['remark'] = $orderExt['mark'];
} else {
    $order['waiting_time'] = 0;
    $order['remark'] = '';
}

$order['source'] = Order::SourceToString($order['source']);

switch ($order['status']) {
    case Order::ORDER_READY:
        $order['status'] = '未报单';
        break;
    case Order::ORDER_CANCEL:
        $order['status'] = '已销单';
        $order['cancel_type'] = Dict::item('cancel_type', $order['cancel_type']);
        break;
    case Order::ORDER_COMFIRM:
        $order['status'] = '销单待审核';
        $order['cancel_type'] = Dict::item('cancel_type', $order['cancel_type']);
        break;
    case Order::ORDER_COMPLATE:
        $order['status'] = '已报单';
        break;
    case Order::ORDER_NOT_COMFIRM:
        $order['status'] = '拒绝销单';
        break;
}
$orderDetail = FinanceCastHelper::getOrderFeeDetail($params['order_id']);
$order= array_merge($order, $orderDetail);
$ret = array(
    'code' => 0,
    'detail' => $order,
    'log' => $log,
    'message' => '读取成功');
echo json_encode($ret);
return;
