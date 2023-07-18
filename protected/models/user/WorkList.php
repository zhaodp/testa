<?php

/**
 * This is the model class for table "{{work_list}}".
 *
 * The followings are the available columns in table '{{work_list}}':
 * @property integer $id
 * @property integer $wid
 * @property string $name
 * @property string $phone
 * @property string $call_time
 * @property string $booking_time
 * @property string $reach_time
 * @property integer $reach_distance
 * @property string $start_time
 * @property string $end_time
 * @property string $start_location
 * @property string $end_location
 * @property integer $distance
 * @property integer $charge
 * @property integer $tip
 * @property string $car_type
 * @property string $car_stative
 * @property string $insert_time
 * @property integer $type
 * @property string $employee_id
 * @property string $user
 * @property string $car_number
 * @property string $vip_id
 */
class WorkList extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return WorkList the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{work_list}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'wid, type', 
				'required'
			), 
			array (
				'wid, reach_distance, distance, charge, tip, type', 
				'numerical', 
				'integerOnly'=>true
			), 
			array (
				'name, phone, employee_id', 
				'length', 
				'max'=>30
			), 
			array (
				'start_location, end_location, car_type, car_stative, user, car_number', 
				'length', 
				'max'=>255
			), 
			array (
				'vip_id', 
				'length', 
				'max'=>50
			), 
			array (
				'call_time, booking_time, reach_time, start_time, end_time, insert_time', 
				'safe'
			), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, wid, name, phone, call_time, booking_time, reach_time, reach_distance, start_time, end_time, start_location, end_location, distance, charge, tip, car_type, car_stative, insert_time, type, employee_id, user, car_number, vip_id', 
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
		return array ();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'wid'=>'Wid', 
			'name'=>'Name', 
			'phone'=>'Phone', 
			'call_time'=>'Call Time', 
			'booking_time'=>'Booking Time', 
			'reach_time'=>'Reach Time', 
			'reach_distance'=>'Reach Distance', 
			'start_time'=>'Start Time', 
			'end_time'=>'End Time', 
			'start_location'=>'Start Location', 
			'end_location'=>'End Location', 
			'distance'=>'Distance', 
			'charge'=>'Charge', 
			'tip'=>'Tip', 
			'car_type'=>'Car Type', 
			'car_stative'=>'Car Stative', 
			'insert_time'=>'Insert Time', 
			'type'=>'Type', 
			'employee_id'=>'Employee', 
			'user'=>'User', 
			'car_number'=>'Car Number', 
			'vip_id'=>'Vip'
		);
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
		$criteria->compare('wid', $this->wid);
		$criteria->compare('name', $this->name, true);
		$criteria->compare('phone', $this->phone, true);
		$criteria->compare('call_time', $this->call_time, true);
		$criteria->compare('booking_time', $this->booking_time, true);
		$criteria->compare('reach_time', $this->reach_time, true);
		$criteria->compare('reach_distance', $this->reach_distance);
		$criteria->compare('start_time', $this->start_time, true);
		$criteria->compare('end_time', $this->end_time, true);
		$criteria->compare('start_location', $this->start_location, true);
		$criteria->compare('end_location', $this->end_location, true);
		$criteria->compare('distance', $this->distance);
		$criteria->compare('charge', $this->charge);
		$criteria->compare('tip', $this->tip);
		$criteria->compare('car_type', $this->car_type, true);
		$criteria->compare('car_stative', $this->car_stative, true);
		$criteria->compare('insert_time', $this->insert_time, true);
		$criteria->compare('type', $this->type);
		$criteria->compare('employee_id', $this->employee_id, true);
		$criteria->compare('user', $this->user, true);
		$criteria->compare('car_number', $this->car_number, true);
		$criteria->compare('vip_id', $this->vip_id, true);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria
		));
	}
}