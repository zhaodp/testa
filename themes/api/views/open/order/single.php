<?php
/**
 * 客户端API：c.order.single 单人订单下单接口，换一个也调用该接口。
 *           验证客户信息（是否为黑名单）---接口中完成
 *           司机状态，是否有重复订单，是否达到服务中订单上限,需要考虑性能---队列处理
 * @param token
 * @param driver_id（可以为空）
 * @param lng 经度
 * @param lng 纬度
 * @return json
 * @author AndyCong 2013-10-14
 * @version 1.0
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';
$lng = isset($params['lng']) ? trim($params['lng']) : '';
$lat = isset($params['lat']) ? trim($params['lat']) : '';
$gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'google';
$type = isset($params['type']) ? $params['type'] : DalOrder::SINGLE_PUSH_DRIVER;
$city_id = isset($params['city_id']) ? $params['city_id'] : '';

$bonus_sn = isset($params['bonus_sn']) ? trim($params['bonus_sn']) : '';              //增加优惠券号
$is_use_bonus = isset($params['is_use_bonus']) ? intval($params['is_use_bonus']) : 1; //是否使用优惠券

if (empty($lng) || empty($lat)) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
	echo json_encode($ret);return ;
}

$open_city = RCityList::model()->getOpenCityList();

//验证token
$validate = CustomerToken::model()->validateToken($token);
//$validate = array('phone' => '13998999647');
if (!$validate) {
	$ret = array('code' => 1 , 'data' => '' , 'message' => '验证失败');
	echo json_encode($ret);return ;
}

//验证黑名单否
$black_customer = CustomerStatus::model()->is_black($validate['phone']);  //此处调用个黑名单验证方法(走缓存)
if ($black_customer) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '您已被列入黑名单,如果要叫代驾,请呼叫 4006913939');
	echo json_encode($ret);return ;
}

//验证订单数量是否到达上限(规则待定)
$order_number = DalOrder::model()->validateOrderNumber($validate['phone']);    //获取订单数量方法走redis
if ($order_number >= DalOrder::QUEUE_MAX) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '最多同时预约5名司机噢~如有另外需要请拨打4006913939');
	echo json_encode($ret);return ;
}

//通过坐标获取
$gps_location = array(
    'longitude' => $lng,
    'latitude' => $lat,
);
$gps = GPS::model()->convert($gps_location , $gps_type);
if (empty($city_id)) {
	$city = GPS::model()->getCityByBaiduGPS($gps['baidu_lng'] , $gps['baidu_lat']);
	$city_id = CityConfig::getIdByName($city);
}

//验证城市是否开通,未开通城市city_id 返回默认 '未知' 的城市id
if (!array_key_exists($city_id , $open_city)) {
    $city_id = CityConfig::getIdByName($city);
}
$address = $gps['street'];

//验证司机是否是空闲 add by sunhongjing 2013-10-22
if($type == DalOrder::SINGLE_PUSH_DRIVER){
	$driver_info = DriverStatus::model()->get($driver_id);
	if( '0' != $driver_info->status ){
		$ret = array('code' => 2 , 'data' => '' , 'message' => '司机服务中或已下班');
		echo json_encode($ret);return ;
	}
	
	//验证司机有没有被400订单锁定 
	$timestamp = QueueDispatchDriver::model()->get($driver_id);
	if ($timestamp != 0) {
		$lock_time = abs(time() - $timestamp);
		$lock_gap = QueueDispatchDriver::DRIVER_LOCK_GAP;
		if ($lock_time < $lock_gap) { //验证时间改成70s 2014-01-13 11:55
			$ret = array('code' => 2 , 'data' => '' , 'message' => '真不巧,司机刚被选走,烦请再选一名!');
			echo json_encode($ret);return ;
		}
	}
	
	//改成锁司机，而不直接改司机的状态。add by sunhongjing 2014-01-17
	//$driver_info->status = 1;
	$is_lock = QueueDispatchDriver::model()->insert($driver_id);
}

//生成booking_id
//$booking_id = Common::genCallId();
$booking_id = Tools::getUniqId('high');

//存储google坐标
if ($gps_type == 'google') {
	$google_lng = $lng;
	$google_lat = $lat;
} else {
	$google_lng = $gps['google_lng'];
	$google_lat = $gps['google_lat'];
}
//存储google坐标 END
if (!empty($driver_id)) {
	$task = array(
	    'method' => 'app_single_booking',
	    'params' => array(
	        'phone' => $validate['phone'],
	        'city_id' => $city_id,
	        'address' => $address,
	        'callid' => $booking_id,
	        'driver_id' => $driver_id,
	        'booking_time' => date('Y-m-d H:i:s' , time()+1200),
	        'lng' => $gps['baidu_lng'],
	        'lat' => $gps['baidu_lat'],
	        'google_lng' => $google_lng,
	        'google_lat' => $google_lat,
	        'type' => $type,

            'bonus_sn' => $bonus_sn,
            'is_use_bonus' => $is_use_bonus,
	    )
	);
} else {
	$task = array(
	    'method' => 'api_customer_single',
	    'params' => array(
	        'phone' => $validate['phone'],
	        'city_id' => $city_id,
	        'address' => $address,
	        'callid' => $booking_id,
	        'driver_id' => $driver_id,
	        'booking_time' => date('Y-m-d H:i:s' , time()+1200),
	        'lng' => $gps['baidu_lng'],
	        'lat' => $gps['baidu_lat'],
	        'google_lng' => $google_lng,
	        'google_lat' => $google_lat,
	        'type' => $type,

            'bonus_sn' => $bonus_sn,
            'is_use_bonus' => $is_use_bonus,
	    )
	);
}

Queue::model()->putin($task,'apporder');


$booking_type = ($type == DalOrder::SINGLE_PUSH_DRIVER) ? 
			DalOrder::QUEUE_CHANNEL_SINGLE_DRIVER : DalOrder::QUEUE_CHANNEL_SINGLE_CHANGE;

$order_time = time();
$timeout = ($type == DalOrder::SINGLE_PUSH_DRIVER) ?  
			DalOrder::POLLING_SECOND_DRIVER : DalOrder::POLLING_SECOND_CHANGE;
			
$ret = array(
			'code' => 0 , 
			'data' => array(
							'booking_id' => $booking_id , 
							'booking_type' => $booking_type , 
							'order_time' => $order_time , 
							'timeout' => $timeout
				) , 
			'message' => '下单成功'
		);
echo json_encode($ret);
return;









