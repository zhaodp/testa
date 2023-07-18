<?php
/**
 * 调用百度的地图接口转换GPS坐标及地址
 * @author dayuer
 *
 */
class GPS extends CRedis {
	public $host='redis04.edaijia-inc.cn';
	public $port=6379;
	public $password='k74FkBwb7252FsbNk2M7';
	private $baidu_key='4b77795a1208a836494492b2ce573f7f';
	const GPS_TYPE_GOOGLE='google';
	const GPS_TYPE_WGS84='wgs84';
	
	
	
	/**
	 * 百度KEY，用时间戳最后一位数字取对应的key访问百度
	 * @var unknown
	 */
	private $key_pool =array(
                        /*
			'E2592722902c788eb7da1a013dd828f9',
			'4b77795a1208a836494492b2ce573f7f',
			'84c17c2ce199e249cc3de5d65a0b54bf',
			'ECb009bbd447071d2f7ff695df2173b8',
			'B3d7318d14eae1bad59734235a642ec4',
			'DAfa552b13d0c7958b62003f5efca618',
			'3131fe0fbfc7f270441d4eeb6aa0f3c5',
			'7f76e109d91d3c19501ed9c971b61965',
			'6d7ae0302a790ecccd92a44a3e267c03',
			'330d92ded0bde7c5c75455cd826ecc90'
                        */
			'ECfffb5d16a4f1b23c885c0527e91774',
			'y3oiH1aOng1q9GatQV9rgqw1',
			'9It1PFXhGixjpkc6okWLO2rU',
			'tYsonVwI79179q0zW89ZqvRp',
			'CDeQbjClBwgoQA1cyLanjBcL',
			'OOnzFxhgbjZTCGxgQuG96fUp',
			'vni0AoOvCMp8aChp9ICUyQIT',
			'zRvFySVryfIsbU6H7Zik4wGX'
	);

	public static function model($className=__CLASS__) {
		$model=null;
		if (isset(self::$_models[$className]))
			$model=self::$_models[$className];
		else {
			$model=self::$_models[$className]=new $className(null);
		}
		return $model;
	}
	
	/**
	 * 得到一个随机的key
	 * 
	 * @author sunhongjing
	 */
	public function getOneKey()
	{
		$k = array_rand($this->key_pool,1);
		return $this->key_pool[$k];
	}
	

	/**
	 * 把坐标转换成3个坐标系
	 * @param array $gps
	 * @param string $type
	 * @return array
	 */
	//public function convert($gps, $type='wgs84',$onlyStreet=false) {
	public function convert_only($gps, $type) {
		$type = strtolower($type); //字符强制转化成小写 BY AndyCong 2013-12-11
		
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

        return $gps;

    }


	public function convert($gps, $type='wgs84',$level= 1) {
        $gps = $this->convert_only($gps, $type);
		$gps['street']="";
		// GPS反查地址
		if(isset($gps['baidu_lng'])){
			$address=self::getStreetByBaiduGPS($gps['baidu_lng'], $gps['baidu_lat'],$level);
			$gps['street']= isset($address) ? $address : '';
		}
		
		
		return $gps;
	}

    public  function getCityByIP($ip) {


        $baidu_key = self::getOneKey();
        $url='http://api.map.baidu.com/location/ip?ak='.$baidu_key.'&ip='.$ip.'&coor=bd09ll';

        $result=self::fetch($url);
        if ($result) {
            try{
                $info = json_decode($result);
                if(!isset( $info->content)){
                    return FALSE;
                }
                $content = $info->content;
                if(!isset( $content->address_detail)){
                    return FALSE;
                }
                $detail = $content->address_detail;
                if(empty($detail->city)){
                    return FALSE;
                }
                $city = $detail->city;
                return $city;
            }
            catch(Exception $e)
            {
                return FALSE;
            }
        }
        return FALSE;
    }

