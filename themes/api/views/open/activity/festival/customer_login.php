<?php
/**
 * 客户登录
 */
$phone = isset($params['phone']) ? trim($params['phone']) : '';
$code  = isset($params['code']) ? trim($params['code']) : '';
if(empty($phone) || empty($code)){
	$ret = array ('code'=>2,'message'=>'请输入正确的手机号和验证码');
	echo json_encode($ret);return;
}
$rcode = RFestival::model()->getFestivalCustomerCode($phone);
if(!$rcode){
    $ret = array ('code'=>2,'message'=>'验证码已失效,请重新获取');
    echo json_encode($ret);return;
}
if($code != $rcode){
    $ret = array ('code'=>2,'message'=>'验证码错误,请重新输入');
    echo json_encode($ret);return;
}
//判断是否报名,如果已报名,则进入报名成功界面
$customer = FestivalCustomer::model()->getCustomerByPhone($phone);
if(!$customer){//未报名
    $ret = array ('code'=>3,'message'=>'登录成功');
    echo json_encode($ret);return;
}
//已报名
$data = array();
$data['start_city'] = $customer['start_city'];
$data['end_city'] = $customer['end_city'];
$ret = array ('code'=>0,'data'=>$data ,'message'=>'登录成功');
echo json_encode($ret);return;
