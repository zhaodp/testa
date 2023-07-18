<?php

/**
 * This is the model class for table "{{customers}}".
 *
 * The followings are the available columns in table '{{customers}}':
 * @property integer $id
 * @property string $phone
 * @property string $name
 * @property string $balance
 * @property integer $type
 * @property integer $created
 */
class Customers extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Customers the static model class
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
		return '{{customers}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('phone, type, created', 'required'),
			array('type, created', 'numerical', 'integerOnly'=>true),
			array('phone', 'length', 'max'=>32),
			array('name', 'length', 'max'=>128),
			array('balance', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, phone, name, balance, type, created', 'safe', 'on'=>'search'),
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
			'phone' => 'Phone',
			'name' => 'Name',
			'balance' => 'Balance',
			'type' => 'Type',
			'created' => 'Created',
		);
	}
	
	/**
	 * 初始化客户电话
	 * @param string $phone 手机号码
	 */
	public static function initCustomer($phone){
		$customer = self::getCustomer($phone);
		if (!$customer){
			$customer = new Customers();
			$customer->attributes = array('phone'=>$phone, 'created'=>time());
			$customer->save();
		} 
		
		return $customer;
	}
	
	/**
	 * 用户的信息 
	 * @param string $phone 手机号码
	 */
	public static function getCustomer($phone) {
		$customer = Customers::model()->find('phone=:phone', array(':phone'=>$phone));
		return $customer;
	}
	
	/**
	 * 用户的信息 
	 * @param integer $id
	 * @param string $phone 手机号码
	 */
	public static function getCustomerByAttr($id, $phone) {
		$customer = Customers::model()->find('id=:id and phone=:phone', array('id'=>$id, ':phone'=>$phone));
		return $customer;
	}
	
	/**
	 * 更新用户的称谓 
	 * @param string $phone 手机号码
	 * @param string $name 称谓
	 */
	public static function updateCustomerName($phone, $name) {
		$customer = Customers::getCustomer($phone);
		$customer->name = $name;
		if ($customer->update()){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 更新用户的余额 
	 * @param string $phone 手机号码
	 * @param string $balance 余额
	 */
	public static function updateCustomerBalance($phone, $balance) {
		$customer = Customers::getCustomer($phone);
		$customer->balance += $balance;
		if ($customer->update()){
			return true;
		} else {
			return false;
		}
	}
	
	
	/**
	 * 用户的手机身份验证token
	 * @param string $passwd  
	 * @param string $phone 手机号码
	 */
	public static function authenticate($phone, $passwd) {
		$validate = CustomerPass::validatePerLoginPasswd($phone, $passwd);
		if ($validate == CustomerPass::CUSTOMERPASS_PASS){
			//生成AuthToken
			$token = CustomerToken::createAuthtoken($phone);
			$ret = array (
					'code'=>0,
					'token'=>$token, 
					'message'=>'登录成功');
		} else {
			switch ($validate){
				case CustomerPass::CUSTOMERPASS_UNPERLOGIN:
					$message = '未进行预登录';
					break;
				case CustomerPass::CUSTOMERPASS_PASS_ERROR:
					$message = '密码错误';
					break;
				case CustomerPass::CUSTOMERPASS_PASS_EXPIRED:
					$message = '预登录密码过期';
					break;
			}
			$ret = array (
					'code'=>1,
					'error'=>$validate, 
					'message'=>$message);
		}
		
		return $ret;
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
		$criteria->compare('phone',$this->phone);
		$criteria->compare('name',$this->name);
		$criteria->compare('balance',$this->balance);
		$criteria->compare('type',$this->type);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}