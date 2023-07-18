<?php
/**
 * Created by PhpStorm.
 * User: aiguoxin
 * Date: 15/3/24
 * Time: 下午2:08
 */
$token = empty($params['token']) ? '' : trim($params['token']);

if(empty($token)){
    $ret['message'] = JsonResponse::validMsgFail;
    $ret['code'] = JsonResponse::EXPIRE_CODE;
    echo JsonResponse::fail($ret);
    return;
}

//验证是否登录
$driverManagerId = DriverStatus::model()->getDriverManagerToken($token);
if (empty($driverManagerId)) {
    $ret['message'] = JsonResponse::validMsgFail;
    $ret['code'] = JsonResponse::EXPIRE_CODE;
    echo JsonResponse::fail($ret);
    return;
}

//退出
DriverManagerToken::model()->logout($token);
$ret = array(
    'message'=>'退出成功',
);

echo JsonResponse::success($ret);
return;