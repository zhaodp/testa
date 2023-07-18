<?php
class Lbs2GPS {
	const GPS_TYPE_GOOGLE='google';
	const GPS_TYPE_WGS84='wgs84';

	/**
	 * 把坐标转换成3个坐标系
	 * @param array $gps
	 * @param string $type
	 * @return array 
	 */
	public static function convert($gps, $type='wgs84') {
		$lng=$gps['longitude'];
		$lat=$gps['latitude'];
		
		switch ($type) {
			case 'google' :
				$gps['longitude']=$lng;
				$gps['latitude']=$lat;
				
				$gps['google_lng']=$lng;
				$gps['google_lat']=$lat;
				
				$baidu=Lbs2GPS::Google2Baidu($lng, $lat);
				if ($baidu) {
					$gps['baidu_lng']=$baidu['longitude'];
					$gps['baidu_lat']=$baidu['latitude'];
				}
				break;
			case 'wgs84' :
			default :
				$gps['longitude']=$lng;
				$gps['latitude']=$lat;
				
				$google=Lbs2GPS::Wgs2Google($lng, $lat);
				if ($google) {
					$gps['google_lng']=$google['longitude'];
					$gps['google_lat']=$google['latitude'];
				}
				
				$baidu=Lbs2GPS::Wgs2Baidu($lng, $lat);
				if ($baidu) {
					$gps['baidu_lng']=$baidu['longitude'];
					$gps['baidu_lat']=$baidu['latitude'];
				}
				break;
		}
		// GPS反查地址
		$address=GPS::model()->getStreetByBaiduGPS($gps['baidu_lng'] , $gps['baidu_lat'] , 3); //3 所有GPS信息
		$gps['street']=$address['name'];
				
		return $gps;
	}

	/**
	 * 
	 * google api返回基站gps座标信息
	 * @param array $params
	 * @param bool  $cache
	 */
	public static function Location($params, $cache=true) {
		if ($params['mcc']==0&&$params['lac']==0&&$params['ci']==0) {
			return null;
		}
		
		if (!isset($params['mnc'])) {
			$params['mnc']=0;
		}
		
		if ($cache) {
			$postion=Lbs::checkLocation($params['mcc'], $params['mnc'], $params['lac'], $params['ci']);
			if (!$postion) {
				//$postion = self::google_location($params);
				$postion=self::juhe_location($params);
				Lbs::addLocation($params['mcc'], $params['mnc'], $params['lac'], $params['ci'], $postion['latitude'], $postion['longitude'], $postion['address']);
			} else {
				$postion=array(
						'longitude'=>$postion->longitude,
						'latitude'=>$postion->latitude,
						'address'=>$postion->address
				);
			}
		} else {
			//$postion = self::google_location($params);
			//$postion = self::juhe_location($params);
		}
		
		return $postion;
	}

	public static function Wgs2Google($longitude, $latitude) {
		$cache_key='GPS_'.md5($longitude.$latitude);
		$json=Yii::app()->cache->get($cache_key);
		if ($json) {
			return json_decode($json, true);
		}
		
		$url='http://api.map.baidu.com/ag/coord/convert?from=0&to=2&x='.$longitude.'&y='.$latitude;
		$count=0;
		while(true) {
			$data=self::curl_get($url);
			if ($data||$count>5) {
				break;
			}
			echo $count;
			$count++;
		}
		$gps=json_decode($data, true);
		
		if (isset($gps['x'])) {
			$longitude=base64_decode($gps['x']);
			$latitude=base64_decode($gps['y']);
		}
		
		if ($longitude&&$latitude) {
			$gps=array(
					'longitude'=>$longitude,
					'latitude'=>$latitude
			);
			Yii::app()->cache->set($cache_key, json_encode($gps), 86400);
			return $gps;
		} else {
			return null;
		}
	}

	public static function Wgs2Baidu($longitude, $latitude) {
		$cache_key='wgs2baidu_GPS_'.md5($longitude.$latitude);
		$json=Yii::app()->cache->get($cache_key);
		if ($json) {
			return json_decode($json, true);
		}
		
		$url='http://api.map.baidu.com/ag/coord/convert?from=0&to=4&x='.$longitude.'&y='.$latitude;
		$count=0;
		while(true) {
			$data=self::curl_get($url);
			if ($data||$count>5) {
				break;
			}
			echo $count;
			$count++;
		}
		$gps=json_decode($data, true);
		
		if (isset($gps['x'])) {
			$longitude=base64_decode($gps['x']);
			$latitude=base64_decode($gps['y']);
		}
		
		if ($longitude&&$latitude) {
			$gps=array(
					'longitude'=>$longitude,
					'latitude'=>$latitude
			);
			Yii::app()->cache->set($cache_key, json_encode($gps), 86400);
			return $gps;
		} else {
			return null;
		}
	}

