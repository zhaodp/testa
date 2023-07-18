<?php
class QueuePool {

    protected static $_models=array();
    protected $redis_pool = array();

    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
               $model=self::$_models[$className];
        else {
               $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }

    public function __construct() {
        self::init();
    }

    public function __destruct() {
        if(!empty($this->redis_pool)) {
            foreach($this->redis_pool as $name => $instance) {
                $this->redis_pool[$name]->close();
                unset($this->redis_pool[$name]);
            }
        }
    }

    public function init() {

        $this->redis_pool = array();
        $zones = Yii::app()->params['queue_pool']['zones'];

        foreach($zones as $zone => $config) {
            $_redis = new Redis();
            $_redis->pconnect($config['host'], $config['port']);
            if(!empty($config['password'])) {
                $_redis->auth($config['password']);
            }
            $this->redis_pool[$zone] = $_redis;
        }

        return $this;
    }

    public function get_zone($queue_type) {
        $zones = Yii::app()->params['queue_pool']['zones'];
        $zone = $this->get_my_zone($queue_type);
        if(!empty($zone)) {
            $ret = array(
                'name'  => $zone,
                'redis' => $this->redis_pool[$zone],
            );

            if(isset($zones[$zone]['brpop'])) {
                $ret['brpop'] = $zones[$zone]['brpop'];
            }
            else {
                $ret['brpop'] = false;
            }

            return $ret;
        }
        else {
            return false;
        }
    }

    private function get_my_zone($queue_type) {
        $zones = Yii::app()->params['queue_pool']['zones'];
        $zone = 'default';
        foreach($zones as $name => $set) {
            if(!empty($set['set']) && in_array($queue_type, $set['set'])) {
                return $name;
            }
        }

        return $zone;
    }
}
