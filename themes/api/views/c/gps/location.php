<?php
/**
 * 客户端API：c.gps.location 计算gps位置。
 * @author sunhongjing 2013-09-09
 * @param gps_type {gps,google,baidu}
 * @return json
 * 
 * @see gps.location
 */
$gps_type = isset($params['gps_type']) ? trim(strtolower($params['gps_type'])) : 'google';
$channel  = isset($params['os']) ? trim($params['os']) : '';

//$lng	= sprintf('%.6f', $params['lng']);
//$lat	= sprintf('%.6f', $params['lat']);

//~ 必须参数验证
if (!isset($params['lng']) || !isset($params['lat'])) {
	$baidu_gps = array();
	$street = null;
	$ret = array ( 'code'=>0, 'location'=>array ( 'gps'=>$baidu_gps, 'street'=>$street), 'message'=>'');
	echo json_encode($ret);
	return;
}

$lng = $params['lng'];
$lat = $params['lat'];

//~ 范围验证
$d_lng = doubleval($lng);
$d_lat = doubleval($lat);

if ($d_lng < -180 || $d_lng > 180 || $d_lat < -90 || $d_lat > 90) {
	$baidu_gps = array();
	$street = null;
	$ret = array ( 'code'=>0, 'location'=>array ( 'gps'=>$baidu_gps, 'street'=>$street), 'message'=>'');
	echo json_encode($ret);
	return;
}

$baidu_gps = array();

switch ($gps_type) {
	case 'wgs84' :
		//把wgs84座标转换为百度座标后查询最近司机
		$baidu_gps = GPS::model()->Wgs2Baidu($lng, $lat);
		break;
	case 'google' :
		//把google座标转换为百度座标
		$baidu_gps = GPS::model()->Google2Baidu($lng, $lat);	
		break;
	case 'baidu' :
		$baidu_gps['longitude'] = $lng;
		$baidu_gps['latitude'] =$lat;
		break;
	default :
		$baidu_gps['longitude'] = $lng;
		$baidu_gps['latitude'] =$lat;
		break;
}

//$baidu = Helper::Wgs2Baidu($lng, $lat);
$street = GPS::model()->getStreetByBaiduGPS( $baidu_gps['longitude'], $baidu_gps['latitude'], 4); //4 所有GPS信息

$ret = array ( 'code'=>0, 'location'=>array ( 'gps'=>$baidu_gps, 'street'=>$street), 'message'=>'');

echo json_encode($ret);

