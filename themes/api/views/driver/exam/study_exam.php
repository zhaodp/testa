<?php
/**
 * 司机考核培训 试题api
 * @author duke
 * @version 2015-03-30
 */
$driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';
$type = isset($params['types']) ? (int)$params['types'] : DriverExamStudy::TYPE_DRIVER_EXAM;
$cate_id = isset($params['cate_id']) ? (int)$params['cate_id'] : '';

$ret = array('code' => 1 , 'message' => 'null','data'=>'');

if(empty($driver_id) || !$type || !in_array($type,array(DriverExamnewOnline::TYPE_DRIVER_EXAM,DriverExamnewOnline::TYPE_DRIVER_PRISE_EXAM))){
    $ret = array('code' => 1 , 'data'=>'', 'message' => '访问错误');
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}

$driver_info = Driver::model()->getProfile($driver_id);
if(!$driver_info) {
    $ret['code'] = 603;
    $ret['message'] = '司机不存在';
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}

$can_exam = DriverExamnewOnline::model()->checkExamTime($driver_id, $type);
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
//$exam_id = $exam_mod->getExamIdFromDb($driver_id,$type,$cate_id);


    if($type == DriverExamnewOnline::TYPE_DRIVER_EXAM){
        $rules = array(DriverExamStudy::CATE_MUST_ID => 20);
        $cate_id = DriverExamStudy::CATE_MUST_ID;
    }else if($type == DriverExamnewOnline::TYPE_DRIVER_PRISE_EXAM){
        $rules = array($cate_id=>20);
    }else{
        $ret['code'] = 1;
        $ret['message'] = '访问错误';
        $json_str = json_encode($ret);
        echo $json_str;Yii::app()->end();
    }
    $data_return = $exam_mod->createQuestionStudy($driver_id, $cate_id, $type, $rules,$driver_info->city_id);


$ret['code']=0;
$ret['data'] = $data_return;
echo json_encode($ret);
Yii::app()->end();




