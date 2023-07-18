<?php
/**
 * 调用百度的地图接口转换GPS坐标及地址
 * @author dayuer
 *
 */
class BaiduGps extends CRedis {
	
	const RouteMatrix_Mode_Driving = 'driving';
	const RouteMatrix_Mode_Walting = 'walking';
	
	public $host='cache01n.edaijia.cn';
	public $port=6379;
	public $password='k74FkBwb7252FsbNk2M7';
	//private $baidu_key='4b77795a1208a836494492b2ce573f7f';
	
	/**
	 * 百度KEY，用时间戳最后一位数字取对应的key访问百度
	 * @var unknown
	 */
	private $key=array(
			'ECfffb5d16a4f1b23c885c0527e91774',
			'y3oiH1aOng1q9GatQV9rgqw1',
			'9It1PFXhGixjpkc6okWLO2rU',
			'tYsonVwI79179q0zW89ZqvRp',
			'CDeQbjClBwgoQA1cyLanjBcL',
			'OOnzFxhgbjZTCGxgQuG96fUp',
			'vni0AoOvCMp8aChp9ICUyQIT',
			'zRvFySVryfIsbU6H7Zik4wGX'
	);
	private $poiType=array(
			'商务大楼',
			'办公大厦',
			'综合商场',
			'购物中心',
			'地产小区',
			'加油站'
	);
	const GPS_TYPE_GOOGLE='google';
	const GPS_TYPE_WGS84='wgs84';

	public static function model($className=__CLASS__) {
		$model=null;
		if (isset(self::$_models[$className]))
			$model=self::$_models[$className];
		else {
			$model=self::$_models[$className]=new $className(null);
		}
		return $model;
	}

	private function getKey() {
		$timestamp=time();
		$key_index=substr($timestamp, -1);
		return $this->key[$key_index];
	}

	/**
	 * 把坐标转换成3个坐标系
	 * @param array $gps
	 * @param string $type
	 * @return array
	 */
	public function convert($gps, $type='wgs84', $onlyStreet=false) {
		$lng=number_format(doubleval($gps['longitude']), 6);
		$lat=number_format(doubleval($gps['latitude']), 6);
		
		switch ($type) {
			case 'google' :
				$gps['longitude']=$lng;
				$gps['latitude']=$lat;
				
				$gps['google_lng']=$lng;
				$gps['google_lat']=$lat;
				
				$baidu=self::Google2Baidu($lng, $lat);
				if ($baidu) {
					$gps['baidu_lng']=$baidu['longitude'];
					$gps['baidu_lat']=$baidu['latitude'];
				}
				break;
			case 'baidu':  //增加baidu类型坐标处理 BY AndyCong 2013-12-11
				$gps['longitude']=$lng;
				$gps['latitude']=$lat;
				
				//百度坐标不能转成google坐标  所以讲百度坐标赋给google坐标
				$gps['google_lng']=$lng;
				$gps['google_lat']=$lat;
				
				//百度自己坐标不处理
				$gps['baidu_lng']=$lng;
			    $gps['baidu_lat']=$lat;
				break;
			case 'wgs84' :
			default :
				$gps['longitude']=$lng;
				$gps['latitude']=$lat;
				
				$google=self::Wgs2Google($lng, $lat);
				if ($google) {
					$gps['google_lng']=$google['longitude'];
					$gps['google_lat']=$google['latitude'];
				}
				
				$baidu=self::Wgs2Baidu($lng, $lat);
				if ($baidu) {
					$gps['baidu_lng']=$baidu['longitude'];
					$gps['baidu_lat']=$baidu['latitude'];
				}
				break;
		}	
		
		$gps['street']="";
		// GPS反查地址
		if (isset($gps['baidu_lng'])) {
			$address=self::getStreetByBaiduGPS($gps['baidu_lng'], $gps['baidu_lat'], $onlyStreet);
			$gps['street']=$address;
		}
		
		return $gps;
	}

	public function getCityByBaiduGPS($longitude, $latitude) {
		$longitude=number_format(doubleval($longitude), 6);
		$latitude=number_format(doubleval($latitude), 6);
		
		$gps=$latitude.','.$longitude;
		$url='http://api.map.baidu.com/geocoder/v2/?location='.$gps.'&output=json&pois=1&ak='.$this->getKey();
		
		$result=self::fetch($url);
		if ($result) {
			$location=json_decode($result, true);
			//增加验证，add by sunhongjing 2013-08-10
			$city=empty($location['result']['addressComponent']['city']) ? '' : $location['result']['addressComponent']['city'];
			$cityName=rtrim($city, '市');
			
			return $cityName;
		}
		return null;
	}

