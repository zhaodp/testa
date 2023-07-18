<?php

/**
 * This is the model class for table "bill".
 *
 * The followings are the available columns in table 'bill':
 * @property integer $id
 * @property integer $userId
 * @property integer $type
 * @property double $cast
 * @property integer $status
 * @property string $month
 * @property integer $created
 */
class ThirdBill extends ThirdStageActiveRecord
{


	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'bill';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('userId, type, cast, status, month, created', 'required'),
			array('userId, type, status, created', 'numerical', 'integerOnly'=>true),
			array('cast', 'numerical'),
			array('month', 'length', 'max'=>8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, userId, type, cast, status, month, created', 'safe', 'on'=>'search'),
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
			'userId' => 'User',
			'type' => 'Type',
			'cast' => 'Cast',
			'status' => 'Status',
			'month' => 'Month',
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

		$criteria->compare('userId',$this->userId);

		$criteria->compare('type',$this->type);

		$criteria->compare('cast',$this->cast);

		$criteria->compare('status',$this->status);

		$criteria->compare('month',$this->month,true);

		$criteria->compare('created',$this->created);

		return new CActiveDataProvider('ThirdBill', array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return ThirdBill the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}