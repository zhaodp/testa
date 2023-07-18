<?php

/**
 * This is the model class for table "{{question}}".
 *
 * The followings are the available columns in table '{{question}}':
 * @property integer $id
 * @property string $type
 * @property string $title
 * @property integer $question_type
 * @property integer $compliant
 * @property string $city_id
 * @property integer $track
 * @property string $contents
 * @property string $correct
 * @property integer $status
 */
class Question extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Question the static model class
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
		return '{{question}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('question_type, compliant, track, status', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>20),
			array('title', 'length', 'max'=>100),
			array('city_id', 'length', 'max'=>1000),
			array('contents', 'length', 'max'=>600),
			array('correct', 'length', 'max'=>10),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type, title, question_type, compliant, city_id, track, contents, correct, status', 'safe', 'on'=>'search'),
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
			'type' => '选择类型',
			'title' => '标题',
			'question_type' => '答题类型',
			'compliant' => '适用于哪种类型的司机',
			'city_id' => '城市ID',
			'track' => '题目类型',
			'contents' => 'Contents',
			'correct' => '正确答案',
			'status' => '题目状态',
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
		$criteria->compare('type',$this->type,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('question_type',$this->question_type);
		$criteria->compare('compliant',$this->compliant);
		$criteria->compare('city_id',$this->city_id,true);
		$criteria->compare('track',$this->track);
		$criteria->compare('contents',$this->contents,true);
		$criteria->compare('correct',$this->correct,true);
		$criteria->compare('status',$this->status);
		$criteria->addCondition('status=:status');
		$criteria->params = array(':status'=>0);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function search_list(){
		$criteria = new CDbCriteria();
		$criteria->compare('id',$this->id);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('question_type',$this->question_type);
		$criteria->compare('compliant', $this->compliant);
		$criteria->compare('city_id',$this->city_id,true);
		$criteria->compare('track', $this->track);
		$criteria->compare('contents',$this->contents,true);
		$criteria->compare('correct',$this->correct,true);
		$criteria->compare('status',$this->status);
		$criteria->addCondition('question_type = :question_type');
		$criteria->params = array(':question_type'=> 2);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function getQuestionContent($qid){
		$question = Question::model()->find('id = :id', array(':id'=>$qid));
		return $question;
	}
	
	/**
	 * 获取考试的题目
	 */
    public function getQuestionList($id_card, $city_id, $isDriverOld) {
        $currNum = 20;
        $arrExam = $this->getExamListByCity($city_id, $isDriverOld, $currNum);
        if (! empty ( $arrExam )) {
            // 插入数据表
            $id = '';
            foreach ( $arrExam as $keys ) {
                $id .= $keys->attributes ['id'] . ',';
            }
            $isbool = DriverExamTest::model ()->addDriverExamTest ($id , $id_card);
            return $isbool ? $arrExam : false;
        } else {
            return false;
        }
    }

  	/**
	 * 获取考试的题目
	 */
	public function getNewQuestionList($id_card, $city_id, $isDriverOld) {
		$currNum = 30;
		$exam_test = new DriverExamTest ();
		
		$criteria = new CDbCriteria ();
		$criteria->addCondition ( 'id_card=:id_card ' );
		$criteria->order = 'id desc';
		$criteria->limit = 1;
		$criteria->params = array (
				':id_card' => $id_card 
		);
		
		$test_id = $exam_test->find ( $criteria );
		// 判断是否第一次考试
		
		if (! empty ( $test_id )) {
			$exam_num = Yii::app ()->params ['exam_num'];
			$arrExam = array ();
			
			foreach ( $exam_num as $key => $item ) {
				$tmpExam = array ();
				$cirteria = new CDbCriteria ();
				$cirteria->select = 'id,title,contents,correct,track';
				if ($isDriverOld == 0) {
					$cirteria->addCondition ( "question_type = 2 and track=" . $key . " and compliant in (0,1) and status = 0" );
				} else {
					$cirteria->addCondition ( "question_type = 2 and track=" . $key . " and compliant in (0,2) and status = 0" );
				}
				
				// 模糊查询
				$cirteria->compare ( 'city_id', $city_id . ',', true );
				$cirteria->order = 'RAND()';
				$cirteria->limit = $item;
				
				$tmpExam = Question::model ()->findAll ( $cirteria );
				if (! empty ( $tmpExam )) {
					$arrExam = array_merge ( $arrExam, $tmpExam );
				}
				unset ( $tmpExam, $cirteria );
			}
			if(count($arrExam)<$currNum){
				$arrExam = Question::model()->getLastExam($arrExam, $city_id, $isDriverOld);
			}
			if (! empty ( $arrExam )) {
				// 插入数据表
				$id = '';
				foreach ( $arrExam as $keys ) {
					$id .= $keys->attributes ['id'] . ',';
				}
				$num = $test_id->attributes ['num'] + 1;
				$params = array ();
				$exam_test->updateAll ( array (
						'question_id' => $id,
						'num' => $num 
				), 'id_card = :id_card', array (
						':id_card' => $id_card 
				) );
				return $arrExam;
			}
		
		} else {

			// 第一次考试，随机查出考题，并插入一条数据
			$exam_num = Yii::app ()->params ['exam_num'];
			$arrExam = array ();
			foreach ( $exam_num as $key => $item ) {
				$tmpExam = array ();
				$cirteria = new CDbCriteria ();
				$cirteria->select = 'id,title,contents,correct,track';
				if ($isDriverOld == 0) {
					$cirteria->addCondition ( "question_type = 2 and track=" . $key . " and compliant in (0,1) and status = 0" );
				} else {
					$cirteria->addCondition ( "question_type = 2 and track=" . $key . " and compliant in (0,2) and status = 0" );
				}
				// 模糊查询
				$cirteria->compare ( 'city_id', $city_id . ',', true );
				$cirteria->order = 'RAND()';
				$cirteria->limit = $item;
				$tmpExam = Question::model ()->findAll ( $cirteria );
				if (! empty ( $tmpExam )) {
					$arrExam = array_merge ( $arrExam, $tmpExam );
				}
				unset ( $tmpExam, $cirteria );
			}
			if(count($arrExam)<$currNum){
				$arrExam = Question::model()->getLastExam($arrExam, $city_id, $isDriverOld);
			}
			if (! empty ( $arrExam )) {
				// 插入数据表
				$id = '';
				foreach ( $arrExam as $keys ) {
					$id .= $keys->attributes ['id'] . ',';
				}
				
				$isbool = DriverExamTest::model ()->addDriverExamTest ($id, $id_card);
				
				if ($isbool) {
					return $arrExam;
				}
			
			}
			
			// //s
		}
	}
	/*
	 * 如果取出的题目不够30个，那么就取该分类下其他题目
	 */
	public function getLastExam($arrExam, $city_id, $isOld, $currNum = 20) {
		$idArr = array ();
		foreach ( $arrExam as $item ) {
			$idArr [] = $item->id;
		}
		$cirteria = new CDbCriteria ();
		$cirteria->select = 'id,title,contents,correct,track,type';
		if ($isOld == 0) {
			$cirteria->addCondition ( "question_type = 2 and compliant in (0,1) and status = 0" );
		} else {
			$cirteria->addCondition ( "question_type = 2 and compliant in (0,2) and status = 0" );
		}
		// 模糊查询
		//$cirteria->compare ( 'city_id', $city_id . ',', true );
        $cirteria->addCondition ('FIND_IN_SET('.$city_id.',city_id)');
        $cirteria->addNotInCondition ( 'id', $idArr );
		$cirteria->order = 'RAND()';
		$cirteria->limit = ($currNum - count ( $idArr ));
		$tmpExam = Question::model ()->findAll ( $cirteria );
		$arrExam = array_merge ( $arrExam, $tmpExam );
		return $arrExam;
	}
	/**
	 * 
	 * 获取答案
	 */
	public function getQueCorrect($id){
		$model = $this->findByPk($id);
		return $model;
	}

    /**
     * 根据城市ID随机获得考题
     * @param int $city_id 城市ID
     * @param int $currNum
     * @return array
     */
    public function getExamListByCity($city_id, $isDriverOld=0, $currNum=20) {
        $exam_num = Yii::app ()->params ['exam_num'];
        $arrExam = array ();
        $track_list = array(3,4);

        foreach ( $exam_num as $key => $item ) {
            $tmpExam = array ();
            $cirteria = new CDbCriteria ();
            $cirteria->select = 'id,title,contents,correct,track,type';
            if ($isDriverOld == 0) {
                $cirteria->addCondition ( "question_type = 2 and track =" . $key . " and compliant in (0,1) and status = 0" );
            } else {
                $cirteria->addCondition ( "question_type = 2 and track =" . $key . " and compliant in (0,2) and status = 0" );
            }
            if (in_array($key, $track_list)) {
                $cirteria->addCondition ('FIND_IN_SET('.$city_id.',city_id)');
            }
            // 模糊查询
            //$cirteria->compare ( 'city_id', $city_id . ',', true );
            $cirteria->order = 'RAND()';
            $cirteria->limit = $item;
            $tmpExam = Question::model ()->findAll ( $cirteria );
            if (! empty ( $tmpExam )) {
                $arrExam = array_merge ( $arrExam, $tmpExam );
            }
            unset ( $tmpExam, $cirteria );
        }	
        if(count($arrExam)<$currNum){
            $arrExam = Question::model()->getLastExam($arrExam, $city_id, $isDriverOld, $currNum);
        }
        return $arrExam;
    }
}
