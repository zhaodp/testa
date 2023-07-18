<?php
/**
 * 接收订单
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-07-20
 */
if(Yii::app()->params['order_architecture_refactor_on']) {
    $result = AcceptOrderService::getInstance()->acceptOrder($params);
    echo json_encode($result);
    return;
}

//接收并验证参数
$queue_id = isset($params['queue_id']) ? $params['queue_id'] : '';
$order_id = isset($params['order_id']) ? $params['order_id'] : '';
//$driver_id = isset($params['driver_id']) ? $params['driver_id'] : '';
$push_msg_id = isset($params['push_msg_id']) ? $params['push_msg_id'] : 0;
$gps_type = isset($params['gps_type']) ? $params['gps_type'] : "wgs84";
$lng = isset($params['lng']) ? $params['lng'] : '';
$lat = isset($params['lat']) ? $params['lat'] : '';
$log_time = isset($params['log_time']) ? $params['log_time'] : '';
$token = isset($params['token']) ? $params['token'] : '';


if (0 == $queue_id || 0 == $order_id || 0 == $push_msg_id ||
    empty($token) || empty($lng) || empty($lat) || empty($log_time)) {
        EdjLog::info('Recevie order|failed|Invalid params|'.$order_id);
	$ret = array('code' => 2 , 'message' =>'参数有误');
	echo json_encode($ret);return ;
}



