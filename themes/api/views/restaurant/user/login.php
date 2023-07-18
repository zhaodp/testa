<?php
/**
 * 登录接口
 * User: zhanglimin
 * Date: 13-8-13
 * Time: 下午2:57
 */

$phone=isset($params['phone'])&&!empty($params['phone']) ? trim($params['phone']) : "";

$passcode=isset($params['passcode'])&&!empty($params['passcode']) ? trim($params['passcode']) : "";

if(empty($phone) || empty($passcode)){
    $ret = array ('code'=>2,'message'=>'参数不正确');
    echo json_encode($ret);return;
}

$getCode = UserToken::model()->getSmsPassCode($phone,false);

if(empty($getCode)){
    $ret = array ('code'=>2,'message'=>'请先获取验证码');
    echo json_encode($ret);return;
}

if($passcode != $getCode){
    $ret = array (
        'code'=>2,
        'message'=>'验证码输入不正确');
    echo json_encode($ret);return;
}

$token = UserToken::model()->getToken($phone);


$tokenParams = array(
    'phone'		=>	$phone,
    'authtoken'	=>	$token);

$flag = QueueProcess::model()->restaurant_login($tokenParams);

if(!$flag['flag']){
    //返回数据
    $ret = array (
        'code'=>0,
        'token'=>$token,
        'message'=>'登录失败,请重试');
}else{

    UserToken::model()->deletePassCodeCache($phone);

    //返回数据
        $ret = array (
            'code'=>0,
            'token'=>$token,
            'name'=>$flag['name'],
            'message'=>'登录成功');

}

echo json_encode($ret);return;