<?php

/**
 * This is the model class for table "{{vip_phone_log}}".
 *
 * The followings are the available columns in table '{{vip_phone_log}}':
 * @property integer $id
 * @property string $vipid
 * @property integer $vipPhone_id
 * @property integer $type
 * @property string $name
 * @property string $phone
 * @property string $status
 * @property string $description
 * @property string $operator
 * @property integer $created
 */
class VipPhoneLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{vip_phone_log}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('vipPhone_id, description, operator', 'required'),
			array('vipPhone_id, type, created', 'numerical', 'integerOnly'=>true),
			array('vipid', 'length', 'max'=>15),
			array('name', 'length', 'max'=>50),
			array('phone', 'length', 'max'=>16),
			array('status', 'length', 'max'=>1),
			array('description', 'length', 'max'=>255),
			array('operator', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, vipid, vipPhone_id, type, name, phone, status, description, operator, created', 'safe', 'on'=>'search'),
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
			'vipid' => 'Vipid',
			'vipPhone_id' => 'Vip Phone',
			'type' => 'Type',
			'name' => 'Name',
			'phone' => 'Phone',
			'status' => 'Status',
			'description' => 'Description',
			'operator' => 'Operator',
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
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('vipid',$this->vipid,true);
		$criteria->compare('vipPhone_id',$this->vipPhone_id);
		$criteria->compare('type',$this->type);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('operator',$this->operator,true);
		$criteria->compare('created',$this->created);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * @return CDbConnection the database connection used for this class
	 */
	public function getDbConnection()
	{
		return Yii::app()->dbstat;
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return VipPhoneLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
