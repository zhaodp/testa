<?php
/**
 * 司机报名
 */
$driver = isset($params['driver']) ? $params['driver'] : '';
$start_time_begin = isset($params['start_time_begin']) ? $params['start_time_begin'] : '';
$start_time_end = isset($params['start_time_end']) ? $params['start_time_end'] : '';
$start_city = isset($params['start_city']) ? $params['start_city'] : '';
$end_city = isset($params['end_city']) ? $params['end_city'] : '';
$pass_city = isset($params['pass_city']) ? $params['pass_city'] : '';
$remark = isset($params['remark']) ? $params['remark'] : '';

$now = date("Y-m-d H:i:s", time());
if($now >= '2015-02-14 00:00:00'){
    $ret = array ('code'=>2,'message'=>'对不起,活动已结束');
    echo json_encode($ret);return;
}

if(empty($driver) || empty($start_time_begin) || empty($start_time_end) || empty($start_city) || empty($end_city)){
	$ret = array ('code'=>2,'message'=>'参数错误');
	echo json_encode($ret);return;
}
$fd = new FestivalDriver();
$fd->driver = $driver;
$fd->start_time_begin = $start_time_begin;
$fd->start_time_end = $start_time_end;
$fd->start_city = $start_city;
$fd->end_city = $end_city;
$fd->pass_city = $pass_city;
$fd->remark = $remark;
$fd->create_time = date("Y-m-d H:i:s", time());

if(!$fd->save()){
    $ret = array ('code'=>2,'message'=>'报名失败');
    echo json_encode($ret);return;
}

$params = array('driver'=>$driver);
$task = array(
    'method' => 'festival_send_message_to_driver',
    'params' => $params
);
Queue::model()->putin($task, 'task');

/*$content = '尊敬的师傅您好:您已成功报名活动,我们会给您匹配客户,如果您要去的城市同路客户较少,则可能会花较长时间匹配,最晚匹配结果将于2月10日左右发送给您';
$driver_number = $params['driver'];
$driver_model = Driver::model()->find('user=:user', array(':user'=>$driver_number));
if(!$driver_model){
    EdjLog::info('工号为'.$driver_number.'的司机不存在');
    return false;
}
$ret_sms = Sms::SendSMS($driver_model['phone'], $content, Sms::CHANNEL_ZCYZ);
if (empty($ret_sms)) {
    EdjLog::info('festival_send_message_to_driver:'.$driver_model['phone'].' falied');
    return false;
}
EdjLog::info('festival_send_message_to_driver:'.$driver_model['phone'].' successful');*/

$ret = array ('code'=>0,'message'=>'报名成功');
echo json_encode($ret);return;

