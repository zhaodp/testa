<?php
class RedisHAProxy02 extends CRedis {

    public $host = 'redishaproxy.edaijia-inc.cn';
//    public $host = 'redisbaseproxy.edaijia-inc.cn';

    public $port = 22121;

    public static function model($className = __CLASS__) {
        $model = null;
        if (isset ( self::$_models [$className] ))
            $model = self::$_models [$className];
        else {
            $model = self::$_models [$className] = new $className ( null );
        }
        return $model;
    }

    public function set($cache_key,$value,$expire_time = '') {
        $res = $this->redis->set($cache_key , $value);
	    if (intval($expire_time) > 0) {
            $res = $this->redis->expire($cache_key, intval($expire_time));
	    }
        return $res;
    }

    public function get($cache_key){
        $value = $this->redis->get($cache_key);
        if(empty($value)){
            return false;
        }
        return $value;
    }

    public function del($cache_key) {
        $res = $this->redis->del($cache_key);
        return $res;
    }


    public function hget($cache_key,$sub_key){
        $value = $this->redis->hget($cache_key,$sub_key);
        if(empty($value)){
            return false;
        }
        return $value;
    }

    public function hset($cache_key, $sub_key, $value, $expire_time){
        $value = $this->redis->hset($cache_key, $sub_key, $value);
        $this->redis->expire($cache_key,$expire_time);
        if(empty($value)){
            return false;
        }
        return $value;
    }




}
