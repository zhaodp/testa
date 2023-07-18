<?php
/**
 * 到达客人地
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-09-04
 */

//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$order_id = isset($params['order_id']) ? intval($params['order_id']) : 0;
$gps_type = isset($params['gps_type']) ? trim($params['gps_type']) : 'wgs84';
$lng = isset($params['lng']) ? trim($params['lng']) : '';
$lat = isset($params['lat']) ? trim($params['lat']) : '';
$log_time = isset($params['log_time']) ? trim($params['log_time']) : "";
if(empty($token) || 0 == $order_id || empty($lng) || empty($lat) || empty($log_time)){
    $ret=array('code'=>2 , 'message'=>'参数不正确!');
    echo json_encode($ret);return;
}

//验证token
$driver = DriverStatus::model()->getByToken($token);
if (empty($driver) || $driver->token===null||$driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}

//添加task队列
$task=array(
    'method'=>'push_order_position',
    'params'=>array(
        'order_id'=>$order_id,
        'flag'=>OrderPosition::FLAG_ARRIVE,
        'gps_type'=>$gps_type,
        'lng'=>$lng,
        'lat'=>$lat,
        'log_time'=>$log_time,
        'name'=>'',
        'phone'=>'',
        'car_number'=>'',
    )
);
Queue::model()->putin($task , 'apporder');

//返回成功信息
$ret=array('code'=>0 , 'message'=>'成功!');
echo json_encode($ret);return;