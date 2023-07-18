<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/4/29
 * Time: 12:24
 */

class VipAccountService {

    public static function getAccountInfo($accountId){

    }

    /**
     * vip 订单产生的扣款
     *
     * @param $vipCard
     * @param $cast
     * @param $order
     * @return mixed
     */
    public static function orderCharge($vipCard, $cast, $order){
        $vipBalance = self::getVipBalance($vipCard, true);
        //TODO ... add empty
        $vipTradeAttributes = array(
            'vipcard' => $order['vipcard'],
            'order_id' => $order['order_id'],
            'type' => VipTrade::TYPE_ORDER,
            'amount' => $cast,
            'comment' => 'VIP消费 单号：' . $order['order_id'],
            'balance' => $vipBalance - $cast,
            'order_date' => $order['created']
        );
        $vipBalanceAttributes = array(
            'vipCard'    => $vipCard,
            'delta'      => $cast * -1, // 这里会司机操作 vip 的余额账户
        );

        $ret = VipAccountLogic::model()->chargeVip($vipTradeAttributes, $vipBalanceAttributes);

        // TODO ... modify ret code and other
        return $ret;
    }


    public static function orderChargeV2($vipTradeAttributes, $vipBalanceAttributes){
        return VipAccountLogic::model()->chargeVip($vipTradeAttributes, $vipBalanceAttributes);
    }

    /**
     * vip 账户余额
     *
     * @param $vipCard
     * @param bool $isCredit
     * @return int|mixed|null
     */
    public static function getVipBalance($vipCard, $isCredit = true){
        return VipAccountLogic::model()->getBalance($vipCard, $isCredit);
    }

    /**
     * vip 账户的退单重结, 主要用于把一个订单里面 vip 的操作都给退掉
     *
     * @param $vipCard
     * @param $cast
     * @param $orderId
     * @param $params
     */
    public static function refundOrder($vipCard, $cast, $orderId, $params = array()){
        return Vip::model()->refundOrderCost($cast, $vipCard, $orderId);
    }

}