	public function getCityByBaiduGPS($longitude, $latitude) {
		$longitude=number_format(doubleval($longitude), 6);
		$latitude=number_format(doubleval($latitude), 6);
		
		$gps=$latitude.','.$longitude;
		
		$baidu_key = self::getOneKey();
		
		//$url='http://api.map.baidu.com/geocoder?output=json&location='.$gps.'&key=e84e1c0102539d473db235592f108beaz';
		//$url='http://api.map.baidu.com/geocoder?output=json&location='.$gps.'&key='.$baidu_key;
		$url='http://api.map.baidu.com/geocoder/v2/?location='.$gps.'&output=json&pois=0&ak='.$baidu_key;
		
		$result=self::fetch($url);
		if ($result) {
			$location=json_decode($result, true);
            try{
                //针对特殊县市 做指定转换 （义乌市县级市，指定为独立城市。三河市划分到北京里）
                $district = (isset($location['result']['addressComponent']['district']) && !empty( $location['result']['addressComponent']['district'] )) ? $location['result']['addressComponent']['district'] : '';
                $special_list = Yii::app()->params['townToCity'];
                if(isset($special_list[$district])){
                    EdjLog::info('lat:'.$latitude.'-- lng:'.$longitude.'-- old_name:'.$location['result']['addressComponent']['district'].'-- new name'.$special_list[$district]."\n");
                    return $special_list[$district];
                }
            }
            catch(Exception $e)
            {
                EdjLog::info($e->getMessage());
                //throw new CHttpException($e->getMessage());
            }

			//增加验证，add by sunhongjing 2013-08-10
			$city = empty( $location['result']['addressComponent']['city'] ) ? '' : $location['result']['addressComponent']['city'];
			//$cityName=rtrim($city, '市');
			$cityName = preg_replace('/市$/', '', $city);
			if($cityName == '襄樊') $cityName = '襄阳';
			return $cityName;
		}
		return null;
	}

