<?php
//更新用户称谓 返回是否成功
$name = $params['name'];
$token = $params['token'];
$validate = CustomerToken::validateToken($token);

if ($validate){
	if (Customers::updateCustomerName($validate->phone, $name)){
		$ret = array(
			'code'=>0,
			'message'=>'用户称谓更新成功。');
	} else {
		$ret = array(
			'code'=>2,
			'message'=>'用户称谓更新失败。');
	}
} else {
	$ret = array(
		'code'=>1,
		'message'=>'token已失效请重新进行预注册');
}


echo json_encode($ret);