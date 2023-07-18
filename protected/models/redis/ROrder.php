<?php
/**
 * 订单数据缓存层，由这一层再去访问数据库,简单定义，细节没想好呢
 * 
 * 
 * @author sunhongjing 2013-12-29
 *
 */
class ROrder extends CRedis {
	public $host='redis03n.edaijia.cn'; //
	public $port=6379;
	public $password='k74FkBwb7252FsbNk2M7';
	protected static $_models=array();
	
	//客户端已电话和call_id做key存储的前缀
	private $_prefix = 'PhoneBookingId_'; 
    private $_prefix_phone = 'GatherPhone_';
        private $_gap=32400; //缓存9小时

	//订单缓存表
	private $_order_key			  = "CDAL_ORDER_KEY_";
	private $_order_queue_key	  = "CDAL_ORDER_QUEUE_KEY_";
	private $_order_queue_map_key = "CDAL_ORDER_QUEUE_MAP_KEY_";
	private $_message_log_key     = "CDAL_MESSAGE_LOG_KEY_";

	private $_order_group_user_key = "GROUP_ORDER_";

	//司机 订单key
	private $_driver_order_key    = "CDAL_D_ORDER_KEY_";

	//客户订单 key
	private $_customer_order_key  = "CDAL_C_ORDER_KEY_";
	
	//自动派单弹回锁定
	private $_prefix_queue_lock = 'QUEUE_BACK_TO_HANDLE_';
	private $_val_queue_lock = 'HandleDispatch';
	private $_gap_queue_lock = 14400;
	
	//定义缓存失效时间
	const MAX_ORDER_DEADLINE = 86400;

        //Push信息缓存时间
        const PUSH_MSG_EXPIRE = 120;

	//选司机下单retry信息
	private $_single_push_retry_gap = 60;
	private $_single_push_retry_pre = 'SINGLE_PUSH_RETRY_';
	
	private $_gather_key_pre = 'GatherKeyPre_';
	private $_gather_key_gap = 86400;
	
	public static function model($className=__CLASS__) {
		$model=null;
		if (isset(self::$_models[$className]))
			$model=self::$_models[$className];
		else {
			$model=self::$_models[$className]=new $className(null);
		}
		return $model;
	}

	public function OrderAddCache($data) {
		if (empty($data['queue']['key']) || empty($data['queue']['data']) 
		    || empty($data['order']['key']) || empty($data['order']['data']) 
		    || empty($data['map']['key']) || empty($data['map']['data']) ) 
		{
			return false;
		}
		$key_order = $data['order']['key'];
		$order = $data['order']['data'];
		$key_queue = $data['queue']['key'];
		$queue = $data['queue']['data'];
		$key_map = $data['map']['key'];
		$map = $data['map']['data'];
		$this->insertOrder($key_order , $order);
		$this->insertQueue($key_queue , $queue);
		$this->insertMap($key_map , $map);
	}
	
	/*++++++++++++++++++++++更新Order+++++++++++++++++++++*/
	
	/**
	 * 将订单信息写入redis
	 * @param string $key
	 * @param array $data 
	 * @return boolean
	 * @author AndyCong<congming@edaijia-staff.cn>
	 * @version 2013-12-30
	 */
	public function insertOrder($key , $data) {
		$order_arr = array();
		$order_arr['order_id'] = isset($data['order_id']) ? $data['order_id'] : '';
		$order_arr['unique_queue_id'] = isset($data['unique_queue_id']) ? $data['unique_queue_id'] : '';
		$order_arr['callid'] = isset($data['callid']) ? $data['callid'] : '';
		$order_arr['order_number'] = isset($data['order_number']) ? $data['order_number'] : '';
		$order_arr['name'] = isset($data['name']) ? $data['name'] : '';
		$order_arr['phone'] = isset($data['phone']) ? $data['phone'] : '';
		$order_arr['contact_phone'] = isset($data['contact_phone']) ? $data['contact_phone'] : '';
		$order_arr['source'] = isset($data['source']) ? $data['source'] : '';
		$order_arr['city_id'] = isset($data['city_id']) ? $data['city_id'] : '';
		$order_arr['driver'] = isset($data['driver']) ? $data['driver'] : '';
		$order_arr['driver_id'] = isset($data['driver_id']) ? $data['driver_id'] : '';
		$order_arr['driver_phone'] = isset($data['driver_phone']) ? $data['driver_phone'] : '';
		$order_arr['imei'] = isset($data['imei']) ? $data['imei'] : '';
		$order_arr['call_time'] = isset($data['call_time']) ? $data['call_time'] : '';
		$order_arr['order_date'] = isset($data['order_date']) ? $data['order_date'] : '';
		$order_arr['booking_time'] = isset($data['booking_time']) ? $data['booking_time'] : '';
		$order_arr['location_start'] = isset($data['location_start']) ? $data['location_start'] : '';
		$order_arr['description'] = isset($data['description']) ? $data['description'] : '';
		$order_arr['created'] = isset($data['created']) ? $data['created'] : '';
		$order_arr['channel'] = isset($data['channel']) ? $data['channel'] : '';
        
		$token_key = $this->_order_key.$key;
    	$this->redis->hMset($token_key , $order_arr);
    	$expire_time = self::MAX_ORDER_DEADLINE;	
		$this->redis->expire($token_key , $expire_time);
		return true;
	}
	
