<?php

/**
 * This is the model class for table "{{customer_exam}}".
 *
 * The followings are the available columns in table '{{customer_exam}}':
 * @property integer $id
 * @property string $exam_title
 * @property integer $type
 * @property string $created
 */
class CustomerExam extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerExam the static model class
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
		return '{{customer_exam}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type','numerical','integerOnly'=>true),
			array('exam_title, created', 'required'),
			array('exam_title', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, exam_title, type,created', 'safe', 'on'=>'search'),
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
			'exam_title' => '问卷标题',
			'type' => '问卷类型',
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
		$criteria->compare('exam_title',$this->exam_title,true);
		$criteria->compare('type', $this->type,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 获取问卷信息列表
	 */
	public function getExamList(){
		$exam_list = array();
		$criteria = new CDbCriteria();
		$criteria->addCondition('type = :type');
		$criteria->params=array(':type'=>1);
		$exam = CustomerExam::model()->findAll($criteria);
		foreach ($exam as $items){
			$exam_list[$items->id] = $items->exam_title;
		}
		return $exam_list;
	}
	
	/**
	 * 新增一个问卷信息
	 */
	public function addExamMsg(){
		$model = new CustomerExam();
		$model->exam_title = $_POST['CustomerExam']['exam_title'];
		$model->type = $_POST['CustomerExam']['type'];
		$model->created = date('Y-m-d',time());
		if ($model->save()) {
			$exam_paper = new CustomerExamPaper();
			if ($exam_paper->addExamPaper($model->id)){
				return true;
			}else {
				return false;
			}
		}else {
			return false;
		}
	}
	
	/**
	 * 修改一个问卷信息
	 * $params int $eid 问卷主键
	 */
	public function updateExamMsg($eid){
		$model = CustomerExam::model()->find('id = :id',array(':id'=>$eid));
		$model->exam_title = $_POST['CustomerExam']['exam_title'];
		$model->type = $_POST['CustomerExam']['type'];
		if ($model->save()){
			$exam_paper = new CustomerExamPaper();
			if ($exam_paper->updateExamPaper($eid)){
				return true;
			}else {
				return false;
			}
		}else {
			return false;
		}
	}
	
}