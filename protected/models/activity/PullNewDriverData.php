<?php
/**
 * 司机拉新活动 司机工号纬度每日报表数据
 */
class PullNewDriverData extends CActiveRecord
{
    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->db_activity;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return RedPacketLog the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_pull_new_driver_data}}';
    }
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('driver,recruitment_drivers_num,sign_drivers_num,total_amount,create_time', 'required'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
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
    }

    /**
     * 获取$date日的数据 格式:2015-02-18
     */
   public function getDriverDataReport($date = '')
    {
        if (empty($date)) {
            EdjLog::info("请输入日期");
            return;
        }
        $criteria = new CDbCriteria;
        $criteria->select = '*';
        $criteria->condition = 'create_time=:create_time';
        $criteria->params = array(':create_time' => $date);
        $criteria->order = ' sign_drivers_num desc';
        $data = self::model()->findAll($criteria);
        return $data;
    }


}
