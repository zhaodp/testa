<?php
/**
 * 新版API订单信息存储
 * @author AndyCong<Congmin@edaijia-staff.cn>
 * @version 2013-10-15
 */
class QueueApiOrder extends CRedis {
	public $host='redis03n.edaijia.cn';
	public $port=6379;
	public $password='k74FkBwb7252FsbNk2M7';
	private $_prefix = 'PhoneBookingId_';
	private $_prefix_phone = 'GatherPhone_';
	private $_gather_key_pre = 'GatherKeyPre_';
	private $_gap=32400; //缓存9小时
	
	private $_gather_key_gap = 86400;
	
	//自动派单弹回锁定
	private $_prefix_queue_lock = 'QUEUE_BACK_TO_HANDLE_';
	private $_val_queue_lock = 'HandleDispatch';
	private $_gap_queue_lock = 14400;
//	protected static $_key = null;

	//Lock the queue when generate the orders
	private $_prefix_gen_order_lock = 'QUEUE_GEN_ORDERS_';
	private $_val_gen_order_lock = 'genorder';
	private $_gap_gen_order_lock = 60;

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
	 * 写入redis
	 * @param string $key
	 * @param array $data
	 * @return boolean
	 */
	public function insert($key , $data) {
		//echo "\r\n ".date('Y-m-d H:i:s', time())." add order cache \r\n"; //增加输出log
		
		$token_key = $this->_prefix.$key;
		
		$queue_id = isset($data['queue_id']) ? $data['queue_id'] : '';
		$booking_id = isset($data['booking_id']) ? $data['booking_id'] : '';
		$booking_type = isset($data['booking_type']) ? $data['booking_type'] : '';
		$booking_time = isset($data['booking_time']) ? $data['booking_time'] : '';
		$contact_phone = isset($data['contact_phone']) ? $data['contact_phone'] : '';
		$created_time = isset($data['created_time']) ? $data['created_time'] : '';
		$city_id = isset($data['city_id']) ? $data['city_id'] : '';
		$number = isset($data['number']) ? $data['number'] : '';
		$address = isset($data['address']) ? $data['address'] : '';
		$lng = isset($data['lng']) ? $data['lng'] : '';
		$lat = isset($data['lat']) ? $data['lat'] : '';
		$google_lng = isset($data['google_lng']) ? $data['google_lng'] : '';
		$google_lat = isset($data['google_lat']) ? $data['google_lat'] : '';
		$flag = isset($data['flag']) ? $data['flag'] : '';
		$orders = isset($data['orders']) ? $data['orders'] : '';
		
		$this->redis->hset($token_key , 'queue_id' , $queue_id);
		$this->redis->hset($token_key , 'booking_id' , $booking_id);
		$this->redis->hset($token_key , 'booking_type' , $booking_type);
		$this->redis->hset($token_key , 'booking_time' , $booking_time);
		$this->redis->hset($token_key , 'contact_phone' , $contact_phone);
		$this->redis->hset($token_key , 'created_time' , $created_time);
		$this->redis->hset($token_key , 'city_id' , $city_id);
		$this->redis->hset($token_key , 'number' , $number);
		$this->redis->hset($token_key , 'address' , $address);
		$this->redis->hset($token_key , 'lng' , $lng);
		$this->redis->hset($token_key , 'lat' , $lat);
		$this->redis->hset($token_key , 'google_lng' , $google_lng);
		$this->redis->hset($token_key , 'google_lat' , $google_lat);
		$this->redis->hset($token_key , 'flag' , $flag);
		$this->redis->hset($token_key , 'orders' , json_encode($orders));

		if(isset($data['source'])) {
		    $this->redis->hset($token_key , 'source' , $data['source']);
		}
		
		//加上随机过期时间，看是否好使。add by sunhongjing 2013-12-21
		$rand_time = rand(4000, 20000);
		$expire_time = $rand_time + $this->_gap;	
		$this->redis->expire($token_key , $expire_time );

        self::gather_phone_key($token_key);
		
		return true;
	}

	/**
	 * 将insert key放入一个集合中
	 * @param string $phone
	 * @param string $key
	 * @return unknown
	 */
	public function gather_phone_key($key) {
        $phone = explode("_", $key);
        if (!empty($phone[1])) {
            $this->redis->hset($this->_prefix_phone.$phone[1] , $key, "");
            return true;
        } else {
            return false;
        }


	}

	
	/**
	 * 将订单缓存key放入一个集合中
	 * @param string $phone
	 * @param string $key
	 * @return unknown
	 */
	public function gather_order_key($phone , $key) {
		$token_key = $this->_gather_key_pre.$phone;
		$key = $this->_prefix.$key;
		$this->redis->hset($token_key , $key , $key);
		$this->redis->expire($token_key , $this->_gather_key_gap);
		return true;
	}
	
	/**
	 * 获取redis所有订单key
	 * @param string phone
	 * @return array $keys
	 */
	public function get_order_keys($phone) {
		$token_key = $this->_gather_key_pre.$phone;
		$keys = $this->redis->hgetall($token_key); 
		return $keys;
	}
	
