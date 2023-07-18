<?php

/**
 * 司机结伴返程
 * @Author zhanglimin 2013-04-28
 * $modify dayuer 2013-05-22
 */
$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";
$goback=isset($params['goback'])&&!empty($params['goback']) ? trim($params['goback']) : 0;

if (empty($token)) {
	$ret=array(
			'code'=>2,
			'message'=>'参数不正确!'
	);
	echo json_encode($ret);
	return;
}

$driver=DriverStatus::model()->getByToken($token);

if (!$driver||$driver->token==null) {
	$ret=array(
			'code'=>1,
			'message'=>'token失效!'
	);
	echo json_encode($ret);
	return;
}

$driver->goback=$goback;

$position = $driver->position;

$params['id']		= $driver->id;
$params['goback']	= $goback;
$params['lng']= isset($position['baidu_lng']) ? $position['baidu_lng'] : 0; //使用百度坐标
$params['lat']	= isset($position['baidu_lat']) ? $position['baidu_lat'] : 0;
$params['driver_id']=$driver->driver_id;
$params['status']	=$driver->status;

//添加task队列更新数据库
$task=array(
		'method'=>'driver_goback_together',
		'params'=>$params,
);

//Queue::model()->task($task);
//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
Queue::model()->putin($task,'task');

$allow_city=Yii::app()->params['kkpinchePushCity'];
if(in_array($driver->city_id,$allow_city)){
    $paramsnew = array(
        'driver_info'=>array(
            'id'=>$driver->id,
            'phone'=>$driver->phone,
            'name'=>$driver->info['name'],
            'udid'=>$driver->info['imei'],
            'city_id'=>$driver->city_id),
        'gps'=>array(
            'lat'=>$params['lat'],
            'lng'=>$params['lng']),
        'goback'=> $goback);

    //添加task队列更新数据库
    $task=array(
        'method'=>'pinche_synccustomer',
        'params'=>$paramsnew,
    );
    Queue::model()->putin($task,'backtogether');

}




$ret=array(
		'code'=>0,
		'message'=>'成功!'
);
echo json_encode($ret);
return;