	/**
	 * 更新订单信息
	 * @param string $key
	 * @param array $data
	 * @return boolean
	 * @author AndyCong<congming@edaijia-staff.cn>
	 * @version 2013-12-30
	 */
	public function updateOrder($key , $data)
	{
		$order_arr = array();
		
		$token_key = $this->_order_key.$key;
		foreach ($data as $feild=>$val) {
			$order_arr[$feild] = $val;
			$this->redis->hset($token_key , $feild , $val);
		}
		return true;
	}
	
	/**
	 * 获取订单信息
	 * @param string $key
	 * @return array
	 * @author AndyCong<congming@edaijia-staff.cn>
	 * @version 2013-12-30
	 */
	public function getOrder($key , $field = '')
	{
		$result = '';
		$token_key = $this->_order_key.$key;
		if (!empty($field)) {
			$result = $this->redis->hget($token_key , $field);
		} else {
			$result = $this->redis->hgetall($token_key);
		}
		return $result;
	}

	/*++++++++++++++++++++更新Order END+++++++++++++++++++*/
	
	/*+++++++++++++++++++更新OrderQueue+++++++++++++++++++*/
	
	/**
	 * 将queue信息写入redis
	 * @param string $key
	 * @param array $data
	 * @return boolean
	 * @author AndyCong<congming@edaijia-staff.cn>
	 * @version 2013-12-30
	 */
	public function insertQueue($key , $data){
		$queue_arr = array();
		$queue_arr['queue_id'] = isset($data['queue_id']) ? $data['queue_id'] : '';
		$queue_arr['phone'] = isset($data['phone']) ? $data['phone'] : '';
    	$queue_arr['contact_phone'] = isset($data['contact_phone']) ? $data['contact_phone'] : '';
    	$queue_arr['city_id'] = isset($data['city_id']) ? $data['city_id'] : '';
    	$queue_arr['callid'] = isset($data['callid']) ? $data['callid'] : '';
    	$queue_arr['name'] = isset($data['name']) ? $data['name'] : '';
    	$queue_arr['number'] = isset($data['number']) ? $data['number'] : '';
    	$queue_arr['address'] = isset($data['address']) ? $data['address'] : '';
    	$queue_arr['booking_time'] = isset($data['booking_time']) ? $data['booking_time'] : '';
    	$queue_arr['flag'] = isset($data['flag']) ? $data['flag'] : '';
    	$queue_arr['type'] = isset($data['type']) ? $data['type'] : '';
    	$queue_arr['update_time'] = isset($data['update_time']) ? $data['update_time'] : '';
    	$queue_arr['agent_id'] = isset($data['agent_id']) ? $data['agent_id'] : '';
    	$queue_arr['dispatch_agent'] = isset($data['dispatch_agent']) ? $data['dispatch_agent'] : '';
    	$queue_arr['dispatch_time'] = isset($data['dispatch_time']) ? $data['dispatch_time'] : '';
    	$queue_arr['created'] = isset($data['created']) ? $data['created'] : '';
    	$queue_arr['lng'] = isset($data['lng']) ? $data['lng'] : '';
    	$queue_arr['lat'] = isset($data['lat']) ? $data['lat'] : '';
    	$queue_arr['google_lng'] = isset($data['google_lng']) ? $data['google_lng'] : '';
    	$queue_arr['google_lat'] = isset($data['google_lat']) ? $data['google_lat'] : '';
    	$queue_arr['channel'] = isset($data['channel']) ? $data['channel'] : '';
    	
    	$queue_arr['dispatch_number'] = isset($data['dispatch_number']) ? $data['dispatch_number'] : 0;
    	$queue_arr['comments'] = isset($data['comments']) ? $data['comments'] : '';
    	
    	$token_key = $this->_order_queue_key.$key;
    	$this->redis->hMset($token_key , $queue_arr);
    	$expire_time = self::MAX_ORDER_DEADLINE;	
		$this->redis->expire($token_key , $expire_time);
		return true;
	}
	