	/**
	 * 根据百度坐标取得对应的地址
	 * 
	 * @param unknown_type $baidu_lng
	 * @param unknown_type $baidu_lat
	 * @param unknown_type $level     默认为2，返回全地址字符串，1：只返回街道 3：返回全信息数组
	 * 
	 * @return string
	 */
	public function getStreetByBaiduGPS($baidu_lng, $baidu_lat,$level = 2) {
		$address='';
		//查询百度地图返回地址
		$gps=$baidu_lat.','.$baidu_lng;
		
		$baidu_key = self::getOneKey();
		//$url='http://api.map.baidu.com/geocoder?output=json&location='.$gps.'&key=e84e1c0102539d473db235592f108bea';
		//$url='http://api.map.baidu.com/geocoder?output=json&location='.$gps.'&key='.$baidu_key;
		//升级v2版本
		$pois = '0';
		if($level == 4) {
                    $pois = '1';
		}
		$url='http://api.map.baidu.com/geocoder/v2/?location='.$gps.'&output=json&pois='.$pois.'&ak='.$baidu_key;
		
		$result=self::fetch($url);
		if ($result) {
			$location=json_decode($result, true);
			//add by sunhongjing 2013-10-08 增加变量验证
			$addressArray= isset($location['result']['addressComponent']) ? $location['result']['addressComponent'] : false;
			if( !$addressArray ){
				return null;
			}
			if( !isset($addressArray['province']) ){
				$addressArray['province'] = '';
			}
            if($addressArray['city'] == '襄樊市') $addressArray['city'] = '襄阳市';
            $address_tmp = array();
            switch($level ){
                case 1://只返回街道
                    if ( !empty($addressArray['street']) ) {
                        $address = $addressArray['street'];
                    }else{
                        if ($addressArray['province']==$addressArray['city']) {
                            $address=$addressArray['city'].$addressArray['district'];
                        } else {
                            $address=$addressArray['province'].$addressArray['city'].$addressArray['district'];
                        }
                    }
                    break;
                case 2:
                    //全部返回
                    if ($addressArray['province']==$addressArray['city']) {
                        $address=$addressArray['city'].$addressArray['district'];
                    } else {
                        $address=$addressArray['province'].$addressArray['city'].$addressArray['district'];
                    }
                    if (!empty($addressArray['street'])) {
                        $address=$address.$addressArray['street'];
                    }
                    break;
                case 3: //返回带city_id 的数组，全部信息
                    if (!empty($addressArray['district'])) {
                        $address_tmp = $addressArray['district'];
                    }
                    if (!empty($addressArray['street'])) {
                        $address_tmp = $address_tmp.$addressArray['street'];
                    }
                    if (!empty($addressArray['street_number'])) {
                        $address_tmp = $address_tmp.$addressArray['street_number'];
                    }

                    try {
                        //针对特殊县市 做指定转换 （义乌市县级市，指定为独立城市。三河市划分到北京里）
                        $district = (isset($addressArray['district']) && !empty( $addressArray['district'] )) ? $addressArray['district'] : '';
                        $special_list = Yii::app()->params['townToCity'];
                        if(isset($special_list[$district])){
                            EdjLog::info('street lat:'.$baidu_lat.'-- lng:'.$baidu_lng.'-- old_name:'.$addressArray['district'].'-- new name'.$special_list[$district]."\n");
                            $city_name = $special_list[$district];
                            $addressArray['city_id'] = CityConfig::getIdByName($city_name);
                        } else {
                            $addressArray['city_id'] = CityConfig::getIdByName( preg_replace('%市$%', '', $addressArray['city']) );
                        }
                    }
                    catch (Exception $e)
                    {
                        EdjLog::info($e->getMessage());
                    }

                    $address = array(
                        'name'=>$address_tmp,
                        'component'=>$addressArray);
                    break;

                case 4: //返回 区街道 posi信息
                    if(isset($addressArray['district'])) {
                        $address_tmp = $addressArray['district'];
                    }
                    else {
                        $address_tmp = $addressArray['city'];
                    }

                    if (isset($addressArray['street'])) {
                        $address_tmp = $address_tmp.$addressArray['street'];
                    }

                   try {
                        //针对特殊县市 做指定转换 （义乌市县级市，指定为独立城市。三河市划分到北京里）
                        $district = (isset($addressArray['district']) && !empty( $addressArray['district'] )) ? $addressArray['district'] : '';
                        $special_list = Yii::app()->params['townToCity'];
                        if(isset($special_list[$district])){
                            EdjLog::info('street lat:'.$baidu_lat.'-- lng:'.$baidu_lng.'-- old_name:'.$addressArray['district'].'-- new name'.$special_list[$district]."\n");
                            $city_name = $special_list[$district];
                            $addressArray['city_id'] = Dict::code('city', $city_name);
                        } else {
                            $addressArray['city_id'] = Dict::code('city', preg_replace('%市$%', '', $addressArray['city']));
                        }
                    }
                    catch (Exception $e)
                    {
                        EdjLog::info($e->getMessage());
                    }

                    //posi信息
                    if(!empty($location['result']['pois'])) {
                        $pois_info = $location['result']['pois'];
                        $i = 10; //最多比较10个
                        $pois_name = '';
                        $pois_dis  = -1;
                        foreach($pois_info as $item) {
                            if(isset($item['distance']) && isset($item['name'])) {
                                if($pois_dis == -1 || $item['distance'] < $pois_dis) {
                                    $pois_name = $item['name'];
                                    $pois_dis = $item['distance'];
                                }
                            }
                            $i--;
                            if($i == 0) {
                                break;
                            }
                        }
                        if(!empty($pois_name)) {
                            // 只需要poi的名称，不再需要返回街道地址——曾坤 2015/3/19
                            //$address_tmp = $pois_name.' '.$address_tmp;
                            $address_tmp = $pois_name;
                        }
                    }

                    $address = array(
                        'name'=>$address_tmp,
                        'component'=>$addressArray);

                    break;
            }

			return $address;
		}
		return null;
	}

