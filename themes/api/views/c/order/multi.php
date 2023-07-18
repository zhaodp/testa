<?php
/**
 * 客户端API：c.order.multi 多人订单下单接口。验证客户信息，司机状态，是否订单队列有重复，是否达到服务中订单上限,需要考虑性能
 *           验证城市是否开通
 * @param token
 * @param type 司机下单方式
 * @param num 预约人数
 * @param contact_phone  联系电话
 * @param address 地址
 * @param lng 经度
 * @param lat 纬度
 * @author AndyCong 2013-10-14
 * @return json
 * @version 1.0
 */
if(Yii::app()->params['c_order_multi_refactor_on']) {
    $result = PlaceOrderService::getInstance()->automatic($params);
    echo json_encode($result);
    return;
}

//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$number = isset($params['number']) ? intval($params['number']) : '';
$contact_phone = isset($params['contact_phone']) ? trim($params['contact_phone']) : '';
$address = isset($params['address']) ? trim($params['address']) : '';
$lng = isset($params['lng']) ? trim($params['lng']) : '';
$lat = isset($params['lat']) ? trim($params['lat']) : '';
$gps_type = isset($params['gps_type']) ? trim($params['gps_type']) : 'google';
$edited = isset($params['edited']) ? $params['edited'] : 0;
$city_id = isset($params['city_id']) ? intval($params['city_id']) : '';
$channel = isset($params['type']) ? $params['type'] : CustomerApiOrder::QUEUE_CHANNEL_BOOKING;
$source = isset($params['source'])? $params['source']: Order::SOURCE_CLIENT;
$os = isset($params['os']) ? $params['os'] : '';
$app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';
$fee = isset($params['fee']) ? $params['fee'] : '';
$booking_time = isset($params['booking_time']) ? intval($params['booking_time']) : 0;
$cash_only = isset($params['cash_only']) ? intval($params['cash_only']) : 0;
$udid = isset($params['udid']) ? $params['udid'] : null;

if($channel !== CustomerApiOrder::QUEUE_CHANNEL_BOOKING &&
	$channel !== CustomerApiOrder::QUEUE_CHANNEL_FIFTEEN_MIN_BOOKING &&
	$channel !== CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER) {
    $ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
    echo json_encode($ret);return ;
}

$bonus_sn = isset($params['bonus_sn']) ? $params['bonus_sn'] : '';                    //增加优惠券号码
$is_use_bonus = isset($params['is_use_bonus']) ? intval($params['is_use_bonus']) : 1; //是否使用优惠券

if (empty($number) || empty($contact_phone) || empty($address) || empty($lng) || empty($lat)) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
	echo json_encode($ret);return ;
}

$customer_lng = isset($params['customer_lng']) ? $params['customer_lng'] : '';
$customer_lat = isset($params['customer_lat']) ? $params['customer_lat'] : '';
$from = isset($params['from']) ? $params['from'] : '';

//验证联系人电话号码是否正确
$is_mobile = Common::checkPhone($contact_phone);
if (!$is_mobile) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '请输入正确的联系人手机号');
	echo json_encode($ret);return ;
}

//验证一个预约信息最大人数不超过5人
if ($number > 5 ) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '预约人数不能超过5人');
	echo json_encode($ret);return ;
}

$open_city = RCityList::model()->getOpenCityList();

//验证token
$validate = CustomerToken::model()->validateToken($token);
//$validate = array('phone' => '15116917782');
if (!$validate) {
	$ret = array('code' => 1 , 'data' => '' , 'message' => '验证失败');
	echo json_encode($ret);return ;
}

// 验证是否在黑名单里面(包括V2后台黑名单，订单流黑名单)
$black_customer = CustomerStatus::model()->is_black($validate['phone'])
                  || OrderBlackListRedis::model()->isInBlacklist($validate['phone'], $udid);
if ($black_customer) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '尊敬的客户，由于您短时间内取消多名司机，造成系统无法为您提供正常服务；如急需代驾，可拨打热线4006913939');
	echo json_encode($ret);return ;
}

//验证订单数量是否到达上限
$order_number = CustomerApiOrder::model()->validateOrderNumber($validate['phone']);    //获取订单数量方法走redis
if (($order_number+$number) > CustomerApiOrder::QUEUE_MAX) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '最多同时预约5名司机噢~如有另外需要请拨打4006913939');
	echo json_encode($ret);return ;
}

