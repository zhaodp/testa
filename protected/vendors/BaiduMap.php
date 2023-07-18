<?php
/**
//百度坐标  转 百度像素坐标  返回的是array(x, y)
print_r(BaiduMap::pointToPixel(113.295750,23.156449));

//百度像素坐标 转 百度坐标   返回的是array(lng, lat)
print_r(BaiduMap::pixelToPoint(12612162.43,2633995.36));

//计算两点直接距离 返回数值 单位米 
BaiduMap::getDistance(113.311028, 23.152521, 113.295750, 23.156449);

//计算路网距离  返回数值 单位米 默认为步行方案
BaiduMap::getRouteDistance(113.311028, 23.152521, 113.295750, 23.156449);

//计算路网距离  返回数值 单位米  使用驾车方案
BaiduMap::getRouteDistance(113.311028, 23.152521, 113.295750, 23.156449, 'nav');

//中心点到 其他点路网距离  （多点处理)
$points = array(
	array(113.307927, 23.150112),
	array(113.304877, 23.153969),
	array(113.303300, 23.145011),
	array(113.291135, 23.143387),
	array(113.295750, 23.156449),
);

返回对应顺序数组
print_r(BaiduMap::getRouteDistanceMulti(113.311028, 23.152521, $points));


//起点，中途点， 终点多点计算距离 最少3个点，否则返回0
$points = array(
	array(113.311028, 23.152521),
	array(113.308992, 23.150120),
	array(113.307927, 23.150112),
	array(113.288797, 23.149972),
	array(113.289438, 23.152472),
	array(113.293261, 23.154627),
	array(113.297059, 23.158018),
	array(113.296552, 23.157329),
	array(113.295875, 23.156892),
	array(113.295750, 23.156449)
);

echo BaiduMap::getRouteDistanceMidWay($points);
**/
class BaiduMap {
	private static $PI = 3.141592653589793;
	private static $EARTHRADIUS = 6370996.81;
	private static $MCBAND = array(12890594.86, 8362377.87, 5591021, 3481989.83, 1678043.12, 0);
	private static $LLBAND = array(75, 60, 45, 30, 15, 0);
	private static $MC2LL = array(
		array(1.410526172116255e-8, 0.00000898305509648872, -1.9939833816331, 200.9824383106796, -187.2403703815547, 91.6087516669843, -23.38765649603339, 2.57121317296198, -0.03801003308653, 17337981.2), 
		array(- 7.435856389565537e-9, 0.000008983055097726239, -0.78625201886289, 96.32687599759846, -1.85204757529826, -59.36935905485877, 47.40033549296737, -16.50741931063887, 2.28786674699375, 10260144.86),
		array(- 3.030883460898826e-8, 0.00000898305509983578, 0.30071316287616, 59.74293618442277, 7.357984074871, -25.38371002664745, 13.45380521110908, -3.29883767235584, 0.32710905363475, 6856817.37),
		array(- 1.981981304930552e-8, 0.000008983055099779535, 0.03278182852591, 40.31678527705744, 0.65659298677277, -4.44255534477492, 0.85341911805263, 0.12923347998204, -0.04625736007561, 4482777.06),
		array(3.09191371068437e-9, 0.000008983055096812155, 0.00006995724062, 23.10934304144901, -0.00023663490511, -0.6321817810242, -0.00663494467273, 0.03430082397953, -0.00466043876332, 2555164.4),
		array(2.890871144776878e-9, 0.000008983055095805407, -3.068298e-8, 7.47137025468032, -0.00000353937994, -0.02145144861037, -0.00001234426596, 0.00010322952773, -0.00000323890364, 826088.5)
	);
	private static $LL2MC = array(
		array(- 0.0015702102444, 111320.7020616939, 1704480524535203, -10338987376042340, 26112667856603880, -35149669176653700, 26595700718403920, -10725012454188240, 1800819912950474, 82.5), 
		array(8.277824516172526E-4, 111320.7020463578, 6.477955746671607E8, -4.082003173641316E9, 1.077490566351142E10, -1.517187553151559E10, 1.205306533862167E10, -5.124939663577472E9, 9.133119359512032E8, 67.5), 
		array(0.00337398766765, 111320.7020202162, 4481351.045890365, -2.339375119931662E7, 7.968221547186455E7, -1.159649932797253E8, 9.723671115602145E7, -4.366194633752821E7, 8477230.501135234, 52.5), 
		array(0.00220636496208, 111320.7020209128, 51751.86112841131, 3796837.749470245, 992013.7397791013, -1221952.21711287, 1340652.697009075, -620943.6990984312, 144416.9293806241, 37.5), 
		array(- 3.441963504368392E-4, 111320.7020576856, 278.2353980772752, 2485758.690035394, 6070.750963243378, 54821.18345352118, 9540.606633304236, -2710.55326746645, 1405.483844121726, 22.5), 
		array(- 3.218135878613132E-4, 111320.7020701615, 0.00369383431289, 823725.6402795718, 0.46104986909093, 2351.343141331292, 1.58060784298199, 8.77738589078284, 0.37238884252424, 7.45)
	);

