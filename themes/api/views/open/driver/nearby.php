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
$from = isset($params['from']) ? $params['from'] : '';
$idel_max_distance=5000;

$lng=sprintf('%.6f', $params['longitude']);
$lat=sprintf('%.6f', $params['latitude']);
$longitude=$lng;
$latitude=$lat;


//增加版本参数
$app_ver = isset($params['app_ver']) ? trim($params['app_ver']) : '';
if(empty($app_ver)) { //老版本保持不变

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
    $idel_driver=DriverGPS::model()->nearby($longitude, $latitude, 0, $idel_count, $idel_max_distance);

    if ( empty($idel_driver)) {
        $json=json_encode(array(
            'code'=>2,
            'driverList'=>array(),
            'message'=>'您的周围暂无空闲司机'
        ));

    } else {
        $drivers = array();
        foreach($idel_driver as $driver) {
            //司机工号大于9800 不显示
            if(substr($driver['driver_id'],2)  >= 9800 && substr($driver['driver_id'],2)  < 10000 ){
                continue;
            }
            $status = isset($driver['status']) ?  $driver['status'] : false;
            $tmp = Helper::foramt_driver_detail($driver['driver_id'], $gps_type, $driver['distance'],'mini',$status);
            if(!empty($tmp)){
                //获取司机的距离 BY AndyCong 2013-12-03
                $driver_distance = Helper::Distance($latitude , $longitude , $tmp['latitude'] , $tmp['longitude']);
                if ($driver_distance >= $idel_max_distance) {
                    continue;
                }
                //获取司机的距离 BY AndyCong 2013-12-03 END

                if( 0 < $tmp['state'] ){
                    continue;
                }

                $tmp['booking_id'] = '';   //将booking_id返回设置为空，保持数据格式一样
                $tmp['state']      = 0;    //状态强转空闲---nearby中取出的是空闲 格式化状态变成服务，但并不是为我服务的司机
                $drivers[]= $tmp;
            }

        }
        $json=json_encode(array(
            'code'=>0,
            'driverList'=>$drivers,
            'message'=>'获取成功',
        ));
    }
    echo $json;
    return;

} else {              //按照版本返回数据

    $gps = Common::getBaiduGps($lng , $lat , $gps_type);
    $longitude = $gps['lng'];
    $latitude  = $gps['lat'];

    //获取司机 多获取10个 防止出现工号大于9800
    $driver_option = DriverGPS::model()->nearby_multi_status($longitude,
      $latitude, array(0,1), $idel_count+10, $idel_max_distance);

    if(empty($driver_option)) {
        $json=json_encode(
          array(
            'code'=>2,
            'driverList'=>array(),
            'message'=>'您的周围暂无空闲司机'
          )
        );
	echo $json;
	return;
    } else {
        $drivers = array();
	$driver_to_format = array();
	$driver_to_format_ids = array();
        foreach($driver_option as $driver) {
            //司机工号大于9800 不显示
            if(substr($driver['driver_id'],2)  >= 9800
                && substr($driver['driver_id'],2)  < 10000 ){
                continue;
            }
            $driver_to_format_ids[] = $driver['driver_id'];
            $driver_to_format[] =
              array(
                'driver_id' => $driver['driver_id'],
                'gps_type'  => $gps_type,
                'distance'  => $driver['distance'],
                'data'      => 'mini',
                'status'    => isset($driver['status'])
		  ? $driver['status'] : false,
              );

            //获取司机 多获取3个 防止redis状态和mongo不符
	    if(count($driver_to_format) >= $idel_count+3) {
                break; 
            }
	}

        // 获取redis中的司机信息
	$driver_status = DriverStatus::model()->batch_get($driver_to_format_ids);

	$logstr = '';
        foreach($driver_to_format as $minfo) {
	    $id = $minfo['driver_id'];
            $tmp = Helper::simple_format_driver_detail($minfo, $driver_status[$id]);
            if(!empty($tmp)) {
                $driver_distance = Helper::Distance($latitude , $longitude , $tmp['latitude'] , $tmp['longitude']);
                if ($driver_distance >= $idel_max_distance) {
                    continue;
                }

                $tmp['booking_id'] = '';   //将booking_id返回设置为空，保持数据格式一样
                $tmp['state'] = intval($tmp['state']);

                $drivers[] = $tmp;

		$logstr = $logstr.'|'.$id.':'.$tmp['state'];
            }

	    if(count($drivers) >= $idel_count) {
	      break;
	    }
        }

	EdjLog::info($latitude.'|'.$longitude.$logstr);

        $json=json_encode(array(
            'code'=>0,
            'driverList'=>$drivers,
            'message'=>'获取成功'
        ));
        echo $json;
        return;
    }

}
