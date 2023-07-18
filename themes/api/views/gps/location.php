<?php
/**
 * 计算gps位置
 * @author sunhongjing 2013-09-09
 * @param gps_type {gps,google,baidu}
 */
$gps_type = isset($params['gps_type']) ? trim(strtolower($params['gps_type'])) : 'google';
$channel  = isset($params['os']) ? trim($params['os']) : '';

//$lng	= sprintf('%.6f', $params['lng']);
//$lat	= sprintf('%.6f', $params['lat']);

$lng = isset($params['lng']) ? $params['lng'] : '';
$lat = isset($params['lat']) ? $params['lat'] : '';

if(empty($lng)|| empty($lat)){
    $ret = array ( 'code'=>0 , 'location'=>array ( 'gps'=>array('latitude'=>'','longitude'=>''), 'street'=>""), 'message'=>'');
    echo json_encode($ret);return;
}
$baidu_gps = array();

switch ($gps_type) {
	case 'wgs84' :
		//把google座标转换为百度座标后查询最近司机
		$baidu		= GPS::model()->Wgs2Baidu($lng, $lat);
		$longitude	= sprintf('%.6f', $baidu['longitude']);
		$latitude	= sprintf('%.6f', $baidu['latitude']);	
		break;
	case 'google' :
		$baidu		= GPS::model()->Google2Baidu($lng, $lat);
		$longitude 	= sprintf('%.6f', $baidu['longitude']);
		$latitude	= sprintf('%.6f', $baidu['latitude']);
		break;
	case 'baidu' :
		$longitude	= $lng;
		$latitude	= $lat;
		break;
	default :
		$longitude	= $lng;
		$latitude	= $lat;
		break;
}
$baidu_gps['latitude'] = $latitude;
$baidu_gps['longitude'] = $longitude;
//$baidu = Helper::Wgs2Baidu($lng, $lat);
$street = GPS::model()->getStreetByBaiduGPS($baidu_gps['longitude'], $baidu_gps['latitude'], 3); //3 所有GPS信息

$ret = array ( 'code'=>0, 'location'=>array ( 'gps'=>$baidu_gps, 'street'=>$street), 'message'=>'');

echo json_encode($ret);

