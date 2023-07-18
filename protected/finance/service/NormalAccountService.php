<?php

/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/4/29
 * Time: 12:59
 */
class NormalAccountService
{

    public static function orderChargeV2($customerTransAttributes, $customerAccountAttributes){
        return NormalAccountLogic::model()->chargeNormal($customerTransAttributes, $customerAccountAttributes);
    }

    public static function orderCharge($phone, $cast, $order)
    {
        $userAccount = self::getUserAmount($phone);
        $customerId  = $userAccount['user_id'];
        //TODO ... check is illgeal customer
        $cast   = $cast * -1; //
        $balance = $userAccount['balance'];
        $balance = $balance +  $cast;

        $transAttributes = array(
            'user_id' => $customerId,
            'trans_order_id' => $order['order_id'],
            'trans_type' => CarCustomerTrans::TRANS_TYPE_F,
            'amount' => $cast,
            'balance' => $balance,
            'source' => CarCustomerTrans::TRANS_SOURCE_S,
            'remark' => '订单号：' . $order['order_id'],
        );
        $accountAttributes = array();
        $accountAttributes['user_id'] = $customerId;
        $accountAttributes['amount'] = $cast;

        $ret = NormalAccountLogic::model()->chargeNormal($transAttributes, $accountAttributes);
        return $ret;
    }


    /**
     * 返回用户的账户, 如果用户没有账户, 会创建一个
     *
     * @param $phone
     * @return array|bool
     */
    public static function forceGetUserAmount($phone){
        $ret =  self::getUserAmount($phone);
        if(empty($ret) || empty($ret['user_id'])){
            //create a new account
            $customerInfo = CustomerMain::model()->forceGetCustomerInfo($phone);
            if($customerInfo){
                $userId = $customerInfo->id;
                $cityId = $customerInfo->city_id;
                self::createAccount($userId, $cityId);
                return self::getUserAmount($phone);
            }else{
                return false;
            }
        }else{
            return $ret;
        }
    }

    /**
     * 获取普通用户的账户信息
     *
     * @param $phone
     * @return array
     */
    public static function getUserAmount($phone)
    {
        $user_info = NormalAccountLogic::model()->getUserAmount($phone);
        $ret = array();
        $userId = 0;
        $userBalance = 0.00;
        if ($user_info['code'] === 0) {
            $userId = isset($user_info['user_id'])? $user_info['user_id'] : 0;
            //用户余额
            $userBalance = isset($user_info['amount']) ? $user_info['amount'] : 0;
        }
        $ret['code'] = 0;
        $ret['user_id'] = $userId;
        $ret['balance'] = $userBalance;
        $ret['amount'] = $userBalance; // 兼容以前版本
        return $ret;
    }


    /**
     * 创建账户
     *
     * @param $userId
     * @param $cityId
     * @param int $amount
     * @return mixed
     */
    public static function createAccount($userId, $cityId, $amount = 0){
        // TODO ... check user account is exist
        $params = array(
            'user_id' => $userId,
            'city_id' => $cityId,
            'type'    => CarCustomerAccount::ACCOUNT_TYPE_S,
            'amount'  => $amount,
            'vip_card'=> 0);
        return NormalAccountLogic::model()->addAccount($params);
    }

    public static function getBalanceFromDb($phone){
        //TODO ... add impl
    }



}