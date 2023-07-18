<?php
/**获取订单详情接口
 * User: zhanglimin
 * Date: 13-7-25
 * Time: 下午4:34
 */
$token = isset($params['token']) ? $params['token'] : "";
$order_id = isset($params['order_id']) ? $params['order_id'] : "";
if(empty($token) || empty($order_id)){
    $ret=array(
        'code'=>2,
        'message'=>'参数不正确'
    );
    echo json_encode($ret);
    return;
}
$driver = DriverStatus::model()->getByToken($params['token']);
if (empty($driver) ||  $driver->token===null||$driver->token!==$params['token']) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}

$condition = array('order_id'=>$order_id);
$order_info = Order::model()->getOrderDetail($condition);
if(empty($order_info)){
    $ret=array(
        'code'=>2,
        'message'=>'获取信息失败'
    );
    echo json_encode($ret);
    return;
}
$ret=array(
    'code'=>0,
    'info'=>$order_info,
    'message'=>'成功'
);
echo json_encode($ret);
return;