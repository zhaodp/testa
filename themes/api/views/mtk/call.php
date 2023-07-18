<?php
/**
 * 呼叫完成后上传通话纪录
 */
//检查imei是否存在
$driver_profile = Driver::getProfileByImei($_GET['imei']);

if (!$driver_profile) {
	die();
}

if (!isset($_REQUEST['phone'])||!$_REQUEST['phone'])
{
	echo '-1';
	Yii::app()->end();
}

$_REQUEST['duration'] = $_REQUEST['calltime'];

unset($_REQUEST['method']);
unset($_REQUEST['sig']);
unset($_REQUEST['ver']);
unset($_REQUEST['query']);
unset($_REQUEST['calltime']);

$sig = Api::createSig($_REQUEST);
$_REQUEST['sig'] = $sig;
$_REQUEST['insert_time'] = date(Yii::app()->params['formatDateTime'], time());

//添加task队列
$task = array(
	'method'=>'mtk_call', 
	'params'=>$_REQUEST);
Queue::model()->dumplog($task);
