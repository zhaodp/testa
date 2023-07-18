<?php
/**
 * valid customer phone can generate order
 * @author aiguoxin
 * @version 2014-05-04 
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
//customer phone
$phone = isset($params['phone']) ? trim($params['phone']) : '';

if(empty($token)){
    $ret=array('code'=>2 , 'message'=>'token不正确!');
    echo json_encode($ret);return;
}

if(empty($phone)){
    $ret=array('code'=>2 , 'message'=>'phone不正确!');
    echo json_encode($ret);return;
}

// 验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver == null || $driver->token===null||$driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}

//0:can generate order; 1:can not generate order
$flag = 0;
$message = '可以生成订单';
//judge phone is owned by driver
if(Driver::getDriverByPhone($phone)){
	$flag = 1;
	$message = '司机电话不能生成订单';
}

//judge phone is in blaklist
if(CustomerStatus::model()->is_black($phone)){
	$flag = 1;
	$message = '黑名单客户不能生成订单';
}


if(CustomerWhiteList::model()->in_whitelist($phone)) {
    $flag = 1;
    $message = '白名单客户不能生成订单';
}

//返回成功信息
$ret=array('code'=>0 , 
	'flag'=>$flag,
	'message'=>$message);
echo json_encode($ret);return;
