<?php
//需要写清楚注释 add by sunhongjing at 2013-5-19
/**
 * @author zhanglimin 2013-06-04
 * 先简单改下，线上着急用，等过会在把代码优化下~
 * modify zhanglimin 2013-06-07
 * 改为队列处理
 */

$driver = DriverStatus::model()->getByToken($params['token']);
if (empty($driver) || $driver->token===null||$driver->token!==$params['token']) {
    $ret=array('code'=>1,'message'=>'请重新登录');
    echo json_encode($ret);return;
}

$order = Order::model()->getOrderById($params['order_id']);
if(empty($order)){
    $ret=array('code'=>1,'message'=>'请重新登录');
    echo json_encode($ret);return;
}

if(strtoupper($order['driver_id']) != strtoupper($driver->driver_id)){
    $ret=array('code'=>1,'message'=>'请重新登录');
    echo json_encode($ret);return;
}

if ($order['status'] != Order::ORDER_READY) {
    $ret = array ('code'=>0,'message'=>'消单成功');
    echo json_encode($ret);return;
}

if ($params['cancel_type'] == 0 && empty($params['log'])) {
    $ret = array ('code'=>2,'message'=>'请选择销单理由并填写销单原因');
    echo json_encode($ret);return;
}

//添加task队列
$task=array(
    'method'=>'push_order_cancel',
    'params'=>array(
        'cancel_type'=>$params['cancel_type'],
        'log'=>$params['log'],
        'order_id'=>$params['order_id'],
        'isComplaint'=>$params['isComplaint'],
        'driver_id'=>$driver->driver_id,
        'name'=>$params['name'],
        'city_id'=>$order['city_id'],
        'phone'=>$order['phone'],
        'complaint_type'=>$params['complaint_type'],
        'complaint'=>$params['complaint'],
        'start_time'=>$order['start_time'],
    )
);
//Queue::model()->task($task);
//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
Queue::model()->putin($task,'apporder');

$ret=array('code'=>0,'message'=>'成功!');
echo json_encode($ret);
return;

