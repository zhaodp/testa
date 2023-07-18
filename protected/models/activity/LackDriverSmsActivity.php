<?php

class LackDriverSmsActivity extends CRedis {
    public $host = 'redishaproxy.edaijia-inc.cn';
    public $port = 22121;

    private static $_lock_prefix = 'LackDriverSmsPhone_';
    private static $_config_key = 'LackDriverSmsConfig';

    public static function model($className = __CLASS__) {
        $model = null;
        if (isset (self::$_models[$className] ))
            $model = self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }

    private static $_config = array(
        'bonus_sn'   => '88888',
        'start_date' => '2015-02-10 00:00:00',
        'end_date'   => '2015-03-04 00:00:00',
        'turn_on'    => 1
    );

    public function run($params) {
        if(empty($params) || empty($params['phone'])) {
            EdjLog::error(__METHOD__."|Error|phone is empty");
            return;
        }

        $config = $this->get_config();

        if($this->is_active($config) && !empty($config['bonus_sn'])) {
            $gap = strtotime($config['end_date']) - time();
            if($this->check_phone($params['phone'], $gap)) {
                $ret = BonusLibrary::model()->bonusBindingSubsidy($config['bonus_sn'], $params['phone']);
                if(!empty($ret) && isset($ret['code']) && $ret['code'] == 0) {
                    EdjLog::info(__METHOD__."|success|binging bonus "
                        .$config['bonus_sn']." to phone ".$params['phone']);
                }
                else {
                    EdjLog::info(__METHOD__."|failed|binging bonus "
                        .$config['bonus_sn']." to phone ".$params['phone']);
                }
            }
            else {
                EdjLog::info(__METHOD__."|done before ".$params['phone']);
            }
        }
    }

    public function get_config() {
        $config = $this->redis->get(self::$_config_key);

        if(!empty($config)) {
            return json_decode($config, true);
        }

        EdjLog::info("config no found in redis, read it from initial config");

        $config = $this->set_config();

        return $config;
    }

    public function set_config($params=array()) {

        if(empty($params)) {
            $config = self::$_config;
        }
        else {
            $config = $params;
        }

        $this->redis->set(self::$_config_key, json_encode($config));

        return $config;
    }

    private function is_active($config) {
        if (!$config['turn_on']) {
            return false;
        }
        $now = time();

        return strtotime($config['start_date']) <= $now && $now <= strtotime($config['end_date']);
    }

    private function check_phone($phone, $gap) {
        $key = self::$_lock_prefix.$phone;

        $ret = $this->redis->setnx($key, time());
        $this->redis->expire($key, $gap);

        return $ret;
    }
}

?>
