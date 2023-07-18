<?php
/**
 * Retrieve config base handler
 * @author wangwenhao
 * 2015-04-07
 */
class ConfigBaseHandler{
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

    private function getConfig(){

    }
}
