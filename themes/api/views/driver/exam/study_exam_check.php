<?php
/**
 * 司机报名微信--在线考试提交答案
 * @author duke
 * @version 2015-03-30
 */
$driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';
$type = isset($params['type']) ? (int)$params['type'] : '';
$exam_id = isset($params['exam_id']) ? (int)($params['exam_id']) : '';
$cate_id = isset($params['cate_id']) ? (int)($params['cate_id']) : '';
$answer = isset($_POST['answer']) ? trim($_POST['answer']) : '';


$ret = array('code' => 1 , 'data'=>'', 'message' => '访问错误');

if(!$driver_id || !$exam_id || !$answer || !in_array($type,array(DriverExamnewOnline::TYPE_DRIVER_EXAM,DriverExamnewOnline::TYPE_DRIVER_PRISE_EXAM))){
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}

$user_info = Driver::model()->getProfile($driver_id);
if(!$user_info){
    $ret = array('code' => 35 , 'data'=>'', 'message' => '用户不存在');
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}
$study_mod = new DriverExamStudy();
$had_exam = $study_mod->checkDriverNeedExam($driver_id,$type); //该司机需要考试的知识点

$user_answer = json_decode($answer,1);
$ret = DriverExamnewOnline::model()->checkAnswer($exam_id,$driver_id, $type, $user_answer, $cate_id);
//print_r($ret);die;
if($ret['code'] == 0 && $ret['data']['status'] == 1){ //考试通过 表示一下报名表的状态

    if($type == DriverExamnewOnline::TYPE_DRIVER_PRISE_EXAM){

        $exam_info = DriverExamnewOnline::model()->getInfo($exam_id);
        $cate_id = $exam_info->cate_id; // 考题的分类id
        //$had_exam = $study_mod->checkDriverNeedExam($driver_id,$type); //该司机需要考试的知识点
        //print_r($had_exam);echo $cate_id;
        if(!in_array($cate_id,$had_exam)){
            $cate_name = QuestionNew::getCategory($cate_id) ;
            $weath_res = DriverExt::model()->driverWealth($driver_id,30,DriverWealthLog::REWARD_PUNISH_TYPE , $user_info->city_id , date('Y-m-d H:i:s'),$cate_name.'奖励');
            if(!$weath_res){

                EdjLog::info($driver_id,30,DriverWealthLog::REWARD_PUNISH_TYPE , $user_info->city_id , date('Y-m-d H:i:s'),$cate_name.'奖励');
            }
        }

    }
}
if($type == DriverExamStudy::TYPE_DRIVER_EXAM){
    $ret['data']['next_cate_id']= '';
}
echo  json_encode($ret);
Yii::app()->end();


