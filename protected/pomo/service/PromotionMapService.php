<?php
/**
 * Created by PhpStorm.
 * User: zhangxiaoyin
 * Date: 2015/3/24
 * Time: 15:53
 */

/**
 * This is the model class for table "t_promotion_map".
 *
 * The followings are the available columns in table 't_promotion_map':
 * @property integer $id
 * @property integer $pid
 * @property integer $shop_id
 * @property integer $city_id
 * @property integer $statue
 * @property integer $pomo_type
 * @property string $creation_date
 * @property string $last_changed_date
 */
class PromotionMapService extends BaseService
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
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        return PromotionMap::model()->search();
    }




    public function getCreatAmount($dateStart, $dateEnd)
    {
        return PromotionMap::model()->getCreatAmount($dateStart, $dateEnd);
    }


    public function getPassAmount($dateStart, $dateEnd)
    {
        return PromotionMap::model()->getPassAmount($dateStart, $dateEnd);
    }


    public function getPassingAmount($dateEnd,$statue)
    {
        return PromotionMap::model()->getPassingAmount($dateEnd,$statue);
    }

}