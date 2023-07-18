<?php
/**
 * 客户端API：c.order.booking 多人订单下单接口。验证客户phone 是否达到服务中订单上限,这个接口只给雷石用  没有token
 *           验证城市是否开通
 * @param num 预约人数
 * @param contact_phone  联系电话
 * @param address 地址
 * @param lng 经度
 * @param lat 纬度
 * @author AndyCong 2013-10-14
 * @return json
 * @version 1.0
 */
//接收并验证参数
$number = isset($params['number']) ? intval($params['number']) : '';
$contact_phone = isset($params['contact_phone']) ? trim($params['contact_phone']) : '';
$address = isset($params['address']) ? trim($params['address']) : '';
$lng = isset($params['lng']) ? trim($params['lng']) : '';
$lat = isset($params['lat']) ? trim($params['lat']) : '';
$gps_type = isset($params['gps_type']) ? trim($params['gps_type']) : 'google';
$city_id = isset($params['city_id']) ? intval($params['city_id']) : 0;
$city_name = isset($params['city_name']) ? $params['city_name'] : '北京';

$p = isset($params['p']) ? trim($params['p']) : '';
$from = isset($params['from']) ? trim($params['from']) : '';


if (empty($p) || empty($number) || empty($contact_phone) || empty($address) || empty($lng) || empty($lat) || $city_id == 0 || empty($city_name)) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
	echo json_encode($ret);return ;
}

//验证渠道
if (empty($from) || $from != 'leishi') {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '来源渠道有误');
	echo json_encode($ret);return ;
}

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

//验证加密字段
if ($p != md5($contact_phone.$from)) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '加密字段有误');
	echo json_encode($ret);return ;
}

//验证黑名单否
$black_customer = false;  //此处调用个黑名单验证方法(走缓存)
if ($black_customer) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '您已被列入黑名单,如果要叫代驾,请呼叫 4006913939');
	echo json_encode($ret);return ;
}

//验证订单数量是否到达上限
$order_number = CustomerApiOrder::model()->validateOrderNumber($contact_phone);    //获取订单数量方法走redis
if (($order_number+$number) > CustomerApiOrder::QUEUE_MAX) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '最多同时预约5名司机噢~如有另外需要请拨打4006913939');
	echo json_encode($ret);return ;
}

//生成booking_id
$booking_id = Common::genCallId();

//验证城市是否开通,未开通城市city_id 返回默认 '未知' 的城市id
if ( !array_key_exists($city_id , $open_city)) {
	$city_id = CityConfig::getIdByName($city_name);
}

//通过gps反推城市及地址
$gps_location = array(
    'longitude' => $lng,
    'latitude' => $lat,
);
$gps = GPS::model()->convert($gps_location , $gps_type);
$google_lng = isset($gps['google_lng']) ? $gps['google_lng'] : $lng;
$google_lat = isset($gps['google_lat']) ? $gps['google_lat'] : $lat;

//下单处理(队列处理：判定司机状态，有误重复下单)
$channel = CustomerApiOrder::QUEUE_CHANNEL_LEISHI;
$task = array(
    'method' => 'api_customer_multi',
    'params' => array(
        'phone' => $contact_phone,
        'contact_phone' => $contact_phone,
        'city_id' => $city_id,
        'address' => $address,
        'callid' => $booking_id,
        'channel' => $channel,
        'booking_time' => date('Y-m-d H:i:s' , time()+1200),
        'number' => $number,
        'lng' => $lng,
        'lat' => $lat,
        'google_lng' => $google_lng,
        'google_lat' => $google_lat,
    )
);
Queue::model()->putin($task , 'apporder');

//下单处理(队列处理：判定司机状态，有误重复下单)
$order_time = time();
$ret = array('code' => 0 , 'data' => array('booking_id' => $booking_id , 'booking_type' => $channel , 'order_time' => $order_time , 'timeout' => CustomerApiOrder::POLLING_SECOND_BOOKING) , 'message' => '下单成功');

echo json_encode($ret);return;