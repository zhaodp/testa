<?php
/**
 * 
 * 验证码缓存
 * @author cuiluzhe 2014-9-16
 *
 */
class RVerifyCode extends CRedis {
	public $host='redis03n.edaijia.cn';
	public $port=6379;
	public $password='k74FkBwb7252FsbNk2M7';
	protected static $_models=array();
	
	public $_prefix = 'verify_code_';
	const EXPIRE_TIME = 300;//验证码有效时间5分钟 

	
	public static function model($className=__CLASS__) {
		$model=null;
		if (isset(self::$_models[$className]))
			$model=self::$_models[$className];
		else {
			$model=self::$_models[$className]=new $className(null);
		}
		return $model;
	}

	public function set($cache_key,$value){
	    $cache_key = $this->_prefix.$cache_key;
	    $this->redis->set($cache_key , $value);
	    $this->redis->expire($cache_key, self::EXPIRE_TIME);
	    return true;
	}

	public function get($cache_key){
            $cache_key = $this->_prefix.$cache_key;
	    if($this->redis->exists($cache_key)) {
                $value = $this->redis->get($cache_key);
	        if(empty($value)){
		    return false;
	        }
	        return $value;
	    }
	    return false;
        }

}