	/**
	 * 更新queue信息
	 * @param string $key
	 * @param array $data
	 * @return boolean
	 * @author AndyCong<congming@edaijia-staff.cn>
	 * @version 2013-12-30
	 */
	public function updateQueue($key , $data) {
		$queue_arr = array();
		$token_key = $this->_order_queue_key.$key;
		foreach ($data as $feild=>$val) {
			$queue_arr[$feild] = $val;
			$this->redis->hset($token_key , $feild , $val);
		}
		return true;
	}
	
	/**
	 * 获取订单信息
	 * @param string $key
	 * @param string $feild
	 * @return array
	 * @author AndyCong<congming@edaijia-staff.cn>
	 * @version 2013-12-30
	 */
	public function getQueue($key , $field = '') {
		$result = '';
		$token_key = $this->_order_queue_key.$key;
		if (!empty($field)) {
			$result = $this->redis->hget($token_key , $field);
		} else {
			$result = $this->redis->hgetall($token_key);
		}
		return $result;
	}
	
	/*+++++++++++++++++更新OrderQueue END+++++++++++++++++*/
	
	/*+++++++++++++++++++++++更新map++++++++++++++++++++++*/
	
	/**
	 * 将map信息写入redis
	 * @param string $key
	 * @param array $data
	 * @return boolean
	 * @author AndyCong<congming@edaijia-staff.cn>
	 * @version 2013-12-30
	 */
	public function insertMap($key , $data) {
		$map_arr = array();
		$map_arr['map_id'] = isset($data['map_id']) ? $data['queue_id'] : '';
		$map_arr['queue_id'] = isset($data['queue_id']) ? $data['queue_id'] : '';
		$map_arr['order_id'] = isset($data['order_id']) ? $data['order_id'] : '';
		$map_arr['driver_id'] = isset($data['driver_id']) ? $data['driver_id'] : '';
		$map_arr['number'] = isset($data['number']) ? $data['number'] : '';
		$map_arr['flag'] = isset($data['flag']) ? $data['flag'] : '';
		$map_arr['dispatch_time'] = isset($data['dispatch_time']) ? $data['dispatch_time'] : '';
		$map_arr['confirm_time'] = isset($data['confirm_time']) ? $data['confirm_time'] : '';
		
		$token_key = $this->_order_queue_map_key.$key;
    	$this->redis->hMset($token_key , $map_arr);
    	$expire_time = self::MAX_ORDER_DEADLINE;	
		$this->redis->expire($token_key , $expire_time);
		return true;
	}
	
	/**
	 * 更新Map信息
	 * @param string $key
	 * @param array $data
	 * @return boolean
	 * @author AndyCong<congming@edaijia-staff.cn>
	 * @version 2013-12-30
	 */
	public function updateMap($key , $data) {
		$map_arr = array();
		$token_key = $this->_order_queue_map_key.$key;
		foreach ($data as $feild=>$val) {
			$map_arr[$feild] = $val;
			$this->redis->hset($token_key , $feild , $val);
		}
		return true;
	}
	
	/**
	 * 获取map信息
	 * @param string $key
	 * @param string $feild
	 * @return array
	 * @author AndyCong<congming@edaijia-staff.cn>
	 * @version 2013-12-30
	 */
	public function getMap($key , $field = '') {
		$result = '';
		$token_key = $this->_order_queue_map_key.$key;
		if (!empty($field)) {
			$result = $this->redis->hget($token_key , $field);
		} else {
			$result = $this->redis->hgetall($token_key);
		}
		return $result;
	}
	
	/*+++++++++++++++++++++更新map END++++++++++++++++++++*/
	
	/*+++++++++++++++++++++++更新msg++++++++++++++++++++++*/
	
	/**
	 * 将push message信息写入redis
	 * @param string $key
	 * @param array $data
	 * @return boolean
	 * @author wangjian
	 * @version 2014-05-27
	 */
    public function insertMessage($key , $data) {
        $message_arr = array();
        $message_arr['timestamp'] = isset($data['timestamp']) ? $data['timestamp'] : time();
        $message_arr['push_msg_id'] = isset($data['push_msg_id']) ? $data['push_msg_id'] : '';
        $message_arr['push_distinct_id'] = isset($data['push_distinct_id'])
            ? $data['push_distinct_id'] : '';
        $message_arr['queue_id'] = isset($data['queue_id']) ? $data['queue_id'] : '';
        $message_arr['type'] = isset($data['type']) ? $data['type'] : '';
        $message_arr['content'] = isset($data['content'])
            ? json_encode($data['content']) : json_encode(array());
        $message_arr['timeout'] = isset($data['timeout']) ? $data['timeout'] : '';

        $message_arr['driver_id'] = isset($data['driver_id']) ? $data['driver_id'] : '';
        $message_arr['offline_time'] = isset($data['offline_time']) ? $data['offline_time'] : '';
        $message_arr['client_id'] = isset($data['client_id']) ? $data['client_id'] : '';

        $token_key = $this->_message_log_key.$key;
        $this->redis->hMset($token_key , $message_arr);
        $this->redis->expire($token_key, self::PUSH_MSG_EXPIRE);

        return true;
    }
    
