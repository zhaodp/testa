<?php

class NewCustomerFreeActivityRedis extends CRedis {

    public $host = 'redishaproxy.edaijia-inc.cn';

    public $port = 22121;

    private static $CITY_PREFIX = 'activity_newCustomerFree_city_';

    private static $ORDER_PREFIX = 'activity_newCustomerFree_order_';

    private static $TEST_PHONE_PREFIX = 'activity_newCustomerFree_test_phone_';

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
        if($this->redis->exists(self::$CITY_PREFIX . $city_id)) {
            $activity = $this->redis->get(self::$CITY_PREFIX . $city_id);
            return json_decode($activity, true);
	}
	else {
            return null;
        }
    }

    public function isTestPhone($phone) {
        $result = $this->redis->get(self::$TEST_PHONE_PREFIX . $phone);
        return !empty($result);
    }

    public function set($city_id, $activity) {
        $this->redis->set(self::$CITY_PREFIX . $city_id, json_encode($activity));
    }

    public function setOrderActivity($order_id) {
        $this->redis->setex(self::$ORDER_PREFIX . $order_id, self::$ORDER_KEY_EXPIRE, 1);
    }

    public function getOrderActivity($order_id) {
        return $this->redis->get(self::$ORDER_PREFIX . $order_id);
    }
}
