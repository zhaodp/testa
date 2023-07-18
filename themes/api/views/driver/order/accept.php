<?php
/**
 * 接收订单 API
 * Created by JetBrains PhpStorm.
 * User: zhanglimin
 * Date: 13-5-25
 * Time: 下午1:35
 * To change this template use File | Settings | File Templates.
 */

$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";

$queue_id=isset($params['queue_id'])&&!empty($params['queue_id']) ? trim($params['queue_id']) : "";

$driver_id=isset($params['driver_id'])&&!empty($params['driver_id']) ? trim($params['driver_id']) : "";

$type=isset($params['type'])&&!empty($params['type']) ? strtolower(trim($params['type'])) : "";

$push_msg_id=isset($params['push_msg_id'])&&!empty($params['push_msg_id']) ? strtolower(trim($params['push_msg_id'])) : "";

$gps_type=isset($params['gps_type'])&&!empty($params['gps_type']) ? strtolower(trim($params['gps_type'])) : "wgs84";

$lng=isset($params['lng'])&&!empty($params['lng']) ? trim($params['lng']) : "";

$lat=isset($params['lat'])&&!empty($params['lat']) ? trim($params['lat']) : "";

$log_time=isset($params['log_time'])&&!empty($params['log_time']) ? trim($params['log_time']) : "";

if (empty($token)||empty($queue_id)||empty($driver_id)||empty($type)||$type!='order'||empty($push_msg_id) || empty($lat) || empty($lng) || empty($log_time)) {
    $ret=array( 'code'=>2, 'message'=>'参数不正确!');
    echo json_encode($ret); return;
}

$driver = DriverStatus::model()->getByToken($token);

if (empty($driver) || $driver->token===null||$driver->token!==$token) {
    $ret=array( 'code'=>1, 'message'=>'token失效' );
    echo json_encode($ret);
    return;
}

if ($driver->driver_id != $driver_id) {
    $ret=array(
        'code'=>2,
        'message'=>'司机信息无效!'
    );
    echo json_encode($ret);
    return;
}

$check_order_queue = OrderQueue::model()->checkComfirm($queue_id);

if ($check_order_queue) {
    $ret=array(
        'code'=>2,
        'message'=>'队列己分配!'
    );
    echo json_encode($ret);
    return;
}

//添加task队列
$task=array(
    'method'=>'push_order_operate_new',
    'params'=>array(
        'queue_id'=>$queue_id,
        'driver_id'=>$driver_id,
        'type'=>$type,
        'push_msg_id'=>$push_msg_id,
        'confirm_time'=>date("Y-m-d H:i:s"), //确认时间
        'gps_type'=>$gps_type,
        'lng'=>$lng,
        'lat'=>$lat,
        'log_time'=>$log_time,
    )
);
//Queue::model()->task($task);
//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
Queue::model()->putin($task,'order');

//设置司机状态为服务中
$driver->status=1;

//删除派单队列
QueueDispatchDriver::model()->delete($driver_id);
$ret=array(
    'code'=>0,
    'message'=>'成功!'
);
echo json_encode($ret);return;
