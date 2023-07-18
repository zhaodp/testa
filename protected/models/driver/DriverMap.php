<?php
// Notice: We extend EMongoDocument class instead of CActiveRecord
class DriverMap extends EMongoDocument {
	public $driver_id;
	public $imei;
	public $longitude;
	public $latitude;
	
	/**
	 * This method have to be defined in every Model
	 * @return string MongoDB collection name, witch will be used to store documents of this model
	 */
	public function getCollectionName() {
		return 'position';
	}
	
	public function primaryKey() {
		return 'driver_id';
	}
	
	// We can define rules for fields, just like in normal CModel/CActiveRecord classes
	public function rules() {
		return array (
			array (
				'driver_id, imei, longitude, latitude', 
				'required'));
	}
	
	// the same with attribute names
	public function attributeNames() {
		return array (
			'driver_id'=>'Driver ID');
	}
	
	/**
	 * This method have to be defined in every model, like with normal CActiveRecord
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
}