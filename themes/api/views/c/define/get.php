<?php
/**
 * 客户端API：c.define.get 
 * @param token
 * @author aiguoxin
 * @return json,成功信息，异常返回错误代码
 * 
 */

$lng = $params['longitude'];
$lat = $params['latitude'];

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
$cityId = 0;
foreach($citys as $key=>$value) {
    if ($value==$cityName){
        $cityId = $key;
        break;
    }
}


$long_distance=Common::getLongDistanceCity($cityId);

$ret = array(
        'code' => 0,
        'message' => '请求成功',
        'data' => $long_distance,
    );
echo json_encode($ret);
