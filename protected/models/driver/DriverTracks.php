<?php

/**
 * This is the model class for table "{{driver_tracks}}".
 *
 * The followings are the available columns in table '{{driver_tracks}}':
 * @property integer $id
 * @property string $imei
 * @property string $hash
 * @property string $location
 * @property string $street
 * @property integer $longitude
 * @property integer $latitude
 * @property string $created
 */
class DriverTracks extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverTracks the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{driver_tracks}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'imei, hash, location, created', 
				'required'), 
			array (
				'imei,longitude, latitude', 
				'length', 
				'max'=>15), 
			array (
				'hash', 
				'length', 
				'max'=>32), 
			array (
				'location', 
				'length', 
				'max'=>512), 
			array (
				'street', 
				'length', 
				'max'=>100), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'imei, hash', 
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
			'imei'=>'Imei', 
			'hash'=>'Hash', 
			'location'=>'Location', 
			'street'=>'Street', 
			'longitude'=>'Longitude', 
			'latitude'=>'Latitude', 
			'created'=>'Created');
	}

	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		$criteria = new CDbCriteria();

		//$criteria->compare('imei', $this->imei);
		$criteria->compare('hash', $this->hash);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
}