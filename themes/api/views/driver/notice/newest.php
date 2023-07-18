<?php
/*
 * modify  zhanglimin 2013-06-08
 * 切换验证token方式
 */
$driver = DriverStatus::model()->getByToken($params['token']);
if (empty($driver) ||  $driver->token===null||$driver->token!==$params['token']) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}

$notice['city_id'] = $driver->city_id;
$notice['driver_id'] = $driver->driver_id;
$newest_id = Notice::model()->getNewest($notice);
if(empty($newest_id)){
    $ret = array (
        'code'=>2,
        'message'=>'无最新公告');
    echo json_encode($ret);
    return;
}

$notice = Notice::model()->getNoticeByClient($newest_id);

$ret = array (
    'code'=>0,
    'notice'=>$notice,
    'message'=>'读取成功');
echo json_encode($ret);
return;