	private static function getLoop($a, $b, $c)
	{
		for(; $a>$b;) $a -= $c - $b;
		for(; $a<$b;) $a += $c - $b;
		return $a;
	}

	private static function getRange($a, $b, $c)
	{
		!is_null($b) && ($a = max($a, $b));
		!is_null($c) && ($a = min($a, $c));
		return $a;
	}

	private static function	convertor($lng, $lat, $t=array()) {
		if (!is_array($t)) return null;
		$x = $t[0] + $t[1] * abs($lng);
		$y = abs($lat) / $t[9];
		$y = $t[2] + $t[3] * $y + $t[4] * $y * $y + $t[5] * $y * $y * $y + $t[6] * $y * $y * $y * $y + $t[7] * $y * $y * $y * $y * $y + $t[8] * $y * $y * $y * $y * $y * $y;

		$x = $x * ( 0 > $lng ? -1 : 1);
		$y = $y * ( 0 > $lat ? -1 : 1);

		return array($x, $y);
	}

	private static function convertMC2LL($x, $y) {
		for ($d = 0; $d < count(self::$MCBAND); $d++) {
			if ($y >= self::$MCBAND[$d]) {
				$t = self::$MC2LL[$d];
				break;
            }
		}
        $ret = self::convertor($x, $y, $t);
		list($lng, $lat) = $ret;

		return array(round($lng, 6), round($lat, 6));
	}

	private static function _getDistance($lng1, $lat1, $lng2, $lat2) {
		return self::$EARTHRADIUS * acos((sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($lng2 - $lng1)));
	}
	
	private static function toRadians($n) {
		return self::$PI * $n / 180;
	}

	private static function toDegrees($n) {
		return (180 * $n) / self::$PI;
	}

	private static function convertLL2MC($lng, $lat) {
		$lng = self::getLoop($lng, -180, 180);
		$lat = self::getRange($lat, -74, 74);
	
		for($d=0; $d<count(self::$LLBAND); $d++) {
			if ($lat >= self::$LLBAND[$d]) {
				$t = self::$LL2MC[$d];
				break;
			}
		}
	
		if (!$t) {
			for ($d = count(self::$LLBAND) - 1; 0 <= $d; $d--) {
				if ($lat <= -self::$LLBAND[$d]) {
					$t = self::$LL2MC[$d];
	                break;
	            }
			}
		}
		$ret = self::convertor($lng, $lat, $t);
		list($x, $y) = $ret;
		return array(round($x, 2), round($y, 2));

	}

	private static function getDistanceByLL($lng1, $lat1, $lng2, $lat2) {
		$lng1 = self::getLoop($lng1, -180, 180);
        $lat1 = self::getRange($lat1, -74, 74);
		$lng2 = self::getLoop($lng2, -180, 180);
        $lat2 = self::getRange($lat2, -74, 74);
		$lng1 = self::toRadians($lng1);
		$lat1 = self::toRadians($lat1);
		$lng2 = self::toRadians($lng2);
		$lat2 = self::toRadians($lat2);
		return self::_getDistance($lng1, $lat1, $lng2, $lat2);
	}

	public static function getDistance($lng1, $lat1, $lng2, $lat2) {
		return self::getDistanceByLL($lng1, $lat1, $lng2, $lat2);
	}

	public static function pointToPixel($lng, $lat) {
		return self::convertLL2MC($lng, $lat);
	}

	public static function pixelToPoint($x, $y) {
		return self::convertMC2LL($x, $y);
	}

	public static function getRouteDistanceMidWay($points, $route_type='nav') {
		if (count($points)<3) return 0;
		$points_num = count($points);
		$chunk = 30;
		$mod = $points_num % $chunk;
		while ($mod == 1) {
				$chunk++;
				$mod = $points_num % $chunk;
		}

		$all_point = array_chunk($points, $chunk);
		$sum = 0;
		foreach($all_point as $p) {
			if (count($p) > 2) {
				$sum += self::_getRouteDistanceMidWay($p, $route_type);
			}
			else if (count($p) == 2) {
				$sum += self::getRouteDistance($p[0][0], $p[0][1], $p[1][0], $p[1][1], $route_type);
			}
		}
		return $sum;
	}