    /**
	 * 更新Message信息
	 * @param string $key
	 * @param array $data
	 * @return boolean
	 * @author AndyCong<congming@edaijia-staff.cn>
	 * @version 2013-12-30
	 */
    public function updateMessage($key , $data) {
        $message_arr = array();
		$token_key = $this->_message_log_key.$key;
		foreach ($data as $feild=>$val) {
			$message_arr[$feild] = $val;
			$this->redis->hset($token_key , $feild , $val);
		}
		return true;
    }
    
    /**
	 * 获取Message信息
	 * @param string $key
	 * @param string $feild
	 * @return array
	 * @author AndyCong<congming@edaijia-staff.cn>
	 * @version 2013-12-30
	 */
    public function getMessage($key , $field = '') {
    	$result = '';
		$token_key = $this->_message_log_key.$key;
		if (!empty($field)) {
			$result = $this->redis->hget($token_key , $field);
		} else {
			$result = $this->redis->hgetall($token_key);
		}
		return $result;
    }

    /**
     * 清除Message信息
     * @param string $key
     * @return boolean
     * @author wangjian<wangjian@edaijia-staff.cn>
     * @version 2014-07-01
     */
    public function delMessage($key) {
        $token_key = $this->_message_log_key.$key;
        $ret = $this->redis->del($token_key);
	return $ret;
    }

    /**
     * 检查Message信息是否存在
     * @param string $key
     * @return boolean
     * @author wangjian<wangjian@edaijia-staff.cn>
     * @version 2014-07-01
     */
    public function existsMessage($key) {
        $token_key = $this->_message_log_key.$key;
        if ($this->redis->exists($token_key)) {
            return true;
	}
	else {
            return false;
	}

	return false;
    }

    
	/*+++++++++++++++++++++更新msg END++++++++++++++++++++*/
	
	
	/**
	 * 写入redis
	 * @param string $key
	 * @param array $data
	 * @return boolean
	 */
	public function insert($key , $data) {
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
		
		
		
		$this->redis->hMset($token_key , array(
		    'queue_id' => $queue_id,
		    'booking_id' => $booking_id,
		    'booking_type' => $booking_type,
		    'booking_time' => $booking_time,
		    'created_time' => $created_time,
		    'contact_phone' => $contact_phone,
		    'city_id' => $city_id,
		    'number' => $number,
		    'address' => $address,
		    'lng' => $lng,
		    'lat' => $lat,
		    'google_lng' => $google_lng,
		    'google_lat' => $google_lat,
		    'flag' => $flag,
		    'orders' => json_encode($orders),
		));
		
		//加上随机过期时间，看是否好使。add by sunhongjing 2013-12-21
		$rand_time = rand(4000, 20000);
		$expire_time = $rand_time + $this->_gap;	
		$this->redis->expire($token_key , $expire_time );

        self::gather_phone_key($token_key);
		
		return true;
	}

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
     * 选司机下单重试
     * @param int $order_number
     * @return string
     * @version 2014-06-18
     */
    public function single_push_can_retry($order_number) {
        $key = $this->_single_push_retry_pre.$order_number;
	//为了保证可以起多个job,取值的同时设置为0
        $retry_info = $this->redis->getset($key, 'done');
        $this->redis->del($key);
        if(!empty($retry_info) && $retry_info != 'done') {
            return $retry_info;
        }
        else {
            return '';
        }
    }

    /**
     * 选司机下单记录重试信息
     * @param int $order_number
     * @return boolean
     * @version 2014-06-18
     */
    public function single_push_retry_record($order_number, $params) {
        $key = $this->_single_push_retry_pre.$order_number;
        $info = json_encode($params);
        $retry_info = $this->redis->set($key, $info);
        $this->redis->expire($key, $this->_single_push_retry_gap);
    }

    /**
	 * 获取相应field的值
	 * @param string $key1
	 * @param unknown_type $key2
	 * @return unknown
	 */
	public function setGroup($key , $field, $value) {
		$token_key = $this->_order_group_user_key.$key;
		$value = json_encode($value);
		$this->redis->hset($token_key , $field, $value);
		return true;
	}

	/**
	 * 获取相应field的值
	 * @param string $key1
	 * @param unknown_type $key2
	 * @return unknown
	 */
	public function getGroup($key , $field) {
		$token_key = $this->_order_group_user_key.$key;
		$data = $this->redis->hget($token_key , $field);
		if (!empty($data)) {
			$data = json_decode($data , true);
		}
		return $data;
	}

}
