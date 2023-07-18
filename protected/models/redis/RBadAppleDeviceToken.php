<?php

class RBadAppleDeviceToken extends CRedis {
    public $host = 'redishaproxy.edaijia-inc.cn';
    public $port = 22121;
    
    private static $BAD_APPLE_DEVICE_TOKEN_PREFIX = "_bad_apple_token_";
    //60 * 60 * 24 * 30
    private static $EXPIRE_TIME = 2592000;
    
    public static function model($className = __CLASS__) {
        $model = null;
        if (isset ( self::$_models [$className] ))
            $model = self::$_models [$className];
        else {
            $model = self::$_models [$className] = new $className ( null );
        }
        return $model;
    }

    /**
     * 判断是否为坏的苹果设备码.
     * 
     * @param string $device_token
     * @return boolean
     */
    public function isBadDeviceToken($device_token) {
        if (!isset($device_token) || empty($device_token)) {
            return false;
        }
        
        return $this->redis->exists(self::$BAD_APPLE_DEVICE_TOKEN_PREFIX . $device_token);
    }
    
    public function addNewBadDeviceToken($device_token) {
        if (!isset($device_token) || empty($device_token)) {
            return;
        }
        
        $key = self::$BAD_APPLE_DEVICE_TOKEN_PREFIX . $device_token;
        $this->redis->set($key, '1');
        $this->redis->expire($key, self::$EXPIRE_TIME);
    }
}