<?php
/**
 * 第三方订单查询系统
 * @author AndyCong<congming@edaijia-staff.cn>
 * @version 2014-02-24
 */

$phone = isset($params['phone']) ? trim($params['phone']) : '';
$contact_phone = isset($params['contact_phone']) ? trim($params['contact_phone']) : '';
$booking_id = isset($params['booking_id']) ? trim($params['booking_id']) : '';
$channel = isset($params['channel']) ? trim($params['channel']) : '';
if (empty($phone) || empty($channel)) {
	$ret = array('code' => 2 , 'message' => '参数有误!');
	echo json_encode($ret);return ;
}

////验证账户电话号否正确
//$account_arr = array(
//    '95518955180',
//    '95518955183',
//);
//if (!is_numeric($phone) || strlen($phone) > 11 || !in_array($phone , $account_arr)) {
//	$ret = array('code' => 2 , 'data' => '' , 'message' => '请输入正确的账户号码!');
//	echo json_encode($ret);return ;
//}

////此处需要验证渠道号  设定几个渠道号验证
//$channel_arr = array(
//    '03014',
//);
//if (!in_array($channel , $channel_arr)) {
//	$ret = array('code' => 2 , 'message' => '渠道号有误!');
//	echo json_encode($ret);return ;
//}

$data = CustomerApiOrder::model()->getThirdSignOrders($phone , $contact_phone , $booking_id , $channel);

$ret = array('code' => 0 , 'data' => $data , 'message' => '获取成功');
echo json_encode($ret);return ;
