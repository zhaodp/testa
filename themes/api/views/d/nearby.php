<?php
/**
 * 司机端API：d.nearby 获取周边最近的空闲司机列表
 * 调用url:
 * @param string $token
 * @param string $udid
 * @param string $gps_type {wgs84,google,baidu}
 * @param int $idel_count
 * @param string $from
 * 
 * @author sunhongjing 2013-11-4
 * 
 * @return json
 * 
 * @example 
 */

//验证用户token

$udid=$params['udid'];
$gps_type = isset($params['gps_type']) ? trim(strtolower($params['gps_type'])) : 'google';
$idel_count = isset($params['idel_count']) ? $params['idel_count'] : 5;
$busy_count	= isset($params['busy_count']) ? $params['busy_count'] : 5;
$from = isset($params['from']) ? $params['from'] : '';
$app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';
$idel_max_distance = 5000;
$busy_max_distance = 10000;

$longitude = $lng = sprintf('%.6f', $params['longitude']);
$latitude  = $lat = sprintf('%.6f', $params['latitude']);

//计算司机端上传经纬坐标与后台当前经纬坐标偏差
//如果偏差小于500米,使用后台当前经纬坐标获取附近司机
//避免获取不到司机自己的情况发生
$token = isset($params['token']) ? $params['token'] : ''; //司机端2.3.4起开始上传token
$my_driver_id = '';
if(!empty($token)) {
    $driver_state = DriverStatus::model()->getByToken($token);
    if($driver_state
        && !empty($driver_state->position['longitude'])
        && !empty($driver_state->position['latitude'])) {
        $offset = Helper::Distance($longitude, $latitude
            , $driver_state->position['longitude']
            , $driver_state->position['latitude']);
        EdjLog::info($driver_state->position['longitude'].'|'.$driver_state->position['latitude'].'|'.$longitude.'|'.$latitude.'|'.$offset.'|'.$driver_state->driver_id);
        if($offset < 500) {
            $longitude = $lng = $driver_state->position['longitude'];
            $latitude  = $lat = $driver_state->position['latitude'];
        }
        $my_driver_id = $driver_state->driver_id;
    }
}

if($app_ver && $app_ver>='2.4.0'){ //2.4.0之后判断token为空情况
	if(empty($token)){
		$ret=array('code'=>1 , 'message'=>'token失效');
    	echo json_encode($ret);return;
	}
}

$drivers = $idel_driver = $busy_driver = array();

switch ($gps_type) {
	case 'wgs84' :
		//把google座标转换为百度座标后查询最近司机
		$baidu		= GPS::model()->Wgs2Baidu($lng, $lat);
		$longitude	= sprintf('%.6f', $baidu['longitude']);
		$latitude	= sprintf('%.6f', $baidu['latitude']);	
		break;
	case 'google' :
		$baidu		= GPS::model()->Google2Baidu($lng, $lat);
		$longitude 	= sprintf('%.6f', $baidu['longitude']);
		$latitude	= sprintf('%.6f', $baidu['latitude']);
		break;
	case 'baidu' :
		$longitude	= $lng;
		$latitude	= $lat;
		break;
	default :
		$longitude	= $lng;
		$latitude	= $lat;
		break;
}


//记录访问日志，走队列
	
//添加task队列
$task=array(
	'method'=>'customer_access_heatmap',
	'params'=>array(
			'lng'=>$longitude,
			'lat'=>$latitude,
			'gps_type'=>'baidu',
			'channel'=>$from,
			'udid'=>$udid,
			'req_time'=>date("Y-m-d H:i:s"),	
			'created'=>date("Y-m-d H:i:s"),	
		),
);
//Queue::model()->putin($task,'heatmap');

$idel_driver = DriverGPS::model()->nearby($longitude, $latitude, 0, $idel_count, $idel_max_distance);
$busy_driver = DriverGPS::model()->nearby($longitude, $latitude, 1, $busy_count, $busy_max_distance);

if( !empty($idel_driver) ){
	$drivers = $idel_driver;
}
if( !empty($busy_driver) ){
	$drivers = array_merge($drivers, $busy_driver);
}


if ( empty($drivers) ) {
	$json=json_encode(array(
			'code'=>2,
			'driverList'=>array(),
			'message'=>'您的周围暂无司机'
	));
	
} else {
    $drivers_all=array();
    
    //空闲司机	
    foreach($drivers as $driver) {	 
    	$status = isset($driver['status']) ?  $driver['status'] : false;
		$tmp = Helper::foramt_driver_detail($driver['driver_id'], $gps_type, $driver['distance'],'driver',$status);
		if(!empty($tmp) && !empty($tmp['longitude'])
		    && !empty($tmp['latitude'])) {
		    $distance = Helper::Distance($latitude, $longitude,
		        $tmp['latitude'], $tmp['longitude']);
		    if($status == 0 && $distance <= $idel_max_distance) {
		        if($tmp['driver_id'] == $my_driver_id){
			    //保证自己总是在列表第一位
			    array_unshift($drivers_all, $tmp);
			}
			else {
		            $drivers_all[] = $tmp;
			}
		    }
		    else if($status == 1 && $distance <= $busy_max_distance) {
		        if($tmp['driver_id'] == $my_driver_id){
			    //保证自己总是在列表第一位
			    array_unshift($drivers_all, $tmp);
			}
                        else {
		            $drivers_all[] = $tmp;
			}
		    }
		    else {
                        EdjLog::info("d.nearby filter|".'|'.$driver['driver_id'].$tmp['driver_id']."|".$tmp['longitude']."|".$tmp['latitude']);
		    }
		}
		else {
                    EdjLog::info("d.nearby driver info error|".$driver['driver_id']);
		}
	}

	$json=json_encode(array(
			'code'=>0,
			'driverList'=>$drivers_all,
			'message'=>''
	));
}

echo $json; return;



