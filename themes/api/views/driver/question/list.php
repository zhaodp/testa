<?php
/**
 * 司机端常见问题列表 
 * @author aiguoxin
 * @version 2014-11-26
 * 
 */
$token = isset($params['token']) ? $params['token'] : '';

if (empty($token)) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}

$list= Yii::app()->params['faq_list'];

$faq_list = array();
foreach ($list as $key => $value) {
    $faq_list[] = array(
    'id' => $key,
	'title' => $value['title']
    );
}

//返回成功信息
$ret=array('code'=>0 , 
	'message'=>'ok',
	'list'=>$faq_list);
echo json_encode($ret);return;
