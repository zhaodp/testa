<?php

/**
 * This is the model class for table "{{rank_mon_list}}".
 *
 * The followings are the available columns in table '{{rank_mon_list}}':
 * @property integer $id
 * @property string $name
 * @property string $driver_id
 * @property integer $city_id
 * @property integer $order_count
 * @property integer $call_count
 * @property double $work_day_count
 * @property integer $phone_count
 * @property integer $income
 * @property string $created
 */
class RankMonList extends CActiveRecord
{	
//	public function init() {
//		self::$db = Yii::app()->dbstat;
//		parent::init();
//	}
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RankMonList the static model class
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
//		self::$db = Yii::app()->dbstat;
		return '{{rank_mon_list}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('created', 'required'),
			array('city_id, order_count, call_count, phone_count, income', 'numerical', 'integerOnly'=>true),
			array('work_day_count', 'numerical'),
			array('name, driver_id', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, driver_id, city_id, order_count, call_count, work_day_count, phone_count, income, created', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'driver_id' => 'Driver',
			'city_id' => 'City',
			'order_count' => 'Order Count',
			'call_count' => 'Call Count',
			'work_day_count' => 'Work Day Count',
			'phone_count' => 'Phone Count',
			'income' => 'Income',
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
		$criteria->compare('name',$this->name);
		$criteria->compare('driver_id',$this->driver_id);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('order_count',$this->order_count);
		$criteria->compare('call_count',$this->call_count);
		$criteria->compare('work_day_count',$this->work_day_count);
		$criteria->compare('phone_count',$this->phone_count);
		$criteria->compare('income',$this->income);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}