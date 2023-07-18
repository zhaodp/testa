<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-2-20
 * Time: 下午5:24
 * auther mengtianxue
 */

$channel = isset($params['channel']) ? $params['channel'] : 25;
$min_id = isset($params['min_id']) ? $params['min_id'] : 0;
$count = isset($params['count']) ? $params['count'] : 10;
$datetime = isset($params['datetime']) ? $params['datetime'] : date('Ym');

$driver = DriverStatus::model()->getByToken($params['token']);
if (empty($driver) || $driver->token === null || $driver->token !== $params['token']) {
    $ret = array(
        'code' => 1,
        'message' => '请重新登录'
    );
    echo json_encode($ret);
    return;
}

$params_arr = array();
$params_arr['driver_id'] = $driver->driver_id;
$params_arr['channel'] = $channel;
$params_arr['min_id'] = $min_id;
$params_arr['count'] = $count;
$params_arr['datetime'] = $datetime;
$historyList = EmployeeAccount::model()->getIncomeListByChannel($params_arr);

$ret = array(
    'code' => 0,
    'message' => '读取成功',
    'datetime' => $historyList['datetime'],
    'list' => $historyList['orderList']
);
echo json_encode($ret);
return;
