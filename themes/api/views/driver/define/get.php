<?php

$token = isset($params['token']) ? $params['token'] : '';
if (empty($token)) {
    $ret = array('code' => 2, 'message' => '参数有误');
    echo json_encode($ret);
    return ;
}

$driver = DriverStatus::model()->getByToken($token);
if (empty($driver) || $driver->token === null || $driver->token !== $token) {
    $ret = array(
        'code' => 1,
        'message' => '请重新登录'
    );
    echo json_encode($ret);
    return;
}

$city_id = $driver->city_id;
$long_distance=Common::getLongDistanceCity($city_id);

$ret = array(
		'code'=>0,
		'idle_call_log_step'=>120,
		'busy_call_log_step'=>120,
		'offline_call_log_step'=>600,
		'heartbeat_step'=>60,
		'max_speed'=>150,
		'accept_time'=>30,
		'idle_position_step'=>50,
		'idle_location_step'=>20,
		'busy_accept2arrive_position_step'=>60,
		'busy_accept2arrive_location_step'=>30,
		'busy_arrive2start_position_step'=>300,
		'busy_arrive2start_location_step'=>60,
		'busy_start2finish_position_step'=>20,
		'busy_start2finish_location_step'=>5,
		'offline_position_step'=>300,
		'offline_location_step'=>300,
		'filter_location_accuracy'=>500,
		'encode_address_interval'=>60,
		'restart_zero_location_service'=>180,
		'config' => array(
		'hot_map' => DriverStatus::model()->getCitySetting($city_id,'hot_map'),
		'driver_register' => DriverStatus::model()->getCitySetting($city_id,'driver_register'),
		'driver_forum' => DriverStatus::model()->getCitySetting($city_id,'driver_forum'),
		'ecoin_standard' => DriverStatus::model()->getCitySetting($city_id,'ecoin_standard'),
		),
		'long_distance'=>array(
			'per_kilometer_time'=>$long_distance['per_kilometer_time'],
		),
		'message'=>'读取成功');

echo json_encode($ret);
