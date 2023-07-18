<?php

class UserNotifyRedis extends CRedis {

    public $host = 'redishaproxy.edaijia-inc.cn';

    public $port = 22121;

    private static $USER_NOTIFY_PREFIX = 'user_notify_push_';
    private static $USER_NOTIFY_IMM_PREFIX = 'user_notify_imm_push_';

    public static function model($className = __CLASS__) {
        $model = null;
        if (isset ( self::$_models [$className] ))
            $model = self::$_models [$className];
        else {
            $model = self::$_models [$className] = new $className ( null );
        }
        return $model;
    }

    public function getUserNotify($phone,$userNotifyId) {
        $rkey = $this->getRKey($phone,$userNotifyId);
        if($this->redis->exists($rkey)) {
            $rval = $this->redis->get($rkey);
            return $rval;
	    }else {
            return null;
        }
    }

    private function getRKey($phone,$userNotifyId){
        return self::$USER_NOTIFY_PREFIX.$phone."_".$userNotifyId;
    }

    public function setUserNotify($phone,$userNotifyId) {
        $rkey = $this->getRKey($phone,$userNotifyId);
        $this->redis->set($rkey, time());
    }



    public function getImmUserNotify($phone,$userNotifyId) {
        $rkey = $this->getImmRKey($phone,$userNotifyId);
        if($this->redis->exists($rkey)) {
            $rval = $this->redis->get($rkey);
            return $rval;
        }else {
            return null;
        }
    }

    private function getImmRKey($phone,$userNotifyId){
        return self::$USER_NOTIFY_IMM_PREFIX.$phone."_".$userNotifyId;
    }

    public function setImmUserNotify($phone,$userNotifyId) {
        $rkey = $this->getImmRKey($phone,$userNotifyId);
        $this->redis->set($rkey, time());
    }

}
