<?php
/**
 * 用户获取验证码
 * 调用的url
 * @author mengtianxue 2013-05-20
 * @param $params     $params['phone'] $params['udid'] $params['macaddress']
 * 
 * @see
 * @since
 */
//用手机号码预登录 发送短信 返回是否成功
$phone 		= empty($params['phone']) ? '' :  trim($params['phone']);
$udid 		= empty($params['udid']) ? '' :  trim($params['udid']);
$macaddress = empty($params['macaddress']) ? '' :  trim($params['macaddress']);

if(empty($phone) || empty($macaddress) || empty($udid)){
	$ret = array ('code'=>1,'message'=>'参数错误');
	echo json_encode($ret);return;
}

//isNew  cache中是否存在  true 存在  false 不存在 
$isNew = FALSE;

$customerPass = array();
$passwd = '';

//获取cache中的cacha
$customerLogic = new CustomerLogic();
$customerPass = $customerLogic->getCustomerSmsPasswd($phone, $macaddress);

if(!empty($customerPass)){
	//如果有加验证  10次 和 一分钟发一次
	if($customerPass['send_times'] > 5){
		$ret = array (
					'code'=>1,
					'message'=>'一天之内只能登录五次。');
		echo json_encode($ret);return;	
	}
	
	//用update_time比较1分钟限制
	if($customerPass['update_time'] + 60 > time()){
		
		$ret = array (
					'code'=>1,
					'message'=>'一分钟之内只能请求一次预登录验证码。');
		echo json_encode($ret);return;
	}
	
	//再有效期内重发短信  但不更新数据
	if( $customerPass['expired'] > time() ){
        $isNew = TRUE;
		$passwd =  $customerPass['passwd'];
		$customerLogic->resetCustomerPasswdCache($customerPass);
	}
	
}else{
	$customerPass['phone'] = $phone;
	$customerPass['macaddress'] = $macaddress;
}

if(empty($passwd)){
	$passwd = rand(1000, 9999);//生成验证码
	$customerPass['passwd'] = $passwd;
	//没有生成pass  添加到cache   发送短信 isnew 判断的是否时新数据
	$customerPass = $customerLogic->setCustomerPasswdCache($customerPass, $isNew);
}

$message = '您的预登录验证码为：%s，预登录验证码将在十分钟后失效。同一手机号码一天可申请五次预登录验证码。';
$content = sprintf($message, $passwd);

$ret_sms = Sms::SendSMS($phone, $content,Sms::CHANNEL_SOAP);
if( empty($ret_sms) ){
	$ret = array (
			'code'=>1, 
			'message'=>'系统延迟，请稍后再试。');
	echo json_encode($ret);return;
}

//重新生成密码时才更新数据
if(!$isNew){
//添加task队列向数据中添加
    $task = array(
        'method'=>'customer_prelogin_passwd',
        'params'=>$customerPass
    );

    //Queue::model()->task($task);//使用redis队列
	//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
	Queue::model()->putin($task,'task');
}

//返回json数据
$ret = array (
		'code'=>0, 
		'message'=>'验证码已成功发送。');
echo json_encode($ret);return;





