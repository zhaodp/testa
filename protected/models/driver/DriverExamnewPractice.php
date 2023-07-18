<?php

/**
 * This is the model class for table "{{driver_examnew_practice}}".
 *
 * The followings are the available columns in table '{{driver_examnew_practice}}':
 * @property integer $id
 * @property string $open_id
 * @property integer $question_id
 * @property integer $result
 * @property integer $type
 * @property string $update_time
 * @property string $create_time
 */
class DriverExamnewPractice extends CActiveRecord
{
    CONST TYPE_TEST = 0; //模拟考试类型
    CONST TYPE_PRICTICE = 1; //在线练习类型


    CONST RESULT_RIGHT = 1;
    CONST RESULT_WRONG = 0;
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_examnew_practice}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('question_id,result,open_id', 'required'),
            array('question_id, result, type', 'numerical', 'integerOnly'=>true),
            array('open_id', 'length', 'max'=>64),
            array('create_time', 'safe'),
            // The following rule is used by search().
            array('open_id, question_id, result, type, update_time, create_time', 'safe', 'on'=>'search'),
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
            'open_id' => 'open_id',
            'question_id' => 'Question_id',
            'result' => '是否正确',
            'type' => '考试类型',
            'update_time' => 'Update Time',
            'create_time' => 'Create Time',
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
        $criteria=new CDbCriteria;

        $criteria->compare('open_id',$this->open_id,true);
        $criteria->compare('question_id',$this->question_id);
        $criteria->compare('result',$this->result);
        $criteria->compare('type',$this->type);
        $criteria->compare('update_time',$this->update_time,true);
        $criteria->compare('create_time',$this->create_time,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DriverExamnewPractice the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function addWrongExam($open_id, $question_id, $result, $type=self::TYPE_TEST){
        $exist = $this->find('open_id = :oid and question_id = :qid ',array(':oid'=>$open_id,':qid'=>$question_id));
        if($exist){
            if($exist->result !=  $result){
                $exist->result = $result;
                $res = $exist->save();
                if(!$res){
                    EdjLog::info('save field:open_id'.$open_id.' ques_id:'.$question_id.' result:'. $result );
                }
            }
        }else if($result == self::RESULT_WRONG){
            $data = array(
                'open_id'     =>    $open_id,
                'question_id' =>    $question_id,
                'result'      =>    self::RESULT_WRONG,
                'type'        =>    $type,
                'create_time' =>    date('Y-m-d H:i:s')
            );
            $mod = new DriverExamnewPractice();
            $mod->attributes = $data;
            $res = $mod->save();
            if(!$res){
                EdjLog::info('insert  field:open_id'.$open_id.' ques_id:'.$question_id.' result:'. $result);
            }
        }
    }


    public function getAllWrong($open_id){
        $criteria=new CDbCriteria;
        $criteria->condition='open_id = :open_id and result = :res';
        $criteria->params=array(':open_id'=>$open_id,':res'=>self::RESULT_WRONG);
        $criteria->order='create_time DESC';
        $wrong_question = $this->findAll($criteria);
        //print_r($wrong_question);die;
        return $wrong_question;
    }
}