	/**
	 * 根据百度坐标取得对应的地址
	 * 
	 * @param unknown_type $baidu_lng
	 * @param unknown_type $baidu_lat
	 * @param unknown_type $onlyStreet     默认为false，返回全地址，如果为true，只返回街道
	 * 
	 * @return string
	 */
	public function getStreetByBaiduGPS($baidu_lng, $baidu_lat, $onlyStreet=false) {
		$longitude=number_format(doubleval($baidu_lng), 6);
		$latitude=number_format(doubleval($baidu_lat), 6);
		
		$address='';
		$poi = array();
		
		//查询百度地图返回地址
		$gps=$latitude.','.$longitude;
		$url='http://api.map.baidu.com/geocoder/v2/?location='.$gps.'&output=json&pois=1&ak='.$this->getKey();
		$result=self::fetch($url);
		
		if ($result) {
			$location=json_decode($result, true)['result'];
			//print_r($location);
			//add by sunhongjing 2013-10-08 增加变量验证
			$addressArray=isset($location['addressComponent']) ? $location['addressComponent'] : false;
			if (!$addressArray) {
				return null;
			}
			
			if ($location['pois']) {
				$poi = $this->getPoi($location);
				//print_r($poi);
			}
			
			if (!isset($addressArray['province'])) {
				$addressArray['province']='';
			}
			
			if ($onlyStreet) {
				//只返回街道
				if (isset($addressArray['street'])) {
					$address=$addressArray['street'].$addressArray['street_number'];
				} else {
					if ($addressArray['province']==$addressArray['city']) {
						$address=$addressArray['city'].$addressArray['district'];
					} else {
						$address=$addressArray['province'].$addressArray['city'].$addressArray['district'];
					}
				}
			} else {
				//全部返回
				if ($addressArray['province']==$addressArray['city']) {
					$address=$addressArray['city'].$addressArray['district'];
				} else {
					$address=$addressArray['province'].$addressArray['city'].$addressArray['district'];
				}
				if (isset($addressArray['street'])) {
					$address=$address.$addressArray['street'].$addressArray['street_number'];
				}
			}
			
			if ($poi) {
				$poi_address=($poi['address']) ? $poi['address'] : $address;
				return $poi_address.$poi['name'];
			}
			return $address;
		}
		return null;
	}

