<?php

/**
 * This is the model class for table "{{driver_bonus_rank}}".
 *
 * The followings are the available columns in table '{{driver_bonus_rank}}':
 * @property integer $id
 * @property string $driver_id
 * @property string $name
 * @property integer $city_id
 * @property string $bonus_code
 * @property double $bonus
 * @property integer $bind_count
 * @property integer $used_count
 * @property integer $created
 */
class DriverBonusRank extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverBonusRank the static model class
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
		return '{{driver_bonus_rank}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, bonus', 'required'),
			array('city_id, bind_count, used_count, created', 'numerical', 'integerOnly'=>true),
			array('bonus', 'numerical'),
			array('driver_id', 'length', 'max'=>10),
			array('name', 'length', 'max'=>255),
			array('bonus_code', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, driver_id, name, city_id, bonus_code, bonus, bind_count, used_count, created', 'safe', 'on'=>'search'),
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
			'name' => 'Name',
			'city_id' => '城市',
			'bonus_code' => 'Bonus Code',
			'bonus' => 'Bonus',
			'bind_count' => 'Bind Count',
			'used_count' => 'Used Count',
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
		$criteria->compare('driver_id',$this->driver_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('bonus_code',$this->bonus_code,true);
		$criteria->compare('bonus',$this->bonus);
		$criteria->compare('bind_count',$this->bind_count);
		$criteria->compare('used_count',$this->used_count);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}