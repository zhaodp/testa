<?php

/**
 * This is the model class for table "{{bonus_used_rp}}".
 *
 * The followings are the available columns in table '{{bonus_used_rp}}':
 * @property integer $id
 * @property string $name
 * @property string $driver_id
 * @property integer $order_id
 * @property integer $city_id
 * @property integer $cast
 * @property string $bonus_sn
 * @property integer $report_time
 * @property string $created
 */
class BonusUsedRpService extends BaseService
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
        return BonusUsedRp::model()->search();
    }
} 