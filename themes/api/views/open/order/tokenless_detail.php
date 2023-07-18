<?php
/**
 *
 * 提供为外部不要 token 即可查询订单信息的接口
 *
 * User: tuan
 * Date: 15/3/17
 * Time: 23:29
 */


$ret = array('code' => 0, 'message' => '操作成功');
$order_id = trim($params['order_id']);
$needPhone = isset($params['need_phone']) ? $params['need_phone'] : 0;

if (empty($order_id)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}

//兼顾报单后详情页展示
//如果是order_number 则将缓存的order_id取出
if (strlen($order_id) > 11 && is_numeric($order_id)) {
    $db_order_id = ROrder::model()->getOrder($order_id , 'order_id');
    if (!empty($db_order_id)) {
        $order_id = $db_order_id;
    }
}

$order= CustomerApiOrder::model()->getOrderInfoByOrderID($order_id);

if (!$order) {
    $ret = array(
        'code' => 2,
        'message' => '操作失败'
    );
} else {
    if($needPhone){
        //过滤敏感信息,不返回
        $tmpOrder = Order::model()->getOrdersById($order_id);
        $order['phone'] = isset($tmpOrder['phone']) ? $tmpOrder['phone'] : 0;
    }
    $driver_info = Helper::foramt_driver_detail($order['driver_id'], 'google');
    //司机信息
    $order['driver'] = $driver_info;
    $ret = array(
        'code' => 0,
        'data' => $order,
        'message' => '获取成功'
    );
}
echo json_encode($ret);
return;