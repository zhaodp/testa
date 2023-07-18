<?php
/**
 * Created by PhpStorm.
 * User: syang
 * Date: 14-2-25
 * Time: 下午2:38
 */

class EdjLog extends Yii {

    public static $category = 'application';
   
    private static function getDefaultCategory(){
        return gethostname().".".Yii::app()->logext->route['logPattern'];
    }

	

    public static function info($msg, $category=null) {
        //$msg .="\n".self::_backtrace();

	if(!isset($category)){
            $category = self::getDefaultCategory();
        }

        self::_log($msg, CLogger::LEVEL_INFO, $category);
    }

    public static function trace($msg, $category=null) {
        //$msg .="\n".self::_backtrace();
	 if(!isset($category)){
            $category = self::getDefaultCategory();
        }
        self::_log($msg, CLogger::LEVEL_TRACE, $category);
    }

    public static function warning($msg, $category=null) {
        //$msg .="\n".self::_backtrace();
 	if(!isset($category)){
            $category = self::getDefaultCategory();
        }
        self::_log($msg, CLogger::LEVEL_WARNING, $category);
    }

    public static function error($msg, $category=null) {
        //$msg .="\n".self::_backtrace();
	 if(!isset($category)){
            $category = self::getDefaultCategory();
        }
        self::_log($msg, CLogger::LEVEL_ERROR, $category);
    }

    public static function monitor($msg) {
        self::info($msg, 'monitor|'.self::getDefaultCategory());
    }


    private static function _log($msg, $level, $category=null) {
        if ($category == null) $category = self::$category;
        self::log($msg, $level, $category);

    }

    private static function _backtrace() {
        $trace=debug_backtrace();
        $stack_num = 1;
        if (PHP_SAPI == 'cli') $stack_num=2;
        $msg = "";
        if (isset($trace[$stack_num])) {
            $i = $stack_num;
            $t = $trace[$stack_num];
            if(!isset($t['file']))
                $t['file']='unknown';
            if(!isset($t['line']))
                $t['line']=0;
            if(!isset($t['function']))
                $t['function']='unknown';
            $msg .= "#$i {$t['file']}({$t['line']}): ";
            if(isset($t['object']) && is_object($t['object']))
                $msg .= get_class($t['object']).'->';
            elseif (isset($t['class']) && isset($t['type']) && $t['type'] == '::')
                $msg .= $t['class'].$t['type'];
            $msg .= "{$t['function']}()\n";
        }
        return $msg;
    }
}