//验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver) {
	//两种类型订单 DAL层司机接单 和 老版本接单方式
	if (strlen($order_id) > 11 && is_numeric($order_id)) {
		//验证订单是否已被接单 BY AndyCong 2013-08-28
		$cache_key = 'receive_detail_'.$order_id;
		$is_dispatch = Yii::app()->cache->get($cache_key);
		// TODO: Use cahe here as lock is not Bug free. 
		// Use redis in the future
		if (!$is_dispatch) {
			$cache_value = $driver->driver_id;
			$set_ret = Yii::app()->cache->set($cache_key, $cache_value, 28800);
			if(!$set_ret) {
			    // Update memechace failed
			    EdjLog::info('|Recevie order|Failed| Write memcached failed|'.$driver->driver_id.'|'.$order_id);
			    $ret = array('code' => 2 , 'message' => '网络问题，接单失败');
			    echo json_encode($ret);
			    return ;
			}
		} else {
		        // Do log now and don't handle the case that the dispatch value does not equal the driver
			// TODO: Check if the driver need to receive the order
		        if ($is_dispatch != $driver->driver_id) {
			    EdjLog::info('|Recevie order|Failed|Order has been received by'.$is_dispatch.
				    '|'.$driver->driver_id.'|'.$order_id);
			    $ret = array('code' => 2 , 'message' => '订单已被派出');
			    echo json_encode($ret);return ;
			}

		        EdjLog::info('|Recevie order|Success|Order has been received by'.$is_dispatch.
				'|'.$driver->driver_id.'|'.$order_id);
			$ret = array('code' => 0 , 'message' => '接单成功');
            if($ret['code'] == 0){//接单成功
                ClientPush::model()->pushShareForCustomer($order_id, PageConfig::TRIGGER_RECEIVE);
            }
			echo json_encode($ret);return ;
		}
		$driver_info = DriverStatus::model()->get($driver->driver_id);
		$driver_info->status = 1;
		$params = array(
		    'queue_id' => $queue_id,
		    'order_id' => $order_id,
		    'driver_id' => $driver->driver_id,
		    'push_msg_id' => $push_msg_id,
		    'gps_type' => $gps_type,
		    'lng' => $lng,
		    'lat' => $lat,
		    'log_time' => $log_time,
		);
		$task = array(
		    'method' => 'dal_order_received',
		    'params' => $params,
		);
		$ret = Queue::model()->putin($task,'order');
		if(!$ret) {
		    // TODO: handle putin failed in the future
		    EdjLog::info('|Recevie order|Failed|Putin array failed|'.$driver->driver_id.'|'.$order_id);
		}
		$ret = array('code' => 0 , 'message' => '接单成功');
        if($ret['code'] == 0){//接单成功
            ClientPush::model()->pushShareForCustomer($order_id, PageConfig::TRIGGER_RECEIVE);
        }
		EdjLog::info('|Recevie order|Success|Order received|'.$driver->driver_id.'|'.$order_id);
		echo json_encode($ret);return ;
	}
	
	//验证是否派单弹回 BY AndyCong 2013-12-28
	$queue_lock = QueueApiOrder::model()->validate_queue_lock($queue_id);
	if ($queue_lock) {
	        EdjLog::info('|Recevie order|Failed| The queue has been locked|'.$driver->driver_id.'|'.$order_id);
		$ret = array('code' => 2 , 'message' => '因网络延迟，订单已失效。');
		echo json_encode($ret);return ;
	}
	
	//验证订单是否已被接单 BY AndyCong 2013-08-28
	$cache_key = 'receive_detail_'.$order_id;
	$is_dispatch = Yii::app()->cache->get($cache_key);
	if (!$is_dispatch) {
		$cache_value = $driver->driver_id;
		$set_ret = Yii::app()->cache->set($cache_key, $cache_value, 28800);
		if(!$set_ret) {
		    // Update memechace failed
		    EdjLog::info('|Recevie order|Failed| Write memcached failed|'.$driver->driver_id.'|'.$order_id);
		    $ret = array('code' => 2 , 'message' => '网络问题，接单失败');
		    echo json_encode($ret);
		    return ;
		}
	} else {
		if ($is_dispatch != $driver->driver_id) {
		        EdjLog::info('|Recevie order|Failed|Order has been received by'.$is_dispatch.
				'|'.$driver->driver_id.'|'.$order_id);
			$ret = array('code' => 2 , 'message' => '订单已被派出');
			echo json_encode($ret);return ;
		} else {
			//如果司机接过此单 直接返回成功 2013-11-12
		        EdjLog::info('|Recevie order|Success|Order already received|'.$driver->driver_id.'|'.$order_id);
			$driver_info = DriverStatus::model()->get($driver->driver_id);
			$driver_info->status = 1;
			$ret = array('code' => 0 , 'message' => '接单成功');
            if($ret['code'] == 0){//接单成功
                ClientPush::model()->pushShareForCustomer($order_id, PageConfig::TRIGGER_RECEIVE);
            }
			echo json_encode($ret);return ;
			//如果司机接过此单 直接返回成功 2013-11-12 END
		}
	}
	//验证订单是否已被接单 BY AndyCong 2013-08-28

	//司机置成服务中
	$driver_info = DriverStatus::model()->get($driver->driver_id);
//	if (!empty($driver_info)) {
		$driver_info->status = 1;
//	}
	//司机置成服务中 END
	
	$params = array(
	    'queue_id' => $queue_id,
	    'order_id' => $order_id,
	    'driver_id' => $driver->driver_id,
	    'push_msg_id' => $push_msg_id,
	    'gps_type' => $gps_type,
	    'lng' => $lng,
	    'lat' => $lat,
	    'log_time' => $log_time,
	);
	$task = array(
	    'method' => 'order_receive_operate',
	    'params' => $params,
	);

	$ret = Queue::model()->putin($task,'order');
	if(!$ret) {
	    EdjLog::info('|Recevie order|Failed|Putin array failed|'.$driver->driver_id.'|'.$order_id);
	}
		
	EdjLog::info('|Recevie order|Success|Order received|'.$driver->driver_id.'|'.$order_id);
	$ret = array('code' => 0 , 'message' => '接单成功');
    if($ret['code'] == 0){//接单成功
        ClientPush::model()->pushShareForCustomer($order_id, PageConfig::TRIGGER_RECEIVE);
    }
} else {
        EdjLog::info('Recevie order|Failed| Invalid token|'.$order_id);
	$ret = array('code' => 1 , 'message' => '请重新登录');
}
echo json_encode($ret);
return ;
