<?php
/**
 * Created by PhpStorm.
 * User: liufugang
 * Date: 2015/4/10
 * Time: 15:36
 */

class UserNotifyTestBean {
    public $word = "goggog";
    public $word_order_status = "201";
    public $banner_picture_url = "wwww.aaaa";
    public $banner_jump_url = "goggogog";
    public $banner_order_status = "301";

    public static function getAAAs()
    {
        $a = self::getAAA("201");
        $b = self::getAAA("301");
        $c = self::getAAA("302");
        $d = self::getAAA("303");
        $e = self::getAAA("304");
        $f = self::getAAA("500");
        $f2 = self::getAAA("500");
        $f2->banner_jump_url = "xiaoganggang";
        return array($a, $b, $c, $d, $e, $f, $f2);
    }

    public static function getAAA($status)
    {
        $a = new UserNotifyTestBean;
        $a->word = "word" . $status;
        $a->word_order_status = $status;
        $a->banner_picture_url = "picture_url" . $status;
        $a->banner_jump_url = "jump_url" . $status;
        $a->banner_order_status = $status;
        return $a;
    }
}