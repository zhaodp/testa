<?php

/**
 * This is the model class for table "{{driver_online_stat}}".
 *
 * The followings are the available columns in table '{{driver_online_stat}}':
 * @property integer $id
 * @property integer $city_id
 * @property string $date
 * @property integer $free
 * @property integer $busy
 * @property double $free_proportion
 * @property integer $accept
 * @property double $accept_proportion
 * @property integer $online
 * @property double $online_proportion
 * @property integer $notonline
 * @property string $create_time
 */
class DriverOnlineStat extends CActiveRecord
{
    public $start_date = '';
    public $end_date = '';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_online_stat}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('city_id,date', 'required'),
            array('city_id, free, busy, accept, online, notonline', 'numerical', 'integerOnly'=>true),
            array('free_proportion, accept_proportion, online_proportion', 'numerical'),
            array('date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('city_id, date', 'safe', 'on'=>'search'),
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
            'id' => 'ID',
            'city_id' => '城市',
            'date' => '统计日期',
            'free' => '空闲司机数',
            'busy' => '峰值期上线人数',
            'free_proportion' => '空闲比例%',
            'accept' => '接单司机数',
            'accept_proportion' => '接单率%',
            'online' => '上线司机数',
            'online_proportion' => '上线率%',
            'notonline' => '未上线司机数',
            'create_time' => '统计时间',
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

        $criteria=new CDbCriteria;

        $criteria->compare('city_id',$this->city_id);
        if($this->start_date){
            $criteria->addCondition('date >= :start_date');
            $criteria->params[':start_date'] = $this->start_date;
        }
        if($this->end_date){
            $criteria->addCondition('date <= :end_date');
            $criteria->params[':end_date'] = $this->end_date;
        }
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination' => array(
                'pageSize' => 32)

        ));
    }

    /**
     * @return CDbConnection the database connection used for this class
     */
    public function getDbConnection()
    {
        return Yii::app()->dbreport;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DriverOnlineStat the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}