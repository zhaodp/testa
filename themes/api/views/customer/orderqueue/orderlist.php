<?php
/**
 * 订单列表-Order
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-05-16
 */

$pageNo = isset($params['pageNo']) ? $params['pageNo'] : 0;
$pageSize = isset($params['pageSize']) ? $params['pageSize'] : 20;
$token = $params['token'];
$offset = $pageNo*$pageSize;

//需要优化，增加缓存。add by sunhongjing
$validate = CustomerToken::model()->validateToken($token);
if ($validate){
	$orders = Order::model()->getOrderByPhone($validate['phone'] , $offset , $pageSize , $token);
	if ($orders) {
		$ret = array(
			'code'=>0,
			'orderList'=>$orders['orderList'],
			'orderCount'=>$orders['orderCount'],
			'message'=>'获取成功'
		);
	} else {
		$ret = array(
			'code'=>1,
			'message'=>'获取失败'
		);
	}
	
} else {
	$ret = array(
		'code'=>2,
		'message'=>'验证失败'
	);
}

echo json_encode($ret);