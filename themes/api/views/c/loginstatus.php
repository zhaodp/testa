<?php
/**
 * 服务端检测登录状态，返回用户登录token
 * @param $params     $params['phone']
 * @param $params     $params['macaddress']
 * @param $params     $params['os']
 * @param $params     $params['udid']
 * @author bidong  2014-01-18
 */


if(empty($params['phone'])){
    $ret = array ('code'=>2,'message'=>'手机号不能为空');
    echo json_encode($ret);return;
}

$phone 			= trim($params['phone']);
$device_type 	= empty($params['os']) ? 'unknown' : strtoupper(trim($params['os']));
$udid 			= empty($params['udid']) ? '' : trim($params['udid']);
$macaddress 	= empty($params['macaddress']) ? '' : trim($params['macaddress']);
$business = empty($params['business']) ? CustomerToken::EDJ_TOKEN_FROM : trim($params['business']);

//业务类型
if(!in_array($business, CustomerToken::$business_list)){
	$ret = array (
			'code'=>2,
			'message'=>'对不起，不支持的业务类型:'.$business);
	echo json_encode($ret);return;
}

//获取快速登录状态
$customerLogic = new CustomerLogic();
$logStatus= $customerLogic->getQuickLoginStatus($phone,$business);

//判断登录状态
if(!$logStatus){
	$ret = array (
			'code'=>2,
			'message'=>'未登录');
	echo json_encode($ret);return;
}

if( empty($phone) || empty($device_type) || empty($udid) ){
	$ret = array ('code'=>2,'message'=>'参数不正确');
	echo json_encode($ret);return;
}

//多业务登录逻辑
// $last_token = $customerLogic->multiLoginCheck($phone, $udid, $business);
$last_token=null;
//记录token
$token = $customerLogic->setCustomerTokenCache($phone, $last_token, $business);

//setCustomerTokenCache 传递的参数
$tokenParams = array(
		'phone'		=>	$phone,
		'udid'		=>	$udid,
		'macaddress'=>	$macaddress,
		'device_type'=>	$device_type,
		'business' => $business,
		'authtoken'	=>	$token);

//task 添加或修改token
$task = array(
	'method'=>'customer_login',
	'params'=>$tokenParams
);

Queue::model()->putin($task,'task');

////清除 密码cache
//$customerPass = $customerLogic->expiredCustomerPasswdCache($phone, $macaddress);

//返回数据
$ret = array (
		'code'=>0,
		'token'=>$token, 
		'message'=>'登录成功');

echo json_encode($ret);return;