	public function Wgs2Google($longitude, $latitude) {
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
     * 多点算距离和时间
     * @param array $start_points
     * @param array $end_points
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-02-27
     */
    public function RouteMatrixMulti($start_points = array() , $end_points = array() , $mode = self::RouteMatrix_Mode_Driving) {
        if (empty($start_points) || empty($end_points)) {
            return false;
        }

        //整理起始点坐标
        $origins = '';
        foreach ($start_points as $start_point) {
            $start_lat=number_format(doubleval($start_point['lat']), 6);
            $start_lng=number_format(doubleval($start_point['lng']), 6);
            $origins .= $start_lat.','.$start_lng.'|';
        }
        $origins = substr($origins , 0 , strlen($origins)-1);

        //整理终点坐标
        $destinations = '';
        foreach ($end_points as $end_point) {
            $end_lat=number_format(doubleval($end_point['lat']), 6);
            $end_lng=number_format(doubleval($end_point['lng']), 6);
            $destinations .= $end_lat.','.$end_lng.'|';
        }
        $destinations = substr($destinations , 0 , strlen($destinations)-1);

        $url = 'http://api.map.baidu.com/direction/v1/routematrix';
        $params=array(
            'origins'=>$origins,
            'destinations'=>$destinations,
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
		
		$baidu_key = self::getOneKey();
		$url='http://api.map.baidu.com/geocoder/v2/?address='.$address.'&output=json&ak='.$baidu_key;
	
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
				$keys=GPS::model()->redis->keys($cache_key);
				foreach($keys as $key) {
					GPS::model()->redis->del($key);
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
		//$cache_key = 'GPS_FETCH_'.md5($url);
		$cache_key='GPS_FETCH_'.md5(preg_replace('%&ak=.*%', '', $url));
		
		//过期时间0.5小时加随机10分钟。防止集中过期，造成压力，这个地方接下来需要改到mongodb里，永久存储。add by sunhongjing 2013-09-11
		$expire_time = 14400; 
		$hit_expire_time = rand(1800, 14400);
		
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
		while(true) {
            $start_time = Common::get_current_time();

			$ch=curl_init();
			curl_setopt($ch, CURLOPT_TIMEOUT, $second);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 0);
			$result=curl_exec($ch);
			curl_close($ch);


            list($usec, $sec) = explode(' ', microtime());
            $end_time = (float)$usec + (float)$sec; 
            $run_time = sprintf("%0.4f", ($end_time - $start_time)*1000);
            EdjLog::info("GPS api get tm: $run_time url: $url");

			
			if ($result) {
			    $tmp_for_check = @json_decode($result, true);
			    if(!isset($tmp_for_check['status'])) {
			        // 避免误伤返回参数中可能没有status的情况,
			        // 虽然现有baidu api中都包含status
				$this->redis->set($cache_key, json_encode($result));
				$this->redis->expire($cache_key, $expire_time);
			    }
			    else {
			        // 如果status不为0,证明返回结果出错,不做缓存
			        if($tmp_for_check['status'] == 0) {
				    $this->redis->set($cache_key, json_encode($result));
				    $this->redis->expire($cache_key, $expire_time);
				}
				else {
                                    EdjLog::info("Error GPS api url: $url status: ".$tmp_for_check['status']);
				}
			    }
			    break;
			}

                        if($i>3) {
                            EdjLog::info("Error GPS api url: $url no result after 3 times");
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
    public function addressHistory($key,$data = array()){
        $cache_key = 'ADDRESS_HISTORY_'.$key;
        if ($this->redis->exists($cache_key) && !empty($data)) {
           $this->redis->set($cache_key, json_encode($data));
        }
        $result=json_decode($this->redis->get($cache_key), true);
        return $result;
    }

    /**
     * 更新缓存中历史访问地址数据
     * @return string
     */
    public function addressHistoryReload(){
        $cache_key = 'ADDRESS_HISTORY_*';
        $lists = $this->redis->keys($cache_key);
        if(!empty($lists)){
            foreach($lists as $key){
                if ($this->redis->exists($key)) {
                    $result=json_decode($this->redis->get($key), true);
                    if(!empty($result) ){
                        $ret = AddressCallHistory::model()->findHistory($result['lng'],$result['lat']);
                        if(!empty($ret)){
                            //往缓存写数据
                            $data = array(
                                'lng' => $result['lng'],
                                'lat' => $result['lat'],
                                'list' => $ret,
                            );
                            $key = substr($key,strlen($cache_key)-1);
                            GPS::model()->addressHistory($key,$data);
                        }
                    }
                }
            }
        }
        return 'ok';
    }
}
