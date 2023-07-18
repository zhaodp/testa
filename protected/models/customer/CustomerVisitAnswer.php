<?php

/**
 * This is the model class for table "{{customer_visit_answer}}".
 *
 * The followings are the available columns in table '{{customer_visit_answer}}':
 * @property integer $id
 * @property integer $customer_id
 * @property integer $question_id
 * @property string $answer
 * @property string $answer_ext
 */
class CustomerVisitAnswer extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return CustomerVisitQuestion the static model class
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
		return '{{customer_visit_answer}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('customer_id, batch_id, question_id, answer, answer_ext', 'required'),
			array('customer_id, batch_id,question_id', 'numerical', 'integerOnly'=>true),
			array('batch_id', 'length', 'max'=>11),
			array('answer', 'length', 'max'=>1),
			array('answer_ext', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, customer_id, batch_id, question_id, answer, answer_ext', 'safe', 'on'=>'search'),
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
			'customer_id' => 'Driver ID',
			'batch_id' => 'Batch ID',
			'question_id' => 'Question ID',
			'answer' => 'Answer',
			'answer_ext' => 'Answer Ext',
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
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('customer_id',$this->customer_id);
		$criteria->compare('question_id',$this->question_id);
		$criteria->compare('answer',$this->answer,true);
		$criteria->compare('answer_ext',$this->answer_ext,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 
	 * 对应批次对应问题对应答案的和
	 */
	public function getAnswer($batch_id,$question_id,$answer){
		$criteria = new CDbCriteria();
		$criteria->condition = 'batch_id = :batch_id and question_id = :question_id and answer = :answer';
		$criteria->params = array(
			':batch_id'=>$batch_id,
			':question_id'=>$question_id,
			':answer'=>$answer,
		);
		$count = CustomerVisitAnswer::model()->count($criteria);
		if (!empty($count)){
			return $count;
		}else {
			return 0;
		}
		
	}
	
	/**
	 * 
	 * 获取对应批次对应问题的回答总数
	 * @param int $batch_id
	 * @param int $question_id
	 */
	public function getAnswerCount($batch_id,$question_id){
		$criteria = new CDbCriteria();
		$criteria->condition = 'batch_id=:batch_id and question_id=:question_id';
		$criteria->params = array(
			':batch_id'=>$batch_id,
			':question_id'=>$question_id,
		);
		$count = CustomerVisitAnswer::model()->count($criteria);
		if (!empty($count)){
			return $count;
		}else {
			return 1;
		}
		
	}
	
	/**
	 * 
	 * 获取对应批次统计数据
	 */
	public function getDataAnswerList($batch_id,$question_id,$answer){
		
		$criteria = new CDbCriteria();

		$criteria->condition = 'batch_id = :batch_id and question_id=:question_id and answer = :answer';
		$criteria->params = array(
			':batch_id'=>$batch_id,
			':question_id'=>$question_id,
			':answer'=>$answer,
		);
		
		$data_list = CustomerVisitAnswer::model()->findAll($criteria);
		
		return $data_list;
	}
	
	/**
	 * 
	 * 导出对应批次统计数据
	 * @param int $batch_id
	 * @param array $answer_list
	 */
	public function exportVisitAnswer($batch_id,$q_list){
		$filename = $batch_id.'.csv';
		header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
		Header('Accept-Ranges: bytes');
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	   	header('Content-Disposition: attachment; filename="'.$filename.'"');
	   	header('Content-Transfer-Encoding: binary');
	   	$answer = array();
	   	$answer['id'] = mb_convert_encoding('题号','gb2312','UTF-8');
	   	$answer['content'] = mb_convert_encoding('回答内容', 'GB2312','UTF-8');
	   	echo implode(',', $answer)."\n";
	   	
	   	if (!empty($q_list)){
	   		
	   		foreach ($q_list as $qkey){
	   			foreach (json_decode($qkey->contents) as $key => $value){
	   				if ($value == '其他'){
	   					$data_list = $this->getDataAnswerList($batch_id,$qkey->id,$key);
	   					foreach ($data_list as $item){
	   						echo $item['question_id'],',';
	   						echo '\'',mb_convert_encoding($item['answer_ext'],'GB2312','UTF-8'),',';
	   						echo "\n";
	   					}
	   				}
	   			}
	   		}
	   	}
	}
	
}