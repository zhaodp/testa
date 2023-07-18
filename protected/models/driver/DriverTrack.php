<?php

/**
 * This is the model class for table "{{driver_track}}".
 *
 * The followings are the available columns in table '{{driver_track}}':
 * @property integer $id
 * @property string $driver_id
 * @property string $imei
 * @property double $latitude
 * @property double $longitude
 * @property string $street
 * @property integer $status
 * @property string $created
 */
class DriverTrack extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverTrack the static model class
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
		return '{{driver_track}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('driver_id, imei, latitude, longitude, street, status, created', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('latitude, longitude', 'numerical'),
			array('driver_id', 'length', 'max'=>10),
			array('imei', 'length', 'max'=>20),
			array('street', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, driver_id, imei, latitude, longitude, street, status, created', 'safe', 'on'=>'search'),
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
			'driver_id' => 'Driver',
			'imei' => 'Imei',
			'latitude' => 'Latitude',
			'longitude' => 'Longitude',
			'street' => 'Street',
			'status' => 'Status',
			'created' => 'Created',
		);
	}
	
	public function beforeSave(){
		if(parent::beforeSave()){
			$this->created = date(Yii::app()->params['formatDateTime'],time());
			return true;
		}
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
		$criteria->compare('driver_id',$this->driver_id,true);
		$criteria->compare('imei',$this->imei,true);
		$criteria->compare('latitude',$this->latitude);
		$criteria->compare('longitude',$this->longitude);
		$criteria->compare('street',$this->street,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}