<?php
/**
 * 客户端API：c.nearby 获取周边最近的空闲司机列表
 * 调用url:
 * @param string $token
 * @param string $udid
 * @param string $gps_type {gps,google,baidu}
 * @param int $idel_count
 * @param string $from
 * 
 * @author sunhongjing 2013-10-11
 * 
 * @return json
 * 
 * @example 
 */

//验证用户token
$udid=$params['udid'];
$gps_type = isset($params['gps_type']) ? trim(strtolower($params['gps_type'])) : 'wgs84';
$idel_count = isset($params['idel_count']) ? $params['idel_count'] : 5;
//$only_idel = isset($params['only_idel']) ? $params['only_idel'] : false;
$from = isset($params['from']) ? $params['from'] : '';
$idel_max_distance=5000;
$idel_count = 10;
$lng=sprintf('%.6f', $params['longitude']);
$lat=sprintf('%.6f', $params['latitude']);

$longitude=$lng;
$latitude=$lat;

//需要将位置偏移坐标与客户坐标匹配，不在一个城市的返回空
$customer_lng = isset($params['customer_lng']) ? $params['customer_lng'] : '';
$customer_lat = isset($params['customer_lat']) ? $params['customer_lat'] : '';
//需要将位置偏移坐标与客户坐标匹配，不在一个城市的返回空 

//开通城市
$open_city = RCityList::model()->getOpenCityList();

$idel_driver=null;
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

//记录访问日志，走队列,开始
	//添加task队列
	$task=array(
			'method'=>'customer_access_heatmap',
			'params'=>array(
					'lng'=>$longitude,
					'lat'=>$latitude,
					'gps_type'=>'baidu',
					'channel'=>$from,
					'udid'=>$udid,
					'req_time'=>date("Y-m-d H:i:s"),	
					'created'=>date("Y-m-d H:i:s"),	
				),
	);
	//Queue::model()->putin($task,'heatmap');
//记录访问日志，走队列,结束

//多取5个司机 如果空闲司机中有为我服务的司机 在空闲司机中去除
$time = time();
$max_count = $idel_count+5;
$idel_driver=DriverGPS::model()->nearby($longitude, $latitude, 0, $max_count, $idel_max_distance);

if ($idel_driver) {
	$drivers = $idel_driver;
} else {
	$drivers=null;
}

//获取为我服务的司机
$token = isset($params['token']) ? $params['token'] : '';
$service_drivers = array();

$validate = CustomerToken::model()->validateToken($token);
if ($validate) {
	$service_drivers = CustomerApiOrder::model()->getServiceDrivers($validate['phone']);
}

