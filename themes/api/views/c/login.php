<?php
/**
 * 客户端API：c.login 用户登陆
 * 调用的url:
 * @author mengtianxue 2013-05-20
 * @param $params     $params['phone']  $params['passwd'] $params['os'] $params['udid'] $params['macaddress']
 * 
 * @see
 * @since
 */

//判断数据有效性,add by sunhongjing 2013-06-12
if(empty($params['phone'])){
	$ret = array ('code'=>2,'message'=>'参数错误');
	echo json_encode($ret);return;
}

$phone 			= trim($params['phone']);
$passwd 		= empty($params['passwd']) ? '' : trim($params['passwd']);
$device_type 	= empty($params['os']) ? 'unknown' : strtoupper(trim($params['os']));
$udid 			= empty($params['udid']) ? '' : trim($params['udid']);
$macaddress 	= empty($params['macaddress']) ? '' : trim($params['macaddress']);
$business = empty($params['business']) ? CustomerToken::EDJ_TOKEN_FROM : trim($params['business']);


// //业务类型
if(!in_array($business, CustomerToken::$business_list)){
	$ret = array (
			'code'=>2,
			'message'=>'对不起，不支持的业务类型:'.$business);
	echo json_encode($ret);return;
}

//获取密码
$customerLogic = new CustomerLogic();
//apple验证测试号码
if($phone == CustomerMain::APPLE_TEST_ACCOUNT && $passwd == CustomerMain::APPLE_TEST_MSG){
	//不进行验证
}else{
//获取密码
//$customerPass = $customerLogic->getCustomerSmsPasswd($phone, $macaddress);
$customerPass = RCustomerInfo::model()->getCustomerSmsPasswd($phone,$business);
//判断提示
if(empty($customerPass)){
	$ret = array (
			'code'=>2,
			'message'=>'请先获取验证码');
	echo json_encode($ret);return;
}

if( empty($passwd) || empty($device_type) || empty($udid) ){
	$ret = array ('code'=>2,'message'=>'参数不正确');
	echo json_encode($ret);return;
}

//密码是否相等
if( trim($customerPass->code) != trim($passwd) ){
	$ret = array (
			'code'=>2,
			'message'=>'验证码输入不正确');
	echo json_encode($ret);return;
}

//密码过期
if( $customerPass->out_time < time() ){
	$ret = array (
			'code'=>2,
			'message'=>'验证码已过期，请重新获取。');
	echo json_encode($ret);return;
}
}

//多业务登录逻辑
// $last_token = $customerLogic->multiLoginCheck($phone, $udid, $business);
$last_token=null; //登录即重新生成token
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
//Queue::model()->task($task);//使用redis队列
//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
Queue::model()->putin($task,'task');

//清除 密码cache
//$customerPass = $customerLogic->expiredCustomerPasswdCache($phone, $macaddress);

//返回数据
$ret = array (
		'code'=>0,
		'token'=>$token, 
		'message'=>'登录成功');

echo json_encode($ret);return;

