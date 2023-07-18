<?php

/**
 * This is the model class for table "{{lbs}}".
 *
 * The followings are the available columns in table '{{lbs}}':
 * @property integer $mcc
 * @property integer $mnc
 * @property integer $lac
 * @property integer $ci
 * @property double $longitude
 * @property double $latitude
 * @property string $update_time
 * @property string $address
 */
class Lbs extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Lbs the static model class
	 */
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{lbs}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array(
				'update_time', 
				'required'), 
			array(
				'mcc, mnc, lac, ci', 
				'numerical', 
				'integerOnly'=>true), 
			array(
				'longitude, latitude', 
				'numerical'), 
			array(
				'address', 
				'length', 
				'max'=>255), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array(
				'mcc, mnc, lac, ci, longitude, latitude, update_time, address', 
				'safe', 
				'on'=>'search'));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'mcc'=>'Mcc', 
			'mnc'=>'Mnc', 
			'lac'=>'Lac', 
			'ci'=>'Ci', 
			'longitude'=>'Longitude', 
			'latitude'=>'Latitude', 
			'update_time'=>'Update Time', 
			'address'=>'Address');
	}
	
	public static function checkLocation($mcc, $mnc, $lac, $ci)
	{
		$cache_key = md5($mcc.$mnc.$lac.$ci);
		$lbs = Yii::app()->cache->get($cache_key);
		
		if (!$lbs)
		{
			$attributes = array(
				'mcc'=>$mcc, 
				'mnc'=>$mnc, 
				'lac'=>$lac, 
				'ci'=>$ci);
			$lbs = self::model()->findByAttributes($attributes);
			Yii::app()->cache->set($cache_key,$lbs,28800);
		}
		return $lbs;
	}
	
	public static function addLocation($mcc, $mnc, $lac, $ci, $latitude, $longitude, $address)
	{
		$attributes = array(
			'mcc'=>$mcc, 
			'mnc'=>$mnc, 
			'lac'=>$lac, 
			'ci'=>$ci, 
			'latitude'=>$latitude, 
			'longitude'=>$longitude, 
			'address'=>$address, 
			'update_time'=>date('Y-m-d H:i:s', time()));
		
		$model = new Lbs();
		$model->attributes = $attributes;
		$model->insert();
		return $model->getPrimaryKey();
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		

		$criteria = new CDbCriteria();
		
		$criteria->compare('mcc', $this->mcc);
		$criteria->compare('mnc', $this->mnc);
		$criteria->compare('lac', $this->lac);
		$criteria->compare('ci', $this->ci);
		$criteria->compare('longitude', $this->longitude);
		$criteria->compare('latitude', $this->latitude);
		$criteria->compare('update_time', $this->update_time, true);
		$criteria->compare('address', $this->address, true);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria));
	}
}