<?php

/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/5/5
 * Time: 18:51
 */
class BaseLogic
{
    private static $_services = array();

    public static function model($className = __CLASS__)
    {
        if (isset(self::$_services[$className])) {
            return self::$_services[$className];
        } else {
            $service = self::$_services[$className] = new  $className();
            return $service;
        }
    }
}