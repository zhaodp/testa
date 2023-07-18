<?php

class BonusChannelLog extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{bonus_channel_log}}';
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }


    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BonusChannel the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * 获取渠道被分配次数
     * @param int $area_id
     * @return int
     * @auther mengtianxue
     */
    public static function getChannelDistriCount($channel = 0)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'count(id)';

        $criteria->compare('channel_id', $channel);
        self::$db = Yii::app()->db_readonly;
        $channel = self::model()->count($criteria);
        self::$db = Yii::app()->db;
        return $channel;
    }

    /**
     * 获取渠道被分配数量
     * @param array $log_id
     * @return array
     * @auther mengtianxue
     */
    public static function getChannelDistri($log_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'id,creation_date';

        $criteria->addInCondition('id', $log_id);
        self::$db = Yii::app()->db_readonly;
        $channel = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;

        $result = array();

        foreach ($channel as $value) {
            $result[$value['id']] = $value['creation_date'];
        }

        return $result;
    }


    /**
     * 获取城市分配人列表
     * @param int $city_id
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getCityDistri($city_id)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'disTri_by';
        if ($city_id != 0) {
            $criteria->addCondition('city_id=:city_id');
            $criteria->params[':city_id'] = $city_id;
        }

        self::$db = Yii::app()->db_readonly;
        $channel = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;

        $result = array();

        foreach ($channel as $value) {
            array_push($result, $value['disTri_by']);
        }

        return $result;
    }

    /**
     * 获取分配人分配渠道列表
     * @param string $disTri_by
     * @return array
     * @auther zhangxiaoyin
     */
    public static function getChannelByDistriBy($disTri_by)
    {
        $criteria = new CDbCriteria;
        $criteria->select = 'channel_id';

        $criteria->addInCondition('disTri_by=:disTri_by');
        $criteria->params[':disTri_by'] = $disTri_by;
        self::$db = Yii::app()->db_readonly;
        $channel = self::model()->findAll($criteria);
        self::$db = Yii::app()->db;

        $result = array();

        foreach ($channel as $value) {
            array_push($result, $value['channel_id']);
        }

        return $result;
    }
}
