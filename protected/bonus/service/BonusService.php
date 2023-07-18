<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/5/6
 * Time: 18:09
 */

class BonusService extends BaseService{

    public static function service($className = __CLASS__)
    {
        return parent::service($className);
    }

    /**
     * 返回订单的优惠券
     *
     * @param $phone
     * @param $orderId
     * @param bool $isUsed | 表示优惠券已经参与了结算
     * @return mixed
     */
    public function getOrderBonus($phone , $orderId, $isUsed = false){
        return CustomerBonus::model()->getBonusUsed($phone, $orderId);
    }
}