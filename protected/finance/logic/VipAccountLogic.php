<?php

/**
 * vip 用户的账户情况,通过继承 vip 类复用 Vip 的属性
 */
class VipAccountLogic extends Vip {

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     *
     * vip 账户的各种操作
     *
     * @param $vipTradeAttributes
     * @param $vipBalanceAttributes
     * @return bool
     */
    public function chargeVip($vipTradeAttributes, $vipBalanceAttributes){
        // TODO ... change the  order and  and  transaction
        EdjLog::info('charge vip vipTradeAttributes is ' . serialize($vipTradeAttributes) .
            '|||vipBalanceAttributes' . serialize($vipBalanceAttributes));
        try {
            //1.insert vip trade
            $vipTrade = new VipTrade();
            $amount = $vipTrade->addTrade($vipTradeAttributes);
            if ($amount === null || $amount === false) {
                EdjLog::info("add trade log fail " . serialize($vipTradeAttributes));
            }
            //2.update vip balance
            $vipCard = $vipBalanceAttributes['vipCard'];
            $delta = $vipBalanceAttributes['delta'];
            $affectRows = $this->updateBalance($vipCard, $delta);
            EdjLog::info('return affectRows is : '.$affectRows);
            return (0 != $affectRows);
        } catch (Exception $e) {
            EdjLog::error('update vip balance error' . $e->getMessage());
            return false;
        }
    }

    /**
     * 返回司机的账户余额, 默认会加上信誉额度
     *
     * @param $vipCard
     * @param bool $isCredit
     * @return float|int|mixed
     */
    public function getBalance($vipCard, $isCredit = true){
        $criteria = new CDbCriteria();
        $criteria->compare('id', $vipCard);
        $criteria->compare('status', Vip::STATUS_NORMAL);
        if($isCredit){
            $criteria->select = ' (balance + credit) as balance';
        }else{
            $criteria->select = 'balance';
        }
        $vipAccount = parent::model()->find($criteria);
        if($vipAccount){
            $balance = isset($vipAccount['balance']) ? $vipAccount['balance'] : 0.00;
            return $balance < 0 ? 0 : $balance; // 如果是负数就返回是0
        }else{
            return 0;
        }
    }

    /**
     * 更新余额
     *
     * @param $vipCard
     * @param $delta
     * @return int
     */
    public function updateBalance($vipCard, $delta){
        return parent::model()->updateBalance($vipCard, $delta);
    }
}
