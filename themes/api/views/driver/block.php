<?php
/**
 * 固定时间拉取司机当前状态
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-3-25
 * Time: 下午2:59
 * auther mengtianxue
 */

$token = isset($params['token']) && !empty($params['token']) ? trim($params['token']) : "";

$driver = DriverStatus::model()->getByToken($token);

if ($driver === null || $driver->token === null || $driver->token !== $token) {
    EdjLog::info('driver is '.serialize($driver));
    $ret = array(
        'code' => 1,
        'message' => '请重新登录'
    );
    echo json_encode($ret);
    return;
}
$driver_id = $driver->driver_id;
$driver_block = DriverStatus::model()->get($driver_id);
//如果缓存没有，直接返回0，为了兼容之前的代码
$block_at = $driver_block->block_at;
$block_mt = $driver_block->block_mt;
$at = empty($block_at) ? 0 : intval($block_at);
$mt = empty($block_mt) ? 0 : intval($block_mt);

//at or mt has one equals 1, mark set 1
//add by aiguoxin
if($at == 1 || $mt == 1){
    $driver_block->mark=1;
}
if($at == 0 && $mt == 0){
    $driver_block->mark=0;
}
//add by aiguoxin

$ret = array(
    'code' => 0,
    'message' => '获取成功',
    'data' => array(
        'mark' => $driver_block->mark,
        'block_at' => $at, //金额屏蔽
        'block_mt' => $mt, //手动屏蔽

    )
);
echo json_encode($ret);
return;


