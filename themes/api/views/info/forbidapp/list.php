<?php
/**
 * 禁用软件列表 
 * @author aiguoxin
 * @version 2014-06-16
 * 
 */

$list= Yii::app()->params['forbid_app'];

//返回成功信息
$ret=array('code'=>0 , 
	'message'=>'ok',
	'list'=>$list);
echo json_encode($ret);return;
