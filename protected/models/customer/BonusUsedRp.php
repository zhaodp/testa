<?php

/**
 * This is the model class for table "{{bonus_used_rp}}".
 *
 * The followings are the available columns in table '{{bonus_used_rp}}':
 * @property integer $id
 * @property string $name
 * @property string $driver_id
 * @property integer $order_id
 * @property integer $city_id
 * @property integer $cast
 * @property string $bonus_sn
 * @property integer $report_time
 * @property string $created
 */
class BonusUsedRp extends ReportActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return BonusUsedRp the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{bonus_used_rp}}';
    }

    /**
     * @return CDbConnection database connection
     */
//    public function getDbConnection()
//    {
//        return Yii::app()->dbreport;
//    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id', 'required'),
            array('order_id, city_id, cast, report_time', 'numerical', 'integerOnly' => true),
            array('name, bonus_sn', 'length', 'max' => 20),
            array('driver_id', 'length', 'max' => 10),
            array('created', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, driver_id, order_id, city_id, cast, bonus_sn, report_time, created', 'safe', 'on' => 'search'),
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
            'id' => '序号',
            'name' => '司机姓名',
            'driver_id' => '司机工号',
            'order_id' => '订单号',
            'city_id' => '城市',
            'cast' => '返现',
            'bonus_sn' => '优惠码',
            'report_time' => '统计时间',
            'created' => '使用日期',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('driver_id', $this->driver_id, true);
        $criteria->compare('order_id', $this->order_id);
        $criteria->compare('city_id', $this->city_id);
        $criteria->compare('cast', $this->cast);
        $criteria->compare('bonus_sn', $this->bonus_sn, true);
        $criteria->compare('report_time', $this->report_time);
        $criteria->compare('created', $this->created, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
} 