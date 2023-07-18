<?php
//百度坐标转换成GPS坐标  
$lnglat = '121.437518,31.224665';
function FromBaiduToGpsXY($lnglat) {
	// 经度,纬度  
	$lnglat = explode(',', $lnglat);
	list($x, $y) = $lnglat;
	$Baidu_Server = "http://api.map.baidu.com/ag/coord/convert?from=0&to=4&x={$x}&y={$y}";
	$result = @file_get_contents($Baidu_Server);
	$json = json_decode($result);
	if ($json->error==0) {
		$bx = base64_decode($json->x);
		$by = base64_decode($json->y);
		$GPS_x = 2*$x-$bx;
		$GPS_y = 2*$y-$by;
		return $GPS_x.','.$GPS_y; //经度,纬度  
	} else
		return $lnglat;
}

function fn_rad($d) {
	return $d*pi()/180.0;
}

// 2点间算法  
function P2PDistance($latlng1, $latlng2) {
	// 纬度1,经度1 ~ 纬度2,经度2  
	$latlng1 = explode(',', $latlng1);
	$latlng2 = explode(',', $latlng2);
	list($lat1, $lng1) = $latlng1;
	list($lat2, $lng2) = $latlng2;
	$EARTH_RADIUS = 6378.137;
	$radLat1 = fn_rad($lat1);
	$radLat2 = fn_rad($lat2);
	$a = $radLat1-$radLat2;
	$b = fn_rad($lng1)-fn_rad($lng2);
	$s = 2*asin(sqrt(pow(sin($a/2), 2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2), 2)));
	$s = $s*$EARTH_RADIUS;
	$s = round($s*10000)/10000;
	return number_format($s, 2);
}
echo '百度坐标: ', $lnglat, '<br><br>', '转换后GPS坐标: ', FromBaiduToGpsXY($lnglat), '<br><br>';
echo '转换前距离: ', P2PDistance('31.224286666667,121.420675', '31.224665,121.437518'), '<br/>';
echo '转换后距离: ', P2PDistance('31.224286666667,121.420675', '31.220157068379,121.42647022694');
