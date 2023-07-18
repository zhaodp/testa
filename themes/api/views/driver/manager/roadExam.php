<?php
/**
 * Created by PhpStorm.
 * User: aiguoxin
 * Date: 15/3/27
 * Time: 下午6:00
 */

$idCard = empty($params['idCard']) ? '' : trim($params['idCard']);
$autoRoadExam = empty($params['autoRoadExam']) ? 0 : trim($params['autoRoadExam']);
$manualRoadExam = empty($params['manualRoadExam']) ? 0 : trim($params['manualRoadExam']);
$pass = empty($params['pass']) ? 0 : trim($params['pass']);
$reason = empty($params['reason']) ? '' : trim($params['reason']);
$token = empty($params['token']) ? '' : trim($params['token']);

if (empty($token)) {
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

$recruitmentInfo = DriverRecruitment::model()->getDriverByIDCard($idCard);
if(empty($recruitmentInfo)){
    $ret['message'] = '身份证号码无效，请重新输入';
    echo JsonResponse::fail($ret);
    return;
}
//打路考成绩
$params=array(
    'idCard'=>$idCard,
    'cityId'=>$recruitmentInfo['city_id'],
    'serialNum'=>$recruitmentInfo['id'],
    'pass'=>$pass,
    'autoRoadExam'=>$autoRoadExam,
    'manualRoadExam'=>$manualRoadExam,
    'operator'=>$driverManagerId,
    'reason'=>$reason
);
$res = DriverRoadExam::model()->addRoadInfo($params);

if($res){
    $ret=array(
        'message'=>'操作成功',
    );
    echo JsonResponse::success($ret);
    return;
}else{
    $ret=array(
        'message'=>'操作失败',
    );    echo JsonResponse::fail($ret);
    return;
}

