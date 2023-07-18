<?php

/*
 * modify  zhanglimin 2013-06-08
 * 切换验证token方式 操作走队列
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

//添加task队列
$task=array(
    'method'=>'push_driver_notice_read',
    'params'=>array(
        'notice_id'=>$params['notice_id'],
        'driver_id'=>$driver->driver_id,
    )
);

Queue::model()->task($task);

$ret=array(
    'code'=>0,
    'message'=>'成功!'
);

echo json_encode($ret);
return;