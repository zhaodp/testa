<?php
/**
 * 消单(走新流程，没有订单则生成一个该司机的取消订单)
 * @author AndyCong<congming@edaijia-staff.cn>
 * @version 2013-11-07
 */

//接收并验证参数
if(Yii::app()->params['order_architecture_refactor_on']) {
    $result = CancelOrderService::getInstance()->driverCancelOrder($params);
    echo json_encode($result);
    return;
}

$order_id = isset($params['order_id']) ? $params['order_id'] : '';
$order_number = isset($params['order_number']) ? $params['order_number'] : '';

$cancel_type = isset($params['cancel_type']) ? $params['cancel_type'] : '';
$log = isset($params['log']) ? $params['log'] : '';
$content = isset($params['content']) ? $params['content'] : '';
$isComplaint = isset($params['isComplaint']) ? $params['isComplaint'] : '';
$name = isset($params['name']) ? $params['name'] : '';
$phone = isset($params['phone']) ? $params['phone'] : '';
$complaint_type = isset($params['complaint_type']) ? $params['complaint_type'] : '';
$source = isset($params['source']) ? $params['source'] : '';
$complaint = isset($params['complaint']) ? $params['complaint'] : '';

//验证消单类型与内容 
if ($complaint_type == 99 && empty($content)) {
    $ret = array ('code'=>2 , 'message'=>'请选择销单理由并填写销单原因');
    echo json_encode($ret);return;
}

if( empty($phone) ){
    $ret=array('code'=>0 , 'message'=>'成功!');
    echo json_encode($ret);return;
}


//验证token
$token = isset($params['token']) ? trim($params['token']) : '';
$driver = DriverStatus::model()->getByToken($token);
if (!$driver) {
    $ret=array('code'=>1,'message'=>'请重新登录');
    echo json_encode($ret);return;
}

//$driver_info = DriverStatus::model()->get($driver->driver_id);
//$driver_info->status = 0;


//添加task队列
if (strlen($order_id) > 11 && is_numeric($order_id)) {
    $task=array(
        'method'=>'dal_driver_cancel_order',
        'params'=>array(
            'cancel_type' => $cancel_type,
            'log' => $log,
            'content' => $content,
            'order_id' => $order_id,
            'order_number' => $order_number,
            'isComplaint' => $isComplaint,
            'driver_id' => $driver->driver_id,
            'name' => $name,
            'phone' => $phone,
            'complaint_type' => $complaint_type,
            'complaint' => $complaint,
            'source' => $source,
        )
    );
} else {
    $task=array(
        'method'=>'driver_cancel_order',
        'params'=>array(
            'cancel_type' => $cancel_type,
            'log' => $log,
            'content' => $content,
            'order_id' => $order_id,
            'order_number' => $order_number,
            'isComplaint' => $isComplaint,
            'driver_id' => $driver->driver_id,
            'name' => $name,
            'phone' => $phone,
            'complaint_type' => $complaint_type,
            'complaint' => $complaint,
            'source' => $source,
        )
    );
}
Queue::model()->putin($task , 'apporder');
$ret=array('code'=>0 , 'message'=>'成功!');
echo json_encode($ret);return;
