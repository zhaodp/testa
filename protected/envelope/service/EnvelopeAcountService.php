<?php

class EnvelopeAcountService extends BaseService
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
        return EnvelopeAcount::model()->search();
    }



    /**
     * save 信息
     */
    public function saveInfo($orderId, $driverId, $day, $city_id)
    {
        return EnvelopeAcount::model()-> saveInfo($orderId, $driverId, $day, $city_id);
    }


    /**
     * 获取该司机当天报单数量
     */
    public function driverOrderNum($driverId, $date_start, $date_end)
    {
        return EnvelopeAcount::model()->driverOrderNum($driverId, $date_start, $date_end);
    }

    /**获取满足条件的司机列表
     * @param array $arr
     * @return mixed
     */
    public function getOrderList($num, $arr, $city_id, $date_start, $date_end)
    {
        return EnvelopeAcount::model()->getOrderList($num, $arr, $city_id, $date_start, $date_end);
    }
}