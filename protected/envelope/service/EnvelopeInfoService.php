<?php

class EnvelopeInfoService extends BaseService
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
        return EnvelopeInfo::model()->search();
    }


    /**获取进行中红包列表
     * @param array $arr
     * @return mixed
     */
    public function getList($arr = array(), $ids = array())
    {
        return EnvelopeInfo::model()->getList($arr, $ids);
    }

    /**生成随机红包金额
     * @param $params
     * @return mixed
     */
    public static function  getEvenlopeNum($params)
    {
        return EnvelopeInfo::getEvenlopeNum($params);
    }

    /**获取报单奖励红包列表
     * @return mixed
     */
    public static function  getEvenlopeList($date)
    {
        return EnvelopeInfo::getEvenlopeList($date);
    }

    /**获取在线时长红包列表
     * @return mixed
     */
    public static function  getEvenlopeHoteTimeList()
    {
        return EnvelopeInfo::getEvenlopeHoteTimeList();
    }
}