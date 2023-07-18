<?php

/**
 * This is the model class for table "{{driver_card}}".
 *
 * The followings are the available columns in table '{{driver_card}}':
 * @property integer $id
 * @property string $user
 * @property string $name
 * @property string $id_card
 * @property integer $status
 */
class DriverTest extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverCard the static model class
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
		return '{{driver_test}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, id_card', 'required'),
			array('status, num, created, city_id', 'numerical', 'integerOnly'=>true),
			array('user', 'length', 'max'=>10),
			array('name', 'length', 'max'=>255),
			array('id_card', 'length', 'max'=>20),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user, name, id_card, city_id, num, status', 'safe', 'on'=>'search'),
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
			'id' => '序号',
			'user' => '司机工号',
			'name' => '司机姓名',
			'city_id' => '城市',
			'id_card' => '身份证号码',
			'num' => '考试次数',
			'status' => '状态',
			'created' =>'最近考试时间',
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
		$criteria->compare('user',$this->user,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('id_card',$this->id_card,true);
		$criteria->compare('city_id',$this->city_id);
		$criteria->compare('status',$this->status);
		$criteria->compare('num',$this->num);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}