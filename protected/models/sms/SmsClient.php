<?php

/**
 * This is the model class for table "{{sms_queue}}".
 *
 * The followings are the available columns in table '{{sms_queue}}':
 * @property integer $id
 * @property string $sender
 * @property string $receiver
 * @property string $message
 * @property integer $subcode
 * @property string $call_date
 * @property integer $status
 * @property integer $created
 */
class SmsClient extends CActiveRecord {
	const WAIT_SEND = 0;
	const SUCCESS_SEND = 1;
	const FAIL_SEND = 2;
	const NOT_SEND = 3;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return SmsQueue the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{sms_client}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'sender, receiver, message, call_date', 
				'required'
			), 
			array (
				'subcode, status, created', 
				'numerical', 
				'integerOnly'=>true
			), 
			array (
				'sender, receiver', 
				'length', 
				'max'=>20
			), 
			array (
				'message', 
				'length', 
				'max'=>256
			), 
			array (
				'call_date', 
				'length', 
				'max'=>8
			), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, sender, receiver, message, subcode, call_date, status, created', 
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
			'employee'=>array (
				self::BELONGS_TO, 
				'Employee', 
				'sender'
			)
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'sender'=>'Sender', 
			'receiver'=>'Receiver', 
			'message'=>'Message', 
			'subcode'=>'Subcode', 
			'call_date'=>'Call Date', 
			'status'=>'Status', 
			'created'=>'Created'
		);
	}
	
	public function beforeSave() {
		if (parent::beforeSave()) {
			//已屏蔽的司机不发送
			if ($this->employee->mark==1) {
				return false;
			}
			//每个客户/司机/天 只发送一次短信, 
			$criteria = new CDbCriteria();
			$criteria->condition = 'sender=:sender and receiver=:receiver and call_date=:call_date';
			$criteria->params = array (
				':sender'=>$this->sender, 
				':receiver'=>$this->receiver, 
				':call_date'=>$this->call_date
			);
			
			$result = $this->find($criteria);
			if (!$result) {
				//$this->created = time();
				return true;
			} else {
				return false;
			}
		}
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
		$criteria->compare('sender', $this->sender, true);
		$criteria->compare('receiver', $this->receiver, true);
		$criteria->compare('message', $this->message, true);
		$criteria->compare('subcode', $this->subcode);
		$criteria->compare('call_date', $this->call_date, true);
		$criteria->compare('status', $this->status);
		$criteria->compare('created', $this->created);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria
		));
	}
}