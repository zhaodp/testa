<?php
/**
 * 司机客户端心跳上传
 */

$driver_id=$params['user'];
$timestamp=$params['timestamp'];

$app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';

$driver=DriverStatus::model()->get($driver_id);

if ($driver) {
    DriverStatus::model()->set_app_ver($driver_id, $app_ver);
	
	$last_heartbeat = intval(@$driver->last_heartbeat);
	//strtotime(urldecode("2013-06-28%2012:18:02"))
	if ( abs(time() - $last_heartbeat) < 30) {
		$ret=array(
				'code'=>0,
				'message'=>'成功ok'
		);
		echo json_encode($ret);
		return ;
	}
	
	$driver->last_heartbeat = time();
	
	//更新redis
	$driver->heartbeat = strtotime($timestamp);

	//2014-09-04 之前心跳写mysql主要是更新app_ver,
	//现在app_ver已在redis存储,所以取消写mysql,在更新状态时更新一次app_ver
	//队列回写数据库
	//$params=array(
	//		'id'=>$driver->id,
	//		'driver_id'=>$driver_id,
	//		'timestamp'=>$timestamp,
	//		'app_ver'=>$app_ver,
	//);

	//$task=array(
	//		'method'=>'driver_heartbeat',
	//		'params'=>$params
	//);R
	//Queue::model()->heartbeat($task);
	//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
	//Queue::model()->putin($task,'heartbeat');

    $allow_city = Yii::app()->params['kkpinchePushCity'];
    if(($driver->goback == 1) && in_array($driver->city_id,$allow_city) ){
        //更新拼车端 用户位置
        $paramsnew = array(
            'driver_info'=>array(
                'id'=>$driver->id,
                'phone'=>$driver->phone,
                'name'=>$driver->info['name'],
                'udid'=>$driver->info['imei'],
                'city_id'=>$driver->city_id),
            'goback'=> $driver->goback);

    //
        $task=array(
            'method'=>'pinche_synccustomer',
            'params'=>$paramsnew,
        );
        Queue::model()->putin($task,'backtogether');
    }
}
$ret=array('code'=>0,'message'=>'成功');
echo json_encode($ret);return ;

