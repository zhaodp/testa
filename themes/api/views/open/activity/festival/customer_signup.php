<?php
/**
 * 客户报名
 */
$phone = isset($params['phone']) ? $params['phone'] : '';
$start_time_begin = isset($params['start_time_begin']) ? $params['start_time_begin'] : '';
$start_time_end = isset($params['start_time_end']) ? $params['start_time_end'] : '';
$start_city = isset($params['start_city']) ? $params['start_city'] : '';
$end_city = isset($params['end_city']) ? $params['end_city'] : '';
$pass_city = isset($params['pass_city']) ? $params['pass_city'] : '';
$road = isset($params['road']) ? $params['road'] : '';
$remark = isset($params['remark']) ? $params['remark'] : '';

$open_id = isset($params['open_id']) ? $params['open_id'] : '';//用户openid
$nick_name = isset($params['nick_name']) ? $params['nick_name'] : '';//用户昵称

/*$now = date("Y-m-d H:i:s", time());
if($now >= '2015-02-14 00:00:00'){
    $ret = array ('code'=>2,'message'=>'对不起,活动已结束');
    echo json_encode($ret);return;
}*/

if(empty($phone) || empty($start_time_begin) || empty($start_time_end) || empty($start_city) || empty($end_city)){
	$ret = array ('code'=>2,'message'=>'参数错误');
	echo json_encode($ret);return;
}
/*$customer = FestivalCustomer::model()->getCustomerByPhone($phone);
if($customer){//未报名
    $ret = array ('code'=>2,'message'=>'不能重复报名');
    echo json_encode($ret);return;
}*/
$customer = new FestivalCustomer();
$customer->phone = $phone;
$customer->start_time_begin = $start_time_begin;
$customer->start_time_end = $start_time_end;
$customer->start_city = $start_city;
$customer->end_city = $end_city;
$customer->pass_city = $pass_city;
$customer->road = $road;
$customer->remark = $remark;
$customer->open_id = $open_id;
$customer->nick_name = $nick_name;
$customer->create_time = date("Y-m-d H:i:s", time());

if(!$customer->save()){
    $ret = array ('code'=>2,'message'=>'报名失败');
    echo json_encode($ret);return;
}
/*$content = '尊敬的客户您好:您已成功报名活动,我们会给您匹配司机,如果您要去的城市同路司机较少,则可能会花较长时间匹配,最晚匹配结果将于2月10日左右发送给您';
$ret_sms = Sms::SendSMS($phone, $content, Sms::CHANNEL_ZCYZ);
if (empty($ret_sms)) {
    $ret = array('code' => 2, 'message' => '系统延迟，请稍后再试。');
    echo json_encode($ret);
    return;
}*/
$params = array('phone'=>$phone);
$task = array(
    'method' => 'festival_send_message_to_customer',
    'params' => $params
);
Queue::model()->putin($task, 'task');

$data = array();
$data['start_city'] = $start_city;
$data['end_city'] = $end_city;

$ret = array ('code'=>0,'data'=>$data,'message'=>'报名成功');
echo json_encode($ret);return;

