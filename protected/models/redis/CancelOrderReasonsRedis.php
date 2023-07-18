<?php

class CancelOrderReasonsRedis extends CRedis {

    public $host = 'redishaproxy.edaijia-inc.cn';

    public $port = 22121;

    private static $CANCEL_RECEIVED_ORDER_REASONS = 'cancel_received_order_reasons_config';
    private static $CANCEL_READY_ORDER_REASONS = 'cancel_ready_order_reasons_config';

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

    public function getReceivedOrderCancelReasons() {
        if($this->redis->exists(self::$CANCEL_RECEIVED_ORDER_REASONS)){ 
            $reasons = $this->redis->get(self::$CANCEL_RECEIVED_ORDER_REASONS);
            return json_decode($reasons, true);
        }
        else {
            return null;
        }
    }

    public function getReadyOrderCancelReasons() {
        if($this->redis->exists(self::$CANCEL_READY_ORDER_REASONS) )  {
            $reasons = $this->redis->get(self::$CANCEL_READY_ORDER_REASONS );
            return json_decode($reasons, true);
        }
        else {
            return null;
        }
    }

    public function setReceivedOrderCancelReasons($reasons) {
        $this->redis->set(self::$CANCEL_RECEIVED_ORDER_REASONS, json_encode($reasons));
    }

    public function setReadyOrdereCancelReasons($reasons) {
        $this->redis->set(self::$CANCEL_READY_ORDER_REASONS, json_encode($reasons));
    }

}
