<?php
/**
 * 初始化活动页
 * clz
 * 2015-01-26
 */
$open_id = isset($params['open_id']) ? trim($params['open_id']) : '';
$phone = isset($params['phone']) ? trim($params['phone']) : '';
$act_name = isset($params['act_name']) ? $params['act_name'] : '';
$time = date('Y-m-d H:i:s',time());
//$isOpen =  RActivity::model()->getOpenLock($act_name);
$activityConfig = ActivityConfig::model()->getInstance($act_name);
$end_time = $activityConfig['end_time'];
if($act_name == 'huarun_39'){
    $end_time = '2015-03-14 00:00:00';
}else if($act_name == 'baidu_map'){
    $end_time = '2015-10-01 00:00:00';
}
//if(!$isOpen){//不存在
if($time>=$end_time || empty($act_name)){
    $data = array('type'=> RActivity::ACT_EXPIRED );
    $ret = array(
        'code' => 0,
        'data' => $data,
        'message' => '活动已结束');
    echo json_encode($ret);
    return;
}


if (empty($act_name)) {
    $ret = array('code' => 2, 'message' => '参数错误');
    echo json_encode($ret);
    return;
}
if(empty($open_id) && empty($phone)){//返回剩余数目
     $left_num = RActivity::model()->getLeftNum($act_name);
     $data = array('type'=> RActivity::ACT_SUCCESS, 'left_num' => $left_num);
     $ret = array(
         'code' => 0,
         'data' => $data,
         'message' => '获取成功');
    echo json_encode($ret);
    return;
}
//判断return;   是否领取
$bind_ret1 = true;
$bind_ret2 = false;
if(!empty($open_id)){
     $bind_ret = RActivity::model()->existsBind($act_name, $open_id, 'open_id');
     $bind_ret1 = $bind_ret;
}else if(!empty($phone)){
     $bind_ret = RActivity::model()->existsBind($act_name, $phone, 'phone');
     $bind_ret2 = $bind_ret;
}
//针对用户换了微信号,这个时候 open_id  为新的, 但是 phone 为微信缓存的, 让客户可以继续绑

if($bind_ret2 && !$bind_ret1){
    $phone = 0;
    $bind_ret = false;
}
if ($bind_ret) {
    if(empty($phone)){
         $phone = RActivity::model()->getBindPhone($act_name, $open_id);
    }
    $data = array('type'=>RActivity::ACT_BINDING, 'phone'=>$phone);
    $ret = array(
        'code' => 0,
        'data' => $data,
        'message' => '您只能领取一次优惠劵哦');
    echo json_encode($ret);
    return;
}
$left_num = RActivity::model()->getLeftNum($act_name);
$data = array('type'=>RActivity::ACT_SUCCESS, 'left_num'=>$left_num);
$ret = array(
    'code' => 0,
    'data' => $data,
    'message' => '获取成功');
echo json_encode($ret);
return;