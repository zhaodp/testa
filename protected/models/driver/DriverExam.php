<?php

/**
 * This is the model class for table "{{driver_exam}}".
 *
 * The followings are the available columns in table '{{driver_exam}}':
 * @property integer $id
 * @property string $title
 * @property integer $type
 * @property string $a
 * @property string $b
 * @property string $c
 * @property string $d
 * @property string $correct
 * @property string $status
 * @property string $created
 */
class DriverExam extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverExam the static model class
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
		return '{{driver_exam}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array ('title','required'), 
			array('type, city_id', 'numerical', 'integerOnly'=>true),
			array('title, a, b, c, d', 'length', 'max'=>255),
			array('status', 'length', 'max'=>1),
			array('correct', 'length', 'max'=>10),
			array('created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, type, city_id, a, b, c, d, correct, status, created', 'safe', 'on'=>'search'),
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
			'title' => '题目',
			'type' => '试题类型',
			'city_id' => '城市',		
			'a' => 'A答案',
			'b' => 'B答案',
			'c' => 'C答案',
			'd' => 'D答案',
			'correct' => '正确答案',
			'status' => '试题状态',
			'created' => '创建时间',
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
		$criteria->compare('title',$this->title,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('city_id', $this->city_id);
		$criteria->compare('a',$this->a,true);
		$criteria->compare('b',$this->b,true);
		$criteria->compare('c',$this->c,true);
		$criteria->compare('d',$this->d,true);
		$criteria->compare('correct',$this->correct,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}