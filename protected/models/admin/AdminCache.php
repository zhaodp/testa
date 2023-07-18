<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Bidong
 * Date: 13-5-29
 * Time: 下午5:35
 * To change this template use File | Settings | File Templates.
 */

class AdminCache  extends CRedis{
    private $redis = null;
    protected static $_models=array();

    const CACHE_MENU=1;


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
        $this->redis = RedisHAProxy::model()->redis;
    }

    public  function set($name,$key,$value) {
        if ($value!==null) {
            if (is_array($value)) {
                $value=json_encode($value);
            }
            $cacheKey='';
            switch ($name) {
                case self::CACHE_MENU :
                    //保存token
                    $cacheKey='ECENTER_MENU_'.$key;
                    break;
            }
            $this->redis->set($cacheKey, $value, 5*60);//5分钟过期
            return true;
        } else {
            return false;
        }
    }

    public  function get($name,$key){
        $cacheKey='';
        switch ($name) {
            case self::CACHE_MENU :
                //保存token
                $cacheKey='ECENTER_MENU_'.$key;
                break;
        }
       return $this->redis->get($cacheKey);
    }

    public  function delete($name,$key){
        $cacheKey='';
        switch ($name) {
            case self::CACHE_MENU :
                //保存token
                $cacheKey='ECENTER_MENU_'.$key;
                break;
        }
        return $this->redis->delete($cacheKey);
    }
    
    /**
     * 400派单队列页面(order/dispatch)显示调度人员列表
     */
    public function cache_agent_id($agent) {
        if(empty($agent)) {
	   return true;
	}
        $this->redis->hset("_AGENT_ID_LIST", $agent, 1);
	$ttl = strtotime(date('Y-m-d 07:00:00', time()+17*3600)) - time();
	$this->redis->expire("_AGENT_ID_LIST", $ttl);
    }

    public function agent_id_list() {
        $list = $this->redis->hkeys("_AGENT_ID_LIST");
        if(isset($list) && !empty($list)) {
            return $list;
        }
	else {
	    return array();
        }
    }
}
