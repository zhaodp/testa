<?php

/**
 * 从Redis中读取access_token，或向redis写入access_token, 时间1天
 *
 * @author syang
 */
class RAuthToken extends CRedis {
    public $host='redis01n.edaijia.cn'; //10.132.17.218
    public $port=6379;
    public $password='k74FkBwb7252FsbNk2M7';

    private $_cache_key = 'AUTH_TOKEN_';

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
     * 保存access_token
     * @param string $access_token
	 * @param string $app_key
	 * @param string $app_secret
     * @return boolean true
     */
    public function save($accessToken, $params) {
		$cacheKey = $this->_cache_key.$accessToken;
		
		$value = json_encode($params);
		$this->redis->set($cacheKey, $value);
		
		//对队，入库
		$task = array(
		    'method' => 'app_auth_token',
		    'params' => $params,
		);
		Queue::model()->task($task);
		//对队，入库 END
		return true;
    }

    /**
     * 获得auth access_token
     * @return boolean 
     */
    public function get($accessToken) {
		$cacheKey = $this->_cache_key.$accessToken;	
		$value=$this->redis->get($cacheKey);	
		if ($value!==FALSE) {
			return json_decode($value, true);
		}
		return false;
    }

}

?>
