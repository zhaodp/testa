<?php
/**
 * 自动派单,订单队列,锁住已派订单90秒
 * @author sunhongjing 2013-06-20
 *
 */
class QueueDispatchOrder extends CRedis {
	
	public $host='redis03n.edaijia.cn'; 
	public $port=6379;
	public $password='k74FkBwb7252FsbNk2M7';
	private $_queue_prefix = 'ORDER_QUEUE_DISPATCH_';
	private $_queue_count_prefix = 'COUNT_ORDER_QUEUE_DISPATCH_';
	private $_gap = 45;
	
	private $_queue_no_driver_prefix = 'NEARBY_NO_DRIVERS_';  //无司机计数前缀
	private $_no_drivers_gap = 1200;  //无司机计数过期时间
	
	private $_queue_drivers_locked_prefix = 'NEARBY_DRIVERS_LOCKED_';  //司机锁定计数前缀
	private $_drivers_locked_gap = 1200;  //司机锁定计数过期时间
	
	private $_queue_dispatched_driver = 'QUEUE_DISPATCHED_DRIVER_';  //queue_id派过的司机
	private $_queue_dispatched_driver_gap = 90;
	
	//强制推送设置
	private $_queue_enforce_push_once_again = 'ENFORCE_PUSH_ONCE_AGAIN_';
	private $_enforce_gap = 150;

	// For remote order
	private $_queue_remoteorder_fee_prefix = 'ORDER_QUEUE_FEE_KEY_';
	private $_queue_remoteorder_fee_gap    = '12000';
	private $_queue_remoteorder_driverdist_prefix = 'ORDER_QUEUE_DRIVERDIST_KEY_';
	private $_queue_remoteorder_driverdist_gap    = '12000';

    //增加优惠券缓存
    private $_queue_bonus_bind_prefix = 'ORDER_QUEUE_BONUS_BIND_';
    private $_queue_bonus_bind_gap    = '12000';
	
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
	 * @author qiujianping@edaijia-staff.cn 2014-04-16
	 *  Check if an order is locked
	 * @param string $order_id
	 * @return bool
	 */
	public function exist($order_id){
		$token_key= $this->_queue_prefix.$order_id;
		
		if ($this->redis->exists($token_key)) {
			$timestamp=$this->redis->get($token_key);
			if((time() - $timestamp)>$this->_gap){
				return 0;
			}else{
				return 1;
			}
		} else {
		  return 0;
		}
	}
	
	/**
	 * 将派单队列放入锁定池
	 * 
	 * @author sunhongjing
	 * @param string $queue_id
	 * @return bool
	 */
	public function insert($queue_id){
		$token_key= $this->_queue_prefix.$queue_id;
		
		if ($this->redis->exists($token_key)) {
			$timestamp=$this->redis->get($token_key);
			if((time() - $timestamp)>$this->_gap){
				return 1;
			}else{
				return 0;
			}
		}
		
		$this->redis->set($token_key, time());
		$this->redis->expire($token_key, $this->_gap);
		return 1;
	}
	
	/**
	 * 从派单队列锁定池中移除订单
	 * @author sunhongjing
	 * @param string $queue_id
	 * @return bool
	 */
	public function delete($queue_id){
		$token_key= $this->_queue_prefix.$queue_id;
		
		$ret = $this->redis->del($token_key);
		return $ret;
	}
	
	/**
	 * 清除队列
	 * 
	 * @author sunhongjing
	 * @return bool
	 */
	public function clean(){
		$token_key= $this->_queue_prefix.'*';
		
		$queue_list = $this->redis->keys($token_key);
		foreach($queue_list as $token){
			$this->redis->del( $token );
		}
		return true;
	}
	
	/**
	 * 返回锁定池中的所有订单
	 */
	public function showall(){
		$token_key = $this->_queue_prefix.'*';
		
		$queue_list = $this->redis->keys($token_key);		
		return $queue_list;
	}

    /**
     * 获得派单次数
     * @param $queue_id
     * @return int
     */
    public function dispatchGetQueueCount($queue_id){
        $token_key= $this->_queue_count_prefix.$queue_id;
        if ($this->redis->exists($token_key)) {
            return $this->redis->get($token_key);
        }
        return 0;
    }

    /**
     * 设置派单次数
     * @param $queue_id
     * @return bool
     */
    public function dispatchSetQueueCount($queue_id){
        $token_key= $this->_queue_count_prefix.$queue_id;
        if ($this->redis->exists($token_key)) {
            $this->redis->incr($token_key);
        } else {
            //初始化为1
            $this->redis->set($token_key, 1);
        }
        return true;
    }

    /**
     * 清除派单次数
     * @param $queue_id
     * @return bool
     */
    public function dispatchDeleteQueueCount($queue_id){
        $token_key= $this->_queue_count_prefix.$queue_id;
        if ($this->redis->exists($token_key)) {
            $this->redis->delete($token_key);
        }
        return true;
    }
    
    /**
     * 判定是否可以强制推送一次
     * @param int $queue_id
     * @return boolean
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-09-02
     */
    public function isDispatchMoreOnce($queue_id) {
    	$token_key = $this->_queue_enforce_push_once_again . $queue_id;
    	if ($this->redis->exists($token_key)) {
    	    return false;	
    	}
    	
    	$this->redis->set($token_key, time());
		$this->redis->expire($token_key, $this->_enforce_gap);
		return true;
    }
    
