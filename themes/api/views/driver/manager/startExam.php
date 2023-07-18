<?php
/**
 * Created by PhpStorm.
 * User: aiguoxin
 * Date: 15/3/27
 * Time: 下午5:22
 */

$token = empty($params['token']) ? '' : trim($params['token']);
$idCard = empty($params['idCard']) ? '' : trim($params['idCard']);

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

if(empty($idCard)){
    $ret['message'] = '司机身份证号不能为空';
    echo JsonResponse::fail($ret);
    return;
}

//根据身份证号获取验证信息
$recruitmentInfo = DriverRecruitment::model()->getDriverByIDCard($idCard);
if (empty($recruitmentInfo)) {
    $ret['message'] = '该司机未报名，请先在线报名';
    echo JsonResponse::fail($ret);
    return;
}

$ret = DriverExamnewOnline::model()->startExam($recruitmentInfo['id']);

echo JsonResponse::success($ret);
return;

