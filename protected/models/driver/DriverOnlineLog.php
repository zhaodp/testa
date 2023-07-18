<?php

/**
 * This is the model class for table "{{driver_online_log}}".
 *
 * The followings are the available columns in table '{{driver_online_log}}':
 * @property integer $id
 * @property integer $driver_id
 * @property integer $online_time
 * @property string $create_time
 * @property string $update_time
 */
class DriverOnlineLog extends CActiveRecord
{
    const MAX_ONLINE_TIME = 86400000; //单位毫秒，最大在线一天

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_online_log}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('update_time', 'required'),
            array('driver_id, online_time', 'numerical', 'integerOnly' => true),
            array('create_time,hot_time,id', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, driver_id, online_time, create_time, update_time', 'safe', 'on' => 'search'),
        );
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
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'driver_id' => 'Driver',
            'online_time' => '在线时长，单位秒',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        );
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
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('driver_id', $this->driver_id);
        $criteria->compare('online_time', $this->online_time);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('update_time', $this->update_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DriverOnlineLog the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


    /**
     * save driver online time
     * add by aiguoxin
     */
    public function addDriverOnlineLog($driver_id, $online_time, $create_time = '')
    {
        if (empty($create_time)) {
            $create_time = date("Y-m-d H:i:s");
        }
        $log = new DriverOnlineLog();
        $log_attr = $log->attributes;
        $log_attr['driver_id'] = $driver_id;
        $log_attr['online_time'] = $online_time;
        $log_attr['create_time'] = $create_time;
        $log->attributes = $log_attr;
        if ($log->insert()) {
            return true;
        }
        return false;
    }


    /**
     * 获取在线时间大于指定时间的司机列表
     * add by aiguoxin
     */
    public function getDriverOnlineList($start, $end, $online_time, $arr_drive=array())
    {
        $result = array();
        try {
            $criteria = new CDbCriteria;
            $criteria->select = 'id,driver_id,create_time';
            $criteria->addBetweenCondition('create_time', $start, $end);
            $criteria->addCondition('hot_time>=:hot_time');
            $criteria->params[':hot_time'] = $online_time;
            if (!empty($arr_drive)) {
                $criteria->addNotInCondition('driver_id', $arr_drive);
            }
            self::$db = Yii::app()->db_readonly;
            $data = self::model()->findAll($criteria);
            self::$db = Yii::app()->db;
            if ($data) {
                foreach ($data as $da) {
                    $result[] = array('driver_id' => $da->driver_id);
                }
            }
        } catch (Exception $e) {
            EdjLog::error($e);
        }
        return $result;
    }
}