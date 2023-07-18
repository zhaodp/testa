<?php
//获得用户信息 返回是否成功
$token = $params['token'];
$validate = CustomerToken::model()->validateToken($token);

if ($validate){
	$customer = CustomerMain::model()->getCustomer($validate->phone);
	$customer->create_time = date('Y-m-d H:i:s', $customer->attributes['create_time']);
	$ret = array(
		'code'=>0,
		'customerInfo'=>$customer->attributes,
		'message'=>'');
} else {
	$ret = array(
		'code'=>1,
		'message'=>'token已失效请重新进行预注册');
}

echo json_encode($ret);