<?php

/**
 * This is the model class for table "{{customer_question}}".
 *
 * The followings are the available columns in table '{{customer_question}}':
 * @property integer $id
 * @property string $title
 * @property string $contents
 * @property string $type
 */
class CustomerQuestion extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerQuestion the static model class
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
		return '{{customer_question}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title', 'length', 'max'=>100),
			array('contents', 'length', 'max'=>600),
			array('ext,type', 'length', 'max'=>1),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title,ext, contents, type', 'safe', 'on'=>'search'),
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
			'title' => '问卷题目',
			'contents' => '问卷内容',
			'type' => '显示类型',
			'ext' => '是否有需要补充',
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
		$criteria->compare('contents',$this->contents,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('ext',$this->ext,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 
	 * 获取问题的列表
	 */
	public function getQuestionList(){
		$modelCustomer = new CustomerQuestion();
		
		$criteria = new CDbCriteria();
		$criteria->addInCondition('id', array(1,2,3,4,5));
		$customer_list = $modelCustomer->findAll($criteria);
		return $customer_list;
	}
}