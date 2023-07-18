<?php
/**
 * 接收订单
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-07-20
 */

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
if (0 == $queue_id || 0 == $order_id || 0 == $push_msg_id || empty($lng) || empty($lat) || empty($log_time)) {
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
		if (!$is_dispatch) {
			$cache_value = $driver->driver_id;
			Yii::app()->cache->set($cache_key, $cache_value, 28800);
		} else {
			$ret = array('code' => 0 , 'message' => '接单成功');
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
		Queue::model()->putin($task,'order');
		$ret = array('code' => 0 , 'message' => '接单成功');
		echo json_encode($ret);return ;
	}
	
	//验证是否派单弹回 BY AndyCong 2013-12-28
	$queue_lock = QueueApiOrder::model()->validate_queue_lock($queue_id);
	if ($queue_lock) {
		$ret = array('code' => 2 , 'message' => '因网络延迟，订单已失效。');
		echo json_encode($ret);return ;
	}
	
	//验证订单是否已被接单 BY AndyCong 2013-08-28
	$cache_key = 'receive_detail_'.$order_id;
	$is_dispatch = Yii::app()->cache->get($cache_key);
	if (!$is_dispatch) {
		$cache_value = $driver->driver_id;
		Yii::app()->cache->set($cache_key, $cache_value, 28800);
	} else {
		if ($is_dispatch != $driver->driver_id) {
			$ret = array('code' => 2 , 'message' => '订单已被派出');
			echo json_encode($ret);return ;
		} else {
			//如果司机接过此单 直接返回成功 2013-11-12
			$driver_info = DriverStatus::model()->get($driver->driver_id);
			$driver_info->status = 1;
			$ret = array('code' => 0 , 'message' => '接单成功');
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
	Queue::model()->putin($task,'order');
	$ret = array('code' => 0 , 'message' => '接单成功');
} else {
	$ret = array('code' => 1 , 'message' => '请重新登录');
}
echo json_encode($ret);
return ;