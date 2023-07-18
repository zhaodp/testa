<?php

/**
 * This is the model class for table "{{driver_bonus}}".
 *
 * The followings are the available columns in table '{{driver_bonus}}':
 * @property integer $id
 * @property integer $city_id
 * @property string $driver_id
 * @property integer $bonus
 * @property string $created
 */
class DriverBonus extends CActiveRecord {
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverBonus the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{driver_bonus}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'city_id, driver_id, bonus', 
				'required'), 
			array (
				'city_id, bonus', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'driver_id', 
				'length', 
				'max'=>10), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, city_id, driver_id, bonus, created', 
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
			'city_id'=>'City', 
			'driver_id'=>'Driver', 
			'bonus'=>'Bonus', 
			'created'=>'Created');
	}
	
	/**
	 * 
	 * 查询优惠码分配的司机工号
	 * @param int $bonus
	 */
	public function getDriverID($bonus) {
		$driver_bonus = self::find('bonus=:bonus', array (
			':bonus'=>$bonus));
		if ($driver_bonus) {
			return $driver_bonus->attributes;
		}
		return null;
	}
	
	public function bindDriverBonus($attributes){
		$driverBonus = new DriverBonus();
		
		$driverBonus->attributes = $attributes;
		
		$driverBonus->created = date(Yii::app()->params['formatDateTime'], time());
		$driverBonus->city_id = Yii::app()->user->city;
		$driverBonus->driver_id = Yii::app()->user->id;
		
		return $driverBonus->save();
	}
	
	public function getCurrentDriverBonus(){
		$driverBonus = DriverBonus::model()->find('driver_id=:driver_id', array (
			':driver_id'=>Yii::app()->user->id));
		
		if (!$driverBonus) {
			$driverBonus = new DriverBonus();
		}
		
		return $driverBonus;
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
		$criteria->compare('city_id', $this->city_id);
		$criteria->compare('driver_id', $this->driver_id, true);
		$criteria->compare('bonus', $this->bonus);
		
		return new CActiveDataProvider($this, array (
			'criteria'=>$criteria));
	}
}