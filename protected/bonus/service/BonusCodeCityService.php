<?php

/**
 * This is the service class for table "{{bonus_code_city}}".
 */
class BonusCodeCityService extends BaseService
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
        return BonusCodeCity::model()->search();
    }

    /**
     * 根据id获取优惠劵信息
     * @param $bonus_id
     * @param $city_id
     * @return mixed
     * author mengtianxue
     */
    public function getBonusCodeCityID($bonus_id, $city_id = null)
    {
        return BonusCodeCity::model()->getBonusCodeCityID($bonus_id, $city_id);
    }

    /**
     * 执行批量插入城市
     * @param array $splitArr
     * @param null $bonus_code_id
     * @return bool
     * @author daiyihui
     */
    public function doCityInsert($splitArr = array(), $bonus_code_id = null)
    {
        return BonusCodeCity::model()->doCityInsert($splitArr , $bonus_code_id );
    }

    /** BonusCode 里的
     * 根据bonusId获取城市信息
     * @param <int> $id
     * @return <string> 城市信息
     * created by liuxiaobo
     */
    public function getCityById($id)
    {
        return BonusCodeCity::model()->getCityById($id);
    }
}