<?php
/**
 * 客户端API：c.logout 用户退出登陆
 * 调用的url:
 * @author mengtianxue 2013-05-20
 * @param $params     $params['phone']  $params['token']
 * 
 * @see
 * @since
 */
//用手机号码和收到的密码登录 返回是否成功 以及 token
$phone = trim($params['phone']);
$token = $params['token'];
$business = empty($params['business']) ? CustomerToken::EDJ_TOKEN_FROM : trim($params['business']);


//需要优化，增加缓存。add by sunhongjing at 2013-05-19
$validate = CustomerToken::model()->validateToken($token);

if (!$validate){
	$ret = array (
				'code'=>2,
				'message'=>'您还没有登陆哦');
	echo json_encode($ret); return;
}

//清除当前用户token
$customerLogic = new CustomerLogic();
$clearTokenCache = $customerLogic->clearCustomerTokenCache($token);

$deleteToken = array('phone' => $phone,'business'=>$business);

//task 添加或修改token
$task = array(
		'method'=>'customer_logout',
		'params'=>$deleteToken
);
//Queue::model()->task($task);//使用redis队列
//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
Queue::model()->putin($task,'task');

$ret = array (
		'code'=>0,
		'message'=>'登出成功');
echo json_encode($ret);













