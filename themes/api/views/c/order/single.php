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
if(Yii::app()->params['c_order_single_refactor_on']) {
    $result = PlaceOrderService::getInstance()->chooseDriver($params);
    echo json_encode($result);
    return;
}

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
$source = isset($params['source']) ? intval($params['source']) : Order::SOURCE_CLIENT;
$booking_time = isset($params['booking_time']) ? intval($params['booking_time']) : 0;
$app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';
$udid = isset($params['udid']) ? $params['udid'] : null;

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

//验证是否在黑名单里面(包括V2后台黑名单，订单流黑名单)
$black_customer = CustomerStatus::model()->is_black($validate['phone']) 
                  || OrderBlackListRedis::model()->isInBlacklist($validate['phone'], $udid);
if ($black_customer) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '尊敬的客户，由于您短时间内取消多名司机，造成系统无法为您提供正常服务；如急需代驾，可拨打热线4006913939');
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

$new_address = GPS::model()->getStreetByBaiduGPS($gps['baidu_lng'], $gps['baidu_lat'], 4);
if (!empty($new_address) && isset($new_address['name'])) {
    $address = $new_address['name'];
}

//普通订单验证
if(in_array($source, Order::$distance_sources)) {
    $check_ret = Order::CheckSpecialOrderSource(
        Order::SOURCE_DAYTIME_CLIENT, $city_id, time());
    //符合日间单
    if($check_ret['flag']) {
        if(Helper::compareVersion('5.3.0', $app_ver)) {
             EdjLog::info("force change source to daytime|".$token.'|'.$driver_id.'|'.$source
                .'|'.$city_id);
             $source = Order::SOURCE_DAYTIME_CLIENT;
        }
        else {
            $ret = array('code' => ApiErrorCode::ORDER_TIME_ERROR,
                'data' => '', 'message' => '当前时段所下订单为日间按时间计费方式，继续为您下单？');

	    EdjLog::info("CheckSpecialOrderSource|".$token.'|'.$driver_id.'|'.$source
                .'|'.$city_id.'|'.$ret['code']);
	    echo json_encode($ret);
            return;
        }
    }
}

//洗车
if(in_array($source, Order::$washcar_sources)) {
    $check_ret = Order::CheckSpecialOrderSource($source, $city_id, time());
    if(!$check_ret['flag']) {
        $ret = array('code' => $check_ret['code'],
	        'data' => '', 'message' => '下单失败');

	EdjLog::info("CheckSpecialOrderSource|".$token.'|'.$driver_id.'|'.$source
            .'|'.$city_id.'|'.$ret['code']);
	echo json_encode($ret);
        return;
    }
}

//日间模式校验
if(in_array($source, Order::$daytime_sources)) {
    // 用当前服务器时间做校验
    $check_ret = Order::CheckSpecialOrderSource($source, $city_id, time());

    if(!$check_ret['flag']) {
        if($check_ret['code'] == ApiErrorCode::ORDER_CITY_ERROR) {
	    $ret = array('code' => $check_ret['code'],
	        'data' => '', 'message' => '当前城市没有开通日间服务，将按普通订单为您下单');
	}
	elseif($check_ret['code'] == ApiErrorCode::ORDER_TIME_ERROR) {
	    $ret = array('code' => $check_ret['code'],
	        'data' => '', 'message' => '当前时段所下订单为普通按里程计费方式，继续为您下单？');
	}
	else {
	    $ret = array('code' => $check_ret['code'],
	        'data' => '', 'message' => '下单失败');
	}

	EdjLog::info("CheckSpecialOrderSource|".$token.'|'.$driver_id.'|'.$source
            .'|'.$city_id.'|'.$ret['code']);
	echo json_encode($ret);
        return;
    }
}

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

$bonus_use_limit=in_array($source, Order::$daytime_sources)?5:4;
if (!empty($driver_id)) {
	$task = array(
	    'method' => 'app_single_booking',
	    'params' => array(
	        'phone' => $validate['phone'],
	        'city_id' => $city_id,
	        'address' => $address,
	        'callid' => $booking_id,
	        'driver_id' => $driver_id,
	        'booking_time' => date('Y-m-d H:i:s',
		    time() + ($booking_time != 0 ? $booking_time : 600)),
	        'lng' => $gps['baidu_lng'],
	        'lat' => $gps['baidu_lat'],
	        'google_lng' => $google_lng,
	        'google_lat' => $google_lat,
	        'type' => $type,
		'source' => $source,
            'bonus_use_limit' => $bonus_use_limit,
            'app_ver' => $app_ver< BonusCode::APP_VER?0:1,
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
	        'booking_time' => date('Y-m-d H:i:s',
		    time() + ($booking_time != 0 ? $booking_time : 600)),
	        'lng' => $gps['baidu_lng'],
	        'lat' => $gps['baidu_lat'],
	        'google_lng' => $google_lng,
	        'google_lat' => $google_lat,
	        'type' => $type,
		'source' => $source,
            'bonus_use_limit' => $bonus_use_limit,
            'app_ver' => $app_ver< BonusCode::APP_VER?0:1,
            'bonus_sn' => $bonus_sn,
            'is_use_bonus' => $is_use_bonus,
	    )
	);
}

//支持客户端取消读秒,生成订单前,预先在redis中写入部分信息
$key = $validate['phone']."_".$booking_id;
try {
    $tmp_info = CustomerApiOrder::model()->init_phonebooking($task['params']);
    QueueApiOrder::model()->insert($key, $tmp_info);
} catch(Exception $e) {
    EdjLog::warning('预先写入redis出错|'.$key.'|'.$e->getMessage());
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
