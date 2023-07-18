<?php

Yii::import('application.models.customer.VipRecord');

class VipRecordRedis extends CRedis {

    public $host = 'redis01n.edaijia.cn'; //10.132.17.218
    public $port = 6379;
    public $password = 'k74FkBwb7252FsbNk2M7';
    protected static $_models = array();

    const KEY_VIP_RECORD_IN_THIS_WEEK = 'vip:record_in_this_week';              //本周跟进的vipid集合
    const KEY_VIP_RECORD_IN_THIS_WEEK_TIME = 'vip:record_in_this_week_time';    //KEY_VIP_RECORD_IN_THIS_WEEK对应的是哪一周

    public static function model($className = __CLASS__) {
        $model = null;
        if (isset(self::$_models[$className])){
            $model = self::$_models[$className];
        }else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }

    /**
     * 存储本周内跟进过的vip的id
     * @param <string> $vipId
     * @return <bool>       添加的数量
     */
    public function addLastRecordToCache($vipId) {
        $time = time();
        $key = self::KEY_VIP_RECORD_IN_THIS_WEEK;
        $keyTime = self::KEY_VIP_RECORD_IN_THIS_WEEK_TIME;
        $ttl = mktime(0, 0, 0, date('n', $time), ((date('j', $time) + 1) - date('N', $time)), date('Y', $time)) + 86400 * 7;
        if (!$this->redis->exists($keyTime)) {
            $this->redis->delete($key);
            $this->redis->set($keyTime, '1');
            $this->redis->expireAt($keyTime, $ttl);
        }
        return $this->redis->sAdd($key, $vipId);
    }

    /**
     * 获取本周内跟进过的vipid
     * @return <array>      vipid组成的数组
     */
    public function getLastRecordThisWeekVipIds() {
        $key = self::KEY_VIP_RECORD_IN_THIS_WEEK;
        $keyTime = self::KEY_VIP_RECORD_IN_THIS_WEEK_TIME;
        if (!$this->redis->exists($keyTime)) {
            $this->redis->delete($key);
        }
        $members = $this->redis->sMembers($key);
        return $members;
    }

}
