<?php


class EnvelopeMapService extends BaseService
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
        return EnvelopeMap::model()->search();
    }


    /**获取红包城市列表
     * @param array $arr
     * @return mixed
     */
    public function getList($arr)
    {
        return EnvelopeMap::model()->getList($arr);
    }


    /**获取城市红包列表
     * @param array $arr
     * @return mixed
     */
    public function getEvenList($city_id)
    {
        return EnvelopeMap::model()->getEvenList($city_id);
    }


    /**获取城市红包列表
     * @param array $arr
     * @return mixed
     */
    public function getCityListByEnvelopeId($envelope_id)
    {
        return EnvelopeMap::model()->getCityListByEnvelopeId($envelope_id);
    }

}