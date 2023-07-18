<?php
/**
 * 客户端API：c.city.pricelist 通过city_id获取城市价格表
 * 调用的url:
 * @author sunhongjing 2014-01-09
 * @param $params $params['_callback']
 *
 * @return json
 * @see  c.city.pricelist
 * @since
 */

$lng = isset($params['longitude']) ? $params['longitude'] : '';
$lat = isset($params['latitude']) ? $params['latitude'] : '';
$cityName = isset($params['cityName']) ? $params['cityName'] : '';

if ($cityName == '') {
    //查询百度地图返回城市名称
    $cityName = GPS::model()->getCityByBaiduGPS($lng, $lat);
} else {
    //临时处理 bidong 2014-1-28
    $city = explode("市", $cityName);
    if (count($city) > 1) {
        $cityName = $city[0];
    }
    //临时处理
}

$city_id = CityConfig::getIdByName($cityName);

$daytimeType = RCityList::model()->getCityById($city_id,'daytime_price');
if ($daytimeType) {
    $isDayTime = true;
} else {
    $isDayTime = false;
}
/////start
$is_old_version = false;
if(in_array($city_id,array(4,48))){ //新日间业务价格表 客户端先上线杭州 徐州
    $is_old_version = false;
}
///// end 全量开通时删掉

$day_time_data = '';

/////start
if($is_old_version){
    $price = RCityList::model()->getFeeOldVersion($city_id, $isDayTime, true);
    $day_time_data = RCityList::model()->getDaytimePriceOld($city_id);
}else {
///// end 全量开通时删掉
    $price = RCityList::model()->getFee($city_id, $isDayTime, true);
    //日间业务
    if ($daytimeType ) {
        $day_time_data = RCityList::model()->getDaytimePrice($city_id,'',true);// is_client = true
    }
}///// one line 全量开通时删掉




$ret = array(
    'code' => 0,
    'city' => $cityName,
    'price_list' => $price,
    'message' => '价格表'
);
//if ($day_time_data) { //全量上线时恢复
if ( $day_time_data) {
    if(!$is_old_version ){///// one line 全量开通时删掉
        $match_price_rule = Yii::app()->params['daytime_price_new'][$daytimeType];
        $day_time_data['price_str'] = $day_time_data['price'].'元起步(含'.($match_price_rule['basic_time']/60).'小时 '.($match_price_rule['basic_distance']).'公里)';
        $day_time_data['desc'] = $match_price_rule['desc'];
    }///// one line 全量开通时删掉
    $ret['daytime_price'] = $day_time_data;
}


/////////////////
echo json_encode($ret);
