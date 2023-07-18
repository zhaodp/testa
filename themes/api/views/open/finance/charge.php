<?php
/**
 * 一口价洗车许磊那边传司机和充值信息，这边负责给对应的司机进行充值或是扣款
 * User: jack
 * Date: 2015/4/3
 * Time: 14:09
 */
//验证参数
$driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';
$is_charge = isset($params['is_charge']) ? $params['is_charge'] : 0;//是否充值 1：充值 2：扣费
$comment = isset($params['comment']) ? $params['comment'] : 0;
$amount = isset($params['amount']) ? $params['amount'] : 0;
$order_id = isset($params['order_id']) ? $params['order_id'] : 0;
EdjLog::info("--financePay一口价洗车司机余额操作参数---- " .json_encode($params));
if (empty($driver_id) || empty($amount)) {
    $ret = array('code' => 2, 'message' => '参数有误');
    echo json_encode($ret);return ;
}
//客户端传过来的是order_num,转换为争取的order_id
if (strlen($order_id) > 11 && is_numeric($order_id)) {
    //从redies中获取数据库中的order_id
    $redis_order_id = ROrder::model()->getOrder($order_id , 'order_id');
    if (empty($redis_order_id)) {
        //redeis不存在则去数据库查找
        $order = Order::model()->getOrderByOrderNumberOnly($order_id);
        $db_order_id = $order->order_id;
        if(empty($db_order_id)){
            $ret = array('code' => 2 ,'message' => '参数有误(不存在该orderId)');
            echo json_encode($ret);return ;
        }else{
            $order_id = $db_order_id;
        }
    }else{
        $order_id = $redis_order_id;
    }
}
$driver_info = Driver::model()->getDriverInfoByDriverId($driver_id);
if(empty($driver_info)){
    $ret = array('code' => 1, 'message' => '司机工号不存在');
    echo json_encode($ret);return ;
}
$cityId = $driver_info['city_id'];
$channel =  EmployeeAccount::CHANNEL_ORDER_UNIT;//一口价业务

$employeeAccountAttributes = array();
$employeeAccountAttributes['type'] = ($amount > 0) ? EmployeeAccount::TYPE_INFOMATION : EmployeeAccount::TYPE_FORFEIT;//根据传值正负判断是充值还是扣款
$employeeAccountAttributes['channel'] = $channel;
$employeeAccountAttributes['city_id'] =  $cityId;
$employeeAccountAttributes['user'] = $driver_id;
$employeeAccountAttributes['order_id'] = $order_id;
$employeeAccountAttributes['order_date'] = time();
$employeeAccountAttributes['cast'] = abs($amount);
$employeeAccountAttributes['comment'] = $comment;

$driverPriceRet = OrderSettlement::model()->insertAccount($employeeAccountAttributes);
if (!$driverPriceRet) {
    EdjLog::info("--- $driver_id --充值或扣费失败---");
    $ret = array('code' => 3, 'message' => '充值扣费失败');
    echo json_encode($ret);return ;
} else {
    EmployeeAccount::model()->ReloadDriverAmountRedis($employeeAccountAttributes['user']);
    $ret = array('code' => 0, 'message' => '操作成功');
    echo json_encode($ret);return ;
}