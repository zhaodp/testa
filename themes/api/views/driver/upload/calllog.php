<?php
/**
 * 上报司机通话纪录，司机下班不上报
 * @param callTime	呼叫开始时间
 * @param driver_id	司机工号
 * @param endTime	呼叫结束时间
 * @param imei		手机IMEI号码
 * @param latitude	经纬度
 * @param longitude	经纬度
 * @param phone		客户电话
 * @param status	司机接电话时的状态
 * @param simcard	手机sim卡号
 * @param talkTime	通话时间
 * @param type		通话类型
 * @param token		
 * 
 * @editor zhanglimin 2013-04-25
 * 
 */
$driver_id=$params['user'];
$token=$params['token'];

$driver = DriverStatus::model()->get($driver_id);
if(!$driver){
	$ret=array(
			'code'=>2,
			'message'=>'工号不存在'
	);
	echo json_encode($ret);
	Yii::app()->end();
	
}

if (empty($driver) || $driver->token===null||$driver->token!==$token) {
	$ret=array(
			'code'=>1,
			'message'=>'请重新登录'
	);
	echo json_encode($ret);
	Yii::app()->end();
}
$params['sim'] = isset($params['sim']) ? $params['sim'] : '';
$params['imei'] = isset($params['imei']) ? $params['imei'] : '';
$params['simcard'] = 'ZNJF'; //王栋有部分上传有问题，先写死
$params['driver_id']=$driver_id;

unset($params['appkey']);
unset($params['method']);
unset($params['token']);
unset($params['sig']);
unset($params['ver']);
unset($params['user']);
unset($params['sim']);
unset($params['timestamp']);

$params['callTime']=strtotime($params['callTime']);
$params['endTime']=strtotime($params['endTime']);


//放入blacklist池，通过黑名单池，超过4次对用户做预警。add by sunhongjing 2013-05-13
DriverStatus::model()->putBlacklistPool($params['phone'],$params);

$flag=true;
//添加task队列
$task=array(
        'method'=>'driver_calllog',
        'params'=>$params
);
//Queue::model()->task($task);
//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
Queue::model()->putin($task,'calllog');


$ret=array(
    'code'=>0,
    'message'=>'上传成功'
);
echo json_encode($ret);
Yii::app()->end();
