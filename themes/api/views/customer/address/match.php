<?php
/**
 * 获取历史地址记录
 * User: zhanglimin
 * Date: 13-07-22
 * Time: 上午10:48
 */
$lng= isset($params['lng']) ? $params['lng'] : "";
$lat= isset($params['lat']) ? $params['lat'] : "";
$gps_type = isset($params['gps_type']) ? $params['gps_type'] : "wgs84";
$address = isset($params['address']) ? trim($params['address']) : "";
$city_name = isset($params['city_name']) ? trim($params['city_name']) : "";
if(empty($lat) || empty($lng) || empty($address)){
    $ret = array (
        'code'=>10,
        'message'=>'参数错误'
    );
    echo json_encode($ret);
    return;
}

$result = AddressCallHistory::model()->nearby($lng,$lat,$gps_type ,$address ,$city_name);
$ret = array (
    'code'=>0,
    'city_name'=>$result['city_name'],
    'address_list'=>$result['address_list'],
    'message'=>'获取成功'
);
echo json_encode($ret);
return;