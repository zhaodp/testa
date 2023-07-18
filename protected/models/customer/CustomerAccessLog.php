<?php

/**
 * This is the model class for table "{{customer_access_log}}".
 *
 * The followings are the available columns in table '{{customer_access_log}}':
 * @property integer $id
 * @property string $udid
 * @property string $gps_type
 * @property string $channel
 * @property string $lng
 * @property string $lat
 * @property string $req_time
 * @property string $created
 */
class CustomerAccessLog extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerAccessLog the static model class
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
		return '{{customer_access_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('udid, gps_type, channel, lng, lat, req_time, created', 'required'),
			array('udid', 'length', 'max'=>50),
			array('gps_type', 'length', 'max'=>15),
			array('channel', 'length', 'max'=>30),
			array('lng, lat', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, udid, gps_type, channel, lng, lat, req_time, created', 'safe', 'on'=>'search'),
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
			'udid' => 'Udid',
			'gps_type' => 'Gps Type',
			'channel' => 'Channel',
			'lng' => 'Lng',
			'lat' => 'Lat',
			'req_time' => 'Req Time',
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
		$criteria->compare('udid',$this->udid,true);
		$criteria->compare('gps_type',$this->gps_type,true);
		$criteria->compare('channel',$this->channel,true);
		$criteria->compare('lng',$this->lng,true);
		$criteria->compare('lat',$this->lat,true);
		$criteria->compare('req_time',$this->req_time,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}