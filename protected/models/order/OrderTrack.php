<?php

/**
 * This is the model class for table "{{order_track}}".
 *
 * The followings are the available columns in table '{{order_track}}':
 * @property integer $id
 * @property integer $order_id
 * @property string $driver_id
 * @property string $imei
 * @property double $latitude
 * @property double $longitude
 * @property string $created
 */
class OrderTrack extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderTrack the static model class
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
		return '{{order_track}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, driver_id, imei, latitude, longitude, created', 'required'),
			array('order_id', 'numerical', 'integerOnly'=>true),
			array('latitude, longitude', 'numerical'),
			array('driver_id', 'length', 'max'=>10),
			array('imei', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, order_id, driver_id, imei, latitude, longitude, created', 'safe', 'on'=>'search'),
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
			'order_id' => 'Order',
			'driver_id' => 'Driver',
			'imei' => 'Imei',
			'latitude' => 'Latitude',
			'longitude' => 'Longitude',
			'created' => 'Created',
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
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('driver_id',$this->driver_id);
		$criteria->compare('imei',$this->imei);
		$criteria->compare('latitude',$this->latitude);
		$criteria->compare('longitude',$this->longitude);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}