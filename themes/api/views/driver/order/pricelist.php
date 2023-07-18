<?php
/**
 * 司机端计价器取不同城市价格表，用城市id取价格表
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-06-05
 */

$token = isset($params['token']) && !empty($params['token']) ? trim($params['token']) : "";
$order_id = isset($params['order_id']) ? $params['order_id'] : '';
$app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';

//$lng = isset($params['lng']) ? $params['lng'] : '';
//$lat = isset($params['lat']) ? $params['lat'] : '';
//$gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'wgs84';

$driver = DriverStatus::model()->getByToken($token);

if (empty($driver) || $driver->token===null||$driver->token!==$token) {
    $ret=array( 'code'=>1, 'message'=>'token失效' );
    echo json_encode($ret);
    return;
}

$lng	= $driver->position['baidu_lng'];
$lat	= $driver->position['baidu_lat'];

$city = GPS::model()->getCityByBaiduGPS($lng , $lat);
$cityId = CityConfig::getIdByName($city);
EdjLog::info("Price list,driver_id:".$driver->driver_id.",cityId:".$cityId);
if($cityId=="191"){
    $cityId = $driver->city_id;
    $city = CityConfig::getNameById($cityId);
    EdjLog::info("Price list,use default cityid,driver_id:".$driver->driver_id.",cityId:".$cityId);
}

$p = array(
    'token' => $token,
    'method' => 'driver.order.pricelist',
    'order_id' => $order_id,
    'lng' => $driver->position['baidu_lng'],
    'lat' => $driver->position['baidu_lat'],
    'city_id' => $cityId,
	'log_time' => time(),
);
$task = array(
    'method' => 'order_pricelist_tmp_log',
    'params' => $p,
);
Queue::model()->putin($task,'test');

$daytimeType = RCityList::model()->getCityById($cityId,'daytime_price');
if ($daytimeType) {
    $isDayTime = true;
} else {
    $isDayTime = false;
}
$price = RCityList::model()->getFeeDriverClient($cityId, $isDayTime, true); //夜间代驾价格

$day_time_data = RCityList::model()->getDaytimePrice($cityId, $app_ver); //日间业务价格
$city_info = RCityList::model()->getCityById($cityId);
$fixed_data = '';
//一口价 代驾洗车业务
$fixed_price = $city_info['wash_car_price'];
if($fixed_price) {
    $fixed_data = CityConfig::getWashCarPrice($fixed_price);
}
else { //应司机端要求，没开通一口价业务返回默认北京的价格表。duke
    $fixed_data = CityConfig::getWashCarPrice(1);
}
$ret = array (
	'code'=>0,
    
    'city_id'=>$cityId,
    'city_name'=>$city,
	'price_list'=>$price,
	'message'=>'价格表');
if($day_time_data) $ret['daytime_price'] = $day_time_data;
if(is_numeric($fixed_data)) $ret['fixed_price'] = array( 'washing_car'=>$fixed_data );


echo json_encode($ret);