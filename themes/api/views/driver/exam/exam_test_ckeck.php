<?php
/**
 * 司机报名微信--模拟考试提交答案
 * @author duke
 * @version 2015-03-30
 */
$open_id = isset($params['open_id']) ? trim($params['open_id']) : '';
$exam_id = isset($params['exam_id']) ? trim($params['exam_id']) : '';
$answer = isset($_POST['answer']) ? trim($_POST['answer']) : '';
$callback = isset($params['callback']) ? trim($params['callback']) : '';

$ret = array('code' => 1 , 'data'=>'', 'message' => '访问错误');

if(!$open_id || !$exam_id || !$answer){
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}

$user_answer = json_decode($answer,1);
if(!is_array($user_answer)){
    $json_str = json_encode($ret);
    echo $json_str;Yii::app()->end();
}

$ret = DriverExamnewOnline::model()->checkAnswer($exam_id,$open_id, DriverExamnewOnline::TYPE_TEST,$user_answer);
echo $callback ? $callback.'('.json_encode($ret).')' : json_encode($ret);
Yii::app()->end();


