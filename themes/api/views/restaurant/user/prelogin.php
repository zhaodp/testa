<?php
/**
 * 获取验证吗
 * User: zhanglimin
 * Date: 13-8-13
 * Time: 下午1:25
 */

$phone=isset($params['phone'])&&!empty($params['phone']) ? trim($params['phone']) : "";

if(empty($phone)){
    $ret = array ('code'=>2,'message'=>'手机号不能为空');
    echo json_encode($ret);return;
}

$user = RestaurantToken::model()->checkPhone($phone);
if(empty($user)){
    $ret = array ('code'=>2,'message'=>'请检查手机号是否正确');
    echo json_encode($ret);return;
}
if($user['status'] == 0 ){
    $ret = array ('code'=>2,'message'=>'当前号码己被禁用');
    echo json_encode($ret);return;
}
$passCode = UserToken::model()->getSmsPassCode($user['phone']);

if(empty($passCode)){
    $ret = array ('code'=>2,'message'=>'验证码获取失败');
    echo json_encode($ret);return;
}

$message = '您的预登录验证码为：%s，预登录验证码将在十分钟后失效。';
$content = sprintf($message, $passCode);

$ret_sms = Sms::SendSMS($phone, $content,Sms::CHANNEL_SOAP);
if( empty($ret_sms) ){
    $ret = array (
        'code'=>1,
        'message'=>'系统延迟，请稍后再试。');
    echo json_encode($ret);return;
}

$ret = array (
    'code'=>0,
    'message'=>'验证码已成功发送。');
echo json_encode($ret);return;