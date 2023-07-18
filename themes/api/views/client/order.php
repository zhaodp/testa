<?php
$ret=array(
    'code'=>0,
    'message'=>'成功!'
);
echo json_encode($ret);
return ;
/**
 * 订单接口
 * Created by JetBrains PhpStorm.
 * User: zhanglimin
 * Date: 13-5-2
 * Time: 上午11:52
 * To change this template use File | Settings | File Templates.
 */
$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";

$queue_id=isset($params['queue_id'])&&!empty($params['queue_id']) ? trim($params['queue_id']) : "";

$driver_id=isset($params['driver_id'])&&!empty($params['driver_id']) ? trim($params['driver_id']) : "";

$type=isset($params['type'])&&!empty($params['type']) ? strtolower(trim($params['type'])) : "";

$push_msg_id=isset($params['type'])&&!empty($params['push_msg_id']) ? strtolower(trim($params['push_msg_id'])) : "";

if (empty($token)||empty($queue_id)||empty($driver_id)||empty($type)||$type!='order'||empty($push_msg_id)) {
	$ret=array(
			'code'=>2,
			'message'=>'参数不正确!'
	);
	echo json_encode($ret);
	return ;
}

$driver=DriverStatus::model()->get($driver_id);

if (!$driver) {
	$ret=array(
			'code'=>2,
			'message'=>'司机信息无效!'
	);
	echo json_encode($ret);
	return ;
}

$check_token=trim($driver->token);
if ($check_token!=$token) {
	$ret=array(
			'code'=>1,
			'message'=>'token失效!'
	);
	echo json_encode($ret);
	return ;
}

//查询queue_id 是否存在
//这里封装方法，不能把数据逻辑写这里，利民，要时刻想着设计~！add by sunhongjing 
// Yii::app()->db change into OrderQueue::getDbMasterConnection()
$order_queue=OrderQueue::model()->getOrderQueueByWaitConfirm($queue_id);

if (empty($order_queue)) {
	$ret=array(
			'code'=>2,
			'message'=>'队列信息不正确!'
	);
	echo json_encode($ret);
	return ;
}

//添加task队列
$task=array(
		'method'=>'push_order_operate',
		'params'=>array(
				'queue_id'=>$queue_id,
				'driver_id'=>$driver_id,
				'type'=>$type,
				'push_msg_id'=>$push_msg_id,
				'confirm_time'=>date("Y-m-d H:i:s") //确认时间
		)
);
//Queue::model()->task($task);
//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
Queue::model()->putin($task,'order');

//设置司机状态为服务中 ,利民，这里确认靠谱？ add by sunhongjing 
$driver->status=1;

//删除派单队列
QueueDispatchDriver::model()->delete($driver_id);
$ret=array(
		'code'=>0,
		'message'=>'成功!'
);
echo json_encode($ret);
return ;






