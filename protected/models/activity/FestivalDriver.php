<?php
/**
 * 春节参加司机送回家活动的司机
 */
class FestivalDriver extends CActiveRecord
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
        return '{{festival_driver}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('driver,start_time_begin,start_time_end,start_city,end_city', 'required'),
            array('pass_city', 'length', 'max'=>100),
            array('remark', 'length', 'max'=>500),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, driver,start_time_begin,start_time_end,start_city,end_city', 'safe', 'on'=>'search'),
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
     * 判断司机是否已报名
     * @param $driver 司机工号
     * @return bool
     */
    public function getDriver($driver){
        $ret = self::model()->find('driver=:driver', array(':driver'=>$driver));
        if(!$ret){
            return false;
        }
        return true;
    }

    /**
     * 获取未匹配的司机
     */
    public function getNotMatchedDrivers(){
        $ret = self::model()->findAll('match_customer_phone = :match_customer_phone', array(':match_customer_phone'=>''));
        if(!$ret){
            return false;
        }
        return $ret;
    }

    /**
     * 获取匹配到的司机
     */
    public function getMatchedDrivers(){
        $ret = self::model()->findAll('match_customer_phone != :match_customer_phone', array(':match_customer_phone'=>''));
        if(!$ret){
            return false;
        }
        return $ret;
    }

    /**
     * @获取待补偿信息费司机
     */
    public function getToCompensateDrivers(){
        $ret = self::model()->findAll('match_customer_phone = :match_customer_phone and compensate_status=:compensate_status', array(':match_customer_phone'=>'',':compensate_status'=> 0));
        if(!$ret){
            return false;
        }
        return $ret;
    }

}
