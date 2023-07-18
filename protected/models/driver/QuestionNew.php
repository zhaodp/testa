<?php

/**
 * This is the model class for table "{{question_new}}".
 *
 * The followings are the available columns in table '{{question_new}}':
 * @property integer $id
 * @property integer $type
 * @property integer $category
 * @property string $title
 * @property string $interpretation
 * @property string $title_img
 * @property string $option_a
 * @property string $img_a
 * @property string $option_b
 * @property string $img_b
 * @property string $option_c
 * @property string $img_c
 * @property string $option_d
 * @property string $img_d
 * @property string $answer
 * @property integer $status
 * @property integer $call_times
 * @property integer $right_times
 * @property string $update_time
 * @property string $create_time
 */
class QuestionNew extends CActiveRecord
{

    CONST TOTAL_QUESTION = 20;
    CONST STATUS_NORMAL = 0;
    CONST STATUS_DELETE = -1;

     public static $category = array(
         '1'=>'常识',
         '2'=>'上岗准备',
         '3'=>'就位准备',
         '4'=>'就位状态',
         '5'=>'服务中',
         '6'=>'结束服务',
         '7'=>'违约管理',
         '8'=>'e币和皇冠',
         '9'=>'司机反馈',
         '10'=>'信息费规则',
         '11'=>'司机端图形题',
         '12'=>'车辆操控',
         '13'=>'地域题',
         '14'=>'安全类',
         '15'=>'代驾基本要求',
         '16'=>'多收费',
     );
    public static $cateWeight = array(
        '1'=>1,
        '2'=>1,
        '3'=>1,
        '4'=>1,
        '5'=>3,
        '6'=>2,
        '7'=>3,
        '8'=>1,
        '9'=>1,
        '10'=>1,
        '11'=>3,
        '12'=>1,
        '13'=>1
    );

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{question_new}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title,answer', 'required'),
            array('type, category, status, call_times, right_times', 'numerical', 'integerOnly'=>true),
            array('title', 'length', 'max'=>100),
            array('interpretation', 'length', 'max'=>512),
            array('title_img, option_a, img_a, option_b, img_b, option_c, img_c, option_d, img_d', 'length', 'max'=>255),
            array('answer', 'length', 'max'=>64),
            array('create_time,update_time', 'safe'),
            // The following rule is used by search().
            array('type, category, title,  answer, status, call_times, right_times, update_time, create_time', 'safe', 'on'=>'search'),
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
            'type' => '类型',
            'category' => '分类',
            'title' => '题目',
            'interpretation' => '答案解析',
            'title_img' => 'Title Img',
            'option_a' => 'Option A',
            'img_a' => 'Img A',
            'option_b' => 'Option B',
            'img_b' => 'Img B',
            'option_c' => 'Option C',
            'img_c' => 'Img C',
            'option_d' => 'Option D',
            'img_d' => 'Img D',
            'answer' => '答案',
            'status' => '状态',
            'call_times' => '调用次数',
            'right_times' => '答对次数',
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

        $criteria->compare('type',$this->type);
        $criteria->compare('category',$this->category);
        $criteria->compare('title',$this->title,true);
        $criteria->compare('status',$this->status);
        $criteria->compare('call_times',$this->call_times);
        $criteria->compare('right_times',$this->right_times);
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
     * @return QuestionNew the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    /**
     * @param string $status
     * @return array|bool
     */
    public static function getStatus($status = ''){

        $data =  array(
            self::STATUS_NORMAL    => '正常',
            self::STATUS_DELETE    => '屏蔽',
        );
        if($status !== ''){
            if(isset($data[$status])) return $data[$status];
            else return false;
        }else return $data;
    }

    /**
     * @param string $type
     * @return bool|int
     */
    public static function getCategory($category = ''){
        $data =  self::$category;
        if($category){
            if(isset($data[$category])) return $data[$category];
            else return false;
        }else return $data;
    }