//生成booking_id
$booking_id = Common::genCallId();

//通过gps反推城市及地址
if(!empty($customer_lng) && !empty($customer_lat)
    && ($customer_lng != $lng || $customer_lat != $lat)) {
    $customer_gps = _gps_convert($customer_lng, $customer_lat, $gps_type);
    if(isset($customer_gps['baidu_lng']) && isset($customer_gps['baidu_lat'])) {
        $customer_lng = $customer_gps['baidu_lng'];
        $customer_lat = $customer_gps['baidu_lat'];
    }
}
$gps = _gps_convert($lng, $lat, $gps_type);
if(isset($gps['baidu_lng']) && isset($gps['baidu_lat'])) {
    $lng = $gps['baidu_lng'];
    $lat = $gps['baidu_lat'];
}

if (!empty($city_id)) { //gps.location给返回的city_id 直接用
    $city = Dict::item('city' , $city_id);
	$city = !$city && GPS::model()->getCityByBaiduGPS($gps['baidu_lng'] , $gps['baidu_lat']);
} else {
	$city = GPS::model()->getCityByBaiduGPS($gps['baidu_lng'] , $gps['baidu_lat']);
	$city_id = CityConfig::getIdByName($city);
}

//验证城市是否开通,未开通城市city_id 返回默认 '未知' 的城市id
if ( !array_key_exists($city_id , $open_city)) {
    $city_id = CityConfig::getIdByName($city);
}

//注意下面的逻辑到赤道或子午线处是走不通的
//如果预约坐标和下单坐标一致,使用客户坐标下单
if(floatval($customer_lng) != 0 && floatval($customer_lat != 0)
    && $customer_lng == $lng && $customer_lat == $lat) {
    //nothing to do
    EdjLog::info('CustomerLocate|'.$lng.','.$lat);
}
else if(false !== stristr($os,'android') && !empty($app_ver) && strcmp($app_ver,'5.0.0') < 0 && abs($customer_lng) > 0.0 && abs($customer_lat) > 0.0){
    //留空的逻辑，android老版本直接用customer_lng,customer_lat参数
    $lng = $customer_lng;
    $lat = $customer_lat;
    EdjLog::info('old android customer location|'.$lng.','.$lat);
}
else if((false === stristr($os,'android') || (!empty($app_ver) && strcmp($app_ver,'5.0.0') >= 0)) && (abs($lng) > 0.0 && abs($lat) > 0.0)){
    //ios 所有版本，android 新版本 都用 lng lat 参数
    EdjLog::info('new customer location|'.$lng.','.$lat);
}
else {
    $gps_baidu = Helper::getBaiduGPSByAddress($city , $address);
    if (!empty($gps_baidu['location'])) {
        EdjLog::info('AddressLocate|'.$lng.','.$lat.'|'.$gps_baidu['location']['lng'].','.$gps_baidu['location']['lat']);
        $lng = $gps_baidu['location']['lng'];
        $lat = $gps_baidu['location']['lat'];
    } else {
        EdjLog::info('customer location not good '.serialize($params));
	    $ret = array('code'=>2 , 'message'=>'该城市暂时还未开通e代驾服务!');
	    echo json_encode($ret);
        return;
    }
}

$new_address = GPS::model()->getStreetByBaiduGPS($lng , $lat, 4);
if (!empty($new_address) && isset($new_address['name'])) {
    $address = $new_address['name'];
}

if ($edited == 0) {
	$google_lng = isset($gps['google_lng']) ? $gps['google_lng'] : $lng;
	$google_lat = isset($gps['google_lat']) ? $gps['google_lat'] : $lat;
} else {
	$google_lng = $lng;
	$google_lat = $lat;
}

//日间暂不支持远程单,为了方便用户端
//如果channel是远程单,把source替换成SOURCE_CLIENT
if($channel == CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER) {
    $source = Order::SOURCE_CLIENT;
}

//普通订单验证
if(in_array($source, Order::$distance_sources)
    && $channel != CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER) {
    $check_ret = Order::CheckSpecialOrderSource(
        Order::SOURCE_DAYTIME_CLIENT, $city_id, time());

    //符合日间单
    if($check_ret['flag']) {
        if(Helper::compareVersion('5.3.0', $app_ver)) {
             EdjLog::info("ChangeSourceToDaytime|".$token.'|'.$source.'|'.$city_id);
             $source = Order::SOURCE_DAYTIME_CLIENT;
        }
        else {
            $ret = array('code' => ApiErrorCode::ORDER_TIME_ERROR,
                'data' => '', 'message' => '当前时段所下订单为日间按时间计费方式，继续为您下单？');
	    echo json_encode($ret);

            EdjLog::info("CheckSpecialOrderSource|".$token.'|'.$source
                .'|'.$city_id.'|'.$ret['code']);

            return;
        }
    }
}

