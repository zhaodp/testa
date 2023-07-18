<?php
$driver_profile = Driver::getProfileByImei($_GET['imei']);

if (!$driver_profile) {
	die();
}

$location = array (
	'imei'=>$_GET['imei'], 
	'mcc'=>$_GET['mcc'], 
	'mnc'=>$_GET['mnc']);

$ta = $_GET['ta'];

$towers[] = array (
	'mcc'=>$_GET['mcc'], 
	'lac'=>$_GET['lac'], 
	'ci'=>$_GET['ci'], 
	'ssi'=>$_GET['rssi'], 
	'ta'=>$ta);
$towers[] = array (
	'mcc'=>$_GET['mcc'], 
	'lac'=>$_GET['lac1'], 
	'ci'=>$_GET['ci1'], 
	'ssi'=>$_GET['rssi1'], 
	'ta'=>$ta);
$towers[] = array (
	'mcc'=>$_GET['mcc'], 
	'lac'=>$_GET['lac2'], 
	'ci'=>$_GET['ci2'], 
	'ssi'=>$_GET['rssi2'], 
	'ta'=>$ta);
$towers[] = array (
	'mcc'=>$_GET['mcc'], 
	'lac'=>$_GET['lac3'], 
	'ci'=>$_GET['ci3'], 
	'ssi'=>$_GET['rssi3'], 
	'ta'=>$ta);
$towers[] = array (
	'mcc'=>$_GET['mcc'], 
	'lac'=>$_GET['lac4'], 
	'ci'=>$_GET['ci4'], 
	'ssi'=>$_GET['rssi4'], 
	'ta'=>$ta);
$towers[] = array (
	'mcc'=>$_GET['mcc'], 
	'lac'=>$_GET['lac5'], 
	'ci'=>$_GET['ci5'], 
	'ssi'=>$_GET['rssi5'], 
	'ta'=>$ta);
$towers[] = array (
	'mcc'=>$_GET['mcc'], 
	'lac'=>$_GET['lac6'], 
	'ci'=>$_GET['ci6'], 
	'ssi'=>$_GET['rssi6'], 
	'ta'=>$ta);

//校验基站信息是否正确
foreach($towers as $tower) {
	$sum = intval($tower['mcc'])+intval($tower['lac'])+intval($tower['ci']);
}

if ($sum==0) {
	die('error tower info.');
}

//添加task队列
$task = array (
	'method'=>'mtk_location', 
	'params'=>array (
		'imei'=>$_GET['imei'], 
		'towers'=>$towers));
Queue::model()->dumplog($task);
//QueueLog::model()->add($task);

//$location['towers'] = $towers;
//$hash_key = md5(json_encode($towers[0]));
//$json_locate = json_encode($location);
//
////保存到数据库
//$attributes = array (
//	'imei'=>$_GET['imei'], 
//	'hash'=>$hash_key, 
//	'location'=>$json_locate, 
//	'created'=>date(Yii::app()->params['formatDateTime'], time()));
//
//$queue = Yii::app()->params['httpsqs']['location']['queue'];
//$host = Yii::app()->params['httpsqs']['location']['host'];
//$port = Yii::app()->params['httpsqs']['location']['port'];
//$password = Yii::app()->params['httpsqs']['location']['password'];
//
//$httpsqs = new Httpsqs($host, $port, $password);
//$httpsqs->put($queue, $json_locate);
//
//$table_name = 't_driver_tracks_'.date('Ym', time());
//Yii::app()->dbstat->createCommand()->insert($table_name, $attributes);
