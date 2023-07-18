<?php
/**
 * 司机报名微信--现场考试
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


$can_exam = DriverExamnewOnline::model()->checkExamTime($open_id,DriverExamnewOnline::TYPE_ONLINE);
if(!$can_exam) {
    $ret['code'] = 601;
    $ret['message'] = '今天的考试次数太多了明天再考吧';
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}


$question_mod = QuestionNew::model();
$exam_mod = DriverExamnewOnline::model();
$now = time();
//先检查当前用户是否有未完成的试题 20分钟内
$exam_id_redis = $exam_mod->checkExistQuestion($open_id,DriverExamnewOnline::TYPE_ONLINE);
if($exam_id_redis){
    $question_info = $exam_mod->findByPk($exam_id_redis);
    $question_old = $question_mod->getQuestionById($question_info->questions);
    foreach($question_old as $v){
        $question_old_format[] = $question_mod->formatQuestion($v);
    }
    $data_return = array(
        'create_time'=>$now,
        'exam_id'=>$exam_id_redis,
        'question'=>$question_old_format
    );
    $exam_mod->delExamId($open_id,DriverExamnewOnline::TYPE_ONLINE);
}else {
    $ret['code'] = 603;
    $ret['message'] = '请先让考官扫描二维码';
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}


$ret['code']=0;
$ret['data'] = $data_return;
echo json_encode($ret);
Yii::app()->end();




