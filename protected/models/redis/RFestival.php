<?php

/**
 * 春节回家活动缓存
 * Class RFestival
 */
class RFestival extends CRedis
{

    public $host = 'redis02n.edaijia.cn'; //10.161.174.78 redis02n.edaijia.cn

    public $port = 6379;
    public $password = 'k74FkBwb7252FsbNk2M7';

    protected static $_models = array();

    private $festival_customer = 'FESTIVAL_CUSTOMER_';//春节参与司机东回家活动用户

    private $festivel_lock = 'FESTIVAL_LOCK_';

    private $come_on = 'COME_ON_';//加油记录

    private $come_on_num = 'COME_ON_NUM_';//加油记录

    private $expire_time = 5184000;//缓存2个月

    public static function model($className = __CLASS__)
    {
        $model = null;
        if (isset(self::$_models[$className]))
            $model = self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }

    /**
 * 设置春节参加司机送回家活动的验证码
 * @param $phone
 * @return bool|int
 */
    public function setFestivalCustomerCode($phone, $code)
    {
        $key = $this->festival_customer . $phone;
        $ret = $this->redis->set($key, $code);
        if(!$ret){
            return false;
        }
        $this->redis->expire($key, 300);//设置缓存300秒
        return true;
    }

    /**
     * 获取春节参加司机送回家活动的验证码
     * @param $phone
     * @return bool|int
     */
    public function getFestivalCustomerCode($phone)
    {
        $key = $this->festival_customer . $phone;
        $code = $this->redis->get($key);
        if(!$code){
            return false;
        }
        return $code;
    }

    /**
     * 点击加油时加锁
     * @param $phone
     * @param $friend_phone
     * @return bool
     */
    public function setLock($phone,$friend_phone)
    {
        $key = $this->festivel_lock . $phone.'_'.$friend_phone;
        $ret = $this->redis->setnx($key,1);
        return $ret;
    }

    /**
     * 判断好友是否为用户点过加油
     * @param $phone
     * @param $friend_phone
     * @return mixed
     */
    public function existsComeonRecord($phone,$friend_phone)
    {
        $key = $this->come_on . $phone.'_'.$friend_phone;
        $ret = $this->redis->exists($key);
        return $ret;
    }

    /**
     * 保存加油记录到缓存,返回最新加油次数
     * @param $phone
     * @param $friend_phone
     * @return mixed
     */
    public function comeon($phone,$friend_phone)
    {
        $key = $this->come_on . $phone.'_'.$friend_phone;
        $expire_time = $this->expire_time;
        $ret = $this->redis->setex($key, $expire_time, 1);
        $come_num_key = $this->come_on_num.$phone;
        $num = $this->redis->incr($come_num_key);
        return $num;
    }
}
