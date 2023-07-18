<?php
/**
 * 开放api:nearby ,获取周边最近的空闲司机列表
 * 
 * @author sunhongjing 2013-09-25
 * @param gps_type {gps,google,baidu}
 * @return json
 */
$udid=$params['udid'];
$gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'google';
$idel_count = isset($params['idel_count']) ? $params['idel_count'] : 6;
$from = isset($params['from']) ? $params['from'] : '';
$idel_max_distance = isset($params['idel_max_distance']) ? $params['idel_max_distance'] : 5000;

$lng=sprintf('%.6f', $params['longitude']);
$lat=sprintf('%.6f', $params['latitude']);
$longitude=$lng;
$latitude=$lat;

$idel_driver=null;

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

//记录访问日志，走队列,开始
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
//记录访问日志，走队列,结束



$idel_driver=DriverGPS::model()->nearby($longitude, $latitude, 0, $idel_count, $idel_max_distance);

if ($idel_driver) {
	$drivers = $idel_driver;
} else {
	$drivers=null;
}

if ( empty($drivers) ) {
	$json=json_encode(array(
			'code'=>2,
			'driverList'=>array(),
			'message'=>'您的周围暂无空闲司机'
	));
	
} else {
	$i=0;
    $drivers_all=array();
    foreach($drivers as $driver) {
        //司机工号大于9800 不显示
        $is_backcar = Common::checkBlackCar($driver['driver_id']);
        if ($is_backcar) {
            continue;
        }
        $status = isset($driver['status']) ?  $driver['status'] : false;
        
        $tmp = Helper::foramt_driver_detail($driver['driver_id'], $gps_type, $driver['distance'],'small',$status);
		if(!empty($tmp)){  
		    $drivers_all[]= $tmp;
		    $i++;
		} 
		if($i>=5){
			break;
		}
    }

	
	$json=json_encode(array(
			'code'=>0,
			'driverList'=>$drivers_all,
			'message'=>''
	));
}

echo $json;