if ( empty($drivers) && empty($service_drivers)) {
	//验证城市是否开通
	$location_nearby = array(
		'longitude' => $lng,
	    'latitude' => $lat,
	);
	$gps_nearby = GPS::model()->convert($location_nearby , $gps_type);
	$city_nearby = GPS::model()->getCityByBaiduGPS($gps_nearby['baidu_lng'] , $gps_nearby['baidu_lat']);
	
	//验证城市是否开通
	$city_id = Dict::code('city', $city_nearby);
	if (empty($city_id) || !array_key_exists($city_id , $open_city)) {
		$ret = array('code'=>4 , 'data' => '' , 'message'=>'您所在城市还未开通e代驾服务!');
		echo json_encode($ret);return;
	}
	
	$json=json_encode(array(
			'code'=>2,
			'driverList'=>array(),
			'message'=>'您的周围暂无空闲司机'
	));
	
} else {
    $drivers_all=array();
    //为我服务的司机
    $service_arr = array(); //为我服务的司机工号数组
    if (!empty($service_drivers)) {
    	foreach ($service_drivers as $service_driver) {
    		//司机工号大于9800 不显示
    		if(substr($service_driver['driver_id'],2)  >= 9800 || $service_driver['driver_id'] == Push::DEFAULT_DRIVER_INFO){
		        continue;
		    }
		    
		    $tmp_service = Helper::foramt_driver_detail($service_driver['driver_id'], $gps_type, 0 ,'mini');
		    if(!empty($tmp_service)){
		    	$tmp_service['booking_id'] = $service_driver['booking_id']; //服务中的将booking_id直接返回
		        $tmp_service['state']      = 1;                             //为我服务的状态都为服务中
		        $tmp_service['new_distance'] = $tmp_service['distance'];
		    	$drivers_all[]= $tmp_service;
		    	
		    	$service_arr[] = $service_driver['driver_id'];
		    }  
    	}
    }
    //空闲司机
    $i = 0;
    $points = array();
    $tmp_free = array();
    if (!empty($drivers)) {
    	foreach($drivers as $driver) {
    		//如果空闲司机数量够了 则退出循环
    		if ($i >= $idel_count) {
    			break;
    		}
    		
			//司机工号大于9800 不显示
		    if(substr($driver['driver_id'],2)  >= 9800){
		        continue;
		    }
		    $status = isset($driver['status']) ?  $driver['status'] : false;
		    $tmp = Helper::foramt_driver_detail($driver['driver_id'], $gps_type, $driver['distance'],'mini',$status);
		    if(!empty($tmp) && !in_array($driver['driver_id'] , $service_arr)){
		    	//获取司机的距离 BY AndyCong 2013-12-03
		    	$driver_distance = Helper::Distance($latitude , $longitude , $tmp['latitude'] , $tmp['longitude']);
		    	if ($driver_distance >= $idel_max_distance) {
		    		continue;
		    	}
		    	//获取司机的距离 BY AndyCong 2013-12-03 END
		    	
		    	$tmp['booking_id'] = '';   //将booking_id返回设置为空，保持数据格式一样
		    	$tmp['state']      = 0;    //状态强转空闲---nearby中取出的是空闲 格式化状态变成服务，但并不是为我服务的司机
//		    	$drivers_all[]= $tmp;
		    	$tmp_free[] = $tmp;
		    	//组织坐标
		    	$points[] = array($tmp['longitude'] , $tmp['latitude']);
		    	//组织坐标 END 
		    	
		    	$i++;
		    }  
		    
		}
    }
    
    /**
     * 获取司机到中心点距离并根据此距离排序
     */
    $distance_arr = BaiduMap::getRouteDistanceMulti($lng , $lat , $points);
    $free_drivers = array();
    foreach ($tmp_free as $key=>$free_driver) {
    	$free_driver['new_distance'] = $distance_arr[$key];
    	$free_drivers[$distance_arr[$key]] = $free_driver;
    }
    unset($tmp_free);
    ksort($free_drivers);
    foreach ($free_drivers as $driver_n) {
    	$driver_n['new_distance'] = format_distince($driver_n['new_distance']);
    	$drivers_all[] = $driver_n;
    }
    /**
     * 获取司机到中心点距离并根据此距离排序 END
     */
    
	$json=json_encode(array(
			'code'=>0,
			'driverList'=>$drivers_all,
			'message'=>'',
			'server' => Common::getMyHostName(),
	));
}
echo $json;return ;

/**
 * 获取坐标
 * @param float $lng
 * @param float $lat
 * @return array $tmp
 */
function curl_xy($lng , $lat) {
	$url = "http://api.map.baidu.com/ag/coord/convert?from=2&to=5&mode=1&x=".$lng."&y=".$lat."&qq-pf-to=pcqq.c2c";
	$data = Common::get_contents($url);
	$xy_arr = json_decode($data , true);
	$tmp = array();
	foreach ($xy_arr as $key=>$value) {
		$tmp[] = array('x' => base64_decode($value['x']) , 'y' => base64_decode($value['y']));
	}
	return $tmp;
}

/**
 * 获取距离
 * @param string $content
 * @return int 
 */
function getDis($content)
{
	$pattern = '#"dis":(\d+),#';
	if (preg_match($pattern, $content, $matches)) {
		return $matches[1];
	}
	return 0;
	
}

/**
 * Enter description here...
 * @param unknown_type $query_arr
 * @return unknown
 */
function curl_multi ($query_arr) { 
	$ch = curl_multi_init(); 
	$count = count($query_arr); 
	$ch_arr = array(); 
	for ($i = 0; $i < $count; $i++) { 
		$query_string = $query_arr[$i]; 
		$ch_arr[$i] = curl_init($query_string); 
		curl_setopt($ch_arr[$i], CURLOPT_RETURNTRANSFER, true); 
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

/**
 * 距离格式化
 * @param int $distance
 * @return string
 */
function format_distince($distance) {
	$distance=intval($distance);
	
	if ($distance<=100) {
		$distance='100米内';
	} elseif ($distance>100&&$distance<=200) {
		$distance='200米内';
	} elseif ($distance>200&&$distance<=300) {
		$distance='300米内';
	} elseif ($distance>300&&$distance<=400) {
		$distance='400米内';
	} elseif ($distance>400&&$distance<=500) {
		$distance='500米内';
	} elseif ($distance>500&&$distance<=600) {
		$distance='600米内';
	} elseif ($distance>600&&$distance<=700) {
		$distance='700米内';
	} elseif ($distance>700&&$distance<=800) {
		$distance='800米内';
	} elseif ($distance>800&&$distance<=900) {
		$distance='900米内';
	} elseif ($distance>900&&$distance<=1000) {
		$distance='1公里';
	} else {
		$distance=number_format(intval($distance)/1000, 1).'公里';
	}
	
	return $distance;
}
