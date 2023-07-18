<?php
/**
 * 司机推送service
 * Created by PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-5-5
 * Time: 下午1:54
 */

class DriverPushService {
    private static $instance;

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new DriverPushService();
            self::$instance->init();
        }
        return self::$instance;
    }
}