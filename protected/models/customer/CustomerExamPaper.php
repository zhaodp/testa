<?php

/**
 * This is the model class for table "{{customer_exam_paper}}".
 *
 * The followings are the available columns in table '{{customer_exam_paper}}':
 * @property integer $id
 * @property string $title
 * @property string $contents
 * @property string $type
 * @property string $ext
 * @property integer $exam_id
 * @property integer $question_id
 */
class CustomerExamPaper extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerExamPaper the static model class
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
		return '{{customer_exam_paper}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, content, type, ext', 'required'),
			array('exam_id, question_id', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>100),
			array('contents', 'length', 'max'=>600),
			array('type', 'length', 'max'=>20),
			array('ext','length','max'=>1),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, title, content,type, ext, exam_id, question_id', 'safe', 'on'=>'search'),
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
			'title' => 'Title',
			'contents' => 'Content',
			'type' => 'type',
			'ext' =>'ext',
			'exam_id' => 'Exam',
			'question_id' => 'Question',
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
		$criteria->compare('type', $this->type,true);
		$criteria->compare('ext', $this->ext,true);
		$criteria->compare('exam_id',$this->exam_id);
		$criteria->compare('question_id',$this->question_id);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 新增问卷下的问题
	 * 
	 */
	public function addExamPaper($cid){
		$question = new Question();
		if (isset($_POST['theText'])){
			$the_text = $_POST['theText'];
			$count = count($the_text);
			foreach ($the_text as $key => $var){
				$params = array();
				$str_sql = "insert into t_customer_exam_paper(title,contents,type,exam_id,question_id) values(:title,:content,:type,:exam_id,:question_id);";
				$params['title'] = $_POST['theText'][$key];
				$question_list = $question->getQuestionContent($_POST['numText'][$key]);
				$params['content'] = $question_list['contents'];
				$params['type'] = $question_list['type'];
				$params['exam_id'] = $cid;
				$params['question_id'] = $_POST['numText'][$key];
				
				$command = Yii::app()->db->createCommand($str_sql);
				$command->execute($params);
				$command->reset();
			}
			return true;
		}
	}
	
	/**
	 * 修改问卷下的内容
	 * $params int $id 问卷标识
	 */
	public function updateExamPaper($id){
		$question = new Question();
		$exam_list = $this->getExamList($id);
		if (isset($_POST['theText'])){
			$text_list = $_POST['theText'];
			$exam_count = count($exam_list);
			$params = array();
			foreach ($text_list as $key => $value){
				if ($key<$exam_count){
					$str_sql = 'update t_customer_exam_paper set title = :title where exam_id = :exam_id and question_id =:question_id and contents = :content and type = :type';
					$params['title'] = $_POST['theText'][$key];
					$params['exam_id'] = $id;
					$question_list = $question->getQuestionContent($_POST['numText'][$key]);
					$params['content'] = $question_list['contents'];
					$params['type'] = $question_list['type'];
					$params['question_id'] = $_POST['numText'][$key];
				}else {
					$str_sql = "insert into t_customer_exam_paper(title,contents,type,exam_id,question_id) values(:title,:content,:type,:exam_id,:question_id);";
					$params['title'] = $_POST['theText'][$key];
					$question_list = $question->getQuestionContent($_POST['numText'][$key]);
					$params['content'] = $question_list['contents'];
					$params['type'] = $question_list['type'];
					$params['exam_id'] = $id;
					$params['question_id'] = $_POST['numText'][$key];
				}
				$command = Yii::app()->db->createCommand($str_sql);
				$command->bindParam(':title', $params['title'],PDO::PARAM_STR);
				$command->bindParam(':content', $params['content'],PDO::PARAM_STR);
				$command->bindParam(':type', $params['type'],PDO::PARAM_STR);
				$command->bindParam(':exam_id', $params['exam_id'],PDO::PARAM_STR);
				$command->bindParam(':question_id', $params['question_id'],PDO::PARAM_STR);
				$command->execute();
			}
			return true;
		}
	}
	
	/**
	 * 
	 * 查询相关批次下对应问卷的问题
	 * @param int $exam_id 问卷标识
	 */
	public function getBatchPaperList($exam_id){
		$paper_list = CustomerExamPaper::model()->findAll('exam_id = :exam_id', array(':exam_id'=>$exam_id));
		return $paper_list;
	}
	/**
	 * 查询相应问卷下的问题信息
	 * $params int $id 问题标识
	 */
	public function getExamPaper($exam_id){
		$paper_list = array();
		$papermodel = CustomerExamPaper::model()->findAll('exam_id = :exam_id', array(':exam_id'=>$exam_id));
		foreach ($papermodel as $items){
			$paper_list[$items->question_id]= $items->title;
		}
		return $paper_list;
	}
	
	public function getExamList($exam_id){
		$paper_list = array();
		$papermodel = CustomerExamPaper::model()->findAll('exam_id = :exam_id', array(':exam_id'=>$exam_id));
		foreach ($papermodel as $items){
			$paper_list[$items->id]= $items->question_id;
		}
		return $paper_list;
	}
}