	/**
	 * 查询所有订单(优化替代getallorders方法)
	 * @param string phone
	 * @return array $data
	 */
	public function get_all_orders($phone) {
		$token_key = $this->_gather_key_pre.$phone;
		$keys = $this->redis->hgetall($token_key); 
		$data = array();
		foreach ($keys as $key) {
			$orders = $this->redis->hgetall($key);
			if (!empty($orders)) {
				$data[] = $orders;
			}
		}
		return $data;
	}
	
	/**
	 * 更新相应field的值
	 * @param string $key
	 * @param string $field
	 * @param string/array $data
	 */
	public function update($key , $field , $data) {
		//echo "\r\n ".date('Y-m-d H:i:s', time())." update order cache \r\n"; //增加输出log
		
		$token_key = $this->_prefix.$key;
		if ($this->redis->exists($token_key)) {
			if ($field == 'orders') {
				$data = json_encode($data);
			}
			$this->redis->hset($token_key , $field , $data);
		}
	}
	
	/**
	 * 更新order信息(已废弃)
	 * @param string $key
	 * @param array $orders
	 * @return boolean
	 */
	public function update_orders($key , $orders) {
		$token_key = $this->_prefix.$key;
		if ($this->redis->exists($token_key)) {
			$this->redis->hset($token_key , 'orders' , json_encode($orders));
		}
		return true;
	}
	
	/**
	 * 更新Queue状态(已废弃)
	 * @param string $key
	 * @param int $flag
	 * @return boolean
	 */
	public function update_flag($key , $flag) {
		$token_key = $this->_prefix.$key;
		if ($this->redis->exists($token_key)) {
			$this->redis->hset($token_key , 'flag' , $flag);
		}
		return true;
	}
	
	
	/**
	 * 查询对应订单
	 * @param string $key
	 * @return array 
	 */
	public function getallfields($key) {
		$token_key = $this->_prefix.$key;
		$order = $this->redis->hgetall($token_key);
		return $order;
	}
	
	/**
	 * 获取相应field的值
	 * @param string $key1
	 * @param unknown_type $key2
	 * @return unknown
	 */
	public function get($key , $field) {
		$token_key = $this->_prefix.$key;
		$data = $this->redis->hget($token_key , $field);
		if (!empty($data) && $field == 'orders') {
			$data = json_decode($data , true);
		}
		return $data;
	}
	/**
	 * 查询所有订单
	 * @param string phone
	 * @return array $data
	 */
	public function getallorders($phone) {
		$token_key = $this->_prefix_phone.$phone;
		$keys = $this->redis->hkeys($token_key);
        if (empty($keys)) {
            return array();
        }

		$data = array();
		foreach ($keys as $key) {
			$orders = $this->redis->hgetall($key);
			if (!empty($orders)) {
				$data[] = $orders;
			} else {
                $this->redis->hdel($token_key, $key);
            }
		}

		return $data;
	}
	
	/**
	 * 删除对应key数据
	 * @param string $key
	 * @return boolean
	 */
	public function delete($key) {
		$token_key= $this->_prefix.$key;
		$ret = $this->redis->del($token_key);
		return true;
	}
	
	/**
	 * 删除用户又有redis
	 * @param string $phone
	 * @return boolean
	 */
	public function deleteall($phone) {
		$prefix = $this->_prefix.$phone.'_*';
		$allkeys = $this->redis->keys($prefix);
		foreach ($allkeys as $key) {
			 $this->redis->del($key);
		}
		return true;
	}
	
	/**
	 * 派单弹回需锁定queue_id
	 * @param int $queue_id
	 * @return int
	 * @version 2013-12-18
	 */
	public function queue_lock($queue_id){
		$cache_key = $this->_prefix_queue_lock.$queue_id;
		$cache_value = $this->_val_queue_lock;
		$this->redis->set($cache_key, $cache_value);
		$this->redis->expire($cache_key, $this->_gap_queue_lock);
		return 1;
	}

	/**
	 * 验证queue_id是否被弹回锁定
	 * @param int $queue_id
	 * @return boolean
	 */
	public function validate_queue_lock($queue_id) {
		$cache_key = $this->_prefix_queue_lock.$queue_id;
		$result = $this->redis->get($cache_key);
		if ($result == $this->_val_queue_lock) {
			return true;
		}
		return false;
	}

	/**
	 * Lock the queue when generate the orders
	 * @param int $queue_id
	 * @return int
	 * @version 2013-12-18
	 */
	public function queue_gen_order_lock($queue_id){
		$cache_key = $this->_prefix_gen_order_lock.$queue_id;
		$cache_value = $this->_val_gen_order_lock;
		$ret = $this->redis->getset($cache_key, $cache_value);
		$this->redis->expire($cache_key, $this->_gap_gen_order_lock);
		if(!empty($ret)) {
		    return false;
		} 
		return true;
	}

	/**
	 * UnLock the queue when generate the orders
	 * @param int $queue_id
	 * @return int
	 * @version 2013-12-18
	 */
	public function queue_gen_order_unlock($queue_id){
		$cache_key = $this->_prefix_gen_order_lock.$queue_id;
		$this->redis->delete($cache_key);
		return 1;
	}
	
}

