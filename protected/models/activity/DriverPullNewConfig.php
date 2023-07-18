<?php

/**
 * Class DriverPullNewConfig
 * 司机拉新司机回家活动配置表
 */
class DriverPullNewConfig extends CActiveRecord
{
    const ENABLE_STATUS = 0;//启用状态
    const UNABLE_STATUS = 1;//停止状态

    public function getDbConnection()
    {
        return Yii::app()->db_activity;
    }


    public function tableName()
    {
        return '{{driver_pull_new_config}}';
    }


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function rules()
    {

        return array(
            array('city_id, begin_time, end_time, amount', 'required'),
            array('city_id', 'length', 'max' => 500),
        );
    }


    public function relations()
    {

        return array();
    }


    public function attributeLabels()
    {
        return array();
    }


    public function search()
    {
        return array();
    }

    public function getConfig()
    {
        $ret = self::model()->find('status=:status', array(':status' => self::ENABLE_STATUS));
        if (!$ret) {
            return false;
        }
        return $ret;
    }
}