//洗车
if(in_array($source, Order::$washcar_sources)) {
    $check_ret = Order::CheckSpecialOrderSource($source, $city_id, time(), $channel);
    if(!$check_ret['flag']) {
        $ret = array('code' => $check_ret['code'],
	        'data' => '', 'message' => '下单失败');

        EdjLog::info("CheckSpecialOrderSource|".$token.'|'.$source
            .'|'.$city_id.'|'.$ret['code']);

	echo json_encode($ret);
        return;
    }
}

//日间模式校验
if(in_array($source, Order::$daytime_sources)) {
    // 用当前服务器时间做校验
    $check_ret = Order::CheckSpecialOrderSource($source, $city_id, time(), $channel);

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

        EdjLog::info("CheckSpecialOrderSource|".$token.'|'.$source
            .'|'.$city_id.'|'.$ret['code']);

	echo json_encode($ret);
        return;
    }
}

//一键下单下一单 预约时间是10分钟
if($channel == CustomerApiOrder::QUEUE_CHANNEL_BOOKING
    && $number == 1) {
    $booking_time = 600;
}

//下单处理(队列处理：判定司机状态，有误重复下单)
$bonus_use_limit=in_array($source, Order::$daytime_sources)?5:4;
$task = array(
    'method' => 'api_customer_multi',
    'params' => array(
        'cash_only' => $cash_only,
        'phone' => $validate['phone'],
        'contact_phone' => $contact_phone,
        'city_id' => $city_id,
        'address' => $address,
        'callid' => $booking_id,
        'booking_time' => date('Y-m-d H:i:s',
	    time() + ($booking_time != 0 ? $booking_time : 1200)),
        'number' => $number,
        'lng' => $lng,
        'lat' => $lat,
        'google_lng' => $google_lng,
        'google_lat' => $google_lat,
        'bonus_use_limit' => $bonus_use_limit,
        'app_ver' => $app_ver< BonusCode::APP_VER?0:1,
        'bonus_sn' => $bonus_sn,
        'is_use_bonus' => $is_use_bonus,
        'channel' => $channel,
        'source' => $source,
        'fee' => $fee,
        'from' => $from
    )
);

//支持客户端取消读秒,生成订单前,预先在redis中写入部分信息
$key = $validate['phone']."_".$booking_id;
try {
    $tmp_info = CustomerApiOrder::model()->init_phonebooking($task['params']);
    QueueApiOrder::model()->insert($key, $tmp_info);
} catch(Exception $e) {
    EdjLog::warning('预先写入redis出错|'.$key.'|'.$e->getMessage());
}

Queue::model()->putin($task , 'apporder');

//下单处理(队列处理：判定司机状态，有误重复下单)
$order_time = time();
if($channel == CustomerApiOrder::QUEUE_CHANNEL_FIFTEEN_MIN_BOOKING) {
    $ret = array('code' => 0 , 'data' => array('booking_id' => $booking_id , 'booking_type' => CustomerApiOrder::QUEUE_CHANNEL_FIFTEEN_MIN_BOOKING , 'order_time' => $order_time , 'timeout' => CustomerApiOrder::POLLING_SECOND_FIFTEEN_MIN_BOOKING) , 'message' => '下单成功');
} else {
    $ret = array('code' => 0 , 'data' => array('booking_id' => $booking_id , 'booking_type' => $channel, 'order_time' => $order_time , 'timeout' => CustomerApiOrder::POLLING_SECOND_BOOKING) , 'message' => '下单成功');
}


echo json_encode($ret);return;

function _gps_convert($lng, $lat, $gps_type) {
    $gps_location = array(
        'longitude' => $lng,
        'latitude' => $lat,
    );
    $gps = GPS::model()->convert($gps_location , $gps_type);
    if(!isset($gps['baidu_lng']) || !isset($gps['baidu_lng'])) {
        EdjLog::info('GPS convert to baidu error|'.$lng.','.$lat);
    }

    return $gps;
}
