<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 15/4/29
 * Time: 13:04
 */

class NormalAccountLogic extends BCustomers{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     *  普通用户的款项
     *
     * @param $customerTransAttributes
     * @param $customerAccountAttributes
     * @return bool
     */
    public function chargeNormal($customerTransAttributes, $customerAccountAttributes){
        EdjLog::info('charge normal amount customerTransAttributes::' . json_encode($customerTransAttributes)
            . '||| customerAccountAttributes' . json_encode($customerAccountAttributes));
        try {
            //1.insert customer trans
            $add_trans = $this->addCustomerTrade($customerTransAttributes);
            if ($add_trans['code'] !== 0) {
                EdjLog::info("add trans fail " . serialize($customerTransAttributes));
            }
            //2.update customer balance
            $update_account = $this->updateAccount($customerAccountAttributes);
            EdjLog::info('return update_account is : '.serialize($update_account));
            return (0 == $update_account['code']);
        } catch (Exception $e) {
            EdjLog::error('update vip balance error' . $e->getMessage());
            return false;
        }
    }
}