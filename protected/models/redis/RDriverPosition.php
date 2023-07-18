<?php
/**
 * DriverPosition use redis to save the value for one day
 * The key is driver_id_date, the value is a set of positions
 *
 * The way to get an order's trace is to get the start and 
 * end time of the order and the driver id related to the order,
 * then get the driver position in the time range.
 * 
 * @author qiujianping 2014-06-05
 */
class RDriverPosition extends CRedis {
    public $host='redisproxy.edaijia-inc.cn'; //
    public $port=22121;
    public $password=null;

	// @add by lifulong
	const LOC_INVALID=0;
	const LOC_NEW=1;
	//private $queue;
	//private $mLastFix=null;
	//private $isStart=true;
	//private $startMilliTimestamp=0;
	//private $maxAccuracy=self::MAX_GPS_ACCURACY;
	private $currentMilliTimestamp=0;
	const MINUTE=60000;
	const QUEUE_CAPACITY=6;
	const MAX_GPS_ACCURACY=200;
	const MAX_NETWORK_ACCURACY=2000;
	const DEF_PI180 = 0.01745329252;
	const DEF_R = 6370693.5;
	const EPS = 1e-10;


    protected static $_models=array();
    
    // Key prefix
    // key = prefix_driverId_orderId
    private $_key_prefix = 'DRIVER_POSITION_'; 

	// @add by lifulong
	// key = prefix_driverId_orderId
    private $_key_prefix_filter_info = 'FILTER_POSITION_INFO_';
    private $_key_prefix_filter_queue = 'FILTER_POSITION_QUEUE_';

    // key = prefix_driverId
    private $_key_prefix_driver_order = 'DRIVER_CURR_ORDER_';

    // For driver
    private $_field_order_id = 'order_id';
    private $_field_order_state = 'order_state';

    private $_key_prefix_order_driver = 'ORDER_DRIVER_';

    // For order
    private $_field_driver = 'order_driver';
    private $_field_state = 'order_state';
    private $_field_accept_time = 'order_accept_time';
    private $_field_arrive_time = 'order_arrive_time';
    private $_field_drive_time = 'order_drive_time';
    private $_field_finish_time = 'order_finish_time';

    private $_field_accept_pos = 'order_accept_pos';
    private $_field_arrive_pos = 'order_arrive_pos';
    private $_field_drive_pos = 'order_drive_pos';
    private $_field_finish_pos = 'order_finish_pos';

    private $_field_current_pos = 'order_current_pos';

    // The expire time to be 1 day
    // Set expire time to be 1 month
    //const EXPIRE_TIME_MONTH = 2592000;
    const EXPIRE_TIME_MONTH = 2160000;
    const EXPIRE_TIME_YEAR = 25920000;
    //const EXPIRE_TIME_MONTH = 172800;
    //const EXPIRE_TIME_MONTH = 1296000;
    const EXPIRE_TIME_DAY = 86400;

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
     * Insert the order id for driver now into redis
     *
     * @author qiujianping@edaijia 2014-06-10
     */
    public function setCurrentOrder($driver_id, $order_id, 
	    $order_state, $position = array(), $time_value = '') {
	if (empty($driver_id) || empty($order_id)) {
	    return false;
	}

	if(empty($position)){
	    $position = array('lat' => 1.0,
		    'lng' => 1.0);
	}

	$cache_key = $this->_key_prefix_driver_order.$driver_id;
	$this->redis->hset($cache_key, $this->_field_order_id, $order_id);
	$this->redis->hset($cache_key, $this->_field_order_state, $order_state);
        $this->redis->expire($cache_key, self::EXPIRE_TIME_DAY);

	// Set the order info map
	$this->setOrderInfo($order_id, $driver_id, 
		$order_state, $position, $time_value);
    }

