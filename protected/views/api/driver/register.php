<?php
/**
 * 司机手机注册，上传imei号和电话号码
 */

$imei = $params['imei'];
$phone = $params['phone'];


//如果前面有+86，清除掉，直接保留手机号码
$phone = str_replace('+86', '', $phone);
$ret = Employee::register($imei, $phone);

switch ($ret) {
	case 0 :
		$message = array (
			'code'=>0, 
			'message'=>'手机已经注册');
		break;
	case 1 :
		$message = array (
			'code'=>1, 
			'message'=>'手机注册成功');
		break;
}

echo json_encode($message);