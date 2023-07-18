<?php

/**
 * This is the model class for table "{{lbs2gps}}".
 *
 * The followings are the available columns in table '{{lbs2gps}}':
 * @property integer $id
 * @property integer $mcc
 * @property integer $mnc
 * @property integer $lac
 * @property integer $ci
 * @property integer $longitude
 * @property integer $latitude
 * @property string $address
 * @property integer $update_time
 */
class Lbs2gps extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Lbs2gps the static model class
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
		return '{{lbs2gps}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('update_time', 'required'),
			array('mcc, mnc, lac, ci, longitude, latitude, baidu_lng, baidu_lat, update_time', 'numerical', 'integerOnly'=>true),
			array('address', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, mcc, mnc, lac, ci, longitude, latitude, baidu_lng, baidu_lat, address, update_time', 'safe', 'on'=>'search'),
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
			'mcc' => 'Mcc',
			'mnc' => 'Mnc',
			'lac' => 'Lac',
			'ci' => 'Ci',
			'longitude' => 'Longitude',
			'latitude' => 'Latitude',
			'address' => 'Address',
			'update_time' => 'Update Time',
		);
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
		$criteria->compare('mcc',$this->mcc);
		$criteria->compare('mnc',$this->mnc);
		$criteria->compare('lac',$this->lac);
		$criteria->compare('ci',$this->ci);
		$criteria->compare('longitude',$this->longitude);
		$criteria->compare('latitude',$this->latitude);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('update_time',$this->update_time);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}