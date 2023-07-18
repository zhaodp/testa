<?php
class ArriveInTimeActivityRedis extends CRedis {
    
    public $host = 'redishaproxy.edaijia-inc.cn';
    
    public $port = 22121;
    
    private static $CITY_PREFIX = 'activity_arriveInTime_city_';
    
    private static $ORDER_PREFIX = 'activity_arriveInTime_order_';
    
    private static $PHONE_PREFIX = 'activity_arriveInTime_phone_'; 
    
    private static $ORDER_KEY_EXPIRE = 604800; // 7 days
    
    public static function model($className = __CLASS__) {
        $model = null;
        if (isset ( self::$_models [$className] ))
            $model = self::$_models [$className];
        else {
            $model = self::$_models [$className] = new $className ( null );
        }
        return $model;
    }
    
    public function getByCity($city_id) {
        $activity = $this->redis->get(self::$CITY_PREFIX . $city_id);
        if ($activity) {
            return json_decode($activity, true);
        }
        return $activity;
    }
    
    public function setContentSent($phone, $expire = null) {
        if (empty($expire)) {
            $this->redis->set(self::$PHONE_PREFIX . $phone, 1);
        } else {
            $this->redis->setex(self::$PHONE_PREFIX . $phone, $expire, 1);
        }
    }
    
    public function isContentSent($phone) {
        $result = $this->redis->get(self::$PHONE_PREFIX . $phone);
        return !empty($result);
    }
    
    public function set($city_id, $activity) {
        $this->redis->set(self::$CITY_PREFIX . $city_id, json_encode($activity));
    }
    
    public function setOrderActivity($order_id, $params) {
        $this->redis->setex(self::$ORDER_PREFIX . $order_id, self::$ORDER_KEY_EXPIRE, json_encode($params));
    }
    
    public function getOrderActivity($order_id) {
        $orderActivity = $this->redis->get(self::$ORDER_PREFIX . $order_id);
        if (!empty($orderActivity)) {
            return json_decode($orderActivity, true);
        }
        return null;
    }
}