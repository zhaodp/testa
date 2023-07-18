<?php
/**
 * Created by PhpStorm.
 * User: zhangxiaoyin
 * Date: 2015/4/28
 * Time: 16:51
 */
class BaseService{

    private static $_services=array();			// class name => service
    /**
     * Returns the static service of the specified  class.
     * The model returned is a static instance of the  class.
     * It is provided for invoking class-level methods (something similar to static class methods.)
     *
     * EVERY derived  class must override this method as follows,
     *
     * @param string $className class name.
     * @return  active record service instance.
     */
    public static function service($className=__CLASS__)
    {
        if(isset(self::$_services[$className])){
            return self::$_services[$className];
        }
        else
        {
            $service=self::$_services[$className]=new  $className();
            return $service;
        }
    }
}