    /**
     * Update the order id and order state for driver now into redis
     * The same as set
     *
     * @author qiujianping@edaijia 2014-06-12
     */
    public function updateCurrentOrder($driver_id, $order_id,
	    $order_state, $position = array(), $time_value = '') {
	if (empty($driver_id) || empty($order_id)) {
	    return false;
	}

	if(empty($position)){
	    $position = array('lat' => 1.0,
		    'lng' => 1.0);
	}

	$cache_key = $this->_key_prefix_driver_order.$driver_id;
	$this->redis->hset($cache_key, $this->_field_order_id, $order_id);
	$this->redis->hset($cache_key, $this->_field_order_state, $order_state);
        $this->redis->expire($cache_key, self::EXPIRE_TIME_DAY);

	$this->setOrderInfo($order_id, $driver_id, 
		$order_state, $position, $time_value);
    }

    /**
     * Update the order current position
     *
     * @author qiujianping@edaijia 2014-07-03
     */
    public function updateCurrentOrderPos($order_id, $position = array()) {
	$cache_key = $this->_key_prefix_order_driver.$order_id;
	$this->redis->hset($cache_key, 
		$this->_field_current_pos, json_encode($position));
        $this->redis->expire($cache_key, self::EXPIRE_TIME_YEAR);
    }

    /**
     * Get the order id for driver now from redis
     *
     * @author qiujianping@edaijia 2014-06-10
     */
    public function getCurrentOrder($driver_id) {
	if (empty($driver_id)) {
	    return false;
	}

	$cache_key = $this->_key_prefix_driver_order.$driver_id;
	$ret = $this->redis->hgetall($cache_key);
	return $ret;
    }

    /**
     * Set order basic info
     *
     * @author qiujianping@edaijia-staff.cn 2014-08-01
     */
    public function setOrderInfo($order_id, $driver_id, 
	    $order_state, $position = array(), $time_value = '') {
	if (empty($order_id)) {
	    return false;
	}

	if(empty($position)){
	    $position = array('lat' => 1.0,
		    'lng' => 1.0);
	}

	if(empty($time_value)) {
            $time_value = time();
        }

	$cache_key = $this->_key_prefix_order_driver.$order_id;
	$this->redis->hset($cache_key,
		$this->_field_driver, $driver_id);
	$this->redis->hset($cache_key,
		$this->_field_state, $order_state);

	switch($order_state) {
	    case OrderProcess::PROCESS_ACCEPT:
		if(!($this->redis->hExists($cache_key, 
				$this->_field_accept_time))) {


		    $this->redis->hset($cache_key, 
			    $this->_field_accept_time, $time_value);
		}
		$this->redis->hset($cache_key, 
			$this->_field_accept_pos, json_encode($position));
		break;
	    case OrderProcess::PROCESS_READY:
		$this->redis->hset($cache_key, 
			$this->_field_arrive_time, $time_value);
		$this->redis->hset($cache_key, 
			$this->_field_arrive_pos, json_encode($position));
		break;
	    case OrderProcess::PROCESS_DRIVING:
		$this->redis->hset($cache_key, 
			$this->_field_drive_time, $time_value);
		$this->redis->hset($cache_key, 
			$this->_field_drive_pos, json_encode($position));
		break;
	    case OrderProcess::PROCESS_DEST:
        case OrderProcess::ORDER_PROCESS_FINISH:
        EdjLog::info('whytest: order_state:'.$order_state,'console');
		$this->redis->hset($cache_key, 
			$this->_field_finish_time, $time_value);
		$this->redis->hset($cache_key, 
			$this->_field_finish_pos, json_encode($position));
		break;
	}

	if($position['lat'] != 1.0 && $position['lng'] != 1.0) {
	    $this->updateCurrentOrderPos($order_id, $position);
	} else {
	    $this->redis->expire($cache_key, self::EXPIRE_TIME_MONTH);
	}
    }

