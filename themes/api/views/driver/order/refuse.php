<?php
/**
 * 推送弹回 API
 * Created by JetBrains PhpStorm.
 * User: zhanglimin
 * Date: 13-6-4
 * Time: 下午2:10
 * To change this template use File | Settings | File Templates.
 */

if(Yii::app()->params['order_architecture_refactor_on']) {
    $result = RefuseOrderService::getInstance()->refuseOrder($params);
    echo json_encode($result);
    return;
}

$token = isset($params['token']) && !empty($params['token']) ? trim($params['token']) : "";
$queue_id=isset($params['queue_id'])&&!empty($params['queue_id']) ? trim($params['queue_id']) : "";
$order_id = isset($params['order_id']) ? $params['order_id'] : '';

//增加类型 BY AndyCong 2013-12-26
$type = isset($params['type']) ? $params['type'] : OrderRejectLog::REJECT_TYPE_SYSTEM_BACK;
if (empty($token) || empty($queue_id)) {
    EdjLog::info('Refuse order|Invalid params|'.$order_id);
    $ret=array('code'=>2 , 'message'=>'参数不正确!');
    echo json_encode($ret);return;
}

//$reject_type_arr = array(
//    OrderRejectLog::REJECT_TYPE_SYSTEM_BACK,
//    OrderRejectLog::REJECT_TYPE_RECEIVE_FAILED,
//    OrderRejectLog::REJECT_TYPE_DRIVER_REJECT,
//    OrderRejectLog::REJECT_TYPE_SERVICE_REJECT,
//    OrderRejectLog::REJECT_TYPE_PHONE_REJECT,
//);
//if (!in_array($type , $reject_type_arr)) {
//	$ret=array('code'=>2 , 'message'=>'类型有误!');
//    echo json_encode($ret);return;
//}
//
$driver = DriverStatus::model()->getByToken($token);

if ($driver === null || $driver->token === null || $driver->token!==$token ) {
    EdjLog::info('Refuse order|Invalid token|'.$order_id);
    $ret=array( 'code'=>1, 'message'=>'token失效' );
    echo json_encode($ret);
    return;
}

$driver_id = $driver->driver_id;

//添加task队列
if (strlen($order_id) > 11 && is_numeric($order_id)) {
	$task=array(
	    'method'=>'upload_driver_reject_log',
	    'params'=>array(
	        'queue_id'=>$queue_id,
	        'driver_id'=>$driver_id,
	        'order_id'=>$order_id,
	        'type' => $type,
	        'created'=>date("Y-m-d H:i:s"), //确认时间
	    )
	);
} else {
	$task=array(
	    'method'=>'push_order_reject_log',
	    'params'=>array(
	        'queue_id'=>$queue_id,
	        'driver_id'=>$driver_id,
	        'order_id'=>$order_id,
	        'type' => $type,
	        'created'=>date("Y-m-d H:i:s"), //确认时间
	    )
	);
}
$ret = Queue::model()->putin($task,'apporder');
if(!$ret) {
    EdjLog::info('|Refuse order|Failed|Putin array failed|'.$driver_id.'|'.$order_id);
}
	
//添加队列结束

//检查司机状态，如果是服务中，则置成空闲
//$driver_info = DriverStatus::model()->get($driver_id);
//$driver_info->status = 0;

//改成解锁司机，而不直接改司机的状态。add by sunhongjing 2014-01-17
QueueDispatchDriver::model()->delete($driver_id);

//解锁订单
if (!empty($order_id)) {
	QueueDispatchOrder::model()->delete($order_id);
}

//如果司机主动拒单,将司机设置为上次派送派过司机
if($type == OrderRejectLog::REJECT_TYPE_DRIVER_REJECT) {
    QueueDispatchOrder::model()->queueDispatchedDriver($order_id , $driver_id);
}

$ret=array(
    'code'=>0,
	'status'=>$driver->status,
    'message'=>'成功!'
);
echo json_encode($ret);
return;


