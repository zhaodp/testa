<?php
/**
 * 通过GPS位置获取城市价格表
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-06-05
 */

//开通城市列表
$open_city = RCityList::model()->getOpenCityList();

if (!isset($params['lng']) || !isset($params['lat'])) {
	$ret = array ( 'code'=>1, 'message'=>'参数错误');
	echo json_encode($ret);return ;
}
$lng = $params['lng'];
$lat = $params['lat'];
$gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'wgs84';

$priceContent = Yii::app()->params['appContent']['priceContent'];


//查询百度地图返回城市名称
$gps_location = array(
    'longitude' => $lng,
    'latitude' => $lat,
);
$gps = GPS::model()->convert($gps_location , $gps_type);
$cityName = GPS::model()->getCityByBaiduGPS($gps['baidu_lng'] , $gps['baidu_lat']);

$cityId = CityConfig::getIdByName($cityName);

$daytimeType = RCityList::model()->getCityById($cityId,'daytime_price');
if ($daytimeType) {
    $isDayTime = true;
} else {
    $isDayTime = false;
}
$price = RCityList::model()->getFee($cityId, $isDayTime, true);



if($isDayTime){
    $no1 = $priceContent['memo']['4']['zh'];
    $n  = Yii::app()->params['daytime_price_new'][$daytimeType];
    $a = $n['basic_time']/60;
    $no1 = sprintf($no1, $n['start_time'], $n['end_time'], $n['price'],  $a, $n['basic_distance'], $n['beyond_time_unit'], $n['beyond_time_price'],
        $n['beyond_distance_unit'],$n['beyond_distance_price'],$n['beyond_time_unit'],$n['beyond_time_unit'],$n['beyond_distance_unit'],$n['beyond_distance_unit']);
    //白天%s~%s时段%s元起步（含$s小时、$s公里）， 超出部分每增加$s分钟$s元，代驾距离每增加$s公里加收$s元。超出部分时间不足$s分钟按$s分钟计算，里程不足$s公里按$s公里计算。夜间
    $first_info = $no1.$priceContent['memo']['1']['zh'];
} else {
    $first_info = $priceContent['memo']['1']['zh'];
}


$second_info = sprintf($priceContent['memo']['2']['zh'], $price['distince'], $price['next_distince'], $price['next_price'], $price['next_distince'], $price['next_distince']);
$third_info = sprintf($priceContent['memo']['3']['zh'], $price['before_waiting_time'], $price['before_waiting_price'],$price['before_waiting_time']);


$ret = array (
	'code'=>0, 
	'price_list'=>$price,
	'memo'=>array (
			'1'=>$first_info, 
			'2'=>$second_info,
			'3'=>$third_info, 
			), 
	'city' => $cityName,
	'message'=>'价格表');


echo json_encode($ret);