<?php
/**
 * 百度地图启动页活动
 * clz
 * 2015-03-12
 */
$open_id = isset($params['open_id']) ? trim($params['open_id']) : '';
$phone = isset($params['phone']) ? trim($params['phone']) : '';
$verify_code = isset($params['verify_code']) ? trim($params['verify_code']) : '';
$act_name = isset($params['act_name']) ? $params['act_name'] : '';
if(empty($phone) || empty($verify_code) || empty($act_name)){
    $ret = array ('code'=>2,'message'=>'参数错误');
    echo json_encode($ret);return;
}
$time = date('Y-m-d H:i:s',time());
//$isOpen =  RActivity::model()->getOpenLock($act_name);
if($act_name == 'baidu_map'){
    $end_time = '2015-10-01 00:00:00';
}
//if(!$isOpen){//不存在
if($time>=$end_time){
    $data = array('type'=> RActivity::ACT_EXPIRED );
    $ret = array(
        'code' => 0,
        'data' => $data,
        'message' => '活动已结束');
    echo json_encode($ret);
    return;
}
$rcode = RActivity::model()->getCode($act_name, $phone);
if(!$rcode){
    $ret = array ('code'=>2,'message'=>'验证码已失效,请重新获取');
    echo json_encode($ret);return;
}
if($verify_code != $rcode){
    $ret = array ('code'=>2,'message'=>'验证码错误,请重新输入');
    echo json_encode($ret);return;
}
//判断是否vip
//$is_vip = VipPhone::model()->find('phone=:phone', array(':phone'=>$phone));
$is_vip = CustomerMain::model()->isVip($phone);
if($is_vip){
    $ret = array ('code'=>2,'message'=>'vip客户不参与此次活动,请换个手机号');
    echo json_encode($ret);return;
}
//这里好像应该先判断是否领取过

$lock_ret = RActivity::model()->setLock($act_name,$phone);
if(!$lock_ret){
    if(!empty($open_id)){//解决浏览器领取->微信领取->再次在微信打开判断是否领取问题
        RActivity::model()->setBindPhone($act_name,'open_id',$open_id,$phone);
    }
    $ret = array ('code'=>4,'message'=>'您只能领取一次哦');
    echo json_encode($ret);return;
}
$left_num = RActivity::model()->getFromCache($act_name);
if($left_num < 0){
    $ret = array ('code'=>3,'message'=>'您来晚了，优惠劵已经被抢完');
    echo json_encode($ret);return;
}
//绑定优惠劵,记录日志
$params = array('open_id'=>$open_id,'phone'=>$phone,'act_name'=>$act_name);
$task = array(
    'method' => 'baidu_map_binding_bonus',
    'params' => $params
);
EdjLog::info('put in queue ---- '.json_encode($task));
Queue::model()->putin($task, 'activity');
//将优惠劵绑定数据放入缓存
if(!empty($open_id)){
    RActivity::model()->setBindPhone($act_name,'open_id',$open_id,$phone);
}
RActivity::model()->setBindPhone($act_name,'phone',$open_id,$phone);
$ret = array(
    'code'=>0,
    'phone'=>$phone,
    'message'=>'获取成功');
echo json_encode($ret);
return;