<?php

/**
 * This is the model class for table "{{driver_examnew_online}}".
 *
 * The followings are the available columns in table '{{driver_examnew_online}}':
 * @property integer $id
 * @property string $open_id
 * @property integer $signup_id
 * @property string $questions
 * @property string $answers
 * @property string $user_answers
 * @property integer $right_times
 * @property integer $type
 * @property string $update_time
 * @property string $create_time
 */
class DriverExamnewOnline extends CActiveRecord
{

    CONST CACHE_KEY = 'EXAM_KEY';

    CONST ALLOW_TIMES = 200;
    CONST ALLOW_TEST_TIIMES = 200;
    CONST ALLOW_STUDY_TIIMES = 200;
    CONST ALLOW_PRISE_TIIMES = 200;

    CONST TYPE_ONLINE = 1; //在线考试正式的
    CONST TYPE_TEST = 0 ; //模拟考试
    CONST TYPE_DRIVER_EXAM = 2; //被投诉司机考核
    CONST TYPE_DRIVER_PRISE_EXAM = 3; //司机端有奖答题


    CONST STATUS_DEFAULT = 0 ; //初始建题
    CONST STATUS_ALLRIGHT = 3 ; //全部正确
    CONST STATUS_PARTRIGHT = 2 ; //部分正确
    CONST STATUS_NOTANSWER = 1 ; //未答题 放弃的


    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{driver_examnew_online}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('update_time', 'required'),
            array('signup_id, right_times, type, cate_id, status', 'numerical', 'integerOnly'=>true),
            array('open_id', 'length', 'max'=>64),
            array('questions', 'length', 'max'=>512),
            array('answers, user_answers', 'length', 'max'=>255),
            array('create_time', 'safe'),
            // The following rule is used by search().
            array(' open_id, signup_id, questions,  right_times, type, update_time, create_time', 'safe', 'on'=>'search'),
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
            'open_id' => 'OpenId',
            'signup_id' => '报名id',
            'cate_id'=>'分类',
            'questions' => '问题编号',
            'answers' => '答案组',
            'user_answers' => '司机答案',
            'right_times' => '对题数',
            'status'=>'状态',
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
        $criteria->compare('signup_id',$this->signup_id);
        $criteria->compare('answers',$this->answers,true);
        $criteria->compare('right_times',$this->right_times);
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
     * @return DriverExamnewOnline the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }


    /** 检测用户是否在允许的考试次数内，允许考试 返回true,否则 false;
     * @param $open_id
     * @param int $type
     * @return bool
     */
    public function checkExamTime($open_id, $type = self::TYPE_TEST){
        $type = (int)$type;
        $user_key = self::CACHE_KEY.$open_id.'_times_'.$type.date('Ymd');
        $redis_mod = RedisHAProxy::model();
        $data = $redis_mod->get($user_key);
        if(!$data){
            $time = 1;

        }else {
            $time = $data + 1;
        }
        //$time = 1;
        $redis_mod->set($user_key,$time,86400);
        if($type == self::TYPE_ONLINE){
            return $time <= self::ALLOW_TIMES;
        }elseif($type == self::TYPE_TEST){
            return $time <= self::ALLOW_TEST_TIIMES;
        }else if($type == self::TYPE_DRIVER_EXAM){
            return $time <= self::ALLOW_STUDY_TIIMES;
        }else if($type == self::TYPE_DRIVER_PRISE_EXAM){
            return $time <= self::ALLOW_PRISE_TIIMES;
        }
        return false;
    }


    /** 存储用户考试id
     * @param $open_id
     * @param int $type
     * @return bool
     */
    public function addExamId($open_id, $exam_id, $type = self::TYPE_TEST) {
        $type = (int)$type;
        $user_key = self::CACHE_KEY.$open_id.'exam_id_'.$type;
        $redis_mod = RedisHAProxy::model();
        $data = $redis_mod->get($user_key);
        if(!$data){
            $redis_mod->set($user_key,$exam_id,20*60);
            return $exam_id;
        }else{
            return $data;
        }

    }


    /** 删除用户考试id
     * @param $open_id
     * @param int $type
     * @return bool
     */
    public function delExamId($open_id, $type = self::TYPE_TEST) {
        $type = (int)$type;
        $user_key = self::CACHE_KEY.$open_id.'exam_id_'.$type;
        $redis_mod = RedisHAProxy::model();
        $data = $redis_mod->del($user_key);
        return true;

    }


    public function getExamId($open_id,$type){
        $type = (int)$type;
        $user_key = self::CACHE_KEY.$open_id.'exam_id_'.$type;
        $redis_mod = RedisHAProxy::model();
        $data = $redis_mod->get($user_key);
        return $data;
        //return 12;
    }



    public function checkExistQuestion($open_id,$type = self::TYPE_TEST) { // create_time + 20 > now
        $type = (int)$type;
        $exam_id = $this->getExamId($open_id,$type);
        if($exam_id){
            return $exam_id;


//            $res = $this->find('update_time = create_time and create_time >= :ctime and open_id = :open_id and type = :type',array(':open_id'=>$open_id,':ctime'=>date('Y-m-d H:i:s', strtotime('-1200 seconds')),':type'=>$type));
//            if($res){
//                return $res;
//            }
        }else{
            return false;
        }
    }


    /**
     * 司机是否需要某个分类参加答题
     * @param $driver_id
     * @param $type
     * @return bool
     */
    public function checkNeedExam($driver_id, $type, $cate_id){
        $res = $this->find('open_id = :open_id and type = :type and cate_id = :cate_id and status = :status',
            array(':open_id'=>$driver_id,':type'=>$type,':cate_id'=>$cate_id,':status'=>self::STATUS_ALLRIGHT));
        if($res) return false;
        return true;

    }


    /**
     * 司机是否需要参加答题
     * @param $driver_id
     * @param $type
     * @return array
     */
    public function checkUserNeedExam($driver_id, $type){
        $res = $this->findAll('open_id = :open_id and type = :type and  status = :status',
            array(':open_id'=>$driver_id, ':type'=>$type, ':status'=>self::STATUS_ALLRIGHT));
        $data = array();
        if($res){
            foreach($res as $obj){
                $data[$obj->cate_id] = $obj->cate_id;
            }
        }
        return $data;

    }


    /**
     * 线上考试开始，司机端点击开始后生成考题
     * @param $id
     * @return array
     */
    public function startExam($id){
        $signupInfo = DriverRecruitment::model()->findByPk($id);
        if(!$signupInfo){
            return array('code'=>1,'message'=>'司机未报名','data'=>'');
        }
        $mod_wechat = new Wechat();
        $id_card = $signupInfo->id_card;
        $driver_open_info = $mod_wechat->getInfoByIdcard($id_card);
        if(!$driver_open_info){
            return array('code'=>2,'message'=>'未绑定微信','data'=>'');
        }
        $open_id = $driver_open_info['data']['openId'];
        $res = $this->createQuestionOnline($open_id,$signupInfo->city_id,$id,self::TYPE_ONLINE);
        if($res['exam_id']){
            return array('code'=>0,'message'=>'可以开始考试了','data'=>'');
        }
        else{
            return array('code'=>3,'message'=>'题都被答光了','data'=>'');
        }
    }


    /**
     * $rules = array(3=>10,5=>10); 分类id => 考题数目
     * 调用前需要先check 是否有题了
     * @param $open_id
     * @param $signup_id
     * @param $type
     * @param $rules
     * @return array
     */
    public function createQuestionStudy($open_id,$cate_id,$type,$rules,$city_id){
        $now = time();
        $question_mod = QuestionNew::model();
        $questions = QuestionNew::model()->getQuestionStudyList($rules,$city_id);
        if($questions){
            foreach($questions as $key => $value){
                $keys[] = $value['id'];
                $answers[] = $value['answer'];
                $question_format[]= $question_mod->formatQuestion($value);
            }
            $d = date('Y-m-d H:i:s');
            $data =array(
                'open_id'=>$open_id,
                'cate_id'=>$cate_id,
                'questions'=>implode(',',$keys),
                'answers'=>implode(',',$answers),
                'type'=>$type,
                'update_time'=>$d,
                'create_time'=>$d
            );
            //print_r($data);die;
            $mod = new DriverExamnewOnline();
            $mod->attributes = $data;
            $res_save = $mod->save();

            if(!$res_save){
                EdjLog::info('save question error'.json_encode($data));
                $ret = array('code' => 610 , 'data'=>'','message' => '发生错误，错误编号610');
                $json_str = json_encode($ret);
                echo $json_str;Yii::app()->end();
            }
            $exam_id = $mod->id;
            $this->addExamId($open_id,$exam_id,$type);
            $data_return = array(
                'create_time'=>$now,
                'exam_id'=>$exam_id,
                'question'=>$question_format
            );

        }else{
            $data_return = array(
                'create_time'=>$now,
                'exam_id'=>'',
                'question'=>array()
            );
        }

        return $data_return;

    }



    public function createQuestionOnline($open_id,$user_city_id,$signup_id,$type){
        $now = time();

        $exam_id_redis = $this->checkExistQuestion($open_id,self::TYPE_ONLINE);
        if(!$exam_id_redis){
            $this->blockTimeOutQuestion($open_id);
            $question_mod = QuestionNew::model();
            $questions = QuestionNew::model()->getQuestionList($user_city_id);
            if($questions){
                foreach($questions as $key => $value){
                    $keys[] = $value['id'];
                    $answers[] = $value['answer'];
                    $question_format[]= $question_mod->formatQuestion($value);
                }
                $d = date('Y-m-d H:i:s');
                $data =array(
                    'open_id'=>$open_id,
                    'signup_id'=>$signup_id,
                    'questions'=>implode(',',$keys),
                    'answers'=>implode(',',$answers),
                    'type'=>$type,
                    'update_time'=>$d,
                    'create_time'=>$d
                );
                //print_r($data);die;
                $mod = new DriverExamnewOnline();
                $mod->attributes = $data;
                $res_save = $mod->save();

                if(!$res_save){
                    EdjLog::info('save question error'.json_encode($data));
                    $ret = array('code' => 610 , 'data'=>'','message' => '发生错误，错误编号610');
                    $json_str = json_encode($ret);
                    echo $json_str;Yii::app()->end();
                }
                $exam_id = $mod->id;
                $this->addExamId($open_id,$exam_id,self::TYPE_ONLINE);

                $data_return = array(
                    'create_time'=>$now,
                    'exam_id'=>$exam_id,
                    'question'=>$question_format
                );

            }else{
                $data_return = array(
                    'create_time'=>$now,
                    'exam_id'=>'',
                    'question'=>array()
                );
            }
        }else{
            $data_return = array(
                'create_time'=>$now,
                'exam_id'=>$exam_id_redis,
                'question'=>array()
            );
        }
        return $data_return;

    }

    /**
     * 更改未答题状态为1
     * @param $open_id
     * @return int
     */
    public function blockTimeOutQuestion($open_id){
        $res = $this->updateAll(array('status'=>self::STATUS_NOTANSWER),'open_id = :open_id and status = :status',array(':open_id'=>$open_id,':status'=>self::STATUS_DEFAULT));
        return $res;
    }


    /**
     * 获取司机有奖答题的考题id
     * @param $open_id
     * @param $type
     * @return string
     */
    public function getExamIdFromDb($open_id,$type,$cate_id = ''){
        $where = 'update_time = create_time and create_time >= :ctime and open_id = :open_id and type = :type';
        $param = array(':open_id'=>$open_id,':ctime'=>date('Y-m-d H:i:s', strtotime('-1200 seconds')),':type'=>$type);
        if($cate_id){
            $where .= ' and cate_id = :cate_id ';
            $param[':cate_id'] = $cate_id;
        }
        $exam_info = $this->find($where,$param);
        //print_r($exam_info);die;
        $checkExam_id = ($exam_info && isset($exam_info->id)) ? $exam_info->id : '';
        return $checkExam_id;
    }


    /**
     * 校验考题id
     * @param $open_id
     * @param $type
     * @return string
     */
    public function getExamIdByOpenid($exam_id, $open_id){
        $where = 'status = :status and  create_time >= :ctime and open_id = :open_id and id = :id';
        $param = array(':open_id'=>$open_id,':ctime'=>date('Y-m-d H:i:s', strtotime('-1200 seconds')),':id'=>$exam_id,':status'=>self::STATUS_DEFAULT);

        $exam_info = $this->find($where,$param);
        $checkExam_id = ($exam_info && isset($exam_info->id)) ? $exam_info->id : '';
        return $checkExam_id;
    }




    /**
     * 计算考题分数，
     * @param $exam_id
     * @param array $user_answer
     * @param string $open_id 用户微信open_id
     * @param int $type 类型 模拟，正式考试
     * @return array 返回总题数，正确数
     */
    public function checkAnswer($exam_id, $open_id, $type, array $user_answer) {
        $ret = array('code' => 1 , 'data'=>'', 'message' => '访问错误');
        $checkExam_id = $this->getExamIdByOpenid($exam_id, $open_id);
        $right = 0;
        $total = 0;
        if($checkExam_id ){
            if($checkExam_id != $exam_id){
                $ret = array('code'=>606,'data'=>'','message'=> '考试题目不匹配，请重新考试');
                EdjLog::info('check answer log:'.json_encode($ret).' exam_id:'.$exam_id.' checkexam_id'.$checkExam_id.' open_id:'.$open_id.'answer:'.json_encode($user_answer));
                return $ret;
            }
            $question_info = $this->find('update_time = create_time and id = :id and open_id = :open_id and type = :type',
                array(':open_id'=>$open_id,':id'=>$exam_id,':type'=>$type));
            if(!$question_info){
                $ret = array('code'=>608,'data'=>'','message'=> '考试题目不匹配，请重新考试');
                EdjLog::info('check answer db log:'.json_encode($ret).' exam_id:'.$exam_id.' checkexam_id'.$checkExam_id.' open_id:'.$open_id.'answer:'.json_encode($user_answer));
                return $ret;
            }

            $question_id = explode(',',$question_info->questions);
            $answers = explode(',',$question_info->answers);
            foreach($answers as $k => $v){
                $standard_answer[$question_id[$k]] = $v;
            }

            foreach($user_answer as $question_id_k => $answer_v){
                if(isset($standard_answer[$question_id_k])){
                    if(strcasecmp($standard_answer[$question_id_k],$answer_v) == 0){
                        $right ++;
                        $user_answer_tmp = 1;

                    }else{
                        $user_answer_tmp = 0;
                    }
                    $total ++;

                    //记录log
                    $log_data = array(
                        'open_id'=>$open_id,
                        'question_id'=>$question_id_k,
                        'answer'=>$standard_answer[$question_id_k],//
                        'user_answer'=>$answer_v,
                        'result'=>$user_answer_tmp,
                        'type'=>$type,
                        'create_time'=>date('Y-m-d H:i:s')
                    );
                    //print_r($log_data);
                    EdjLog::info('exam_log : data'.json_encode($log_data));
                    $log_mod = new DriverExamnewLog();
                    $log_mod->attributes = $log_data;
                    $res = $log_mod->save();

                    //模拟考试计入错题题库 正确的题也要把错题库更正
                    if(($type == self::TYPE_TEST) || ($type == self::TYPE_ONLINE)){
                        DriverExamnewPractice::model()->addWrongExam($open_id,$question_id_k,$user_answer_tmp,$type);
                    }
                }
            }

            //添加考卷的考题答案

            $question_info->user_answers = json_encode($user_answer);
            $question_info->right_times = $right;
            $question_info->status = ($right == count($standard_answer)) ? self::STATUS_ALLRIGHT : self::STATUS_PARTRIGHT;
            $question_info->type = $type;
            $question_info->update_time = date('Y-m-d H:i:s');
            $res = $question_info->save();
            if(!$res){
                $online_data = array(
                    'user_answers'=>json_encode($user_answer),
                    'right_times'=>$right,
                    'type'=>$type,
                );
                EdjLog::info('save data error id:'.$exam_id.' data:'.json_encode($online_data));
            }

            $this->delExamId($open_id,$type);




            $ret['code']= 0;
            $status = ( ($right == $total)  && ($right > 0) ) ? 1 : 0;
            $ret['data'] = array('right'=>$right,'total'=>$total,'status'=>$status);
            $ret['message'] = 'ok';
        }else{
            $ret = array('code'=>604,'data'=>'','message'=> '考试时间已经超时');
        }


        return $ret;
    }


    public function getInfo($id){
        return $this->findByPk($id);
    }


    public function getExamTimes($id_card,$type=self::TYPE_ONLINE){
        $driver_info = DriverRecruitment::model()->getDriverByIDCard($id_card);
        if($driver_info){
            $signup_id = $driver_info['id'];
            $today = $this->count('type = :type and signup_id = :sid and create_time > :ctime',array(':type'=>$type,':sid'=>$signup_id,':ctime'=>date('Y-m-d').' 00:00:00'));
            $all = $this->count('type = :type and signup_id = :sid',array(':type'=>$type,':sid'=>$signup_id));
            return array('total'=>$all,'today'=>$today);
        }
        return false;
    }
}