    /**
     * 模拟考试和线上考试 随机获取题目
     * @param $city_id
     * @return array
     */
    public function  getQuestionList($city_id){
        $res = array();
        $questions = Yii::app()->db_readonly->createCommand()
            ->select('t.*')
            ->from('t_question_new  t')
            ->where('t.status=:status1 and q.status=:status and city_id = :city_id',
                array(':status1' => self::STATUS_NORMAL,':status'=>Question2city::STATUS_NORMAL,':city_id'=>$city_id))
            ->leftJoin('t_question2city  q',' q.question_id = t.id')
            ->order('id DESC')
            ->queryAll();
        if($questions){
            //print_r($questions);
            $question_new = array();
            foreach($questions as $v){
                $question_new[$v['category']][]=$v;
            }
            //print_r($question_new);die;
            foreach(self::$cateWeight as $category_id => $weight){
                $question_num = $weight;
                //echo $question_num."\n";
                if($question_num && isset($question_new[$category_id]) && !empty($question_new[$category_id])){
                    $c = count($question_new[$category_id]);
                    shuffle($question_new[$category_id]); //打乱数组
                    if($weight < $c){
                        $tmp = array_slice($question_new[$category_id],0,$weight);//截取前n个数据
                    }else{
                        $tmp = $question_new[$category_id];
                    }

                    $res = array_merge($res,$tmp);
                }
            }
        }
        shuffle($res);

        return $res;

    }


    /**
     * 模拟考试和线上考试 随机获取题目
     * @param $city_id
     * $rules = array(3=>10,9=>10)
     * @return array
     */
    public function getQuestionStudyList($rules, $city_id){
        $res = array();
        $questions = Yii::app()->db_readonly->createCommand()
            ->select('t.*')
            ->from('t_question_new  t')
            ->where('t.status=:status1 and q.status=:status and city_id = :city_id',
                array(':status1' => self::STATUS_NORMAL,':status'=>Question2city::STATUS_NORMAL,':city_id'=>$city_id))
            ->leftJoin('t_question2city  q',' q.question_id = t.id')
            ->order('id DESC')
            ->queryAll();
        if($questions){
            //print_r($questions);
            $question_new = array();
            foreach($questions as $v){
                $question_new[$v['category']][]=$v;
            }
            //print_r($question_new);die;
            foreach($rules as $category_id => $weight){
                //echo $question_num."\n";
                if($weight && isset($question_new[$category_id]) && !empty($question_new[$category_id])){
                    $c = count($question_new[$category_id]);
                    shuffle($question_new[$category_id]); //打乱数组
                    //$weight = $weight <= $c ? $weight : $c;
                    if($weight < $c){
                        $tmp = array_slice($question_new[$category_id],0,$weight);//截取前n个数据
                    }else{
                        $tmp = $question_new[$category_id];
                    }


                    $res = array_merge($res,$tmp);
                }
            }
        }
        shuffle($res);

        return $res;

    }


    public function getQuestionById($ids, $shuffle = true){
        if(!empty($ids)){
            //$id_str = implode(',',$ids);
            $questions = Yii::app()->db_readonly->createCommand()
                ->select('*')
                ->from('t_question_new')
                ->where('id in ('.$ids.') and status = :sta',array(':sta'=>self::STATUS_NORMAL))
                ->queryAll();
            //print_r($questions);
            if($shuffle) { shuffle($questions);}
            return $questions;
        }
        return false;
    }

    public  function formatQuestion($question, $have_answer = false){
        unset($question['status']);
        unset($question['call_times']);
        unset($question['right_times']);
        unset($question['update_time']);
        unset($question['create_time']);
        if(!$have_answer) {
            unset($question['answer']);
            unset($question['interpretation']);
        }
        ($question['option_a'] || $question['img_a'] ) && $tmp1[] = array(
            'num'=>'a',
            'option'=>$question['option_a'],
            'img'=>$question['img_a']
        );
        ($question['option_b'] || $question['img_b'] ) &&  $tmp1[] = array(
            'num'=>'b',
            'option'=>$question['option_b'],
            'img'=>$question['img_b']
        );
        ($question['option_c'] || $question['img_c'] ) && $tmp1[] = array(
            'num'=>'c',
            'option'=>$question['option_c'],
            'img'=>$question['img_c']
        );
        ($question['option_d'] || $question['img_d'] ) && $tmp1[] = array(
            'num'=>'d',
            'option'=>$question['option_d'],
            'img'=>$question['img_d']
        );
        unset($question['img_a']);
        unset($question['img_b']);
        unset($question['img_c']);
        unset($question['img_d']);
        unset($question['option_a']);
        unset($question['option_b']);
        unset($question['option_c']);
        unset($question['option_d']);
        $question['options']= $tmp1;
        //print_r($tmp1);
        return $question;

    }



}

