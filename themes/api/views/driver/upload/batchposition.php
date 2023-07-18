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
    $result = DriverRealTimeInfoApiService::getInstance()->driverAutoUpload($params);
    echo json_encode($result);
    return;
}

$status	= isset($params['status']) ? $params['status'] : '2';//没传就默认下班
$token	= isset($params['token']) ? $params['token'] : '';
$log_time = isset($params['log_time']) ? $params['log_time'] : date('YmdHis');
$gps_time = isset($params['gps_timestamp']) ? $params['gps_timestamp'] : time();
$positions = isset($params['positions']) ? $params['positions'] : '';
$force_update = isset($params['force_update']) ? $params['force_update'] : '';
$app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';

//司机端 v2.2.6 新增字段 提高时间精度
$milli_timestamp = isset($params['milli_timestamp']) ? $params['milli_timestamp']/1000 : $gps_time;

//默认延迟提交位置时间为5秒
$delay_time = 5;

if( empty($token) || empty($positions)){
    $ret=array(
	    'code'=>2,
	    'delay'=>$delay_time,
	    'message'=>'参数错误'
	    );
    echo json_encode($ret);
    return;
}


$driver = DriverStatus::model()->getByToken($token);

if ($driver) { // Empty driver
    $driver_id 	= $driver->driver_id;;

    //解析url中引号
    $positions = htmlspecialchars_decode($positions, ENT_QUOTES);

    //解析 positions
    $de_json = json_decode($positions,TRUE);
    $count_json = count($de_json);

    $lat =1.0;
    $lng =1.0;
    $gps_type = 'baidu';
    $gps_time = time();

    $positions_arr = array();
    $pos_status =  $status;

    $track_params['driver_id'] = $driver_id;
    $track_params['log_time'] = $log_time;

    for ($i = 0; $i < $count_json; $i++){
	if(count($positions_arr) > 0 && $curr_status != $de_json[$i]['status']) {
	    //更新track
	    $track_params['positions'] = $positions_arr;
	    $track_params['status']	= $curr_status;
	    $task=array(
		    'method'=>'driver_batch_position_track',
		    'params'=>$track_params
		    );
	    Queue::model()->putin($task,'position');
	    $positions_arr = array();
	}
	$curr_status = $de_json[$i]['status'];
	if($curr_status == 1) {
	    $pos_status =  $curr_status;
	}

	$lat = $de_json[$i]['lat'];
	$lng = $de_json[$i]['lng'];
	$gps_type = $de_json[$i]['gps_type'];
	$gps_time = $de_json[$i]['gps_time'];
	$provider = isset($de_json[$i]['provider'])? $de_json[$i]['provider']:'';
	$accuracy = isset($de_json[$i]['accuracy'])? $de_json[$i]['accuracy']:'';
	$pos_milli_timestamp = isset($de_json[$i]['milliTimestamp'])? 
	    $de_json[$i]['milliTimestamp']/1000:$gps_time;
	$positions_arr[] = array(
		'lat' => $lat,
		'lng' => $lng,
		'gps_type' => $gps_type,
		'gps_time' => $gps_time,
		'provider' => $provider,
		'accuracy' => $accuracy,
		'status' => $curr_status,
		'milli_timestamp' => $pos_milli_timestamp
		);
    }

    if(count($positions_arr) > 0) {
	$track_params['positions'] = $positions_arr;
	$track_params['status']	= $pos_status;
	$task=array(
		'method'=>'driver_batch_position_track',
		'params'=>$track_params
		);
	Queue::model()->putin($task,'position');
    }

    // Update driver positions
    if ((intval($lat) + intval($lng)) > 10) {

	// If the status is changed, update the status
        // 如果有force_update字段,更新状态
        if($force_update == 1 || $status != $driver->status ){
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

        $last_upload_position = @$driver->last_upload_position;

        //间隔小于10秒不放队列，直接返回。
        if ( abs(time() - $last_upload_position) < $delay_time) {
            $ret=array(
                'code'=>0,
                'delay'=>$delay_time,
                'message'=>'上报间隔太短啦'
            );
            echo json_encode($ret);
            return;
        }

        $driver->last_upload_position = time();

        $pos_params['gps_timestamp'] = $gps_time;
	$pos_params['longitude'] = $lng;
	$pos_params['latitude'] = $lat;
	$pos_params['driver_id'] = $driver_id;
	$pos_params['gps_type'] = $gps_type;
	$pos_params['towers'] = '';
	$pos_params['status'] = $status;
	$pos_params['log_time'] = $log_time;

        //添加到更新司机当前位置的队列
        $task=array(
            'method'=>'driver_current_pos',
            'params'=>$pos_params
        );
        Queue::model()->putin($task,'current');

    } else { // if update positions else update status 
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
                    'time' => time(),
		    		'app_ver' => $app_ver,
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
} else {  // empty driver
    $ret=array(
	    'code'=>1,
	    'delay'=>$delay_time,
	    'message'=>'请重新登录'
	    );
}


echo json_encode($ret);
return;
