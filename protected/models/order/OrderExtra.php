<?php

/**
 * This is the model class for table "t_order_info".
 *
 * The followings are the available columns in table 't_order_info':
 * @property integer $id
 * @property string $order_id
 * @property string $driver_id
 * @property integer $customer_status
 * @property string $car_number
 * @property string $car_type
 * @property string $order_detail
 * @property string $comment
 * @property string $meta
 * @property integer $created
 */
class OrderExtra extends CActiveRecord
{

	/**
	 * 保存一下订单额外统计信息
	 *
	 * @param $orderId
	 * @param array $attributes
	 */
	public function saveOrderInfo($orderId, $driverId, $attributes = array()){
		$model = new OrderExtra();
		$model->attributes = $attributes;
		$model['order_id'] = $orderId;
		$model['driver_id'] = $driverId;
		$model['created']  = time();
		if(!$model->save()){
			EdjLog::error(json_encode($model->getErrors()));
		}
	}

	/**
	 * 返回订单的额外信息
	 *
	 * @param $orderId | t_order 里面的 order_id
	 * @return CActiveRecord
	 */
	public function getOrderExtra($orderId){
		$criteria = new CDbCriteria();
		$criteria->compare('order_id', $orderId);
		return self::model()->find($criteria);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_order_extra';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('order_id, driver_id, created', 'required'),
			array('customer_status, created', 'numerical', 'integerOnly'=>true),
			array('order_id', 'length', 'max'=>32),
			array('driver_id', 'length', 'max'=>10),
			array('car_number, car_type', 'length', 'max'=>12),
			array('order_detail', 'length', 'max'=>512),
			array('comment', 'length', 'max'=>256),
			array('meta', 'length', 'max'=>1024),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, order_id, driver_id, customer_status, car_number, car_type, order_detail, comment, meta, created', 'safe', 'on'=>'search'),
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
			'id' => 'Id',
			'order_id' => 'Order',
			'driver_id' => 'Driver',
			'customer_status' => 'Customer Status',
			'car_number' => 'Car Number',
			'car_type' => 'Car Type',
			'order_detail' => 'Order Detail',
			'comment' => 'Comment',
			'meta' => 'Meta',
			'created' => 'Created',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('order_id',$this->order_id,true);

		$criteria->compare('driver_id',$this->driver_id,true);

		$criteria->compare('customer_status',$this->customer_status);

		$criteria->compare('car_number',$this->car_number,true);

		$criteria->compare('car_type',$this->car_type,true);

		$criteria->compare('order_detail',$this->order_detail,true);

		$criteria->compare('comment',$this->comment,true);

		$criteria->compare('meta',$this->meta,true);

		$criteria->compare('created',$this->created);

		return new CActiveDataProvider('OrderInfo', array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return OrderExtra the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getDbConnection() {
		return Yii::app()->dbstat;
	}
}