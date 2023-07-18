<?php

/**
 * This is the model class for table "{{order_queue_dispatch}}".
 *
 * The followings are the available columns in table '{{order_queue_dispatch}}':
 * @property integer $id
 * @property integer $queue_id
 * @property string $driver_id
 * @property string $created
 */
class OrderQueueDispatch extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderQueueDispatch the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{order_queue_dispatch}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'queue_id, driver_id', 
				'required'), 
			array (
				'queue_id', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'driver_id', 
				'length', 
				'max'=>6), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, queue_id, driver_id, created', 
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
			'queue_id'=>'Queue', 
			'driver_id'=>'Driver', 
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
		
		$criteria->compare('queue_id', $this->queue_id);
		$criteria->compare('driver_id', $this->driver_id);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
}