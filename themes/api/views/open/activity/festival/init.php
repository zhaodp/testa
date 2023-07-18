<?php
/**
 * 初始化加油页面
 */
$phone = isset($params['phone']) ? trim($params['phone']) : '';
if(empty($phone)){
    $ret = array ('code'=>2,'message'=>'参数错误');
    echo json_encode($ret);return;
}
$customer = FestivalCustomer::model()->find('phone=:phone', array(':phone'=>$phone));
if(!$customer){
    $ret = array ('code'=>2,'message'=>'该用户未参加活动');
    echo json_encode($ret);return;
}
$data = array();
$data['start_city'] = $customer['start_city'];
$data['end_city']   = $customer['end_city'];
$data['come_on_times'] = $customer['come_on_times'];
$data['open_id'] = $customer['open_id'];
$data['nick_name']   = $customer['nick_name'];
$ret = array(
    'code'=>0,
    'customer'=>$data,
    'message'=>'获取成功');
echo json_encode($ret);
return;