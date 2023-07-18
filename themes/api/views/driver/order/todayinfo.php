<?php
/**
 * get driver orderNum and money after every day 7
 * @author aiguoxin
 * @version 2014-05-04 
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';

if(empty($token)){
    $ret=array('code'=>2 , 'message'=>'参数不正确!');
    echo json_encode($ret);return;
}

// 验证token
$driver_id = DriverToken::model()->getDriverIdByToken($token);
if ($driver_id == null) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}

$order = Order::model()->getOrderNumAndIncome($driver_id);
if(!$order){
	$ret=array('code'=>2 , 'message'=>'获取数据失败');
	echo json_encode($ret);return;
}
if(empty($order['order_income_today'])){
	$order['order_income_today'] = 0;
}
//返回成功信息
$ret=array('code'=>0 , 
	'order_count_today'=>(int)$order['order_count_today'],
	'order_income_today'=>(int)$order['order_income_today']);
echo json_encode($ret);return;
