<?php

class VipService extends BaseService
{
    /**
     * Returns the static service of the specified AR class.
     * @param string $className active record class name.
     * @return  the static model class
     */
    public static function service($className = __CLASS__)
    {
        return parent::service($className);
    }

    /**
     * 是否是 vip
     * TODO ... unit test
     * @param $phone
     * @return array|bool
     */
    public function isVip($phone){
        $vipInfo = $this->getVipInfo($phone);
        return $vipInfo;
    }

    /**
     *
     *
     * @param $phone | 可能是主卡手机号, 也可能是副卡手机号
     * @param bool $isCredit
     * @return array|bool
     */
    public function getVipInfo($phone, $isCredit = true){
        $ret = array();
        $vipPhone = VipPhone::model()->getPrimary($phone);
        if($vipPhone){
            $vipCard = $vipPhone['vipid'];
            $ret['vipid'] = $vipCard;
            $vip = Vip::model()->getPrimary($vipCard);
            $name = isset($vip['name']) ? $vip['name'] : '';
            $ret['name'] = $name;
            $ret['vipid'] = $vipCard;
            $amount = VipAccountService::getVipBalance($vipCard, $isCredit);
            $ret['amount'] = $amount;
            $ret['status'] = isset($vip['status']) ? $vip['status'] : Vip::STATUS_NORMAL;
            $ret['phone']  = $vip['phone'];
            $ret['type']  = $vip['type'];
            return $ret;
        }
        return false;
    }


    /**
     * 根据手机号（可能是副卡）取得vip信息
     *
     * @author sunhongjing 2013-12-28
     * @param string $phone 手机号
     * @param bool $need_balance 是否需要返回余额
     * @return array
     */
    public function getVipInfoByPhone($phone, $need_balance = false)
    {
        $ret = false;
        if (empty($phone)) {
            return $ret;
        }

        $ret = $vip_phone = VipPhone::model()->getPrimary(trim($phone));

        if ($need_balance && !empty($vip_phone)) {
            $vip = Vip::model()->getPrimary($vip_phone['vipid']);
            if(!$vip){
                EdjLog::info($phone." is ok but vipid ".$vip_phone['vipid'].' is disable');
                return false;
            }
            $ret['vipcard'] = $vip->id;
            $ret['phone'] = $phone;
            $ret['card_customer_name'] = $vip->name;
            $ret['customer_name'] = $vip_phone['name'];
            $ret['balance'] = $vip->balance;
            $ret['credit'] = $vip->credit;
            $ret['total_balance'] = $vip->balance + $vip->credit;
        }
        return $ret;
    }

    /**
     *
     *
     * @param $phone | 可能是主卡手机号, 也可能是副卡手机号
     * @return array|bool
     */
    public function getVipPhoneInfo($phone){
        return  VipPhone::model()->getPrimary($phone);
    }

    /**
     * 根据 vip 的卡号返回优惠券信息
     *
     * @param $vipCard
     * @param bool $isCredit
     * @return array|bool|mixed|null
     */
    public function getVipInfoByVipCard($vipCard, $isCredit = true){
        if(empty($vipCard)){
            return false;
        }
        $vip = Vip::model()->getPrimary($vipCard);
        if($vip){
            $phone = $vip['phone'];
            $vipInfo   = $this->getVipInfo($phone, $isCredit);
            return $vipInfo;
        }
        return false;
    }
}
