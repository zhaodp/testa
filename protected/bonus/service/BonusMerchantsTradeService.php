<?php

/**
 * This is the model class for table "{{bonus_merchants_trade}}".
 */
class BonusMerchantsTradeService extends BaseService
{
    /**
     * Returns the static service of the specified  class.
     * @param string $className active service class name.
     * @return  the static service class
     */
    public static function service($className = __CLASS__)
    {
        return parent::service($className);
    }

    public function getBonusMerchantsTradeList($merchants_id, $begin, $end)
    {
        return BonusMerchantsTrade::model()->getBonusMerchantsTradeList($merchants_id, $begin, $end);
    }

    public function getTatolTrade($bonus_merchants_id)
    {
        return BonusMerchantsTrade::model()->getTatolTrade($bonus_merchants_id);
    }
}
