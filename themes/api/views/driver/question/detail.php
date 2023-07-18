<?php
/**
 * 司机端常见问题单个问题具体详情 
 * @author aiguoxin
 * @version 2014-11-26
 * 
 */
$token = isset($params['token']) ? $params['token'] : '';
$questionId = isset($params['questionId']) ? $params['questionId'] : 0;


if (empty($token)) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}

if(empty($questionId)){
	$ret=array(
        'code'=>2,
        'message'=>'请选择具体问题'
    );
    echo json_encode($ret);
    return;
}


$list= Yii::app()->params['faq_list'];


//返回成功信息
$ret=array('code'=>0 , 
	'message'=>'ok',
	'answer'=>$list[$questionId]['answer']);
echo json_encode($ret);return;
