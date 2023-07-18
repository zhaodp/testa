<?php

/**
 * This is the model class for table "{{rank_day_list}}".
 *
 * The followings are the available columns in table '{{rank_day_list}}':
 * @property integer $id
 * @property string $name
 * @property string $driver_id
 * @property integer $city_id
 * @property integer $order_count
 * @property integer $call_count
 * @property integer $phone_count
 * @property integer $income
 * @property string $created
 */
class RankDayList extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RankDayList the static model class
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
		return '{{rank_day_list}}';
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
			array('name, driver_id', 'length', 'max'=>20),
			array('mon', 'length', 'max'=>6),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, driver_id, city_id, order_count, call_count, phone_count, income, created', 'safe', 'on'=>'search'),
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
			'name' => '司机姓名',
			'driver_id' => '司机工号',
			'city_id' => 'City',
			'order_count' => '总接单量',
			'call_count' => '呼叫中心派单量',
			'phone_count' => '客户直接呼叫量',
			'income' => '收入',
			'mon' => 'mon',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('driver_id',$this->driver_id,true);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('order_count',$this->order_count);
		$criteria->compare('call_count',$this->call_count);
		$criteria->compare('phone_count',$this->phone_count);
		$criteria->compare('income',$this->income);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}