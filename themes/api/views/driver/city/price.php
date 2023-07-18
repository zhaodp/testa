<?php
/**
 * 通过GPS位置获取城市价格表
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-06-05
 */

if (!isset($params['lng']) || !isset($params['lat'])) {
	$ret = array ( 'code'=>2, 'message'=>'参数错误');
	echo json_encode($ret);exit;
}
$lng = $params['lng'];
$lat = $params['lat'];
$app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';
$gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'wgs84';

/**
 * 
 * 修改价格表信息,把结束日期减一分钟
 * @author sunhongjing 2013-05-08
 * @param unknown_type $d
 */
function getLastFeeHour($d)
{
	$ret = $d;
	$hour = @explode(':',$d);
	if(!empty($hour)){
		if(!empty($hour[0]) && '00' != $hour[0] ){
			$ret = sprintf("%02s:59", $hour[0]-1);
		}else{
			$ret = '23:59';
		}
	}
	return  $ret;
    
}

//查询百度地图返回城市名称
$gps_location = array(
    'longitude' => $lng,
    'latitude' => $lat,
);
$gps = GPS::model()->convert($gps_location , $gps_type);
$cityName = GPS::model()->getCityByBaiduGPS($gps['baidu_lng'] , $gps['baidu_lat']);
if (empty($cityName)) {
	$cityName = '未开通';
}

$cityId = CityConfig::getIdByName($cityName);
$daytimeType = RCityList::model()->getCityById($cityId,'daytime_price');
if ($daytimeType) {
    $isDayTime = true;
} else {
    $isDayTime = false;
}
$price = RCityList::model()->getFeeDriverClient($cityId, $isDayTime, true); //夜间代驾价格

$day_time_data = RCityList::model()->getDaytimePrice($cityId, $app_ver); //日间代驾价格
$city_info = RCityList::model()->getCityById($cityId);
$fixed_data = '';
//一口价 代驾洗车业务
$fix_type = $city_info['wash_car_price']; //洗车
if($fix_type) {
    $fixed_data = CityConfig::getWashCarPrice($fix_type);
}
else{ //应司机端要求，没开通一口价业务返回默认北京的价格表。duke
    $fixed_data =  CityConfig::getWashCarPrice(1);
}

$ret = array (
    'code'=>0,
    'city_id'=>$cityId,
    'city_name'=>$cityName,
    'price_list'=>$price,
    'message'=>'价格表');
if($day_time_data) $ret['daytime_price'] = $day_time_data;
if(is_numeric($fixed_data)) $ret['fixed_price'] = array( 'washing_car'=>$fixed_data );



echo json_encode($ret);