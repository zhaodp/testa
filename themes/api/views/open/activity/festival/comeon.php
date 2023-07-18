<?php
/**
 * 点击加油
 */
$friend_phone = isset($params['friend_phone']) ? trim($params['friend_phone']) : '';
$phone = isset($params['phone']) ? trim($params['phone']) : '';
if(empty($friend_phone) || empty($phone)){
	$ret = array ('code'=>2,'message'=>'参数错误');
	echo json_encode($ret);return;
}
//加锁
$lock_ret = RFestival::model()->setLock($phone,$friend_phone);
if(!$lock_ret){
    $ret = array ('code'=>2,'message'=>'您只能为同一位朋友加一次油');
    echo json_encode($ret);return;
}
//获取加油记录
$come_ret = RFestival::model()->existsComeonRecord($phone,$friend_phone);
if($come_ret){
    $ret = array ('code'=>2,'message'=>'您只能为同一位朋友加一次油哦');
    echo json_encode($ret);return;
}
//保存加油记录到缓存,返回加油最新次数
$comeon_num = RFestival::model()->comeon($phone,$friend_phone);

$params = array('friend_phone'=>$friend_phone, 'phone'=>$phone, 'comeon_num'=>$comeon_num);
$task = array(
    'method' => 'update_festival_data',
    'params' => $params
);
Queue::model()->putin($task, 'task');
/*$comeon = new FestivalComeon();
$comeon->phone = $params['phone'];
$comeon->friend_phone = $params['friend_phone'];
$comeon->create_time = date('Y-m-d H:i:s',time());
$ret = $comeon->save();
$count = FestivalCustomer::model()->updateCounters(array('come_on_times'=>1),'phone=:phone',array(':phone'=>$params['phone']));
if($ret && $count>0){
    EdjLog::info('festival:'.$params['friend_phone'].' come on for '.$params['phone'].' successful');
}
EdjLog::info('festival:'.$params['friend_phone'].' come on for '.$params['phone'].' falied');*/

$ret = array ('code'=>0, 'comeon_num'=>$comeon_num ,'message'=>'加油成功');
echo json_encode($ret);return;