    /**
     * 附近没司机计数
     * @param int $queue_id
     * @return int
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-14
     */
    public function insertNoDriversCount($queue_id) {
    	$count = 0;
    	$token_key = $this->_queue_no_driver_prefix.$queue_id;
    	if ($this->redis->exists($token_key)) {
    		$count = $this->redis->get($token_key);
    	}
    	
		$count = $count + 1;
		$this->redis->set($token_key , $count);
		$this->redis->expire($token_key, $this->_no_drivers_gap);
    	
    	return $count;
    }
    
    /**
     * 司机全部锁定计数
     * @param int $queue_id
     * @return int
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-14
     */
    public function insertLockDriversCount($queue_id) {
    	$count = 0;
    	$token_key = $this->_queue_drivers_locked_prefix.$queue_id;
    	if ($this->redis->exists($token_key)) {
    		$count = $this->redis->get($token_key);
    	}
    	
		$count = $count + 1;
		$this->redis->set($token_key , $count);
		$this->redis->expire($token_key, $this->_drivers_locked_gap);
    	
    	return $count;
    }
    
    /**
     * 验证司机接接没接过这个订单
     * @param int $order_id
     * @param string $driver_id
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-02-26
     *
     * @modified qiujianping@edaijia-staff.cn 2014-04-14
     *  Change for a hash to just key value, only a driver
     *  is remembered
     */
    public function queueDispatchedDriver($order_id , $driver_id) {
        $token_key = $this->_queue_dispatched_driver.$order_id;
        
        //验证token_key有无设定值 没有需要设定过期时间 有则不需要设定过期时间 
	$this->redis->set($token_key , $driver_id);
	$this->redis->expire($token_key , $this->_queue_dispatched_driver_gap);
    }
    
    /**
     * 获取queue上次派过的司机
     * @param int $order_id
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-02-26
     */
    public function isDriverDispatched($order_id, $driver_id) {
    	$token_key = $this->_queue_dispatched_driver.$order_id;
	$value = $this->redis->get($token_key);
	if(!empty($value) && $value == $driver_id) {
	  return 1;
    	}
    	return 0;
    }

    /**
     * Save the fee for remote order in redis, If one wants to 
     * use it in other case, Double check it
     *
     * @param $queue_id
     * @param $fee
     * @return bool
     * 
     * @param qiujianping@edaijia-staff.cn
     * @created 2014-10-24
     */
    public function setQueueRemoteOrderFee($queue_id, $fee) {
	$token_key = $this->_queue_remoteorder_fee_prefix.$queue_id;
        $this->redis->set($token_key , $fee);
        $this->redis->expire($token_key, $this->_queue_remoteorder_fee_gap);
        return true;
    }

    /**
     * Get the fee for remote order in redis
     *
     * @param $queue_id
     * @return fee or fail
     * 
     * @param qiujianping@edaijia-staff.cn
     * @created 2014-10-24
     */
    public function getQueueRemoteOrderFee($queue_id) {
	$token_key = $this->_queue_remoteorder_fee_prefix.$queue_id;
        $fee = $this->redis->get($token_key);
        return $fee;
    }

    /**
     * Save the dist for drivers to customer for remote order in redis, 
     * If one wants to use it in other case, Double check it
     *
     * @param $order_id
     * @param $driver_id
     * @param $dis dispatch data
     * @return bool
     * 
     * @param qiujianping@edaijia-staff.cn
     * @created 2014-11-13
     */
    public function setOrderDriverDisData($order_id, $driver_id, $order_dis_data) {
	$token_key = $this->_queue_remoteorder_driverdist_prefix.$order_id;
        $this->redis->hset($token_key, $driver_id, json_encode($order_dis_data));
        $this->redis->expire($token_key, $this->_queue_remoteorder_driverdist_gap);
        return true;
    }

    /**
     * Get the dist for drivers to customer for remote order in redis, 
     * If one wants to use it in other case, Double check it
     *
     * @param $order_id
     * @param $driver_id
     * @return dis data
     * 
     * @param qiujianping@edaijia-staff.cn
     * @created 2014-11-13
     */
    public function getOrderDriverDisData($order_id, $driver_id) {
	$token_key = $this->_queue_remoteorder_driverdist_prefix.$order_id;
        $dis = $this->redis->hget($token_key, $driver_id);
	$ret_dis_data = json_decode($dis, true);
        return $ret_dis_data;
    }

    /**
     * @param $queue_id
     * @param $bonus_sn
     * @return bool
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-03-19
     */
    public function queueBonusBind($queue_id , $bonus_sn) {
	 $token_key = $this->_queue_bonus_bind_prefix.$queue_id;
        $this->redis->set($token_key , $bonus_sn);

        $this->redis->expire($token_key, $this->_queue_bonus_bind_gap);
        return true;
    }


    /**
     * @param $queue_id
     * @return mixed
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-03-19
     */
    public function getQueueBonus($queue_id) {

        $token_key = $this->_queue_bonus_bind_prefix.$queue_id;
        $bonus_sn  = $this->redis->get($token_key);

        return $bonus_sn;
    }

}
