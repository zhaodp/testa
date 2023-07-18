<?php

/**
 * This is the model class for table "{{record}}".
 *
 * The followings are the available columns in table '{{record}}':
 * @property integer $id
 * @property string $uuid
 * @property string $phone
 * @property string $employee_id
 * @property string $longitude
 * @property string $latitude
 * @property string $insert_time
 * @property integer $duration
 */
class UserCall extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return UserCall the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{record}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'uuid, phone, employee_id, longitude, latitude', 
				'required'
			), 
			array (
				'duration', 
				'numerical', 
				'integerOnly'=>true
			), 
			array (
				'uuid, phone, employee_id, longitude, latitude', 
				'length', 
				'max'=>255
			), 
			array (
				'insert_time', 
				'safe'
			), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, uuid, phone, employee_id, longitude, latitude, insert_time, duration', 
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
			'uuid'=>'Uuid', 
			'phone'=>'Phone', 
			'employee_id'=>'Employee', 
			'longitude'=>'Longitude', 
			'latitude'=>'Latitude', 
			'insert_time'=>'Insert Time', 
			'duration'=>'Duration'
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
		$criteria->compare('uuid', $this->uuid, true);
		$criteria->compare('phone', $this->phone, true);
		$criteria->compare('employee_id', $this->employee_id, true);
		$criteria->compare('longitude', $this->longitude, true);
		$criteria->compare('latitude', $this->latitude, true);
		$criteria->compare('insert_time', $this->insert_time, true);
		$criteria->compare('duration', $this->duration);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria
		));
	}
}