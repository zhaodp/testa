<?php

class EnvelopeExtendService extends BaseService
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
        return EnvelopeExtend::model()->search();
    }


    /**获取城市红包列表
     * @param array $arr
     * @return mixed
     */
    public function getDriveList($envelope_id, $date_start, $date_end)
    {
        return EnvelopeExtend::model()->getDriveList($envelope_id, $date_start, $date_end);
    }


    /**获取城市红包列表
     * @param array $arr
     * @return mixed
     */
    public function envelopeInsert($arr)
    {
        return EnvelopeExtend::model()->envelopeInsert($arr);
    }


    /**获取司机红包列表
     * @param array $arr
     * @return mixed
     */
    public function driveEnvelopeList($drive_id)
    {
        return EnvelopeExtend::model()->driveEnvelopeList($drive_id);
    }


    /**
     *
     * 更新红包推送状态
     *
     * @param array $arr
     * @return mixed
     */
    public function updateEnvelopeStatus($id, $driver_id, $status = 0)
    {
        return EnvelopeExtend::model()->updateEnvelopeStatus($id, $driver_id, $status);
    }

    /**更新红包领取状态
     * @param array $arr
     * @return mixed
     */
    public function updateEnvelopeReceiveStatus($id, $driver_id, $status = 2)
    {
        return EnvelopeExtend::model()->updateEnvelopeReceiveStatus($id, $driver_id, $status);
    }

    /**获取司机红包列表
     * @param array $arr
     * @return mixed
     */
    public function extendList($params)
    {
        return EnvelopeExtend::model()->extendList($params);
    }

    /**获取司机红包列表
     * @param array $arr
     * @return mixed
     */
    public function extendAcountList()
    {
        return EnvelopeExtend::model()->extendAcountList();
    }


    /**获取司机红包列表
     * @param array $arr
     * @return mixed
     */
    public function extendAcount()
    {
        return EnvelopeExtend::model()->extendAcount();
    }


    /**获取司机红包城市列表
     * @param array $arr
     * @return mixed
     */
    public function cityList()
    {
        return EnvelopeExtend::model()->cityList();
    }


    /**获取城市红包发放情况
     * @param array $arr
     * @return mixed
     */
    public function cityEnvelopeList($city_id)
    {
        return EnvelopeExtend::model()->cityEnvelopeList($city_id);
    }


    /**获取待push数据列表
     * @param array $arr
     * @return mixed
     */
    public function getPushList($status, $offset = 0, $limit = 5000)
    {
        return EnvelopeExtend::model()->getPushList($status, $offset = 0, $limit);
    }

    /**获取待push数据列表
     * @param array $arr
     * @return mixed
     */
    public function getPushSecondList($status = 0, $offset = 0, $limit = 5000)
    {
        return EnvelopeExtend::model()->getPushSecondList($status, $offset, $limit);
    }

    /**获取待push数据列表
     * @param array $arr
     * @return mixed
     */
    public function getDriveEnvelopeNum($drive_id, $envelope_id)
    {
        return EnvelopeExtend::model()->getDriveEnvelopeNum($drive_id, $envelope_id);
    }


    /**查看司机是否已经获取红包
     * @param array $arr
     * @return mixed
     */
    public function driveEnvelope($drive_id, $envelope_id)
    {
        return EnvelopeExtend::model()->driveEnvelope($drive_id, $envelope_id);
    }


    /**重新发放红包
     * @param array $arr
     * @return mixed
     */
    public function rePush($id)
    {
       return EnvelopeExtend::model()->rePush($id);
    }
}