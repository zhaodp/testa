<?php
/**
 * 接收订单 (仿滴滴打车)
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-07-20
 */

//接收并验证参数
$queue_id = isset($params['queue_id']) ? $params['queue_id'] : 0;
$order_id = isset($params['order_id']) ? $params['order_id'] : 0;
//$driver_id = isset($params['driver_id']) ? $params['driver_id'] : '';
$push_msg_id = isset($params['push_msg_id']) ? $params['push_msg_id'] : 0;
$gps_type = isset($params['gps_type']) ? $params['gps_type'] : "wgs84";
$lng = isset($params['lng']) ? $params['lng'] : '';
$lat = isset($params['lat']) ? $params['lat'] : '';
$log_time = isset($params['log_time']) ? $params['log_time'] : '';
$token = isset($params['token']) ? $params['token'] : '';
if (0 == $queue_id || 0 == $order_id || 0 == $push_msg_id || empty($lng) || empty($lat) || empty($log_time)) {
    $ret = array('code' => 2 , 'message' =>'参数有误');
    echo json_encode($ret);return ;
}

//验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver) {
    $params = array(
        'queue_id' => $queue_id,
        'order_id' => $order_id,
        'driver_id' => $driver->driver_id,
        'push_msg_id' => $push_msg_id,
        'gps_type' => $gps_type,
        'lng' => $lng,
        'lat' => $lat,
        'log_time' => $log_time,
    );
    $task = array(
        'method' => 'order_audio_receive_operate',
        'params' => $params,
    );
    Queue::model()->putin($task,'order');
    $ret = array('code' => 0 , 'message' => '接单成功');
} else {
    $ret = array('code' => 1 , 'message' => '请重新登录');
}
echo json_encode($ret);
return ;