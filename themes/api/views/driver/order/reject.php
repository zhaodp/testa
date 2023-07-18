<?php
/**
 * 推送弹回 API
 * Created by JetBrains PhpStorm.
 * User: zhanglimin
 * Date: 13-6-4
 * Time: 下午2:10
 * To change this template use File | Settings | File Templates.
 */

$token = isset($params['token']) && !empty($params['token']) ? trim($params['token']) : "";

$driver_id = isset($params['driver_id'])&&!empty($params['driver_id']) ? strtoupper(trim($params['driver_id'])) : "";

$queue_id=isset($params['queue_id'])&&!empty($params['queue_id']) ? trim($params['queue_id']) : "";

$order_id = isset($params['order_id']) ? $params['order_id'] : '';


//$driver = DriverStatus::model()->getByToken($token);
//
//if ($driver->token===null||$driver->token!==$token) {
//    $ret=array( 'code'=>1, 'message'=>'token失效' );
//    echo json_encode($ret);
//    return;
//}
//
//if ($driver->driver_id != $driver_id) {
//    $ret=array(
//        'code'=>2,
//        'message'=>'司机信息无效!'
//    );
//    echo json_encode($ret);
//    return;
//}


if (empty($driver_id)||empty($queue_id)||empty($token)){
    $ret=array(
        'code'=>2,
        'message'=>'参数不正确!'
    );
    echo json_encode($ret);
    return;
}

//添加task队列
$task=array(
    'method'=>'push_order_reject_log',
    'params'=>array(
        'queue_id'=>$queue_id,
        'driver_id'=>$driver_id,
        'order_id'=>$order_id,
        'created'=>date("Y-m-d H:i:s"), //确认时间
    )
);

//Queue::model()->task($task);
//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
Queue::model()->putin($task,'apporder');


//解锁司机（不解锁司机）
//QueueDispatchDriver::model()->delete($driver_id);

//检查司机状态，如果是服务中，则置成空闲
$driver_info = DriverStatus::model()->get($driver_id);

$driver_info->status = 0;


//解锁订单
if (!empty($order_id)) {
	QueueDispatchOrder::model()->delete($order_id);
}


$ret=array(
    'code'=>0,
	'status'=>$driver_info->status,
    'message'=>'成功!'
);
echo json_encode($ret);
return;
