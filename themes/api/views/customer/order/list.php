<?php
//获得用户订单列表 返回是否成功
$token = $params['token'];
$pageNo = $params['pageNo'];
$pageSize = $params['pageSize'];
$validate = CustomerToken::model()->validateToken($token);

if ($validate){
	$orders = Order::getListByCustomerPhone($pageNo, $pageSize, $validate['phone']);
	
	$ret = array(
		'code'=>0,
		'orderList'=>$orders,
		'message'=>'获取成功');
} else {
	$ret = array(
		'code'=>1,
		'message'=>'获取失败');
}

echo json_encode($ret);