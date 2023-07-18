<?php
/**
 * Created by PhpStorm.
 * User: aiguoxin
 * Date: 15/3/24
 * Time: 上午10:38
 */
$driverId = empty($params['driverId']) ? '' : strtoupper(trim($params['driverId']));
$password = empty($params['password']) ? '' : trim($params['password']);

$errorMsg='抱歉，你未被授权使用此软件。如需使用请联系所在城市e代驾分公司';

$driver = Driver::getProfile($driverId);

//用户不存在、离职、密码不正确、不是司管，都不能登陆
if(empty($driver)){
    $ret['message'] = '工号不存在';
    echo JsonResponse::fail($ret);
    return;
}

if($driver->driver_manager != Driver::IS_DRIVER_MANAGER) {
    $ret['message'] = $errorMsg;
    echo JsonResponse::fail($ret);
    return;
}

if($password != md5($driver->password)){
    $ret['message'] = '密码不正确';
    echo JsonResponse::fail($ret);
    return;
}

//删除其他登陆的token,保证单点登陆,不能多设备同时登陆
DriverManagerToken::model()->delTokenByDriverId($driverId);

//生成token
$token = DriverToken::model()->createManagerToken($driverId);
$picture = $driver->picture;
if(empty($picture)){
    $picture = 'http://pic.edaijia.cn/0/default_driver.jpg_small?ver=1428462348';
}

$ret = array(
    'driverId'=>$driverId,
    'headUrl'=>$picture,
    'name'=>$driver->name,
    'token'=>$token,
);

echo JsonResponse::success($ret);
return;