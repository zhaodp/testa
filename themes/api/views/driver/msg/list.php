<?php
/**
 * 获取公告列表 包含 己读 未读
 * User: zhanglimin
 * Date: 13-8-28
 * Time: 下午4:15
 */
$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";
$pageSize=isset($params['pageSize'])&&!empty($params['pageSize']) ? intval(trim($params['pageSize'])) : 10;
$pageNo=isset($params['pageNo'])&&!empty($params['pageNo']) ? intval(trim($params['pageNo'])) : 1;

if ( empty($token) ) {
    $ret=array(
        'code'=>2,
        'message'=>'参数不正确'
    );
    echo json_encode($ret);
    return;
}

$driver = DriverStatus::model()->getByToken($token);
if ( empty($driver) ||  $driver->token===null || $driver->token!==$token ) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}

$notice = array(
    'driver_id' => $driver->driver_id,
    'city_id' => $driver->city_id,
    'pageNo' => $pageNo < 1 ? 1 : $pageNo,
    'pageSize' => $pageSize,
);
$noticeList = NewNotice::model()->getList($notice);

$ret = array (
    'code'=>0,
    'list'=>$noticeList,
    'message'=>'读取成功');
echo json_encode($ret);
return;