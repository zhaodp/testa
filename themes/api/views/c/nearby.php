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
$token = isset($params['token']) ? $params['token'] : '';
EdjLog::info('Invoke user notify push:'.json_encode($params));
$userNotify = new UserNotifyPush();
$userNotify->nearByPush($params);
$gps_type = isset($params['gps_type']) ? trim(strtolower($params['gps_type'])) : 'wgs84';
$idel_count = isset($params['idel_count']) ? $params['idel_count'] : 5;
$from = isset($params['from']) ? $params['from'] : '';
$idel_max_distance= isset(Yii::app()->params['OrderSingleRange']) ?
    Yii::app()->params['OrderSingleRange'] : 5000;
$busy_max_distance= isset(Yii::app()->params['OrderSingleRange']) ?
    Yii::app()->params['OrderSingleRange'] : 5000;
$lng=sprintf('%.6f', $params['longitude']);
$lat=sprintf('%.6f', $params['latitude']);
$longitude=$lng;
$latitude=$lat;

//开通城市
$open_city = RCityList::model()->getOpenCityList();

// 判断是否恶劣天气，是的话给用户发送恶劣天气加价的短信
// 但是VIP用户不受此业务逻辑的影响。此短信一天最多发送
// 一次——曾坤 2015/3/26
while (true) {
    if (empty($token)) {
        break;
    }

    $validate = CustomerToken::model()->validateToken($token);
    if (empty($validate) || !isset($validate['phone'])) {
        break;
    }

    // 如果用户是VIP，那么不受恶劣天气加价方案的影响——曾坤 2015/3/26
    if (!empty((VipPhone::model()->getVipByphone($validate['phone'])))) {
        break;
    }

    $gps_location = array(
        'longitude' => $lng,
        'latitude' => $lat,
    );
    $gps = GPS::model()->convert($gps_location , $gps_type);
    $city = GPS::model()->getCityByBaiduGPS($gps['baidu_lng'] , $gps['baidu_lat']);
    $city_id = CityConfig::getIdByName($city);
    if (empty($city_id)) {
        break;
    }

    // 在今天往后的时间里有没有恶劣天气加价的预案，
    // 如果有的话，发送短信提前告知用户——曾坤 2015/3/26
    $surcharge = RaisePrice::model()->getBadWeatherSurcharge($city_id);
    if (empty($surcharge)) {
        break;
    } 
    
    if (RedisHAProxy::model()->get('bad_weather_sms_'.$validate['phone'])) {
        break;
    }

    CustomerApiOrder::model()->sendBadWeatherSmsNotify($validate['phone'], $surcharge->app_message);

    // key的有效期只到今天——曾坤 2015/3/26
    $tomorrow = strtotime(date('Y-m-d 00:00:00', strtotime("+1 day")));
    RedisHAProxy::model()->set('bad_weather_sms_'.$validate['phone'], 1, $tomorrow - time());
    break;
}

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
	    EdjLog::info('|'.$app_ver.'|'.$udid.'|'.$latitude.'|'.$longitude);
        $json=json_encode(array(
            'code'=>2,
            'driverList'=>array(),
            'message'=>'您的周围暂无空闲司机'
        ));

    } else {
        $drivers = array();
        foreach($idel_driver as $driver) {
            //司机工号大于9800 不显示
            $is_backcar = Common::checkBlackCar($driver['driver_id']);
            if ($is_backcar) {
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
    $location_nearby = array(
        'longitude' => $lng,
        'latitude' => $lat,
    );
    $gps_nearby = GPS::model()->convert($location_nearby , $gps_type);
    $city_nearby = GPS::model()->getCityByBaiduGPS($gps_nearby['baidu_lng'],
    $gps_nearby['baidu_lat']);

    //验证城市是否开通
    $city_id = CityConfig::getIdByName($city_nearby);
    $long_distance=Common::getLongDistanceCity($city_id);
    $day_order_data= RCityList::model()->getDayOrderCity($city_id, $app_ver);

    //分城市距离
    if(isset(Yii::app()->params['OrderSingleRangeByCity'][$city_id])) {
        $idel_max_distance = Yii::app()->params['OrderSingleRangeByCity'][$city_id];
    }

    $max_distance = max($idel_max_distance, $busy_max_distance);

    //判断是否是日间单
    $driver_app_ver = null;
    if(!empty($app_ver) && $app_ver >= '5.3.0') {
        $check_ret = Order::model()->CheckSpecialOrderSource(
            Order::SOURCE_DAYTIME_CLIENT, $city_id, time());
        if($check_ret['flag']) {
            $driver_app_ver = '2.5.0';
            EdjLog::info('Daytime nearby|'.$city_id.'|'.$app_ver.'|'.$udid.'|'.$latitude.'|'.$longitude);
        }
    }

    //先取空闲的   获取司机 多获取10个 防止出现工号大于9800
    $driver_option = DriverGPS::model()->nearby($longitude,
      $latitude, 0, $idel_count+10, $idel_max_distance, $driver_app_ver);
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
        $busy_driver_option = DriverGPS::model()->nearby($longitude,
        $latitude, 1, $idel_count + 10 - count($driver_option), $busy_max_distance, $driver_app_ver);
        if(!empty($busy_driver_option)){
            foreach($busy_driver_option as $driver){
                $is_backcar = Common::checkBlackCar($driver['driver_id']);
                if(!$is_backcar){
                    $driver_option[] = $driver;
                }
            }
        }
    }

    if(empty($driver_option)) {

	EdjLog::info('|'.$app_ver.'|'.$udid.'|'.$latitude.'|'.$longitude);

        //没司机触发的活动
        if(!empty($token)) {
            $activity_task = array(
                'method' => 'nearby_nodriver_activity',
                'params' => array(
                    'token' => $token,
                ),
            );
            Queue::model()->putin($activity_task , 'activity');
        }

        $json=json_encode(
          array(
            'code'=>2,
            'driverList'=>array(),
            'data'=>$long_distance,
            'day_order_data'=>$day_order_data,
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
                if ($driver_distance >= $max_distance) {
                    continue;
                }

                $tmp['booking_id'] = '';   //将booking_id返回设置为空，保持数据格式一样
                $tmp['state'] = intval($tmp['state']);

                $drivers[] = $tmp;

		$logstr = $logstr.'|'.$id.':'.$tmp['state'].':'.$tmp['latitude'].':'.$tmp['longitude'].':'.$driver_distance;
            }

	    if(count($drivers) >= $idel_count) {
	      break;
	    }
        }

	EdjLog::info('|'.$city_id.'|'.$app_ver.'|'.$udid.'|'.$latitude.'|'.$longitude.$logstr);

        $json=json_encode(array(
            'code'=>0,
            'driverList'=>$drivers,
            'data'=>$long_distance,
            'day_order_data'=>$day_order_data,
            'message'=>'获取成功'
        ));
        echo $json;
        return;
    }

}