    /**
     * Get order driver map
     *
     * @author qiujianping@edaijia-staff.cn 2014-06-12
     */
    public function getOrderInfo($order_id) {
	if (empty($order_id)) {
	    return false;
	}

	$cache_key = $this->_key_prefix_order_driver.$order_id;
	$ret = $this->redis->hgetall($cache_key);
	return $ret;
    }

    /**
     * Insert a position into redis
     *
     * @author qiujianping@edaijia 2014-06-05
     */
    public function insertPosition($datas) {
	if (empty($datas['positions']) 
		|| empty($datas['driver_id'])
		|| empty($datas['order_id'])
		|| empty($datas['order_state'])) {
	    return false;
	}

	$curr_time = time();
	$position_data = array('positions' => $datas['positions'],
		'order_state' => $datas['order_state'],
		'created' => $curr_time);

	$cache_key = $this->_key_prefix.$datas['driver_id']."_".$datas['order_id'];
	$this->redis->sAdd($cache_key, json_encode($position_data));
        $this->redis->expire($cache_key, self::EXPIRE_TIME_MONTH);
    }

    /**
     * Insert a batch of positions into redis
     *
     * @author qiujianping@edaijia 2014-06-05
     */
    public function insertBatchPosition($datas, $time_value = 0) {
	if (empty($datas['positions']) 
		|| empty($datas['driver_id'])
		|| empty($datas['order_id'])
		|| empty($datas['order_state'])) {
	    return false;
	}

	if($time_value == 0) {
	    $time_value = time();
	}
	
	$position_data = array('positions' => $datas['positions'],
		'order_state' => $datas['order_state'],
		'created' => $time_value);

	$cache_key = $this->_key_prefix.$datas['driver_id']."_".$datas['order_id'];
	$this->redis->sAdd($cache_key, json_encode($position_data));
        $this->redis->expire($cache_key, self::EXPIRE_TIME_MONTH);
    }


    /**
     * Delete a driver position in redis
     *
     * @author qiujianping@edaijia 2014-06-09
     */
    public function deleteDriverPositions($datas) {
	if (empty($datas['order_id'])|| empty($datas['driver_id'])) {
	    return false;
	}

	$cache_key = $this->_key_prefix.$datas['driver_id']."_".$datas['order_id'];
	$this->redis->delete($cache_key);
    }


	/**
	 * 判断最近六个点是否都是network提供的
	 *
	 * @author: lifulong@edaijia 2014-12-08
	 */

	private function isAllNetworkLocation($queue){
		foreach($queue as $provider){
			if($provider != "network"){
				return false;
			}
		}
		return true;
	}

	private function adjustMaxAccuraryIfNeed(&$filterInfo, $position) {

		$currentMilliTimestamp=$position['milli_timestamp']*1000;

		$isStart = $filterInfo['is_start'];
		$maxAccuracy = $filterInfo['max_accuracy'];
		$startMilliTimestamp = $filterInfo['start_milli_time_stamp'];
		$filterQueue = $filterInfo['queue'];

		//Update filter queue if needed.
		if($isStart) {
			$isStart=false;
			$startMilliTimestamp=$currentMilliTimestamp;
		}
		else {
			if($currentMilliTimestamp-$startMilliTimestamp>self::MINUTE){
				$isStart=true;
				unset($filterQueue);
			}
		}

		//Update max accuracy if needed.
		$filterQueue[] = $position['provider'];
		if(count($filterQueue) == self::QUEUE_CAPACITY) {
			if($this->isAllNetworkLocation($filterQueue)){
				$maxAccuracy=self::MAX_NETWORK_ACCURACY;
			}
			else{
				$maxAccuracy=self::MAX_GPS_ACCURACY;
			}
			array_shift($filterQueue);
		}

		$filterInfo['is_start'] = $isStart;
		$filterInfo['max_accuracy'] = $maxAccuracy;
		$filterInfo['start_milli_time_stamp'] = $startMilliTimestamp;
		$filterInfo['queue'] = $filterQueue;
	}

