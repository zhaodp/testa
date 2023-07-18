<?php

/**
 * This is the model class for table "{{customer_car}}".
 *
 * The followings are the available columns in table '{{customer_car}}':
 * @property integer $car_id
 * @property integer $user_id
 * @property string $brand
 * @property integer $status
 * @property string $number
 */
class CustomerCar extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerCar the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{customer_car}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'user_id, brand, status, number, create_time', 
				'required'), 
			array (
				'user_id', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'brand, number', 
				'length', 
				'max'=>20), 
			array (
				'status', 
				'length', 
				'max'=>100), 
			
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'car_id, user_id, brand, status, number, create_time', 
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
			'car_id'=>'Car', 
			'user_id'=>'User', 
			'brand'=>'Brand', 
			'status'=>'Status', 
			'number'=>'Number', 
			'create_time'=>'Create Time');
	}
	
	public function beforeSave() {
		if (parent::beforeSave()) {
			$criteria = new CDbCriteria();
			$criteria->compare('number', $this->number);
			$criteria->compare('user_id', $this->user_id);
			$result = $this->find($criteria);
			if (!$result) {
				$this->car_id = $this->getPrimaryKey();
				return true;
			} else {
				$this->car_id = $result['car_id'];
				return false;
			}
		}
	}
	
	/**
	 * 
	 * 根据车牌号码查询汽车ID
	 * @param string $number
	 */
	public function getCustomerCar($number, $user_id = 0) {
		$condition = 'number=:number';
		$params = array(':number'=>$number);
		if($user_id != 0){
			$condition .= ' and user_id = :user_id';
			$params[':user_id'] = $user_id;
		}
		$customerCar = Yii::app()->db_readonly->createCommand()
					->select("*")
					->from("t_customer_car")
					->where($condition, $params)
					->queryRow();
		if ($customerCar) {
			return $customerCar;
		}
	}
	
	/**
	 * 根据车牌号码查询汽车车牌号
	 * Enter description here ...
	 * @param unknown_type $user_id
	 */
	public function getCarNumber($car_id){
		return Yii::app()->db->createCommand()
						->select("number")
						->from('t_customer_car')
						->where('car_id = :car_id', array('car_id' => $car_id))
						->queryScalar();
	}
	
	
	public static function getCars($user_id) {
		$criteria = new CDbCriteria(array (
			'condition'=>'user_id=:user_id', 
			'params'=>array (
				':user_id'=>$user_id)));
		
		$cars = self::model()->findAll($criteria);
		if ($cars) {
			return $cars;
		}
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		

		$criteria = new CDbCriteria();
		
		$criteria->compare('car_id', $this->car_id);
		$criteria->compare('user_id', $this->user_id);
		$criteria->compare('brand', $this->brand);
		$criteria->compare('status', $this->status);
		$criteria->compare('number', $this->number);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
}