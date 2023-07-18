<?php

/**
 * This is the model class for table "{{bonus_merchants_ids}}".
 */
class BonusMerchantsIdsService extends BaseService
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
    /**
     *查询某一商家绑定的优惠劵种类
     **/
    public function getBonusNumByMerchantsId($merchantsId)
    {
        return BonusMerchantsIds::model()->getBonusNumByMerchantsId($merchantsId);
    }

    /**
     *返回该商家绑定的优惠劵id
     **/
    public function getBonusByMerchantsId($merchantsId)
    {
        return BonusMerchantsIds::model()->getBonusByMerchantsId($merchantsId);
    }

    public function getRelatedTimeByBonusId($bonusId)
    {
        return BonusMerchantsIds::model()->getRelatedTimeByBonusId($bonusId);
    }

    public function deleteRelation($id)
    {
        return BonusMerchantsIds::model()->deleteRelation($id);
    }

    public function checkBind($bonusId)
    {
        return BonusMerchantsIds::model()->checkBind($bonusId);
    }
}
