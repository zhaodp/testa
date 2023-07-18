<?php
/**
 * 实物卡与订单绑定返现
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-12-20
 * Time: 上午11:27
 * auther mengtianxue
 */
$driver = DriverStatus::model()->getByToken($params['token']);
if (empty($driver) || $driver->token === null || $driver->token !== $params['token']) {
    $ret = array(
        'code' => 1,
        'message' => '请重新登录'
    );
    echo json_encode($ret);
    return;
}

if (empty($params['order_id']) || empty($params['bonus_sn'])) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}

$order_id = trim($params['order_id']);
$bonus_sn = trim($params['bonus_sn']);

//调取返回报错信息
$bonus = OrderSettlement::model()->couponUsed($order_id, $bonus_sn);
echo json_encode($bonus);
return;
