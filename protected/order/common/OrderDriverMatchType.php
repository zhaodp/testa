<?php

abstract class OrderDriverMatchType {

    const CHOOSE_DRIVER = 0;

    const AUTOMATIC = 1;

    public static function checkType($orderId) {
        return strlen($orderId) > 11 && is_numeric($orderId)
            ? OrderDriverMatchType::CHOOSE_DRIVER
            : OrderDriverMatchType::AUTOMATIC;
    }
}
