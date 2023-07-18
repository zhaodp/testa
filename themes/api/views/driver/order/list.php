<?php
//需要写清楚注释，增加缓存 add by sunhongjing at 2013-5-19
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
    $order = array();
    $order['driver_id'] = $driver->driver_id;
    $order['status'] = $params['status'];
    $order['pageSize'] = $params['pageSize'];
    $order['offset'] = $params['pageSize'] * ($params['pageNo'] - 1);
    $orderList = Order::model()->getDriverOrderListByType($order);

    // cash_only属性保存在订单扩展表里——曾坤 2015/3/25 
    foreach ($orderList as $k=>$v) {
        $orderext = OrderExt::model()->getPrimary($v['order_id']); 
        if (!empty($orderext)) {
            $orderList[$k]['cash_only'] = $orderext['cash_only'];
        }
    }

    $ret = array (
        'code'=>0,
        'list'=>$orderList,
        'message'=>'读取成功'
    );

    echo json_encode($ret);
    return;
