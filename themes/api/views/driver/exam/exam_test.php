<?php
/**
 * 司机报名微信--模拟考试
 * @author duke
 * @version 2015-03-30
 */
$open_id = isset($params['open_id']) ? trim($params['open_id']) : '';
$ret = array('code' => 1 , 'data'=>'', 'message' => '访问错误');

if(empty($open_id)){
    $ret = array('code' => 1 , 'data'=>'', 'message' => '访问错误');
    $json_str = json_encode($ret);
//    if(isset($callback)&&!empty($callback)){
//        $json_str=$callback.'('.$json_str.')';
//    }
    echo $json_str;Yii::app()->end();
}

$user_info = DriverRecruitment::model()->getInfoByOpenid($open_id);
if(!$user_info){
    $ret = array('code' => 35 , 'data'=>'', 'message' => '您尚未报名，请先报名考试');
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}

$can_exam = DriverExamnewOnline::model()->checkExamTime($open_id);
if(!$can_exam) {
    $ret['code'] = 601;
    $ret['message'] = '今天的考试次数太多了明天再考吧';
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}



$user_city_id = $user_info['city_id'];
$signup_id = $user_info['id'];


$question_mod = QuestionNew::model();
$exam_mod = DriverExamnewOnline::model();
$now = time();
//先检查当前用户是否有未完成的试题 20分钟内
$exam_id_redis = $exam_mod->checkExistQuestion($open_id,DriverExamnewOnline::TYPE_TEST);
//var_dump($exam_id_redis);die;
if($exam_id_redis){
    $exam_info = $exam_mod->findByPk($exam_id_redis);
    $question_old = $question_mod->getQuestionById($exam_info->questions);
    foreach($question_old as $v){
        $question_old_format[] = $question_mod->formatQuestion($v);
    }
    $data_return = array(
        'create_time'=>$now,
        'exam_id'=>$exam_id_redis,
        'question'=>$question_old_format
    );
}else {
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
            'type'=>DriverExamnewOnline::TYPE_TEST,
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
        DriverExamnewOnline::model()->addExamId($open_id,$exam_id,DriverExamnewOnline::TYPE_TEST);
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
}
$ret['code']=0;
$ret['data'] = $data_return;
echo json_encode($ret);
Yii::app()->end();




