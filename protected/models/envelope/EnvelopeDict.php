<?php

/**
 * This is the model class for table "t_envelope_dict".
 *
 * The followings are the available columns in table 't_envelope_dict':
 * @property integer $id
 * @property string $dictname
 * @property integer $code
 * @property string $name
 */
class EnvelopeDict extends FinanceActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{envelope_dict}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dictname', 'required'),
			array('code', 'numerical', 'integerOnly'=>true),
			array('dictname', 'length', 'max'=>50),
			array('name', 'length', 'max'=>200),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, dictname, code, name', 'safe', 'on'=>'search'),
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
			'dictname' => 'Dictname',
			'code' => 'Code',
			'name' => 'Name',
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

		$criteria->compare('dictname',$this->dictname,true);

		$criteria->compare('code',$this->code);

		$criteria->compare('name',$this->name,true);

		return new CActiveDataProvider('EnvelopeDict', array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * @return EnvelopeDict the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}