<?php
/**
 * 司机订单service
 * Created by PhpStorm.
 * User: zhaoyingshuang
 * Date: 15-4-30
 * Time: 上午11:19
 */

class DriverOrderService {
    private static $instance;

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new DriverOrderService();
        }
        return self::$instance;
    }

    public function checkOpenCity($city_id) {
        return DriverOrder::model()->checkOpenCity($city_id);
    }
}