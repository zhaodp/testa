<?php

/**
 * This is the model class for table "{{invest_answer}}".
 *
 * The followings are the available columns in table '{{invest_answer}}':
 * @property integer $id
 * @property integer $invest_id
 * @property integer $phone
 * @property integer $order_id
 * @property string $driver_id
 * @property integer $city_id
 * @property integer $question_id
 * @property string $answer
 * @property string $create_time
 * @property string $update_time
 * @property string $operator
 */
class InvestAnswer extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{invest_answer}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('update_time', 'required'),
			array('invest_id, phone, order_id, city_id, question_id', 'numerical', 'integerOnly'=>true),
			array('driver_id', 'length', 'max'=>10),
			array('answer', 'length', 'max'=>500),
			array('operator', 'length', 'max'=>50),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, invest_id, phone, order_id, driver_id, city_id, question_id, answer, create_time, update_time, operator', 'safe', 'on'=>'search'),
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
			'invest_id' => 'Invest',
			'phone' => 'Phone',
			'order_id' => 'Order',
			'driver_id' => 'Driver',
			'city_id' => 'City',
			'question_id' => 'Question',
			'answer' => 'Answer',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
			'operator' => 'Operator',
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
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('invest_id',$this->invest_id);
		$criteria->compare('phone',$this->phone);
		$criteria->compare('order_id',$this->order_id);
		$criteria->compare('driver_id',$this->driver_id,true);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('question_id',$this->question_id);
		$criteria->compare('answer',$this->answer,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);
		$criteria->compare('operator',$this->operator,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @return CDbConnection the database connection used for this class
	 */
	public function getDbConnection()
	{
		return Yii::app()->db_activity;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return InvestAnswer the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
