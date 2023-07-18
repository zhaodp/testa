<?php

/**
 * This is the model class for table "{{customer_device_token}}".
 *
 * The followings are the available columns in table '{{customer_device_token}}':
 * @property integer $id
 * @property string $deviceToken
 * @property string $phone
 * @property integer $type
 */
class CustomerDeviceToken extends CActiveRecord
{
	const DEVICETOKENBYIPHONE = 0;
	const DEVICETOKENBYANDROID = 1;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerDeviceToken the static model class
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
		return '{{customer_device_token}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('deviceToken', 'required'),
			array('type', 'numerical', 'integerOnly'=>true),
			array('deviceToken', 'length', 'max'=>64),
			array('phone', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, deviceToken, phone, type', 'safe', 'on'=>'search'),
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
			'deviceToken' => 'Device Token',
			'phone' => 'Phone',
			'type' => 'Type',
		);
	}
	
	/**
	 * 记录iphone客户端的deviceToken
	 * @param deviceToken
	 * @param phone
	 * @param type
	 * @return 
	 */
	public static function initDeviceToken($deviceToken, $phone='', $type = 0){
		$device = self::model()->find('deviceToken=:deviceToken', array(':deviceToken'=>$deviceToken));
		if (!$device){
			$device = new CustomerDeviceToken();
		} 
		
		$device->attributes = array('deviceToken'=>$deviceToken, 'phone'=>$phone, 'type'=>$type);
		
		if ($device->save()){
			return true;
		} else {
			return false;
		}
	}
	
	public static function getDeviceTokenList(){
		
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
		$criteria->compare('deviceToken',$this->deviceToken,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('type',$this->type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}