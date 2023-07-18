<?php
/**
 * 三周年客户回馈活动接口
 * @author cuiluzhe 2014-12-12
 * @see
 * @since
 */
$token = isset($params['token'])?$params['token']:'';
/*if(empty($token)){
    $ret = array('code'=>1, 'message'=>'获取token失败');echo json_encode($ret);return;
}
$validate = CustomerToken::model()->validateToken($token);
if(!$validate){
    $ret = array('code'=>1, 'message'=>'token无效');echo json_encode($ret);return;
}
$phone = $validate['phone'];*/

if(empty($token)){
    $ret = array('code'=>1, 'message'=>'获取token失败');echo json_encode($ret);return;
}
$current_time = date("Y-m-d H:i:s", time());
if($current_time >= '2015-01-04 00:00:00'){
    $ret = array('code'=>1, 'message'=>'对不起，活动已结束。');echo json_encode($ret);return;
}

/*if($token == 'a2a3a4'){
    $ret = array('code'=>2, 'message'=>'该用户不是A1类用户');echo json_encode($ret);return;
}*/
$phone = base64_decode($token);//将base64位编码转换为手机号

$redisRet = RPay::model()->lockUserFeedBack($phone, $token);
if(is_array($redisRet)){
    echo json_encode($redisRet);
    return;
}

$customerLevel = CustomerLevel::model()->find('phone=:phone', array('phone'=>$phone));
if(!$customerLevel){
    $ret = array('code'=>1, 'message'=>'系统不存在此用户');echo json_encode($ret);return;
}
if($customerLevel['level'] != 1){
    $ret = array('code'=>2, 'message'=>'该用户不是A1类用户');echo json_encode($ret);return;
}
if($customerLevel['given'] == 1){
    $ret = array('code'=>1, 'message'=>'已赠送过99元代驾费，感谢您的支持');echo json_encode($ret);return;
}
$phone_param = array('phone'=>$phone);
$customer_info = BCustomers::model()->getCustomerInfo($phone_param);
if(!$customer_info || $customer_info['code'] == 1){
    $ret = array('code'=>1, 'message'=>'用户不存在');echo json_encode($ret);return;
}
$customer = $customer_info['data'];//用户信息
$user_account = $customer_info['user_account'];//用户账户
$amount = 99;//充值金额
$param = array(
    'user_id'=>$customer['id'],
    'city_id'=>$customer['city_id'],
    'type'   =>$customer['type'],
    'amount' =>$amount,
    'vip_card'=>$customer['vip_card'],
);
$ret = BCustomers::model()->addAccount($param);
if($ret['code'] == 1){
    $msg = '三周年活动为'.$phone.'充值失败';
    EdjLog::info($msg);
    Mail::sendMail(array('cuiluzhe@edaijia-inc.cn'), $msg, '三周年活动充值失败');
    RPay::model()->delUserFeedBack($phone, $token);
    $ret = array('code'=>1, 'message'=>'充值失败');echo json_encode($ret);return;
}
RPay::model()->incrUserFeedBack($phone, $token);
//充值成功记录交易流水
if(!empty($user_account) && isset($user_account['amount'])){
    $balance = $user_account['amount'] + $amount;
}else{
    $balance = $amount;
}

$trans_param = array(
    'user_id'=>$customer['id'],
    'trans_order_id'=>0,
    'trans_card'=>0,
    'trans_type'=>CarCustomerTrans::TRANS_TYPE_RE,//充值奖励
    'amount'=>$amount,
    'balance'=>$balance,
    'source'=>CarCustomerTrans::TRANS_SOURCE_RE,//系统奖励
    'operator'=>'系统',
    'create_time'=>date("Y-m-d H:i:s", time()),
    'remark'=>'三周年活动奖励',
);
$ret = BCustomers::model()->addCustomerTrade($trans_param);
if($ret['code'] == 1){
    $msg = '三周年活动为'.$phone.'添加用户交易流水失败';
    EdjLog::info($msg);
    Mail::sendMail(array('cuiluzhe@edaijia-inc.cn'), $msg, '三周年活动记录交易流水失败');
}
//更新用户状态为已赠送
$customerLevel->given = 1;
$customerLevel->given_time = time();
$ret = $customerLevel->save();
if(!$ret){
    $msg = '三周年活动更新'.$phone.'用户赠送状态失败';
    EdjLog::info($msg);
    Mail::sendMail(array('cuiluzhe@edaijia-inc.cn'), $msg, '三周年活动更新用户赠送状态失败');
}
$ret = array('code'=>0, 'message'=>'充值成功');echo json_encode($ret);return;
