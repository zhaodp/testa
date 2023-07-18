<?php
/**
 * 客户端API：c.driver.nearby 获取周边最近的空闲司机列表,需要传入业务代码
 * 调用url:
 * @param string $token
 * @param string $udid
 * @param string $gps_type {gps,google,baidu}
 * @param int $idel_count
 * @param string $from
 * 
 * @author aiguoxin
 * 
 * @return json
 * 
 * @example 
 */

//验证用户token
$udid=$params['udid'];
$gps_type = isset($params['gps_type']) ? trim(strtolower($params['gps_type'])) : 'wgs84';
$idel_count = isset($params['idel_count']) ? $params['idel_count'] : 5;
$from = isset($params['from']) ? $params['from'] : 'app';
$idel_max_distance= isset(Yii::app()->params['OrderSingleRange']) ?
    Yii::app()->params['OrderSingleRange'] : 5000;
$lng=sprintf('%.6f', $params['longitude']);
$lat=sprintf('%.6f', $params['latitude']);
$longitude=$lng;
$latitude=$lat;
//默认是代驾业务
$service_type = isset($params['service_type']) ? $params['service_type'] : Driver::SERVICE_TYPE_FOR_DAIJIA;


//司机端2.5.0及以上才支持
$driver_app_ver = '2.5.0';



//增加版本参数
$app_ver = isset($params['ver']) ? trim($params['ver']) : '';

    $gps = Common::getBaiduGps($lng , $lat , $gps_type);
    $longitude = $gps['lng'];
    $latitude  = $gps['lat'];

    //先取空闲的   获取司机 多获取10个 防止出现工号大于9800
    $driver_option = DriverGPS::model()->nearbyService($longitude,
      $latitude, 0, $idel_count+10, $idel_max_distance,$service_type,$driver_app_ver);
    if(!empty($driver_option)){
        foreach($driver_option as $key => $driver) {
            $is_backcar = Common::checkBlackCar($driver['driver_id']);
            if($is_backcar){
                unset($driver_option[$key]);
            }
        }
    }
    //空闲的凑不满,取点忙碌的 空闲的也多取3个，防止redis和mongo状态不一致
    if(count($driver_option) < $idel_count + 3) {
        $busy_driver_option = DriverGPS::model()->nearbyService($longitude,
        $latitude, 1, $idel_count + 10 - count($driver_option), $idel_max_distance,$service_type,$driver_app_ver);
        if(!empty($busy_driver_option)){
            foreach($busy_driver_option as $driver){
                $is_backcar = Common::checkBlackCar($driver['driver_id']);
                if(!$is_backcar){
                    $driver_option[] = $driver;
                }
            }
        }
    }
    $location_nearby = array(
        'longitude' => $lng,
        'latitude' => $lat,
    );
    $gps_nearby = GPS::model()->convert($location_nearby , $gps_type);
    $city_nearby = GPS::model()->getCityByBaiduGPS($gps_nearby['baidu_lng'],
    $gps_nearby['baidu_lat']);


    $city_id = CityConfig::getIdByName($city_nearby);
    $long_distance=Common::getLongDistanceCity($city_id);

    if(empty($driver_option)) {

	EdjLog::info('|'.$city_id.'|'.$app_ver.'|'.$udid.'|'.$latitude.'|'.$longitude);
        $json=json_encode(
          array(
            'code'=>2,
            'driverList'=>array(),
            'data'=>$long_distance,
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
	$city_id = 0;
        foreach($driver_to_format as $minfo) {
	    $id = $minfo['driver_id'];
            $tmp = Helper::simple_format_driver_detail($minfo, $driver_status[$id]);
	    if($city_id == 0) {
		$city_id = $driver_status[$id]['city_id'];
	    }
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

	EdjLog::info('|'.$from.'|'.$city_id.'|'.$app_ver.'|'.$udid.'|'.$latitude.'|'.$longitude.$logstr);

        $json=json_encode(array(
            'code'=>0,
            'driverList'=>$drivers,
            'data'=>$long_distance,
            'message'=>'获取成功'
        ));
        echo $json;
        return;
    }


