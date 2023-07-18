<?php

/**
 * Class DriverPullNewConfig
 * 司机拉新司机回家活动'帮他报名'数据
 */
class DriverPullNewRecommendLog extends CActiveRecord
{
    public function getDbConnection()
    {
        return Yii::app()->db_activity;
    }


    public function tableName()
    {
        return '{{driver_pull_new_recommend_log}}';
    }


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    public function rules()
    {

        return array(
            array('phone, id_card', 'required'),
            array('phone', 'length', 'max' => 20),
            array('id_card', 'length', 'max' => 20),
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
}
