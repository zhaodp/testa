<?php

/**
 * This is the model class for table "{{bonus_merchants_trade}}".
 */
class BonusMerchantsTrade extends CActiveRecord
{
    const TRADE_TYPE_PREPAY = 1;//预付款
    const TRADE_TYPE_FULL = 2 ;//全额
    
    static $trade_type = array(
	self::TRADE_TYPE_PREPAY => '预付款',
	self::TRADE_TYPE_FULL => '全额购买',
    );
  
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return BonusCode the static model class
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
        return '{{bonus_merchants_trade}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
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
        );
    }

    /**
     * 保存之前要更新的字段
     * @return bool
     * author cuiluzhe
     */
    public function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $this->created = Yii::app()->user->getId();
                $this->create_time = date("Y-m-d H:i:s");
            }
            return true;
        }
        return parent::beforeSave();
    }
    
   public function getBonusMerchantsTradeList($merchants_id,$begin,$end)
    {

        $criteria = new CDbCriteria;
	if(isset($merchants_id) && !empty($merchants_id)){
                $criteria->addCondition("bonus_merchants_id = " . $merchants_id);
        }
        if(isset($begin) && !empty($begin)){
                $criteria->addCondition("create_time >= '" . $begin."'");
        }
	if(isset($end) && !empty($end)){
                $criteria->addCondition("create_time <= '" . $end."'");
        }
        return new CActiveDataProvider($this, array(
            'pagination'=>array ('pageSize'=>50),
            'criteria' => $criteria,
        ));
    }

    public function getTatolTrade($bonus_merchants_id){
        $criteria = new CDbCriteria;
        $criteria->select='sum(amount) as amount';
        $criteria->addCondition("bonus_merchants_id=:bonus_merchants_id");
        $criteria->addCondition("type!=3");
        $criteria->params['bonus_merchants_id']=$bonus_merchants_id;
        $data=self::model()->find($criteria);
        if($data){
            return $data->amount;
        }else{
            return 0;
        }
    }
}
