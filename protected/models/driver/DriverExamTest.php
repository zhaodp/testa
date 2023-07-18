<?php

/**
 * This is the model class for table "{{driver_exam_test}}".
 *
 * The followings are the available columns in table '{{driver_exam_test}}':
 * @property integer $id
 * @property string $id_card
 * @property string $question_id
 * @property integer $num
 * @property string $created
 */
class DriverExamTest extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return DriverExamTest the static model class
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
		return '{{driver_exam_test}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_card', 'required'),
			array('num', 'numerical', 'integerOnly'=>true),
			array('id_card', 'length', 'max'=>20),
			array('question_id', 'length', 'max'=>250),
			array('created', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, id_card, question_id, num, created', 'safe', 'on'=>'search'),
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
			'id_card' => 'Id Card',
			'question_id' => 'Question',
			'num' => 'Num',
			'created' => 'Created',
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
		$criteria->compare('id_card',$this->id_card,true);
		$criteria->compare('question_id',$this->question_id,true);
		$criteria->compare('num',$this->num);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * 
	 * 新增司机在线自主考试的记录
	 * @param 考试的题目集合  $id
	 */
	public function addDriverExamTest($id, $id_card){
        $this->delExamTest($id_card);
		$model = new DriverExamTest();
		//$model->id_card = $_REQUEST['id_card'];
        //author zhangtingyi 不清楚为啥总把$_REQUEST放在model里
        $model->id_card = $id_card;
        $model->question_id = $id;
		$model->num = 0;
		$model->created = date('Y-m-d',time());
		if ($model->save()){
			return true;
		}else {
			return false;
		}
	}

    public function delExamTest($id_card) {
 		$model = DriverExamTest::model()->find('id_card=:id_card',array(':id_card'=>$id_card));
		if (!$model){
            return false;
		} else {
            return $model->delete();
        }
    }

	public function updateExamNum($id_card){
		$model = DriverExamTest::model()->find('id_card=:id_card and created=:created',array(':id_card'=>$id_card,':created'=>date('Y-m-d',time())));
		if (!empty($model)){
			$num = $model->attributes['num'];
		}
		
		if ($num < 3){
			$params = array();
			$update_sql = 'update t_driver_exam_test set num = num+1 where id_card=:id_card and created=:created';
			$params[':id_card'] = $id_card;
			$params[':created'] = date('Y-m-d',time());
			
			$command = Yii::app()->db->createCommand($update_sql);
			$command->execute($params);
			$command->reset();
		}
	}
	
	/**
	 * 
	 * 获取当前司机考试的试题ID
	 * @param string $id_card 司机身份证标识
	 */
	public function getQuestionId($id_card){
		
		$criteria = new CDbCriteria();
		$criteria->addCondition('id_card=:id_card ');
		$criteria->order='id desc';
		$criteria->limit=1;
		$criteria->params=array(':id_card'=>$id_card);
		$model = DriverExamTest::model()->find($criteria);
		//return $model->attributes['question_id'];
		$str = '';
		
		//当$model存在时返回 修复t_error_log中的bug  @author mengtianxue 2013-05-19
		if($model){
			$newcriteria = new CDbCriteria();
			$newcriteria->select='id';
			$newcriteria->addInCondition('id', explode(',', $model->question_id));
			$newmodel = Question::model()->findAll($newcriteria);
			
			foreach($newmodel as $item){
				$str.=$item->id.',';
			}
		}
		return $str;
	}
	
	/**
	 * 
	 * 获取当前司机自主在线考试的记录
	 */
	public function getQuestionList($q_num){

		if (!empty($q_num)){
			$str_id = explode(',', $q_num);
			$arrQuestion = array();
			foreach ($str_id as $key => $list){
				if (!empty($list)){
					$exam_list = Question::model()->find('id=:id', array(':id'=>$list));
					$q_list = array(
						'id'=>$exam_list->attributes['id'],
                        'type' => $exam_list->attributes['type'],
						'title'=>$exam_list->attributes['title'],
						'contents'=>$exam_list->attributes['contents'],
						'correct'=>$exam_list->attributes['correct'],
					);
					array_push($arrQuestion, $q_list);
				}
			}
			return $arrQuestion;
		}
	}
	
	public function getExamAgainList($id_card){
		$exam_test = new DriverExamTest();
		$test_id = $exam_test->find('id_card=:id_card and created = :created',array(':id_card'=>$id_card,':created'=>date('Y-m-d',time())));
		if (!empty($test_id)){
			$question_id = $test_id->attributes['question_id'];
			$str_id = explode(',', $question_id);
			$arrQuestion = array();
			foreach ($str_id as $key => $list){
				if (!empty($list)){
					$exam_list = Question::model()->find('id=:id', array(':id'=>$list));
					$q_list = array(
						'id'=>$exam_list->attributes['id'],
						'title'=>$exam_list->attributes['title'],
						'contents'=>$exam_list->attributes['contents'],
						'correct'=>$exam_list->attributes['correct'],
					);
					array_push($arrQuestion, $q_list);
				}
			}
			return $arrQuestion;
		}
	}
}