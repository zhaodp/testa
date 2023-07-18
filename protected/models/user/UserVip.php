<?php

/**
 * This is the model class for table "{{vip_info}}".
 *
 * The followings are the available columns in table '{{vip_info}}':
 * @property string $id
 * @property string $pass
 * @property string $name
 * @property string $phone
 * @property integer $money
 * @property integer $type
 * @property integer $remain
 * @property string $create_time
 */
class UserVip extends CActiveRecord {
	/**
	 * 储值卡
	 */
	const TYPE_CREDIT = 0;
	/**
	 * 定额卡
	 */
	const TYPE_FIXED = 1;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserVip the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{vip_info}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'id, name, phone', 
				'required'
			), 
			array (
				'money, remain, type', 
				'numerical', 
				'integerOnly'=>true
			), 
			array (
				'id, pass', 
				'length', 
				'max'=>100
			), 
			array (
				'name', 
				'length', 
				'max'=>20
			), 
			array (
				'phone', 
				'length', 
				'max'=>1024
			), 
			array (
				'create_time', 
				'safe'
			), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, pass, name, phone, money, remain, type, create_time', 
				'safe', 
				'on'=>'search'
			)
		);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array (
			'trades'=>array (
				self::HAS_MANY, 
				'VipTrade', 
				'vipcard'
			)
		);
	}
	
	public function afterSave(){
		parent::afterSave();
	}
	
	public function createVIP($params){
		$vip = new UserVip();
		
		$vip->attributes = $params;
		$vip->create_time = date('Y-m-d H:i:s', time());
		if ($vip->insert()){
			//VIP客户消费明细
			$attributes = array (
				'vipcard'=>$params['id'], 
				'order_id'=>0, 
				'type'=>VipTrade::TYPE_INCOME, 
				'amount'=>$params['money'], 
				'comment'=>'VIP卡充值：' . $params['id'], 
				'created'=>time());
			$trade = new VipTrade();
			$amount = $trade->addTrade($attributes);
			
			return true;
		}
		return false;
	}
	
	public function getCreateTimeByCard($cardNo){
		$userVip = UserVip::model()->getByCard($cardNo);
		if ($userVip){
			return $userVip->create_time;
		} 
		return '';
	}
	
	/**
	 * 
	 * 修改VIP账户余额,参数为变化金额
	 * @param int $vipcard
	 * @param int $amount
	 */
	public function setBalance($amount) {
		

		$amount = $this->remain + $amount;
		
		//如果是固定额度卡，不允许透支，设为零
		if ($this->type == 1 && $amount < 0)
		{
			$amount = 0;
		}
		
		return $this->updateByPk($this->id, array (
			'remain'=>$amount
		));
	}
	
	/**
	 * 
	 * 查询VIP余额
	 */
	public function getBalance() {
		return self::getByCard($this->id)->remain;
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'pass'=>'Pass', 
			'name'=>'Name', 
			'phone'=>'Phone', 
			'money'=>'Money', 
			'remain'=>'Remain',
			'type'=>'Type', 
			'create_time'=>'Create Time'
		);
	}
	
	/**
	 * 
	 * 按电话号码查询vip信息
	 * @param int $phone
	 */
	public function getByPhone($phone) {
		if (!empty($phone)){
			
			//礼品卡优先 0:透支卡 1：礼品卡			
			$criteria = new CDbCriteria();
			$criteria->condition = 'phone = :phone and type=1 and remain > 0';
			$criteria->params = array (
				':phone'=>trim($phone)
			);
			$vip = UserVip::model()->find($criteria);
			if (!$vip)
			{
				$criteria = new CDbCriteria();
				$criteria->condition = 'phone like :phone and type=0';
				$criteria->params = array (
					':phone'=>'%'.trim($phone).'%'
				);
				$vip = UserVip::model()->find($criteria);				
			}

		} 
		else {
			$vip = null;
		}
		return $vip;
	}
	
	/**
	 * 
	 * 按卡号查询vip信息
	 * @param int $vipcard
	 */
	public function getByCard($vipcard) {
		$criteria = new CDbCriteria();
		$criteria->condition = 'id =:vipcard';
		$criteria->params = array (
			':vipcard'=>$vipcard
		);
		
		$vip = UserVip::model()->find($criteria);
		return $vip;
	}
	
	/**
	 * 
	 * 增加一笔VIP用户的费用，计算余额并返回，
	 * @param int $vipcard
	 * @param int $order_id
	 * @param int $cast
	 * @return int 余额
	 */
	public function addAccount($vipcard, $cast, $order_id) {
		//查询卡号，增加一条消费记录，扣除余额
		$vip = self::getByCard($vipcard);
		if ($vip) {
			
			$vip->updateByPk($vipcard, array (
				'remain'=>$vip->remain-$cast
			));
		}
	
	}
	
	public function recharge($vipId, $cardNo, $balance) {
		//查询卡号，增加一条消费记录，增加余额
		$vip = self::getByCard($vipId);
		if ($vip) {
			$remain = $vip->remain + $balance;
			$money = $vip->money + $balance;
			$vip->updateByPk($vipId, array (
				'remain'=>$remain,
				'money'=>$money
			));
			
			//VIP客户消费明细
			$attributes = array (
				'vipcard'=>$vipId, 
				'order_id'=>0, 
				'type'=>VipTrade::TYPE_INCOME, 
				'amount'=>$balance, 
				'comment'=>'VIP卡充值：' . $cardNo, 
				'created'=>time());
			
			$trade = new VipTrade();
			$amount = $trade->addTrade($attributes);
		}
		
		return true;
	}
	
	public function getVipBalanceTotal(){
		$criteria = new CDbCriteria();
		$criteria->select = 'SUM(remain) AS money';
		UserVip::$db = Yii::app()->db_readonly;
		$result= UserVip::model()->find($criteria);
		UserVip::$db = Yii::app()->db;
		return $result;
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		

		$criteria = new CDbCriteria();
		
		$criteria->compare('id', $this->id, true);
		$criteria->compare('pass', $this->pass, true);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('phone', $this->phone, true);
		$criteria->compare('money', $this->money);
		$criteria->compare('remain', $this->remain);
		$criteria->compare('type', $this->type);		
		$criteria->compare('create_time', $this->create_time, true);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria
		));
	}
}
