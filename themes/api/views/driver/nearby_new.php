<?php
/**
 * 获取周边最近的空闲司机列表
 * @param gps_type {gps,google,baidu}
 */
$udid=$params['udid'];
$gps_type=isset($params['gps_type']) ? $params['gps_type'] : 'google';
$idel_count=isset($params['idel_count']) ? $params['idel_count'] : 5;
$busy_count=isset($params['busy_count']) ? $params['busy_count'] : 3;
$idel_max_distance=6000;
$busy_max_distance=15000;

$lng=sprintf('%.6f', $params['longitude']);
$lat=sprintf('%.6f', $params['latitude']);
$longitude=$lng;
$latitude=$lat;

$idel_driver=$busy_driver=null;

switch ($gps_type) {
	case 'gps' :
	case 'google' :
		//把google座标转换为百度座标后查询最近司机
		$baidu=Lbs2GPS::Wgs2Baidu($lng, $lat);
		$longitude=sprintf('%.6f', $baidu['longitude']);
		$latitude=sprintf('%.6f', $baidu['latitude']);
		break;
	case 'baidu' :
		//$baidu = Lbs2GPS::Wgs2Baidu($lng, $lat);
		$longitude=$lng;
		$latitude=$lat;
		break;
	default :
		$longitude=$lng;
		$latitude=$lat;
		break;
}

$idel_driver=DriverGPS::model()->nearby($longitude, $latitude, 0, $idel_count, $idel_max_distance);
$busy_driver=DriverGPS::model()->nearby($longitude, $latitude, 1, $busy_count, $busy_max_distance);

if ($idel_driver&&$busy_driver) {
	$drivers=array_merge($idel_driver, $busy_driver);
} elseif ($idel_driver) {
	$drivers=$idel_driver;
} elseif ($busy_driver) {
	$drivers=$busy_driver;
} else {
	$drivers=null;
}

if (!$drivers) {
	if (empty($idel_driver)&&empty($busy_driver)) {
		$json=json_encode(array(
				'code'=>2,
				'driverList'=>array(),
				'message'=>'对不起，您所在区域服务没有开通e代驾服务。'
		));
	} elseif (empty($idel_driver)) {
		$json=json_encode(array(
				'code'=>1,
				'driverList'=>array(),
				'message'=>'对不起，您周边暂时没有空闲的e代驾司机。'
		));
	}
} else {
	$drivers_all=array();
	foreach($drivers as $driver) {
		$drivers_all[]=detail($driver['driver_id'], $gps_type, $driver['distance']);
	}
	$json=json_encode(($drivers_all));
}

echo $json;

/**
 * 格式化司机信息
 * @param string $driver_id
 */
function detail($driver_id, $gps_type, $distance) {
	$driver=DriverStatus::model()->get($driver_id);
	$id=$driver['id'];
	
	$driver_info=DriverStatus::model()->info($driver_id);
	$driver_position=DriverStatus::model()->position($driver_id);
	$driver_service=DriverStatus::model()->service($driver_id);
	
	if ($driver_info['level']==''||empty($driver_info['level'])) {
		$new_level=0;
	} else {
		$new_level=$driver_info['level'];
	}
	
	$id_card=isset($driver_info['id_card']) ? substr_replace($driver_info['id_card'], '******', 10, 6) : '';
	$car_card=isset($driver_info['car_card']) ? substr_replace($driver_info['car_card'], '******', 10, 6) : '';
	
	switch ($gps_type) {
		case 'google' :
			$longitude=$driver_position['google_lng'];
			$latitude=$driver_position['google_lat'];
			break;
		case 'baidu' :
			$longitude=$driver_position['baidu_lng'];
			$latitude=$driver_position['baidu_lat'];
			break;
	}
	
	$detail=array(
			'driver_id'=>$driver_id,
			'id'=>$driver_info['imei'],
			'name'=>$driver_info['name'],
			'picture'=>'',
			'phone'=>$driver['phone'],
			'idCard'=>$id_card,
			'domicile'=>$driver_info['domicile'],
			'card'=>$car_card,
			'year'=>$driver_info['year'],
			'level'=>round($driver_info['level']),
			'new_level'=>$new_level,
			'goback'=>$driver['goback'],
			'state'=>$driver['status'],
			'price'=>'',
			'order_count'=>$driver_service['service_times'],
			'comment_count'=>$driver_service['high_opinion_times'],
			'longitude'=>$longitude,
			'latitude'=>$latitude,
			'distance'=>distince_format($distance),
			'picture_small'=>$driver_info['picture_small'],
			'picture_middle'=>$driver_info['picture_middle'],
			'picture_large'=>$driver_info['picture_large']
	);
	
	return $detail;
}

/**
 * 格式化距离显示
 * @param unknown $distance
 * @return Ambigous <string, number>
 */
function distince_format($distance) {
	$distance=intval($distance);
	
	if ($distance<=100) {
		$distance='100米内';
	} elseif ($distance>100&&$distance<=200) {
		$distance='200米内';
	} elseif ($distance>200&&$distance<=300) {
		$distance='300米内';
	} elseif ($distance>300&&$distance<=400) {
		$distance='400米内';
	} elseif ($distance>400&&$distance<=500) {
		$distance='500米内';
	} elseif ($distance>500&&$distance<=600) {
		$distance='600米内';
	} elseif ($distance>600&&$distance<=700) {
		$distance='700米内';
	} elseif ($distance>700&&$distance<=800) {
		$distance='800米内';
	} elseif ($distance>800&&$distance<=900) {
		$distance='900米内';
	} elseif ($distance>900&&$distance<=1000) {
		$distance='1公里';
	} else {
		$distance=number_format(intval($distance)/1000, 1).'公里';
	}
	
	return $distance;
}