	public static function getLongDistance($lon1, $lat1, $lon2, $lat2) {

		if(!(abs($lat1)<=90&&abs($lat1)>0&&abs($lon1)<=180&&abs($lon1)>0))
			return null;
		if(!(abs($lat2)<=90&&abs($lat2)>0&&abs($lon2)<=180&&abs($lon2)>0))
			return null;
        // 角度转换为弧度
        $ew1 = $lon1 * self::DEF_PI180;
        $ns1 = $lat1 * self::DEF_PI180;
        $ew2 = $lon2 * self::DEF_PI180;
        $ns2 = $lat2 * self::DEF_PI180;
        // 求大圆劣弧与球心所夹的角(弧度)
        $distance = sin($ns1) * sin($ns2) + cos($ns1)
                * cos($ns2) * cos($ew1 - $ew2);
        // 调整到[-1..1]范围内，避免溢出
        if ($distance > 1.0) {
            $distance = 1.0;
        } else if ($distance < -1.0) {
            $distance = -1.0;
        }
        // 求大圆劣弧长度
        $distance = self::DEF_R * acos($distance);
        return $distance;
    }

	private function isTooFar($lastFix, $position, $interval){
		if($lastFix == null || $position == null || $interval == 0){
			return true;
		}
		$second = $interval/1000.0;
		$dist = self::getLongDistance($lastFix['lng'], $lastFix['lat'], $position['lng'], $position['lat']);
		if($dist === null){
			return true;
		}
		return ($dist / $second) * 3.6 > 150;
	}

	private function isSameLocation($loc1, $loc2)
	{
		if($loc1 == null || $loc2 == null){
			return false;
		}

		if((abs($loc1['lat'] - $loc2['lat']) < self::EPS) && (abs($loc1['lng'] - $loc2['lng']) < self::EPS))
			return true;

		return false;
	}

	/**
	 * Temp test interface, will removed.
	 **/
	public function initFilterInfo($driver_id, $order_id)
	{
		$cache_filter_info_key = $this->_key_prefix_filter_info.$driver_id."_".$order_id;
		$this->redis->delete($cache_filter_info_key);
	}


