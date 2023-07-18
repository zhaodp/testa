<?php
//需要写清楚注释，增加缓存，封装业务逻辑，写库走队列 add by sunhongjing at 2013-5-19
/**
 * 获取周边最近的空闲司机列表
 * @param gps_type {gps,google,baidu}
 */

$denyAppKeys = array(
    '10000002', // Android 2.2.11 and by liutuanwang  2014-06-16
);

//对于一个appkey进行特殊处理
$isDenied =  in_array($params['appkey'], $denyAppKeys);

$udid=$params['udid'];
$gps_type=isset($params['gps_type']) ? $params['gps_type'] : 'wgs84';
$idel_count=isset($params['idel_count']) ? $params['idel_count'] : 5;

if($isDenied){
    $idel_count = 1; //特殊处理的app只返回一个空闲司机
}

$busy_count=isset($params['busy_count']) ? $params['busy_count'] : 3;
$from = isset($params['from']) ? strtolower($params['from']) : '';
$idel_max_distance=6000;
$busy_max_distance=15000;

$lng=sprintf('%.6f', $params['longitude']);
$lat=sprintf('%.6f', $params['latitude']);
$longitude=$lng;
$latitude=$lat;

//因为现在ios版本把wgs84坐标的gps_type传成了google,所以会偏，因为到服务端再给转对了 add by sunhongjing 2013-12-04
if( 'appstore' == $from || 'netdragon'==$from ){
	$gps_type = ('google'==$gps_type) ? 'wgs84' : $gps_type;
}

$idel_driver=$busy_driver=null;

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

//记录访问日志，走队列
	
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


$idel_driver=DriverGPS::model()->nearby($longitude, $latitude, 0, $idel_count, $idel_max_distance);

//特殊处理的app 不需要忙碌中的司机
if( !$isDenied ){
    $busy_driver=DriverGPS::model()->nearby($longitude, $latitude, 1, $busy_count, $busy_max_distance);
}


if ($idel_driver&&$busy_driver) {
	$drivers=array_merge($idel_driver, $busy_driver);
} elseif ($idel_driver) {
	$drivers=$idel_driver;
} elseif ($busy_driver) {
	$drivers=$busy_driver;
} else {
	$drivers=null;
}

if (!$drivers) {
	if (empty($idel_driver)&&empty($busy_driver)) {
		$json=json_encode(array(
				'code'=>2,
				'driverList'=>array(),
				'message'=>'您的周围暂无空闲司机'
		));
	} elseif (empty($idel_driver)) {
		$json=json_encode(array(
				'code'=>1,
				'driverList'=>array(),
				'message'=>'您的周围暂无空闲司机'
		));
	}
} else {
    if($from == "driverclient"){
        //司机客户端
        $drivers_all=array();
        foreach($drivers as $driver) {
        	//增加判断，对错误数据不返回,add by sunhongjing 2013-10-13
        	$tmp = detail($driver['driver_id'], $gps_type, $driver['distance']);
        	if(!empty($tmp)){
        		$drivers_all[]= $tmp;
        	}   
        }
    }else{

        $drivers_all=array();
        foreach($drivers as $driver) {
            //司机工号大于9800 不显示
            $is_backcar = Common::checkBlackCar($driver['driver_id']);
            if ($is_backcar) {
                continue;
            }
            $status = isset($driver['status']) ?  $driver['status'] : false;
            //增加判断，对错误数据不返回,add by sunhongjing 2013-10-13
            $tmp = detail($driver['driver_id'], $gps_type, $driver['distance'],$status);
        	if(!empty($tmp)){
        		$drivers_all[]= $tmp;
        	}  
        }
    }

	
// 	$key=md5(time());
// 	$result='';
// 	foreach($drivers_all as $item) {
// 		foreach($item as $k=>$v) {
// 			$result.=$v.' '.$v;
// 		}
// 	}
	
// 	Yii::app()->db->createCommand()->insert('t_tt', array(
// 			'driver_id'=>$key,
// 			'phone'=>$result
// 	));
	
// 	json_encode($drivers_all);
	
// 	Yii::app()->db->createCommand()->delete('t_tt', 'driver_id=:driver_id', array(
// 			':driver_id'=>$key
// 	));
	
	$json=json_encode(array(
			'code'=>0,
			'driverList'=>$drivers_all,
			'message'=>''
	));
}

echo $json;

/**
 * 格式化司机信息
 * @param string $driver_id
 */
function detail($driver_id, $gps_type, $distance,$d_status=false) {
	
	if(empty($driver_id)){
		return null;
	}
	
	if( -1 == $d_status ){
		return null;
	}
	
	$driver = DriverStatus::model()->get($driver_id);
	
	if(empty($driver)){
		return null;
	}
	
	//比对mongodb和redis里的状态是否对
	if( false !== $d_status){
		if( $d_status != $driver->status ){
			return null;
		}
	}
	
	//如果坐标有问题，也抛掉
	if( 10 > ( $driver->position['baidu_lng'] + $driver->position['baidu_lat'] ) ){
		return null;
	}
		
	if ($driver->info['level']==''||empty($driver->info['level'])) {
		$new_level=0;
	} else {
		$new_level=$driver->info['level'];
	}
	
	$id_card=isset($driver->info['id_card']) ? substr_replace($driver->info['id_card'], '******', 10, 6) : '';
	$car_card=isset($driver->info['car_card']) ? substr_replace($driver->info['car_card'], '******', 10, 6) : '';
	
	switch ($gps_type) {
		case 'google' :
			$longitude=$driver->position['google_lng'];
			$latitude=$driver->position['google_lat'];
			break;
		default :
			$longitude=$driver->position['baidu_lng'];
			$latitude=$driver->position['baidu_lat'];
			break;
	}
	
	//验证司机是否为皇冠 BY AndyCong 2013-08-09 
	$recommand = 0;
	$driver_recommand = $driver->recommand;
	if (!empty($driver_recommand)) {
		$begin_time = isset($driver_recommand['begin_time']) ? strtotime($driver_recommand['begin_time']) : 0;
		$end_time = isset($driver_recommand['end_time']) ? strtotime($driver_recommand['end_time']) : 0;
		$current_time = time();
		if ($current_time > $begin_time && $current_time < $end_time) {
			$recommand = 1;
		}
	}
	//验证司机是否为皇冠 BY AndyCong 2013-08-09 END
	
	$detail=array(
			'driver_id'=>$driver_id,
			'id'=>$driver->info['imei'],
			'name'=>$driver->info['name'],
			'picture'=>'',
			'phone'=>$driver->phone,
			'idCard'=>$id_card,
			'domicile'=>$driver->info['domicile'],
			'card'=>$car_card,
			'year'=>$driver->info['year'],
			'level'=>round($driver->info['level']),
			'new_level'=>$new_level,
			'goback'=>$driver->goback,
			'state'=>$driver->status,
			'price'=>'',
			'order_count'=>$driver->service['service_times'],
			'comment_count'=>$driver->service['high_opinion_times'],
			'recommand'=>$recommand,
			'longitude'=>$longitude,
			'latitude'=>$latitude,
			'distance'=>distince_format($distance),
			'picture_small'=>$driver->info['picture_small'],
			'picture_middle'=>$driver->info['picture_middle'],
			'picture_large'=>$driver->info['picture_large'],
	);
	
	return $detail;
}

/**
 * 格式化距离显示
 * @param unknown $distance
 * @return Ambigous <string, number>
 */
function distince_format($distance) {
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
