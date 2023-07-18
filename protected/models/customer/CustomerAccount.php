<?php

/**
 * This is the model class for table "{{customer_account}}".
 *
 * The followings are the available columns in table '{{customer_account}}':
 * @property integer $id
 * @property integer $type
 * @property integer $customer_id
 * @property string $cast
 * @property integer $order_id
 * @property string $comment
 * @property integer $created
 */
class CustomerAccount extends FinanceActiveRecord {
	/**
	 * 现金充值
	 */
	const TYPECASH = 0;
	/**
	 * 支付宝充值
	 */
	const TYPEALIPAY = 1;
	/**
	 * 邀请码充值
	 */
	const TYPEINVITECODE = 2;
	/**
	 * 
	 */
	const TYPECHARGECODE = 3;
	/**
	 * 消费
	 */
	const TYPEPAY = 4;
	/**
	 * 呼叫中心充值
	 */
	const TYPECHARGECODECALLCENTER = 5;
	/**
	 * 银联充值
	 */
	const TYPEUNIONPAY = 6;
	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerAccount the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{customer_account}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'type, customer_id, cast, comment, created', 
				'required'), 
			array (
				'type, customer_id, order_id, created', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'cast', 
				'length', 
				'max'=>10), 
			array (
				'comment', 
				'length', 
				'max'=>64), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, type, customer_id, cast, order_id, comment, created', 
				'safe', 
				'on'=>'search'));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array ();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'type'=>'Type', 
			'customer_id'=>'Customer', 
			'cast'=>'Cast', 
			'order_id'=>'Order', 
			'comment'=>'Comment', 
			'created'=>'Created');
	}
	
	public static function initAccount($accountType, $customer_id, $cast, $comment = '系统自动执行', $order_id = 0) {
		$customerAccount = new CustomerAccount();
		$attr = array (
			'customer_id'=>$customer_id, 
			'type'=>$accountType, 
			'cast'=>$cast, 
			'order_id'=>$order_id, 
			'created'=>time(), 
			'comment'=>$comment);
		
		$customerAccount->attributes = $attr;
		$customerAccount->save();
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		

		$criteria = new CDbCriteria();
		
		$criteria->compare('id', $this->id);
		$criteria->compare('type', $this->type);
		$criteria->compare('customer_id', $this->customer_id);
		$criteria->compare('cast', $this->cast, true);
		$criteria->compare('order_id', $this->order_id);
		$criteria->compare('comment', $this->comment, true);
		$criteria->compare('created', $this->created);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
    public function insertCustomer($data){
        $customer = CustomerService::service()->initCustomer( $data['phone']);
        $customerIncomeParams = array(
            'trans_type' => CarCustomerTrans::TRANS_TYPE_CARD,
            'source' => CarCustomerTrans::TRANS_SOURCE_CARD_PAY,
            'operator'=>$data['operator'],
            'remark'=>'充值卡:'.$data['id'],
        );
        $ret =  BCustomers::model()->income($customer->id,$data['balance'],$customerIncomeParams);
        if( $ret['code']===0){
            return true;
        }else{
            return false;
        }
    }
}
