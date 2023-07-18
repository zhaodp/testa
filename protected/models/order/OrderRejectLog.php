<?php

/**
 * This is the model class for table "{{order_reject_log}}".
 *
 * The followings are the available columns in table '{{order_reject_log}}':
 * @property string $id
 * @property integer $queue_id
 * @property integer $order_id
 * @property integer $type
 * @property string $driver_id
 * @property string $created
 */
class OrderRejectLog extends CActiveRecord
{
	//增加拒绝类型 BY AndyCong 2013-12-26
	const REJECT_TYPE_SYSTEM_BACK    = 1; //系统收回
	const REJECT_TYPE_RECEIVE_FAILED = 2; //30s未接成功
	const REJECT_TYPE_DRIVER_REJECT  = 3; //司机主动拒绝
	const REJECT_TYPE_SERVICE_REJECT = 4; //司机服务中弹回拒绝
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderRejectLog the static model class
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
		return '{{order_reject_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('queue_id, driver_id, created', 'required'),
			array('queue_id, order_id, type', 'numerical', 'integerOnly'=>true),
			array('driver_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, queue_id, order_id, type, driver_id, created', 'safe', 'on'=>'search'),
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
			'queue_id' => 'Queue',
			'order_id' => 'Order',
			'type' => 'Type',
			'driver_id' => 'Driver',
			'created' => 'created',
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
		$criteria->compare('queue_id',$this->queue_id);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('driver_id',$this->driver_id);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}