	public static function Google2Baidu($longitude, $latitude) {
		$cache_key='GPS_Google_'.md5($longitude.$latitude);
		$json=Yii::app()->cache->get($cache_key);
		if ($json) {
			return json_decode($json, true);
		}
		
		$url='http://api.map.baidu.com/ag/coord/convert?from=2&to=4&x='.$longitude.'&y='.$latitude;
		$count=0;
		while(true) {
			$data=self::curl_get($url);
			if ($data||$count>5) {
				break;
			}
			echo $count;
			$count++;
		}
		$gps=json_decode($data, true);
		
		if (isset($gps['x'])) {
			$longitude=base64_decode($gps['x']);
			$latitude=base64_decode($gps['y']);
		}
		
		if ($longitude&&$latitude) {
			$gps=array(
					'longitude'=>$longitude,
					'latitude'=>$latitude
			);
			Yii::app()->cache->set($cache_key, json_encode($gps), 86400);
			return $gps;
		} else {
			return null;
		}
	}

	/** 
	 * 位置转化为百度坐标
	 * @param string $palce
	 */
	public static function Place2Baidu($palce) {
		$url='http://api.map.baidu.com/place/search?&query=建国门火锅&region=%E5%8C%97%E4%BA%AC&output=json&key=e84e1c0102539d473db235592f108bea';
		$json=self::curl_get($url);
		$place_points=json_decode($json, true);
		$lines=array();
		$points=$place_points['results'];
		
		for($i=0; $i<count($points); $i++) {
			$x=array_shift($place_points['results']);
			$ret=self::points_distance($points[$i], $place_points['results']);
			echo count($ret)."\n";
		}
	}

	private static function points_distance($point, $points) {
		$lines=array();
		foreach($points as $item) {
			$distance=Helper::Distance($point['location']['lat'], $point['location']['lng'], $item['location']['lat'], $item['location']['lng']);
			$lines[]=array(
					'start'=>$point['location'],
					'end'=>$item['location']
			);
		}
		return $lines;
	}

	private static function google_location($params) {
		$postion=null;
		$call_towers='{
							"cell_id":'.$params['ci'].',
							"location_area_code":'.$params['lac'].',
							"mobile_country_code":'.$params['mcc'].',
							"mobile_network_code":'.$params['mnc'].',
  							"signal_strength":-'.$params['ssi'].'
						}';
		
		$vars='{
					"version": "1.1.0" ,
					"host": "maps.google.com",
					"access_token": "2:k7j3G6LaL6u_lafw:4iXOeOpTh1glSXe",
			  	    "home_mobile_country_code": '.$params['mcc'].',
					"home_mobile_network_code": '.$params['mnc'].',
					"address_language": "zh_CN",
					"radio_type": "gsm",
					"request_address": true ,
					"cell_towers":['.$call_towers.']
				}';
		
		$rdata=self::curl_post('http://www.google.com/loc/json', $vars);
		$r_ary=json_decode($rdata, true);
		if ($r_ary) {
			$latitude=$r_ary['location']['latitude'];
			$longitude=$r_ary['location']['longitude'];
			
			$last_value='';
			$address='';
			if (isset($r_ary['location']['address'])) {
				foreach($r_ary['location']['address'] as $item) {
					if ($item!=$last_value&&$item!='中国'&&$item!='CN') {
						$address.=$item;
					}
					$last_value=$item;
				}
			}
			$postion=array(
					'longitude'=>$longitude,
					'latitude'=>$latitude,
					'address'=>$address
			);
		}
		return $postion;
	}

	/**
	 * 
	 * juhe.cn
	 */
	private static function juhe_location($params) {
		$postion=null;
		
		$url='http://apis.juhe.cn/cell/get';
		$vars=array(
				'cell'=>$params['ci'],
				'lac'=>$params['lac'],
				'mnc'=>$params['mnc'],
				'mcc'=>$params['mcc'],
				'key'=>'88f7656daa3f1c45fa3704911ed88040'
		);
		
		$rdata=self::curl_post($url, $vars);
		if ($rdata) {
			$ret=json_decode($rdata, true);
			if ($ret['resultcode']==200) {
				$postion=array(
						'longitude'=>$ret['result']['data'][0]['LNG'],
						'latitude'=>$ret['result']['data'][0]['LAT'],
						'address'=>$ret['result']['data'][0]['ADDRESS']
				);
			}
		}
		return $postion;
	}

	private static function curl_post($url, $vars, $second=30) {
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
		$data=curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	private static function curl_get($url, $second=60) {
		$ch=curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 0);
		$data=curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}