	/**
	  * Filter invalid position
	  * 
	  * Check if the position is an valid position, if valid
	  * return the position else return null.
	  *
	  * @author: lifulong@edaijia 2014-12-08
	  * @return: true if the position data valid,else false
	  */
	public function validPosition($data)
	{
		if(empty($data['driver_id'])
			|| empty($data['order_id'])
			|| empty($data['position'])) {
			return false;
		}
		
		$position = $data['position'];
		$lat = $position['lat'];
		$lng = $position['lng'];

		if(!(abs($lat)<=90&&abs($lat)>0&&abs($lng)<=180&&abs($lng)>0))
			return false;

		if((abs($lng) + abs($lat)) <= 10.0)
			return false;

		$cache_filter_info_key = $this->_key_prefix_filter_info.$data['driver_id']."_".$data['order_id'];

		$filterInfoStr = $this->redis->get($cache_filter_info_key);
		$filterInfo = json_decode($filterInfoStr, true);
		if($filterInfo == null) {
			$filterInfo = array(
					'last_fix' => null,
					'is_start' => true,
					'max_accuracy' => self::MAX_GPS_ACCURACY,
					'start_milli_time_stamp' => 0,
					'queue' => array(),
				);
			$filterInfoStr = json_encode($filterInfo);
			$this->redis->set($cache_filter_info_key, $filterInfoStr);
			$this->redis->expire($cache_filter_info_key, self::EXPIRE_TIME_DAY);
		}

		$this->adjustMaxAccuraryIfNeed($filterInfo, $position);

		$lastFix = $filterInfo['last_fix'];
		$maxAccuracy = $filterInfo['max_accuracy'];

		if($this->isSameLocation($lastFix, $position)){
			$filterInfoStr = json_encode($filterInfo);
			$this->redis->set($cache_filter_info_key, $filterInfoStr);
			return false;
		}
		// 定位精度
		if ($position['accuracy'] > $maxAccuracy) {
			$filterInfoStr = json_encode($filterInfo);
			$this->redis->set($cache_filter_info_key, $filterInfoStr);
			return false;
		}

		if ($lastFix == null) {
			//FIXME: it's urgly.   by lifulong
			$lastFix = $position;
			$filterInfo['last_fix'] = $lastFix;
			$filterInfoStr = json_encode($filterInfo);
			$this->redis->set($cache_filter_info_key, $filterInfoStr);
			return true;
		}
		/*
		$filterInfoStr = json_encode($filterInfo);
		$this->redis->set($cache_filter_info_key, $filterInfoStr);
		*/

		//gps速度没传，没法判断
		// 定位Location时间
		if ($position['gps_time'] <= $lastFix['gps_time']) {
			$filterInfoStr = json_encode($filterInfo);
			$this->redis->set($cache_filter_info_key, $filterInfoStr);
			return false;
		}
		// 异常点排除
		if ($this->isTooFar($lastFix, $position, 
					$position['gps_time'] - $lastFix['gps_time'])) {
			$filterInfoStr = json_encode($filterInfo);
			$this->redis->set($cache_filter_info_key, $filterInfoStr);
			return false;
		}

		$lastFix = $position;
		$filterInfo['last_fix'] = $lastFix;
		$filterInfoStr = json_encode($filterInfo);
		$this->redis->set($cache_filter_info_key, $filterInfoStr);

		return true;
	}

 
	/**
	  * batch filter invalid position
	  * 
	  * Check if the position is an valid position, if valid
	  * return the position else return null.
	  *
	  * @author: lifulong@edaijia 2015-01-12
	  * @return: return valid positions array
	  */
	public function validPositions($datas)
	{
		$valid_positions = array();

		if(empty($datas['driver_id'])
			|| empty($datas['order_id'])
			|| empty($datas['positions'])) {
			return $valid_positions;
		}

		$new_key = false;
		$cache_filter_info_key = $this->_key_prefix_filter_info.$datas['driver_id']."_".$datas['order_id'];

		$filterInfoStr = $this->redis->get($cache_filter_info_key);
		$filterInfo = json_decode($filterInfoStr, true);
		if($filterInfo == null) {
			$new_key = true;
			$filterInfo = array(
					'last_fix' => null,
					'is_start' => true,
					'max_accuracy' => self::MAX_GPS_ACCURACY,
					'start_milli_time_stamp' => 0,
					'queue' => array(),
				);
		}
	
		$positions = $datas['positions'];
		foreach( $positions as $position)
		{
			if(1 != $position['status'])
				continue;

			$lat = $position['lat'];
			$lng = $position['lng'];

			if(!(abs($lat)<=90&&abs($lat)>0&&abs($lng)<=180&&abs($lng)>0))
			{
				EdjLog::info("validPositions:\tdriver_id".$datas['driver_id']."\torder_id:".$datas['order_id']."\tlat:".$position['lat']."\tlng:".$position['lng']."\tnonvalid");
				continue;
			}

			if((abs($lng) + abs($lat)) <= 10.0)
			{
				EdjLog::info("validPositions:\tdriver_id".$datas['driver_id']."\torder_id:".$datas['order_id']."\tlat:".$position['lat']."\tlng:".$position['lng']."\tnonvalid");
				continue;
			}

			$this->adjustMaxAccuraryIfNeed($filterInfo, $position);

			$lastFix = $filterInfo['last_fix'];
			$maxAccuracy = $filterInfo['max_accuracy'];

			if($this->isSameLocation($lastFix, $position)){
				EdjLog::info("validPositions:\tdriver_id".$datas['driver_id']."\torder_id:".$datas['order_id']."\tlat:".$position['lat']."\tlng:".$position['lng']."\tnonvalid");
				continue;
			}
			// 定位精度
			if ($position['accuracy'] > $maxAccuracy) {
				EdjLog::info("validPositions:\tdriver_id".$datas['driver_id']."\torder_id:".$datas['order_id']."\tlat:".$position['lat']."\tlng:".$position['lng']."\tnonvalid");
				continue;
			}

			if ($lastFix == null) {
				//FIXME: it's urgly.   by lifulong
				$valid_positions[] = $position;
				$lastFix = $position;
				$filterInfo['last_fix'] = $lastFix;
				EdjLog::info("validPositions:\tdriver_id".$datas['driver_id']."\torder_id:".$datas['order_id']."\tlat:".$position['lat']."\tlng:".$position['lng']."\tvalid");
				continue;
			}

			//gps速度没传，没法判断
			// 定位Location时间
			if ($position['gps_time'] <= $lastFix['gps_time']) {
				EdjLog::info("validPositions:\tdriver_id".$datas['driver_id']."\torder_id:".$datas['order_id']."\tlat:".$position['lat']."\tlng:".$position['lng']."\tnonvalid");
				continue;
			}
			// 异常点排除
			if ($this->isTooFar($lastFix, $position, 
						$position['gps_time'] - $lastFix['gps_time'])) {
				EdjLog::info("validPositions:\tdriver_id".$datas['driver_id']."\torder_id:".$datas['order_id']."\tlat:".$position['lat']."\tlng:".$position['lng']."\tnonvalid");
				continue;
			}

			$valid_positions[] = $position;
			$lastFix = $position;
			$filterInfo['last_fix'] = $lastFix;
			EdjLog::info("validPositions:\tdriver_id".$datas['driver_id']."\torder_id:".$datas['order_id']."\tlat:".$position['lat']."\tlng:".$position['lng']."\tvalid");
		}

		$filterInfoStr = json_encode($filterInfo);
		$this->redis->set($cache_filter_info_key, $filterInfoStr);
		if($new_key)
			$this->redis->expire($cache_filter_info_key, self::EXPIRE_TIME_DAY);

		return $valid_positions;
	}


