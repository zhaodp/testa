<?php
/**
 * Created by PhpStorm.
 * User: aiguoxin
 * Date: 15/3/31
 * Time: 下午12:01
 */
$token = empty($params['token']) ? '' : trim($params['token']);
$idCard = empty($params['idCard']) ? '' : trim($params['idCard']);

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

$driverManager = DriverStatus::model()->get($driverManagerId);
if(empty($driverManager)){
    $ret['message'] = JsonResponse::validMsgFail;
    echo JsonResponse::fail($ret);
    return;
}

//判断司机是否已经签约
$driver = Driver::model()->getDriverByIdCard($idCard);
if($driver){
    $driverId = $driver['user'];
}else{
    //设置缓存10小时，和v2后台保持一致
    $mem_key = "TMP_DRIVER_ID_".$idCard;
    $driverId = DriverStatus::model()->single_get($mem_key);
    if(empty($driverId)){
        $cityId = $driverManager->city_id;
        $address = new DriverIdPool();
        $driverId = $address->getDriverIdToEntry($cityId);
        //设置到缓存
        DriverStatus::model()->single_set($mem_key,$driverId,36000);
    }
}

$ret['driverId']=$driverId;
echo JsonResponse::success($ret);
return;


