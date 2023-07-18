<?php
/**
 * 司机报名微信--错题解析
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
$question_mod = QuestionNew::model();

$user_info = DriverRecruitment::model()->getInfoByOpenid($open_id);
if(!$user_info){
    $ret = array('code' => 35 , 'data'=>'', 'message' => '您尚未报名，请先报名考试');
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}

$question_new = array();
$wrong_question = DriverExamnewPractice::model()->getAllWrong($open_id);
if($wrong_question){
    foreach($wrong_question as $v){
        $ids[] = $v->question_id;
    }
    $ids_str = implode(',',$ids);
    $questions = QuestionNew::model()->getQuestionById($ids_str,false);
    foreach($questions as $obj) {
        $question_new[$obj['id']] = $obj;
    }
    $question_old_format = array();
    foreach($ids as $id){
        if(isset($question_new[$id])) {
            $question_old_format[] = $question_mod->formatQuestion($question_new[$id],true);
        }
    }
    $data_return = array(
        'question'=>$question_old_format
    );
    $ret['code']=0;
    $ret['data'] = $data_return;
}else{
    $ret['message'] = '没有错题了';
    $ret['code'] = 7;
}
echo json_encode($ret);
Yii::app()->end();






