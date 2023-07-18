<?php

/**
 * 从Redis中读取ApiKey，或从Mysql中载入ApiKey到Redis
 *
 * @author syang
 */
class RApiKey extends CRedis {
    public $host='redis01n.edaijia.cn'; //10.132.17.218
    public $port=6379;
    public $password='k74FkBwb7252FsbNk2M7';

    private $_cache_key = 'api_keys';

    protected static $_models=array();

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
     * 获得key值
     * @param string $appkey
     * @return mixed if key not exists was false
     */
    public function key($appkey) {
	$keys  = $this->loadKeys();
	return isset($keys[$appkey]) ? $keys[$appkey] : false;
    }

    /**
     * 加载loadKeys from redis
     * @return type
     */
    private function loadKeys() {
	$keys = array();
	$cache_key = md5($this->_cache_key);
	$strings = $this->redis->get($cache_key);
	$keys = @json_decode($strings, true);

	if ( $keys === false || is_null($keys)) return array();
	else return $keys;
    }

    /**
     * from mysql read keys and save to redis
     */
    public function reloadKeys() {
	$new_keys = array();
	$keys = ApiKey::model()->findAll();
	foreach($keys as $key)
	    $new_keys[$key->appkey] = array (
		'secret'=>$key->secret, 
		'enable'=>$key->enable
	    );

	$string = @json_encode($new_keys);
	$cache_key = md5($this->_cache_key);
	$this->redis->set($cache_key, $string);

    }
}

?>