	private static function _getRouteDistanceMidWay($points, $route_type='nav') {
		if (count($points)<3) return 0;
		$start_point = array_shift($points);
		$start_point_mc = self::pointToPixel($start_point[0], $start_point[1]);

		$service_url = 'http://api.map.baidu.com/?qt='. $route_type .'&sn=1%24%24%24%24'. join(',', $start_point_mc) .'%24%24%24%24%24%24&en=';

		foreach($points as $key => $point) {
			$mc = self::pointToPixel($point[0], $point[1]);
			if ($key !=0 ) $service_url .='+to:';
			$service_url .= '1$$$$'. join(',', $mc). '$$$$$$';
		}

		$content = self::fetch($service_url);
		return self::_getRouteDistance($content);
	}


	/**
	计算两点路网距离
	 **/
	public static function getRouteDistance($lng1, $lat1, $lng2, $lat2, $route_type="walk")
	{
		$xy1 = self::pointToPixel($lng1, $lat1);
		$xy2 = self::pointToPixel($lng2, $lat2);
		$service_url = 'http://api.map.baidu.com/?qt='. $route_type .'&sn=1%24%24%24%24'. join(',', $xy1) .'%24%24%24%24%24%24&en=1%24%24%24%24'. join(',', $xy2) .'%24%24%24%24%24%24';

		$content = self::fetch($service_url);
		return self::_getRouteDistance($content);
	}

	public static function getRouteDistanceMulti($lng, $lat, $others= array(), $route_type="walk")
	{
		$center_xy = self::pointToPixel($lng, $lat);
		$service_urls = array();
		foreach($others as $o) {
			$xy = self::pointToPixel($o[0], $o[1]);
			$service_urls[] = 'http://api.map.baidu.com/?qt='. $route_type .'&sn=1%24%24%24%24'. join(',', $center_xy) .'%24%24%24%24%24%24&en=1%24%24%24%24'. join(',', $xy) .'%24%24%24%24%24%24';
		}

		$results = self::fetchMulti($service_urls);
		$ret = array();
		foreach($results as $result) {
			$ret[] = self::_getRouteDistance($result);
		}
		return $ret;
	}

    /**
     * 百度坐标返查地址
     * @param $lng
     * @param $lat
     * @param bool $poi
     */
    public static function getLocation($lng, $lat, $poi=false) {
        $pixels = BaiduMap::pointToPixel($lng, $lat);
        if ($poi) $poi_query = "&dis_poi=100&poi_num=10";
        else  $poi_query = "&dis_poi=-1&poi_num=0";
        $url = "http://api.map.baidu.com/?qt=rgc&x=". $pixels[0] ."&y=". $pixels[1] . $poi_query;
        $data = self::fetch($url);
        $data = @json_decode($data, true);
        return $data;
    }

    /**
     * 地址，查坐标
     * @param $address
     * @param $city
     * @return mixed
     */
    public static function getPoint($address, $city) {
        $address = urlencode($address);
        $city = urlencode($city);
        $url = "http://api.map.baidu.com/?qt=gc&wd=". $address ."&cn=". $city;
        $data = self::fetch($url);
        $data = @json_decode($data, true);

        if (isset($data['content']) && isset($data['content']['coord'])) {
            $coord = $data['content']['coord'];
            $location = BaiduMap::pixelToPoint($coord['x'],$coord['y']);
            $data['content']['location'] = array(
                'lng'=>$location[0], 'lat'=>$location[1]
            );
            unset($data['content']['coord']);
        }
        return $data;
    }

	private static function _getRouteDistance($content)
	{
		$pattern = '#"dis":(\d+),#';
		if (preg_match($pattern, $content, $matches)) {
			return $matches[1];
		}
		return 0;
	}

	private static function fetch($url) {
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	private static function fetchMulti($urls) {
		$ch = curl_multi_init(); 
		$count = count($urls); 
		$ch_arr = array(); 
		for ($i = 0; $i < $count; $i++) { 
			$query_string = $urls[$i]; 
			$ch_arr[$i] = curl_init($query_string); 
			curl_setopt($ch_arr[$i], CURLOPT_TIMEOUT, 1);
			curl_setopt($ch_arr[$i], CURLOPT_RETURNTRANSFER, 1); 
			curl_multi_add_handle($ch, $ch_arr[$i]); 
		} 
		$running = null; 
		do { 
			curl_multi_exec($ch, $running); 
		} while ($running > 0);
		
		for ($i = 0; $i < $count; $i++) { 
			$results[$i] = curl_multi_getcontent($ch_arr[$i]); 
			curl_multi_remove_handle($ch, $ch_arr[$i]); 
		} 
		curl_multi_close($ch); 
		return $results;
	}
}
