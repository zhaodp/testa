<?php
/**
 * 第三方合作者工厂类.
 * User: zhangtingyi
 * Date: 13-11-1
 * Time: 上午10:55
 * To change this template use File | Settings | File Templates.
 */

class PartnerFactory {
    /**
     * 通过不同的渠道号返回不同的合作者对象
     */
    public static function factory($channel){
        $model = false;
        switch ($channel) {

            case '03002':
                $model = new CarClub($channel);
                break;

            case '03004':
                $model = new PingAn();
                break;
        }
        return $model;
    }

}