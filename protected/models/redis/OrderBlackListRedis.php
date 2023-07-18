<?php

/**
 * 订单流维护的blacklist，满足blacklist中的条件(phone, udid)都不允许下单
 *
 */
class OrderBlackListRedis extends CRedis {

    public $host = 'redishaproxy.edaijia-inc.cn';

    public $port = 22121;

    private static $CONSTANT_PHONE_PREFIX = 'order_blacklist_constant_phone_';
    
    private static $MALICIOUS_CANCEL_PHONE_PREFIX = 'order_blacklist_malicious_cancel_phone_';
    
    private static $MALICIOUS_CANCEL_UDID_PREFIX = 'order_blacklist_malicious_cancel_udid_';
    
    public static function model($className = __CLASS__) {
        $model = null;
        if (isset (self::$_models[$className]))
            $model = self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }
    
    public function isInBlacklist($phone, $udid = null) {
        return $this->isPhoneInBlacklist($phone) || $this->isUdidInBlacklist($udid);
    }
    
    public function isPhoneInBlacklist($phone) {
        return $this->isPhoneInConstantList($phone) || $this->isPhoneInMaliciousCancelList($phone);
    }
    
    public function isPhoneInConstantList($phone) {
        $key = $this->getConstantPhoneKey($phone);
        if(empty($key)) {
            return false;
        }
        
        $result = $this->redis->sIsMember($key, $phone);
        return empty($result) ? false : true;
    }
    
    public function isPhoneInMaliciousCancelList($phone) {
        if(empty($phone)) {
            return false;
        }
        $result = $this->redis->get(self::$MALICIOUS_CANCEL_PHONE_PREFIX . $phone);
        return empty($result) ? false : true;
    }
    
    public function isUdidInBlacklist($udid) {
        return $this->isUdidInMaliciousCancelList($udid);
    }
    
    public function isUdidInMaliciousCancelList($udid) {
        if(empty($udid)) {
            return false;
        }
        
        $result = $this->redis->get(self::$MALICIOUS_CANCEL_UDID_PREFIX . $udid);
        return empty($result) ? false : true;
    }
    
    public function addConstantPhone($phone) {
        $key = $this->getConstantPhoneKey($phone);
        if(empty($key)) {
            return false;
        }
        return $this->redis->sAdd($key, $phone);
    }
    
    public function removeConstantPhone($phone) {
        $key = $this->getConstantPhoneKey($phone);
        if(empty($key)) {
            return false;
        }
        return $this->redis->sRem($key, $phone);
    }
    
    public function addMaliciousCancel($params) {
        if(isset($params['timeout']) && is_numeric($params['timeout'])) {
            $timeout = $params['timeout'];
        } else {
            $timeout = -1;
        }
        
        if(isset($params['phone'])) {
            $this->redis->setex(self::$MALICIOUS_CANCEL_PHONE_PREFIX . $params['phone'], $timeout, 1);
        }
        if(isset($params['udid'])) {
            $this->redis->setex(self::$MALICIOUS_CANCEL_UDID_PREFIX . $params['udid'], $timeout, 1);
        }
    }
    
    private function getConstantPhoneKey($phone) {
        if(empty($phone)) {
            return null;
        }
        
        return self::$CONSTANT_PHONE_PREFIX . substr($phone, -1, 1);
    }
}
