<?php
/**
 * 未读公告条数
 * User: zhanglimin
 * Date: 13-12-17
 * Time: 上午11:59
 */
$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";
//add by aiguoxin type=0未读公告,type=1投诉
$type=isset($params['type'])&&!empty($params['type']) ? trim($params['type']) : "0";

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

$count=0;
if($type == '0'){
    $notice = array(
        'driver_id' => $driver->driver_id,
        'city_id' => $driver->city_id,
    );
    $count = NewNotice::model()->getDriverUnreadcount($notice);
}elseif ($type == '1') {
    //add by aiguoxin
    $count = CustomerComplain::model()->getUnreadComplainCount($driver->driver_id);         
}



$ret=array(
    'code'=>0,
    'message'=>'成功',
    'count'=>$count,
);
echo json_encode($ret);
return;