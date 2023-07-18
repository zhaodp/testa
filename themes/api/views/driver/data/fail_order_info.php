<?php
/**
 * @author qiujianping@edaijia-inc.cn 2014-09-19
 * 
 * Api used to get cancel and reject order detail 
 * for this month
 * Params need include the token
 */

$token = isset($params['token']) && !empty($params['token']) ? trim($params['token']) : "";

$driver = DriverStatus::model()->getByToken($token);

if ($driver == null || $driver->token === null || $driver->token !== $token) {
    $ret = array(
        'code' => 1,
        'message' => '请重新登录'
    );
    echo json_encode($ret);
    return;
}

$cancel_rst_detail = array();
$cancel_detail = Order::model()->getDriverMonCancelOrders($driver->driver_id);
foreach($cancel_detail as $detail) {
    $cancel_rst_detail[] = array(
	    'created'=> $detail['created'],
	    'order_id'=> $detail['order_id'],
	    'fail_type'=> '销单',
	    'description'=> '司机销单');
}

$reject_rst_detail = array();
$reject_detail = DriverRejectOrderDetail::model()->getMonRejectOrdersByDriverId($driver->driver_id); 
foreach($reject_detail as $detail) {
    if($detail['fail_type'] ==  3) {
	$reject_rst_detail[] = array(
		'created'=> $detail['created'],
		'order_id'=> $detail['order_id'],
		'fail_type'=> '拒单',
		'description'=> '主动拒绝');
    } else {
	$reject_rst_detail[] = array(
		'created'=> $detail['created'],
		'order_id'=> $detail['order_id'],
		'fail_type'=> '拒单',
		'description'=> '30S未接单');
    }
}

$ret = array(
	'code' => 0,
	'cancel_orders' => $cancel_rst_detail,
	'reject_orders' => $reject_rst_detail,
        'message' => '获取成功'
	);
echo json_encode($ret);
return;

