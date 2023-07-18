<?php

class BonusMerchantsService extends BaseService
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

    public function getBonusMerchantsByName($name, $shop_type)
    {
        return BonusMerchants::model()->getBonusMerchantsByName($name, $shop_type);
    }


    /**
     * 商家信息
     */
    public function findInfo($bonusMerchants_name, $bonusMerchants_id = 0)
    {
        return BonusMerchants::model()->findInfo($bonusMerchants_name, $bonusMerchants_id);
    }
}