   /**
     * Get the positon by driver id in a time range
     *
     * Check if the position is found in redis, if not
     * get it in db.
     * The data in redis will on stay for 1 day, so if the key 
     * is larger than 1 day, then just get it in database.
     *
     * @author qiujianping@edaijia 2014-06-05
     * @return the positions sorted in an array
     */
    public function getPositions($datas) {
	if(empty($datas['driver_id']) 
		||empty($datas['order_id'])) {
	    return array();
	}
	$cache_key = $this->_key_prefix.$datas['driver_id']."_".$datas['order_id'];
	$positions_source = $this->redis->sMembers($cache_key);
	$positions_rst = array();
	$inner_positions = array();
	// Get the positions in the time range
	foreach($positions_source as $position) {
	    $position_data = json_decode($position, true);
	    if(((isset($position_data['created']) 
		    && isset($datas['start'])
		    && $position_data['created'] >= $datas['start']) 
		    || (!isset($datas['start'])))
		    && isset($position_data['positions'])) {
		// Save the order
		$inner_positions[$position_data['created']] =
		    array('positions' => $position_data['positions'],
			    'order_state' => $position_data['order_state']);
	    }
	}

	// sort the postion by key created
	ksort($inner_positions);

	// Change the result to be lat and lng
	foreach($inner_positions as $created=>$positions){
	    // For each save it to positions_rst
	    $single_upload_pos = $positions['positions'];
	    foreach($single_upload_pos as $loop_pos) {
		if(count($loop_pos) == 0) {
		    continue;
		}
		$positions_rst[] = 
		   array('lat' => $loop_pos['lat'],
			   'lng' => $loop_pos['lng'],
			   'created' => $created,
			   'order_state' => $positions['order_state']);
	    }
	}
	return $positions_rst;
    }
}

// end file
