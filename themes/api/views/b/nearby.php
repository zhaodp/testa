<?php
/**
 * 后台API：b.nearby 获取周边最近的空闲司机列表
 * 调用url:
 * @param string $udid
 * @param string $gps_type baidu}
 * @param int $idel_count
 * @param string $from
 * 
 * @author syang 2014-1-6
 * 
 * @return json
 * 
 * @example 
 */

//验证用户token

$udid=$params['udid'];
$gps_type = isset($params['gps_type']) ? trim(strtolower($params['gps_type'])) : 'baidu';
$idel_count = isset($params['idel_count']) ? $params['idel_count'] : 10;
$from = isset($params['from']) ? $params['from'] : '';
$idel_max_distance = isset($params['idel_max_distance'])?$params['idel_max_distance']:5000;
$longitude = $lng = sprintf('%.6f', $params['longitude']);
$latitude  = $lat = sprintf('%.6f', $params['latitude']);
$queue_id = isset($params['queue_id']) ? $params['queue_id'] : '';
$idel_driver = array();

$idel_driver =  DriverGPS::model()->nearby_client($lng, $lat, 0, $idel_count, $idel_max_distance);

	$new_idel_drivers = array();

    if (!empty($idel_driver)) {
    	foreach($idel_driver as $driver) {
            //司机工号大于9800 不显示
            $is_backcar = Common::checkBlackCar($driver['driver_id']);
            if ($is_backcar) {
                continue;
            }
		    $status = isset($driver['status']) ?  $driver['status'] : false;
		    $tmp = Helper::foramt_driver_detail($driver['driver_id'], $gps_type, $driver['distance'],'mini',$status);
		    if(!empty($tmp)){
		    	
		    	if( 0 < $tmp['state'] ){
		    		continue;
		    	}
		    	
			    //验证司机有没有被400订单锁定,如果锁定，就不在客户端显示了
				$lock_time = QueueDispatchDriver::model()->get($driver['driver_id']);
				$lock_gap = QueueDispatchDriver::DRIVER_LOCK_GAP;
				if ( abs(time() - $lock_time) < $lock_gap) { 
					continue;
				}
				$final['id'] 		= $driver['id'];
				$final['driver_id'] = $driver['driver_id'];
				$final['name'] 		= $tmp['name'];
				$final['distance'] 	= $driver['distance'];
				$final['lng'] 		= $tmp['longitude'];
				$final['lat'] 		= $tmp['latitude'];
				$final['status'] 	= $tmp['state'];
				$final['recommand'] = $tmp['recommand'];
				$final['new_level'] = $tmp['new_level'];
				$final['service_times'] = $tmp['service_times'];
				
		    	$new_idel_drivers[]= $final;
		    }  
		    
		}
		
		
	    foreach($new_idel_drivers as &$driver) {
		    $driver_id = $driver['driver_id'];
		
		    //create sms url
		    $appkey=Yii::app()->params['edj_api_key'];
		    $params=array(
		        'appkey'=>$appkey,
		        'ver'=>'3',
		        'func'=>'client/sendsms',
		        'wid'=>$driver['driver_id'],
		        'queue_id'=>$queue_id,
		        'max_distance'=>$idel_max_distance,//加载的公里数
		        'timestamp'=>date('Y-m-d H:i')
		    );
		
		    $sig=Api::createSigV2($params, $appkey);
		
		    $url='/v2/index.php?r=api&'.http_build_query($params).'&sig='.$sig;
		    $driver['url'] = $url;
		}
		
    }


//foreach($idel_driver as &$driver) {
//    $driver_id = $driver['driver_id'];
//
//    //create sms url
//    $appkey=Yii::app()->params['edj_api_key'];
//    $params=array(
//        'appkey'=>$appkey,
//        'ver'=>'3',
//        'func'=>'client/sendsms',
//        'wid'=>$driver['driver_id'],
//        'queue_id'=>$queue_id,
//        'timestamp'=>date('Y-m-d H:i')
//    );
//
//    $sig=Api::createSigV2($params, $appkey);
//
//    $url='/v2/index.php?r=api&'.http_build_query($params).'&sig='.$sig;
//    $driver['url'] = $url;
//}

$json=json_encode(array(
        'code'=>0,
        'driverList'=>$new_idel_drivers,
        'message'=>''
));
echo $json; return;



