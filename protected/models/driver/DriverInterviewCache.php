<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ZhangTingyi
 * Date: 13-6-13
 * Time: 下午5:35
 * To change this template use File | Settings | File Templates.
 */

class DriverInterviewCache  extends CRedis{
    public $host='redis01n.edaijia.cn'; //10.132.17.218
    public $port=6379;
    public $password='k74FkBwb7252FsbNk2M7';
    protected static $_models=array();

    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }

    public  function set($key,$value) {
        return $this->redis->set($key, $value);
    }

    public  function get($key){
        return $this->redis->get($key);
    }

    public  function delete($key){
        return $this->redis->delete($key);
    }

}