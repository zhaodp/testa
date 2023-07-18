<?php
/**
 * 客户端API：c.driver.background 获取客户端司机头像背景 
 * @param token
 * @author aiguoxin
 * @return json,成功信息，异常返回错误代码
 * 
 */

$lng = isset($params['longitude']) ? $params['longitude'] : '';
$lat = isset($params['latitude']) ? $params['latitude'] : '';

if (empty($lng) || empty($lat)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}

$cityName = GPS::model()->getCityByBaiduGPS($lng,$lat);

$citys = Dict::items('city');
$citys = array_flip ($citys); 
$city_id=0;
if(isset($citys[$cityName])){
    $city_id=$citys[$cityName];
}

$image_data=ClientDriverBackground::model()->getBackGround($city_id);


$ret = array(
        'code' => 0,
        'image_data' => $image_data,
        'message' => '请求成功'
    );
echo json_encode($ret);
