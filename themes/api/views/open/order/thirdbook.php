<?php
/**
 * 第三方下单接口
 * @author AndyCong<congming@edaijia-staff.cn>
 * @version 2014-02-21
 */

//接收验证参数
$phone = isset($params['phone']) ? trim($params['phone']) : '';
$contact_phone = isset($params['contact_phone']) ? trim($params['contact_phone']) : '';
$address = isset($params['address']) ? trim($params['address']) : '';
$channel = isset($params['channel']) ? trim($params['channel']) : '';
$number = isset($params['number']) ? intval($params['number']) : 1;
$city_name = isset($params['city_name']) ? trim($params['city_name']) : '';
$city_id = isset($params['city_id']) ? $params['city_id'] : 0;
$lng = isset($params['lng']) ? trim($params['lng']) : '';
$lat = isset($params['lat']) ? trim($params['lat']) : '';
$bookingTime = isset($params['bookingTime']) ? trim($params['bookingTime']) : (date('Y-m-d H:i:s' , time() + 1200));
$needManual = isset($params['need_manual']) ? trim($params['need_manual']) : false;


if (empty($phone) || empty($contact_phone) || empty($address) || $number == 0 ) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
	echo json_encode($ret);return ;
}

//此处需要验证渠道号  设定几个渠道号验证
//$channel_arr = array(
//    '03014',
//);
//if (!in_array($channel , $channel_arr)) {
//	$ret = array('code' => 2 , 'message' => '渠道号有误!');
//	echo json_encode($ret);return ;
//}
//
////验证账户电话号否正确
//$account_arr = array(
//    '95518955180',
//    '95518955183',	
//);
if (!is_numeric($phone) || strlen($phone) > 11 ) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '请输入正确的账户号码!');
	echo json_encode($ret);return ;
}

//验证联系人电话号码是否正确
$is_mobile = Common::checkPhone($contact_phone);
if (!$is_mobile) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '请输入正确的联系人手机号!');
	echo json_encode($ret);return ;
}

//验证预约人数不能超过5人
if ($number > 5) {
	$ret = array('code' => 2 , 'data' => '' , 'message' => '预约最大人数不能超过5人');
	echo json_encode($ret);return ;
}

//获取开通城市
$open_city = RCityList::model()->getOpenCityList();


if ($city_id != 0) {
	$city_name = CityConfig::getNameById($city_id);
} else {
	$city_id = CityConfig::getIdByName($city_name);
}

//$gps_baidu = Helper::getBaiduGPSByAddress($city_name , $address);
//if ($city_id == 0 || empty($gps_baidu['location'])) {
//	$ret = array('code'=>2 , 'message'=>'该城市暂时还未开通e代驾服务!');
//	echo json_encode($ret);return;
//}

//$lng = !empty($lng) ? $lng : $gps_baidu['location']['lng'];
//$lat = !empty($lat) ? $lat : $gps_baidu['location']['lat'];

//生成booking_id
$booking_id = Common::genCallId();
$flag = OrderQueue::QUEUE_WAIT_COMFIRM;
if($needManual){
	$flag = OrderQueue::QUEUE_WAIT;
}


$source = Order::SOURCE_CALLCENTER;
$check_ret = Order::CheckSpecialOrderSource(
	Order::SOURCE_DAYTIME_CALLCENTER, $city_id, strtotime($bookingTime));

EdjLog::info($phone."是否是日间单检测：".json_encode($check_ret));

//符合日间单
if($check_ret['flag']) {
	$source = Order::SOURCE_DAYTIME_CALLCENTER;
}

$task = array(
    'method' => 'api_third_book',
    'params' => array(
        'phone' => $phone,
        'contact_phone' => $contact_phone,
        'city_id' => $city_id,
        'address' => $address,
        'callid' => $booking_id,
        'booking_time' => $bookingTime,
        'number' => $number,
        'lng' => $lng,
        'lat' => $lat,
        'channel' => $channel,
		'flag'	  => $flag,
		'source' => $source,
    ),
);

Queue::model()->putin($task , 'apporder');
$order_time = time();
$ret = array(
    'code' => 0,
    'data' => array(
        'booking_id' => $booking_id,
        'order_time' => $order_time,
    ),
    'message' => '下单成功',
);
echo json_encode($ret);return ;
