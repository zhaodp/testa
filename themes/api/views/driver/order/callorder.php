<?php
/**
 * 电话生成订单（新）
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-07-03
 */
if(Yii::app()->params['order_architecture_refactor_on']) {
    $result = DriverPlaceOrderService::getInstance()->placeOrderByPhone($params);
    echo json_encode($result);
    return;
}

//接收参数
$time = time();
$phone = isset($params['phone']) ? $params['phone'] : '';
$order_number = isset($params['order_number']) ? $params['order_number'] : '';
$token = isset($params['token']) ? $params['token'] : '';
$booking_time = isset($params['booking_time']) ? strtotime($params['booking_time']) : ($time+1200);
$call_type = isset($params['call_type']) ? $params['call_type'] : '';
$lng = isset($params['lng']) ? $params['lng'] : '';
$lat = isset($params['lat']) ? $params['lat'] : '';
$gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'baidu';

//验证预约时间和当前时间不能相隔半小时以上
if (abs($booking_time - $time) >= 3600) {
	$booking_time = $time;
}

//验证参数
if (empty($phone) || empty($order_number) || empty($token)) {
	$ret = array('code' => 2 , 'message' => '参数验证失败');
	echo json_encode($ret);return ;
}

//验证token
$driver = DriverStatus::model()->getByToken($token);
if (empty($driver) || $driver->token===null||$driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}

if (!empty($driver)) {
    //如果司机端没有上传坐标,使用司机上次上传坐标
    if(empty($lng) || empty($lat)) {
         $lng = isset($driver->position['longitude'])
             ? $driver->position['longitude'] : 0;
         $lat = isset($driver->position['latitude'])
             ? $driver->position['latitude'] : 0;
    }
    $gps_location = array(
        'longitude' => $lng,
	'latitude' => $lat,
    );
    $gps = GPS::model()->convert($gps_location , $gps_type);
    $city_id = $driver->city_id;
    if(empty($gps['street'])) {
        $gps['street'] = Dict::item('city', $city_id);
    }

	//组织参数
	$data = array(
	   'phone' => $phone,
	   'driver_id' => $driver->driver_id,
	   'order_number' => $order_number,
	   'booking_time' => $booking_time,
	   'call_type'  => $call_type,
	   'longitude' => $lng, 
	   'latitude' => $lat,
	   'address' => $gps['street'],
	   'city_id' => $city_id,
	);
	
	$task = array(
	    'method' => 'call_order',
	    'params' => $data,
	);
	
	Queue::model()->putin($task,'order');
	$ret = array('code' => 0 , 'message' => '操作成功');
	
} else {
	$ret = array('code' => 1 , 'message' => '验证失效,请重新登录');
}
echo json_encode($ret);return ;
