<?php

class BonusOwnerService extends BaseService
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
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        return BonusOwner::model()->search();
    }

    /**
     * @param $bonus_id
     * @param $owner
     * @return bool
     * @auther mengtianxue
     */
    public function addOwner($bonus_id, $owner)
    {
        return BonusOwner::model()->addOwner($bonus_id, $owner);
    }

    /**
     * 检查 Owner 是否存在
     * @param $bonus_id
     * @param $owner_id
     * @param $area
     * @return CActiveRecord
     * @auther mengtianxue
     */
    public function checkedOwner($bonus_id, $owner_id, $area)
    {
       return BonusOwner::model()->checkedOwner($bonus_id, $owner_id, $area);
    }
}