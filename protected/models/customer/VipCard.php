<?php

/**
 * This is the model class for table "{{vip_card}}".
 *
 * The followings are the available columns in table '{{vip_card}}':
 * @property string $id
 * @property string $pass
 * @property integer $money
 * @property integer $status
 * @property string $saled_by
 * @property string $activated_by
 * @property integer $atime
 */
class VipCard extends FinanceActiveRecord
{
	/**
	 * 初始化充值卡
	 */
	const STATUS_CREATED = 0;
	/**
	 * 售出充值卡
	 */
	const STATUS_SALED = 1;
	/**
	 * 激活充值卡
	 */
	const STATUS_ACTIVATED = 2;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return VipCard the static model class
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
		return '{{vip_card}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('money, status, atime', 'numerical', 'integerOnly'=>true),
            array('id, pass', 'length', 'max'=>100),
            array('saled_by, activated_by', 'length', 'max'=>50),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, pass, money, status, saled_by, activated_by, atime', 'safe', 'on'=>'search'),
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
	
	public function getVipCardBalance($cardNo){
		$vipCard = self::model()->getVipCard($cardNo);
		
		return $vipCard->money;
	}
	
	public function getSaledTotal(){
		$criteria = new CDbCriteria();
		$criteria->select = "COUNT(id) as id, money, status";
		$criteria->group = 'money, status';
		
		$vipCards = VipCard::model()->findAll($criteria);
		$saleTotal = array();
		foreach($vipCards as $vipCard){
			if ($vipCard->status == VipCard::STATUS_SALED){
				$status = '未激活';
			} else {
				if ($vipCard->status == VipCard::STATUS_ACTIVATED){
					$status = '已激活';
				} else {
					$status = '未售出';
				}
			}
			
			$saleTotal[] = array('count'=>$vipCard->id, 'money'=>$vipCard->money, 'status'=>$status);
		}
		
		return $saleTotal;
	}
	
	public function getVipCard($cardNo){
		return self::model()->find('id=:id', array(':id'=>$cardNo));
	}
	
	public function saleVipCard($cardNo, $status){
		$time = time();
		$attributes = array(
				'id'=>$cardNo,
				'status'=>$status,
				'atime'=>$time,
				'saled_by'=>Yii::app()->user->id);
		
		if (VipCard::model()->updateByPk($cardNo, $attributes)) {
			return true;
		} 
		return false;
	}
	
	public function activateVipCard($cardNo){
		$attributes = array(
				'id'=>$cardNo,
				'status'=>VipCard::STATUS_ACTIVATED,
				'activated_by'=>Yii::app()->user->id);
		
		if (VipCard::model()->updateByPk($cardNo, $attributes)) {
			return true;
		} 
		return false;
	}
	
	public function existsVIPCard($cardNo, $pass){
		$vipCardInformate = self::model()->find('id=:id AND pass=:pass', 
								array(
									':id'=>$cardNo,
									':pass'=>$pass));
								
		if ($vipCardInformate) {
			return $vipCardInformate;
		} else {
			return 0;
		}
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '卡号',
			'pass' => '密码',
			'money' => '额度',
			'status' => '状态',
			'saled_by' => '销售确认人',
            'activated_by' => '激活人',
			'atime' => '售出确认时间',
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('pass',$this->pass);
		$criteria->compare('money',$this->money);
		$criteria->compare('status',$this->status);
		$criteria->compare('saled_by',$this->saled_by);
        $criteria->compare('activated_by',$this->activated_by);
		$criteria->compare('atime',$this->atime);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}