	public function Wgs2Google($longitude, $latitude) {
		$longitude=number_format(doubleval($longitude), 6);
		$latitude=number_format(doubleval($latitude), 6);
		
		$url='http://api.map.baidu.com/ag/coord/convert?from=0&to=2&x='.$longitude.'&y='.$latitude;
		
		$result=self::fetch($url);
		if ($result) {
			$gps=json_decode($result, true);
			if (isset($gps['x'])) {
				$longitude=number_format(doubleval(base64_decode($gps['x'])), 6);
				$latitude=number_format(doubleval(base64_decode($gps['y'])), 6);
			}
			
			if ($longitude&&$latitude) {
				$position=array(
						'longitude'=>$longitude,
						'latitude'=>$latitude
				);
				return $position;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	public function Google2Baidu($longitude, $latitude) {
		$longitude=number_format(doubleval($longitude), 6);
		$latitude=number_format(doubleval($latitude), 6);
		
		$url='http://api.map.baidu.com/ag/coord/convert?from=2&to=4&x='.$longitude.'&y='.$latitude;
		
		$result=self::fetch($url);
		if ($result) {
			$gps=json_decode($result, true);
			
			if (isset($gps['x'])) {
				$longitude=number_format(doubleval(base64_decode($gps['x'])), 6);
				$latitude=number_format(doubleval(base64_decode($gps['y'])), 6);
			}
			
			if ($longitude&&$latitude) {
				$position=array(
						'longitude'=>$longitude,
						'latitude'=>$latitude
				);
				return $position;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	public function Wgs2Baidu($longitude, $latitude) {
		$longitude=number_format(doubleval($longitude), 6);
		$latitude=number_format(doubleval($latitude), 6);
		
		$url='http://api.map.baidu.com/ag/coord/convert?from=0&to=4&x='.$longitude.'&y='.$latitude;
		
		$result=self::fetch($url);
		if ($result) {
			$gps=json_decode($result, true);
			if (isset($gps['x'])) {
				$longitude=base64_decode($gps['x']);
				$longitude=number_format(doubleval($longitude), 6);
				$latitude=base64_decode($gps['y']);
				$latitude=number_format(doubleval($latitude), 6);
			}
			
			if ($longitude&&$latitude) {
				$position=array(
						'longitude'=>$longitude,
						'latitude'=>$latitude
				);
				return $position;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
	
	/**
	 * 计算起始点的车行距离和大约时间
	 * @param array $start
	 * @param array $end
	 */
	public function RouteMatrix($start_lng,$start_lat,$end_lng, $end_lat,$mode=self::RouteMatrix_Mode_Driving){
		$start_lat=number_format(doubleval($start_lat), 6);
		$start_lng=number_format(doubleval($start_lng), 6);
		$end_lat=number_format(doubleval($end_lat), 6);
		$end_lng=number_format(doubleval($end_lng), 6);
		
		$url = 'http://api.map.baidu.com/direction/v1/routematrix';
		$params=array(
				'origins'=>$start_lng.','.$start_lat,
				'destinations'=>$end_lng.','.$end_lat,
				'mode'=>$mode,//导航模式，包括：driving（驾车）、walking（步行）
				'output'=>'json',
				'coord_type'=>'bd09ll',//坐标类型，可选参数，默认为bd09ll。允许的值为：bd09ll（百度经纬度坐标）、bd09mc（百度摩卡托坐标）、gcj02（国测局加密坐标）、wgs84（gps设备获取的坐标）。
				'tactics'=>11,//导航策略。导航路线类型，10，不走高速；11、最少时间；12、最短路径。
				'ak'=>self::getKey()
		);
		
		$url =  $url . '?' . http_build_query($params);
		$result = self::fetch($url);
		
		return json_decode($result,true);
	}

	/**
	 * 地址转换为GPS坐标
	 * @param string $address
	 * @return mixed|NULL
	 */
	public function geocoding($address) {
		$url='http://api.map.baidu.com/geocoder/v2/?address='.$address.'&output=json&ak='.$this->baidu_key;
		
		$result=self::fetch($url);
		if ($result) {
			$location=json_decode($result, true);
			if ($location['status']==0) {
				return $location['result'];
			}
		}
		return null;
	}

	/**
	 * 清理全部GPS缓存
	 */
	public function cacheClean() {
		$cache_keys=array(
				'GPS_FETCH_'
		);
		foreach($cache_keys as $item) {
			for($i=0; $i<=16; $i++) {
				$cache_key=$item.dechex($i).'*';
				$keys=BaiduGps::model()->redis->keys($cache_key);
				foreach($keys as $key) {
					BaiduGps::model()->redis->del($key);
					echo $key."\n";
				}
			}
		}
	}

	/**
	 * 获取解析结果，重复5次没有结果则放弃
	 * @param string $url
	 * @param number $second
	 * @return mixed
	 */
	private function fetch($url, $second=60) {
		$cache_key='GPS_FETCH_'.md5(preg_replace('%&ak=.*%', '', $url));
		//过期时间0.5小时加随机10分钟。防止集中过期，造成压力，这个地方接下来需要改到mongodb里，永久存储。add by sunhongjing 2013-09-11
		$expire_time=86400;
		$hit_expire_time=rand(1800, 7200);
		
		if ($this->redis->exists($cache_key)) {
			$result=json_decode($this->redis->get($cache_key), true);
			if ($result) {
				//命中缓存则增加有效时间
				$this->redis->expire($cache_key, $hit_expire_time);
				return $result;
			}
		}
		
		$i=0;
		$result='';
		while($i<3) {
			$ch=curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, $second);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 0);
			$result=curl_exec($ch);
			curl_close($ch);
			
			if ($result) {
				$this->redis->set($cache_key, json_encode($result));
				$this->redis->expire($cache_key, $expire_time);
				break;
			}
			$i++;
		}
		
		return $result;
	}

	/**
     * 设置历史访问地址缓存
     * @author zhanglimin
     * @param $key
     * @param array $data
     * @return mixed
     */
	public function addressHistory($key, $data=array()) {
		$cache_key='ADDRESS_HISTORY_'.$key;
		if ($this->redis->exists($cache_key)&&!empty($data)) {
			$this->redis->set($cache_key, json_encode($data));
		}
		$result=json_decode($this->redis->get($cache_key), true);
		return $result;
	}
	
	
	/**
	 * 根据坐标取得对应的周边POI+我们自己地址库里的POI
	 * 
	 * @param string $lng
	 * @param string $lat
	 * @param string $gps_type
	 * 
	 * @return string
	 */
	public function getNearbyPoi($lng, $lat, $gps_type='baidu') {
				
		$lng = number_format(doubleval($lng), 6);
		$lat = number_format(doubleval($lat), 6);
		
		switch ($gps_type) {
			case 'google' :
				
				$baidu=self::Google2Baidu($lng, $lat);
				if ($baidu) {
					$lng = $baidu['longitude'];
					$lat = $baidu['latitude'];
				}
				break;
			case 'wgs84' :
				$baidu=self::Wgs2Baidu($lng, $lat);
				if ($baidu) {
					$lng = $baidu['longitude'];
					$lat = $baidu['latitude'];
				}
			default : break;
		}
			
		$address='';
		$poi = array();
		
		//查询百度地图返回地址
		$gps = $lat.','.$lng;
		$url='http://api.map.baidu.com/geocoder/v2/?location='.$gps.'&output=json&pois=1&ak='.$this->getKey();
		$result=self::fetch($url);
		
		if ($result) {
			$location=json_decode($result, true)['result'];
			//print_r($location);
			//add by sunhongjing 2013-10-08 增加变量验证
			$addressArray=isset($location['addressComponent']) ? $location['addressComponent'] : false;
			if (!$addressArray) {
				return null;
			}
			
			print_r($location['pois']);
			
			if ($location['pois']) {
				$poi = $this->getPoi($location);
				//print_r($poi);
			}
			
		}
		return $poi;
	}
	
	
	

	/**
     * 更新缓存中历史访问地址数据
     * @return string
     */
	public function addressHistoryReload() {
		$cache_key='ADDRESS_HISTORY_*';
		$lists=$this->redis->keys($cache_key);
		if (!empty($lists)) {
			foreach($lists as $key) {
				if ($this->redis->exists($key)) {
					$result=json_decode($this->redis->get($key), true);
					if (!empty($result)) {
						$ret=AddressCallHistory::model()->findHistory($result['lng'], $result['lat']);
						if (!empty($ret)) {
							//往缓存写数据
							$data=array(
									'lng'=>$result['lng'],
									'lat'=>$result['lat'],
									'list'=>$ret
							);
							$key=substr($key, strlen($cache_key)-1);
							GPS::model()->addressHistory($key, $data);
						}
					}
				}
			}
		}
		return 'ok';
	}
	
	private function getPoi($location){
		$pois = array();
		$poi_name = $poi_address = '';
		foreach($location['pois'] as $item) {
			foreach($this->poiType as $type) {
				$address=str_replace($location['addressComponent']['city'], '', $item['addr']);
				$address=str_replace($location['addressComponent']['district'], '', $address);
				$address=str_replace($location['addressComponent']['province'], '', $address);
				if (strstr($item['poiType'], $type)) {
					$pois[]=array(
							'addr'=>$address,
							'name'=>$item['name'],
							'distance'=>$item['distance']
					);
					break;
				}
			}
		}
		if ($pois) {
			$pois=self::arraySortByKey($pois, 'distance');
			if ($pois) {
				$poi_name=$pois[0]['name'];
				//去除地址里的杂乱字符
				$address = $pois[0]['addr'];
				$address = preg_replace('%\(.*\)%', '', $address);
				$address = preg_replace('%（.*）%', '', $address);
				$address = preg_replace('%（.*%', '', $address);
				$address = preg_replace('%,.*%', '', $address);
				
				$poi_address=$address;
			}
		}
		
		return array('name'=>trim($poi_name),'address'=>trim($poi_address));
	}

	private function arraySortByKey(array $array, $key) {
		$asc=true;
		$result=array();
		// 整理出准备排序的数组
		foreach($array as $k=>&$v) {
			$values[$k]=isset($v[$key]) ? $v[$key] : '';
		}
		unset($v);
		// 对需要排序键值进行排序
		$asc ? asort($values) : arsort($values);
		// 重新排列原有数组
		$i=0;
		foreach($values as $k=>$v) {
			$i++;
			$result[]=$array[$k];
		}
		
		return $result;
	}
}
