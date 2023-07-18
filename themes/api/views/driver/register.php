<?php
//需要写清楚注释，增加缓存，封装业务逻辑，写库走队列 add by sunhongjing at 2013-5-19
$imei = $params['imei'];
$sim = $params['sim'];
$device = isset($params['device']) ? $params['device'] : '';
$timestamp = isset($params['timestamp']) ? $params['timestamp'] : 0;
$app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';

//add by aiguoxin 2014-11-03
if($timestamp){
	$timestamp=strtotime($timestamp);
	$now = time();
	if((abs($now-$timestamp))>60){ //服务器端时间与客户端相差60,司机端口不可用
		$message = array (
			'code'=>4, 
			'client_time'=>$timestamp,
			'server_time'=>$now,
			'message'=>'手机时间不正确，请检查并参考北京时间设置。');
		echo json_encode($message);
		return;
	}
}

//add by aiguoxin 2014-07-24
if(empty($imei)){
	$ret = array(
	'code'=>2,
	'message'=>'imei不能为空');
	echo json_encode($ret);return;
}

if(empty($sim)){
	$ret = array(
	'code'=>2,
	'message'=>'请检查SIM卡安装情况，双卡手机请更换SIM卡槽再试。');
	echo json_encode($ret);return;
}


if (isset($params['phone'])) {
	$phone = $params['phone'];
} else {
	$phone = '';
}

$ret = DriverPhone::model()->registerDriverPhone($imei, $sim, $phone, $device, $app_ver);

echo json_encode($ret);