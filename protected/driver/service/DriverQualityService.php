<?php
/**
 * 司机品质service
 * Created by PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-5-5
 * Time: 下午3:08
 */

class DriverQualityService {
    private static $instance;

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new DriverQualityService();
            self::$instance->init();
        }
        return self::$instance;
    }

}