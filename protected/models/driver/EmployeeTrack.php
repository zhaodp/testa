<?php

/**
 * This is the model class for table "{{employee_track}}".
 *
 * The followings are the available columns in table '{{employee_track}}':
 * @property integer $id
 * @property string $imei
 * @property integer $mnc
 * @property integer $mcc
 * @property integer $lac
 * @property integer $ci
 * @property integer $state
 * @property string $insert_time
 */
class EmployeeTrack extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return EmployeeTrack the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{employee_track}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'mnc, mcc, lac, ci, state', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'imei', 
				'length', 
				'max'=>50), 
			array (
				'insert_time', 
				'safe'), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, imei, mnc, mcc, lac, ci, state, insert_time', 
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
			'mnc'=>'Mnc', 
			'mcc'=>'Mcc', 
			'lac'=>'Lac', 
			'ci'=>'Ci', 
			'state'=>'State', 
			'insert_time'=>'Insert Time');
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
		$criteria->compare('imei', $this->imei);
		$criteria->compare('mnc', $this->mnc);
		$criteria->compare('mcc', $this->mcc);
		$criteria->compare('lac', $this->lac);
		$criteria->compare('ci', $this->ci);
		$criteria->compare('state', $this->state);
		$criteria->compare('insert_time', $this->insert_time);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
}