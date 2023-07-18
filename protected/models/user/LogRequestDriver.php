<?php

/**
 * This is the model class for table "{{log_request_driver}}".
 *
 * The followings are the available columns in table '{{log_request_driver}}':
 * @property integer $id
 * @property string $uuid
 * @property integer $longitude
 * @property integer $latitude
 * @property integer $baidu_lng
 * @property integer $baidu_lat
 * @property integer $created
 * @property string $device
 * @property string $os
 * @property string $version
 */
class LogRequestDriver extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return LogRequestDriver the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{log_request_driver}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'uuid, longitude, latitude, created', 
				'required'), 
			array (
				'longitude, latitude, baidu_lng, baidu_lat, created', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'uuid', 
				'length', 
				'max'=>50), 
			array (
				'device, os', 
				'length', 
				'max'=>20), 
			array (
				'version', 
				'length', 
				'max'=>10), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, uuid, longitude, latitude, baidu_lng, baidu_lat, created, device, os, version', 
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
			'uuid'=>'Uuid', 
			'longitude'=>'Longitude', 
			'latitude'=>'Latitude', 
			'baidu_lng'=>'Baidu Lng', 
			'baidu_lat'=>'Baidu Lat', 
			'created'=>'Created', 
			'device'=>'device', 
			'os'=>'Os', 
			'version'=>'Version');
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
		$criteria->compare('longitude', $this->longitude);
		$criteria->compare('latitude', $this->latitude);
		$criteria->compare('baidu_lng', $this->baidu_lng);
		$criteria->compare('baidu_lat', $this->baidu_lat);
		$criteria->compare('created', $this->created);
		$criteria->compare('device', $this->device, true);
		$criteria->compare('os', $this->os, true);
		$criteria->compare('version', $this->version, true);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
}