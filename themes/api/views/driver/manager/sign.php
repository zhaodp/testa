<?php
/**
 * Created by PhpStorm.
 * User: aiguoxin
 * Date: 15/3/30
 * Time: 下午5:53
 */
$token = empty($params['token']) ? '' : trim($params['token']);
$driverName = empty($params['driverName']) ? '' : trim($params['driverName']);
$driverId = empty($params['driverId']) ? '' : trim($params['driverId']);
$driverIdCard = empty($params['driverIdCard']) ? '' : trim($params['driverIdCard']);
$driverNum = empty($params['driverNum']) ? '' : trim($params['driverNum']);
$applyTime = empty($params['applyTime']) ? '' : trim($params['applyTime']);
$type = empty($params['type']) ? 0 : trim($params['type']); //默认0，A1类型，不能默认-1
$vNum = empty($params['vNum']) ? '' : trim($params['vNum']);
$driverPhone = empty($params['driverPhone']) ? '' : trim($params['driverPhone']);

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

//判断司机姓名
if(empty($driverName)){
    $ret['message'] = '司机姓名不能为空';
    echo JsonResponse::fail($ret);
    return;
}

//司机工号
if(empty($driverId)){
    $ret['message'] = '司机工号不能为空';
    echo JsonResponse::fail($ret);
    return;
}


//司机档案编号
if(empty($driverNum)){
    $ret['message'] = '司机档案编号不能为空';
    echo JsonResponse::fail($ret);
    return;
}

//驾照申领时间
if(empty($applyTime)){
    $ret['message'] = '司机驾照申领时间不能为空';
    echo JsonResponse::fail($ret);
    return;
}

//驾照类型
if($type == -1){
    $ret['message'] = '请选择司机驾照类型';
    echo JsonResponse::fail($ret);
    return;
}

////司机V号
//if(empty($vNum)){
//    $ret['message'] = 'V号不能为空';
//    echo JsonResponse::fail($ret);
//    return;
//}

//司机手机号
if(empty($driverPhone)){
    $ret['message'] = '手机号不能为空';
    echo JsonResponse::fail($ret);
    return;
}

$recruitmentInfo = DriverRecruitment::model()->getDriverByIDCard($driverIdCard);
if(empty($recruitmentInfo)){
    $ret['message'] = '请先报名';
    echo JsonResponse::fail($ret);
    return;
}

////路考通过的才能签约
//if($recruitmentInfo['status'] != DriverRecruitment::STATUS_ROAD_PASS){
//    $ret['message'] = '只有处于路考通过状态才能签约，当前状态:'.DriverRecruitment::$status_dict[$recruitmentInfo['status']];
//    echo JsonResponse::fail($ret);
//    return;
//}

$id = $recruitmentInfo['id'];
//修改司机信息
DriverRecruitment::model()->updateByPk($recruitmentInfo['id'],
    array(
        'name'=>$driverName,
        'id_driver_card'=>$driverNum,
        'driver_year'=>strtotime($applyTime),
        'driver_type'=>$type,
    )
);
//司机签约
$r_data = Driver::model()->driverEntry($id, $driverId, $vNum, $driverPhone, 8);
if(empty($r_data)){
    $ret['message'] = '签约失败';
    echo JsonResponse::fail($ret);
    return;
}
$status = $r_data['status'];
if($status){//成功签约
    $ret['message'] = '签约成功';
    echo JsonResponse::success($ret);
    return;
}else{
    $ret['message'] = $r_data['data'];
    echo JsonResponse::fail($ret);
    return;
}