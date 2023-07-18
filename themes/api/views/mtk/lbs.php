<?php
$driver_profile = Driver::getProfileByImei($_GET['imei']);

if (!$driver_profile) {
	die();
}

if ($_GET['mcc']==0&&$_GET['lac']==0&&$_GET['ci']==0) {
	die('error tower info.');
}

$location = array (
	'imei'=>$_GET['imei'], 
	'mcc'=>$_GET['mcc'], 
	'mnc'=>$_GET['mnc']);

$ta = 255;
$rssi = 100;

$towers[] = array (
	'mcc'=>$_GET['mcc'], 
	'lac'=>$_GET['lac'], 
	'ci'=>$_GET['ci'], 
	'ssi'=>$rssi, 
	'ta'=>$ta);

//添加task队列
$task = array (
	'method'=>'mtk_location', 
	'params'=>array (
		'imei'=>$_GET['imei'], 
		'towers'=>$towers));
QueueTask::model()->add($task);