<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/5/5
 * Time: 18:48
 */

class SettleStatusLogic extends BaseLogic{

    private static $DIS_SETTLE_ORDER_STATUS = array(
        Order::ORDER_CANCEL,
        Order::ORDER_COMFIRM,
        Order::ORDER_NOT_COMFIRM,
    );

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function canSettle($order){
        $status = isset($order['status']) ? $order['status'] : -1;
        $isDisSettle = in_array($status, self::$DIS_SETTLE_ORDER_STATUS);
        $isSettled   = $this->isSettled($order);
        return !$isDisSettle && !$isSettled;

    }

    private function isSettled($order){
        $settledFee = DriverAccountService::getSettledFee($order);
        return  ($order->status == Order::ORDER_COMPLATE && $settledFee > 0);
    }
}