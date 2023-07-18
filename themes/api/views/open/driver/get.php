<?php
//需要写清楚注释，增加缓存，封装业务逻辑，写库走队列 add by sunhongjing at 2013-5-19
//获得司机信息 返回是否成功
$driver_id=$params['driverID'];
$gps_type= isset($params['gps_type'])?$params['gps_type']:'google';

$driver=DriverStatus::model()->get($driver_id);

if ($driver) {
	$phone=$driver->phone;
	
	if ($driver->info['level']==''||empty($driver->info['level'])) {
		$new_level=0;
	} else {
		$new_level=$driver->info['level'];
	}
	
	$id_card=isset($driver->info['id_card']) ? substr_replace($driver->info['id_card'], '******', 10, 6) : '';
	$car_card=isset($driver->info['car_card']) ? substr_replace($driver->info['car_card'], '******', 10, 6) : '';
	
	switch ($gps_type) {
		case 'google' :
			$longitude=$driver->position['google_lng'];
			$latitude=$driver->position['google_lat'];
			break;
		default:
			$longitude=$driver->position['baidu_lng'];
			$latitude=$driver->position['baidu_lat'];
			break;
	}
	
	$driverInfo=array(
			'driverID'=>$driver_id,
			'phone'=>$driver->phone,
			'picture'=>'',
			'picture_small'=>$driver->info['picture_small'],
			'picture_middle'=>$driver->info['picture_middle'],
			'picture_large'=>$driver->info['picture_large'],
			'name'=>$driver->info['name'],
			'level'=>round($driver->info['level']),
			'new_level'=>$new_level,
			'goback'=>$driver->goback,
			'carCard'=>$car_card,
			'year'=>$driver->info['year'],
			'state'=>$driver->status,
			'mark'=>$driver->mark,
			'serviceTimes'=>$driver->service['service_times'],
			'highOpinionTimes'=>$driver->service['high_opinion_times'],
			'lowOpinionTimes'=>$driver->service['low_opinion_times'],
			'longitude'=>$longitude,
			'domicile'=>$driver->info['domicile'],
			'latitude'=>$latitude
	);
	$ret=array(
			'code'=>0,
			'driverInfo'=>$driverInfo,
			'message'=>'获取成功'
	);
} else {
	$ret=array(
			'code'=>1,
			'message'=>'获取失败'
	);
}

echo json_encode($ret);
