<?php
class Redis03 extends CRedis {

    public $host='redis03n.edaijia.cn';
    public $port=6379;
    public $password='k74FkBwb7252FsbNk2M7';

    public static function model($className = __CLASS__) {
        $model = null;
        if (isset ( self::$_models [$className] ))
            $model = self::$_models [$className];
        else {
            $model = self::$_models [$className] = new $className ( null );
        }
        return $model;
    }

    public function getRedis() {
        return $this->redis;
    }
}
