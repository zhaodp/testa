<?php
/**
 * 司机客户端位置上报
 * @param token
 * @param status
 * @param gps_type
 * @param longitude
 * @param latitude
 * @param log_time
 * 
 */

if(Yii::app()->params['order_architecture_refactor_on']) {
    $result = DriverRealTimeInfoApiService::getInstance()->driverManualUpload($params);
    echo json_encode($result);
    return;
}

$driver_id 	= isset($params['user']) ? $params['user'] : '';
$status		= isset($params['status']) ? $params['status'] : '2';//没传就默认下班
$token		= isset($params['token']) ? $params['token'] : '';
$gps_type	= (isset($params['gps_type'])&&!empty($params['gps_type'])) ? trim($params['gps_type']) : 'wgs84';
$lng		= isset($params['longitude']) ? $params['longitude'] : '1';
$lat		= isset($params['latitude']) ? $params['latitude'] : '1';
$log_time	= isset($params['log_time']) ? $params['log_time'] : date('YmdHis');
$gps_time 	= isset($params['gps_timestamp']) ? $params['gps_timestamp'] : time();
$app_ver        = isset($params['app_ver']) ? $params['app_ver'] : '';

//司机端 v2.2.6 新增字段 提高时间精度
$milli_timestamp = isset($params['milli_timestamp']) ? $params['milli_timestamp']/1000 : $gps_time;

//默认延迟提交位置时间为5秒
$delay_time = 5;

if( empty($token) ){
	EdjLog::info('token is empty for driver '.$driver_id);
	$ret=array(
			'code'=>2,
			'delay'=>$delay_time,
			'message'=>'参数错误'
	);
    echo json_encode($ret);
    return;
}

$driver = DriverStatus::model()->getByToken($token);

//20150402:司机端做题支持,20150415 如果司机不答题，就永远工作不了。
if($driver->status != DriverStatus::STATUS_DRIVING && $status != DriverStatus::STATUS_OFFDUTY){
    $driverPosition = DriverPosition::model()->getDriverPosition($driver->id);
    $appVer = $driverPosition->app_ver;
    if(!empty($appVer) && $appVer>="2.6.0.0"){
        $unlock = QueueDispatchDriver::model()->isUnLock($driver_id);
        if($unlock){
            $des = new DriverExamStudy();
            $examUrl = $des->getExamUrl($driver_id);
            if(isset($examUrl ['num'] )&&isset($examUrl ['url'] )){
                if($examUrl ['num']>0){
                    $ret=array(
                        'code'=>11,
                        'message'=>'请先做题',
                        'title'=>'e代驾试题',
                        'url'=>$examUrl ['url']
                    );
                    echo json_encode($ret);
                    return;
                }
            }
        }
    }
}

if ($driver) {

    //add by aiguoxin 司机没有领取工服，不能共工作,判断条件，到时再修改
    if($status == DriverPosition::POSITION_IDLE){
        $canWork = DriverRecruitment::model()->canWork($driver_id);
        if($canWork && $canWork['code']){
            $ret=array(
                'code'=>3,
                'message'=>$canWork['message']
            );
            echo json_encode($ret);
            return;
        }
    }
	//FIX:discard positions upload from driver/upload/position interface
	/*
	$position['gps_timestamp'] 	= $gps_time;
	$position['longitude']		= $lng;
	$position['latitude']		= $lat;
	$position['driver_id']		= $driver_id;
	$position['gps_type']		= $gps_type;
	$position['towers']			= '';
	$position['status']			= $status;
	$position['log_time']		= $log_time;
	
	//更新track
	$task=array(
		'method'=>'driver_position_track',
		'params'=>$position
	);
	Queue::model()->putin($task,'position');
	*/
	
    //如果是更新位置，
    if ( ( intval($params['longitude']) + intval($params['latitude']) ) > 10 ) {
        //暂时注释掉，排查问题
        $last_upload_position = @$driver->last_upload_position;
        //间隔小于10秒不放队列，直接返回。
        if ( abs(time() - $last_upload_position) < $delay_time) {
            $ret=array(
                'code'=>0,
                'delay'=>$delay_time,
                'message'=>'活逼该成功'
            );
            echo json_encode($ret);
            return;
        }

        $driver->last_upload_position = time();
        
        $position['gps_timestamp'] 	= $gps_time;
		$position['longitude']		= $lng;
		$position['latitude']		= $lat;
		$position['driver_id']		= $driver_id;
		$position['gps_type']		= $gps_type;
		$position['towers']			= '';
		$position['status']			= $status;
		$position['log_time']		= $log_time;

        //添加到更新司机当前位置的队列
        $task=array(
            'method'=>'driver_current_pos',
            'params'=>$position
        );
        Queue::model()->putin($task,'current');

        //如果传了位置，同时状态有变化，那么就更新状态 add by sunhongjing 2014-01-04
        if( $status != $driver->status ){
            
        	$last_upload_status = @$driver->last_upload_status;

	        //判断是否更新状态
	        if ( floatval($last_upload_status) <= $milli_timestamp
		    && $milli_timestamp < floatval(time() + 3600) ) {
	            $driver->last_upload_status = $milli_timestamp;
	            $task=array(
	                'method'=>'driver_current_status',
	                'params'=>array(
	                    'id' => $driver->id,
	                    'driver_id' => $driver_id,
	                    'status' => $status,
	                    'gps_timestamp' => $gps_time, //无用参数
	                    't' => date("Y-m-d H:i:s", $gps_time), //无用参数
                        'time' => time(),
                        'last_status'=>$driver->status,//记录上次状态
	                ),
	            );
	            Queue::model()->putin($task,'current_status');
                $driver->status = $status;
	        }
		else {
	            EdjLog::info('UploadStatusTimeError'.'|'.$driver_id.'|'.$milli_timestamp.'|'.$last_upload_status);
		}
        }
        
        $ret=array(
            'code'=>0,
            'delay'=>$delay_time,
            'message'=>'上传成功'
        );

        echo json_encode($ret);
        return;
    }
    else { //更新状态
        $last_upload_status = @$driver->last_upload_status;

        //判断是否更新状态
        if ( floatval($last_upload_status) <= $milli_timestamp
               && $milli_timestamp < floatval(time() + 3600) ) {
            $driver->last_upload_status = $milli_timestamp;


            $task=array(
                'method'=>'driver_current_status',
                'params'=>array(
                    'id' => $driver->id,
                    'driver_id' => $driver_id,
                    'status' => $status,
                    'gps_timestamp' => $gps_time, //无用参数
                    't' => date("Y-m-d H:i:s", $gps_time), //无用参数
                    'time' => time(),
                    'last_status'=>$driver->status,//记录上次状态
		              'app_ver' => $app_ver,
                ),
            );
            Queue::model()->putin($task,'current_status');
            $driver->status = $status;


            /*$params = array(
                'driver_id' => $driver_id,
                'status' => $status,
            );

            //写入唯一列表
            Queue::model()->statusUniqueList('LAST_STATUS', $driver_id, $params);*/
        }
        else {
            EdjLog::info('UploadStatusTimeError'.'|'.$driver_id.'|'.$milli_timestamp.'|'.$last_upload_status);
        }

        $ret=array(
            'code'=>0,
            'delay'=>$delay_time,
            'message'=>'上传成功'
        );

        echo json_encode($ret);
        return;
    }
} else {
	EdjLog::info('driver '.$driver_id.' should relogin');
	$ret=array(
			'code'=>1,
			'delay'=>$delay_time,
			'message'=>'请重新登录'
	);
    echo json_encode($ret);
    return;
}
