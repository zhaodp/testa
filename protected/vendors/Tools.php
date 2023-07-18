<?php

class Tools
{ 
    
    /**
     * 生成$uniq_id
     * 
     * @author sunhongjing  2013-12-30
	 * 
	 * @param unknown_type $type
	 * @param unknown_type $prefix
	 * @return string
	 */
    public static function getUniqId($type='nomal',$prefix='')
    {
    	$uniq_id = '';
    	
    	switch ($type) {
    		case 'high':
    		 	$uniq_id = md5(uniqid(rand(), true));
    			break;
    		case 'nomal':
    			list($usec, $sec) = explode(" ", microtime());
				$sub = substr($usec,2,6);
    		 	$uniq_id = $sec.$sub;
    			break;
    		default:
    			$uniq_id = time().rand(0,99);
    		break;
    	}
    	
        return $prefix ? $prefix.$uniq_id : $uniq_id;
    }
	
	
    /**
     * 返回订单的track信息，从又拍云获取
     * 
     * @author sunhongjing 2013-11-07
     * @param unknown_type $path
     */
     public static function getOrderTrack($order_id='',$order_no='',$booking_date=''){
     	
     	$ret = null; 	
     	if( empty($order_id) || empty($booking_date)){
     		return $ret;
     	}
     	
     	//如果订单号不为空，同时用order_id取不到数据，就用order_no再取一次。
     	//token:1qAzmkoi76T4Dsy0yQ
     		
     	$url = 'http://etrack.b0.upaiyun.com/';
     	$path = date("Ymd",strtotime($booking_date));
     	$name = trim($order_id).".json";  	
     	$name2 = trim($order_no).".json";
     	
     	$etime = time()+600; 			// 授权5分钟后过期
		$key = '1qAzmkoi76T4Dsy0yQ'; 	// token防盗链密钥
		$uri_path = '/'.$path.'/'.$name; 		// 图片相对路径
		
		$uri_path2 = '/'.$path.'/'.$name2;
		$sign = substr(md5($key.'&'.$etime.'&'.$uri_path), 12,8).$etime;
		
		$sign2 = substr(md5($key.'&'.$etime.'&'.$uri_path2), 12,8).$etime;
  
     	$track_url 		= $url.$path.'/'.$name.'?_upt='.$sign;
     	$track_url_2 	= $url.$path.'/'.$name2.'?_upt='.$sign2;	
			
		$order_track = self::__getOrderTrackingData($track_url);
		
     	if(empty($order_track)){
   			$order_track = self::__getOrderTrackingData($track_url_2);
     		if(empty($order_track)){
     			return $ret;
     		}
     	} 
     	$ret = @json_decode($order_track,true);
     	
		return $ret;
     }
     
     private static function __getOrderTrackingData($url)
     {
     	$ret = null;
     	$ch = curl_init();
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$ret = curl_exec($ch);
		curl_close($ch);
		
		if ( false !== strpos($ret,'404 Not Found') ){
			$ret = null;
		}
		
		return $ret;
     } 
     
     /**
      * 得到订单轨迹的
      * 
      */
     public static function getOrderTrackPoint($order_id='',$booking_date='')
     {
     	$ret = false;
     	
     	$tracklist = self::getOrderTrack($order_id='',$booking_date='');
     	
//     	    "etrack": [
//        {
//            "timestamp": 1385593005,
//            "gps_type": "google",
//            "status": 1,
//            "speed": 20.78,
//            "bearing": 154.10000610351562,
//            "provider": "gps",
//            "lng": "121.476364",
//            "lat": "31.231577"
//        },
     	
     	if(!empty($tracklist)){
     		
     		$addPoint = '';
			$linePoint = '';
			foreach($tracklist['etrack'] as $position) {
				$latitude = $position['lat'];
				$longitude = $position['lng'];
				$state = $position['status'];
				$datetime = $position['timestamp'];
				if ($latitude!=''&&$longitude!='') {
					$addPoint .= sprintf('marker = addDriver(%s, %s, "%s",%s);'."\n", $latitude, $longitude, $datetime, $state);
					$linePoint .= sprintf('new BMap.Point(%s, %s),'."\n", $longitude, $latitude);
				}
			}
			
			$ret['addPoint'] = $addPoint;
			
			$ret['linePoint'] = $linePoint;
     	}
     	
		return $ret;
     }
     
    /**
     * 对二维数组按照指定的键值进行排序，也可以指定升序或降序排序法（默认为降序）,增加经纬度，机场接送机新业务测试
     * @author zhanglimin 2013-06-20
     * @param $arr
     * @param $keys 排序键值
     * @param string $type
     * @param bool $dispaly
     * @return array
     */
    public static function driver_dispatch_sort($arr, $keys = 'weight', $type = 'desc', $lng=0,$lat=0,$dispaly = false)
    {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        foreach ($keysvalue as $k => $v) {
            if (!$dispaly) {
                unset($arr[$k][$keys]); //将$keys属性移除
            }
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }
    
	/**
	 * 得到测试工号并计算距离，机场接送机新业务测试号
	 * 
	 * @param unknown_type $lng
	 * @param unknown_type $lat
	 * 
	 * @return array
	 */
	public static function getTestDrivers($lng=0,$lat=0,$max=10000)
	{
		$ret = array();
		$drivers = array('BJ9006','BJ1371','BJ0638','BJ0311','BJ0322','BJ2182','BJ2167');
		
		if($lng>0 && $lat>0){
			foreach ($drivers as $driver_id) {
				$driver = DriverStatus::model()->get($driver_id);
				
				if(!empty($driver)){
					if( 0 == $driver->status ){
						//如果坐标有问题，也抛掉
						if( isset($driver->position['baidu_lng']) ){
							if( 10 < ( $driver->position['baidu_lng'] + $driver->position['baidu_lat'] ) ){
								$distance = Helper::Distance($lat, $lng, $driver->position['baidu_lat'], $driver->position['baidu_lng']);
								if($distance<$max){
									//有效的司机，需要对信息进行整理
									$ret[] = array(
												'id' => $driver->id,
												'driver_id' => $driver->driver_id,
												'status' => $driver->status,
												'distance' => $distance,
											);
								}
							}
						}
					}
				}
			}
		}
		
		return $ret;
		
	}

    /**
     *
     * 升级随机的字符串
     *
     * @param int $length
     * @return array
     */
    public static function randStringGenerator($length = 4)
    {
       $chars =  array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
                'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
                    'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
                        'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
                            'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2',
                                '3', '4', '5', '6', '7', '8', '9');
        $str = '';
        for ($i = 0; $i < $length; ++$i) {
            $str .= $chars[array_rand($chars)];
        }
        return $str;
    }
    
}