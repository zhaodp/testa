<?php
/**
 * 南京活动(司机接单到司机就位时间在9分45秒之内，根据公里数补贴司机，否则客户免单)
 * @params type            活动类型（1、南京活动）
 * @params ready_time      司机接单到司机就位时间:秒
 * @params ready_distance  司机接单到司机就位公里数
 * @author AndyCong<congming@edaijia-staff.cn>
 * @version 2014-04-02
 */

//接收参数
$token          = isset($params['token']) ? trim($params['token']) : '';
$order_id       = isset($params['order_id']) ? trim($params['order_id']) : '';
$type           = isset($params['type']) ? intval($params['type']) : 0;
$ready_time     = isset($params['ready_time']) ? intval($params['ready_time']) : 0;
$ready_distance = isset($params['ready_distance']) ? floatval($params['ready_distance']) : 0.00;

if(empty($token) || empty($order_id)) {
    $ret = array('code' => 2 , 'message' => '参数有误');
    echo json_encode($ret);return;
}

//客户端存在异常 则不走活动
if($ready_time == 0 && ($ready_distance == 0.00 || $ready_distance == 0)) {
    $ret = array('code' => 0 , 'message' => '');
    echo json_encode($ret);return;
}

//验证token
$driver = DriverStatus::model()->getByToken($token);
if(!$driver) {
    $ret = array('code' => 1 , 'message' => '请重新登录');
    echo json_encode($ret);return;
}

$activity_config = Yii::app()->params['activity'];

$city_id = $driver->city_id;

//城市活动不存在返回空
if(empty($activity_config[$city_id])) {
    $ret = array('code' => 0 , 'message' => '');
    echo json_encode($ret);return;
}

//城市活动关闭返回空
if(!isset($activity_config[$city_id]['subsidy']) || !$activity_config[$city_id]['subsidy']['turn_on']) {
    $ret = array('code' => 0 , 'message' => '');
    echo json_encode($ret);return;
}

//查找订单
if(strlen($order_id) > 11) {
    $order = Order::model()->getOrderByOrderNumberOnly($order_id);
} else {
    $order = Order::model()->getOrdersById($order_id);
}

//订单不存在||400订单||补单 不参与补贴活动
$source = isset($order['source']) ? $order['source'] : Order::SOURCE_CLIENT;
$channel = isset($order['channel']) ? $order['channel'] : CustomerApiOrder::QUEUE_CHANNEL_SINGLE_DRIVER;
if(empty($order) || !Order::model()->checkActivityOrder($source,$channel) || $city_id != $order['city_id']) {
    $ret = array('code' => 0 , 'message' => '');
    echo json_encode($ret);return;
}

$subsidy = $activity_config[$city_id]['subsidy'];

$msg = '';
$subsidy_money = 0;
if($ready_time < $subsidy['ready_time']) {

    //补偿司机
    foreach($subsidy['driver_subsidy'] as $key=>$driver_subside) {

        if($ready_distance >= $driver_subside['start_distance'] && $ready_distance < $driver_subside['end_distance']) {
            $msg           = $driver_subside['msg'];
            $subsidy_money = $driver_subside['subsidy'];
        }

    }

} else {

    //客户免单
    $msg           = $subsidy['customer_free'];
    $subsidy_money = 0;

}

$ret = array('code' => 0 , 'subsidy' => $subsidy_money , 'message' => $msg);
echo json_encode($ret);return;
