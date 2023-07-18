<?php
/**
 * 测试redis
 * @author sunhongjing
 * @version 2013-11-30
 */
class ShjTest extends CRedis {
	public $host='redis01n.edaijia.cn';
	public $port=6379;
	public $password='k74FkBwb7252FsbNk2M7';
	private $_prefix = 'sunhongjing_test_';
	private $_gap=43200; //缓存12小时
//	protected static $_key = null;

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
	public function insert($key , $filed, $data) {
		$token_key = $this->_prefix.$key;
		$this->redis->hset($token_key , $filed , $data);
		$this->redis->expire($token_key , $this->_gap);	
		return true;
	}
	
	/**
	 * 更新相应field的值
	 * @param string $key
	 * @param string $field
	 * @param string/array $data
	 */
	public function update($key , $field , $data) {
		$token_key = $this->_prefix.$key;
		if ($this->redis->exists($token_key)) {
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
	 * 删除对应key数据
	 * @param string $key
	 * @return boolean
	 */
	public function delete($key) {
		$token_key= $this->_prefix.$key;
		$ret = $this->redis->del($token_key);
		return true;
	}
	
}

