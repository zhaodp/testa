<?php
/**
 * 将司机电话载入redis
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-07-05
 */
class DriverPhoneRedis extends CRedis {
	public $host='cache02n.edaijia.cn';
	public $port=6379;
	public $password='k74FkBwb7252FsbNk2M7';
	private $_keypre = 'DRIVER_PHONE_CACHE_';
	private $_gap = 300;
	
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
	 * 将司机电话号做key载入redis
	 * @param array $driver
	 */
	public function insert($driver){
		if (empty($driver['phone']) || empty($driver['driver_id'])) {
			return false;
		}
		$cache_key = $this->_keypre.$driver['phone'];
		$this->redis->set($cache_key, $driver['driver_id']);
		return true;
	}
	
	/**
	 * 通过司机电话号获取司机工号$driver_id
	 * @param string $phone
	 * @return boolean
	 */
	public function getByPhone($phone) {
		$cache_key = $this->_keypre.$phone;
		$driver_id = $this->redis->get($cache_key);
		if (!empty($driver_id)) {
			return true;
		}
		return false;
	}
	/**
	 * 将电话号从redis中删除
	 * @param string $phone
	 */
	public function delete($phone){
		$cache_key = $this->_keypre.$phone;
		$ret = $this->redis->del($cache_key);
		return $ret;
	}
	
	/**
	 * 清除队列全部司机
	 */
	public function clean(){
		$cache_key = $this->_keypre."*";
		$drivers = $this->redis->keys($cache_key);
		foreach($drivers as $key){
			$this->redis->del($key);
		}
		return true;
	}
	
	/**
	 * 返回全部队列司机名单
	 */
	public function showall(){
		$cache_key = $this->_keypre."*";
		$drivers = $this->redis->keys($cache_key);		
		return $drivers;
	}
	
}