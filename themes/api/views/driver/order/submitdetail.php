<?php
/**
 * 报单订单详情api---改善detail接口
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-06-24
 */
//验证参数
$order_id = isset($params['order_id']) ? $params['order_id'] : 0;
$token = isset($params['token']) ? $params['token'] :'';
if (0 == $order_id || '' == $token) {
	$ret=array(
        'code'=>2,
        'message'=>'参数有误!'
    );
    echo json_encode($ret);
    return;
}
//验证参数 END

//验证token
$driver = DriverStatus::model()->getByToken($params['token']);
if (empty($driver) ||  $driver->token===null||$driver->token!==$params['token']) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}
//验证token END

//获取订单信息
$condition = array();
$condition['order_id'] = $order_id;
$condition['driver_id'] = $driver->driver_id;
$order = Order::model()->getSubmitOrderDetail($condition);
if(empty($order)){
    $ret=array(
        'code'=>2,
        'message'=>'获取信息有误,请重新获取',
    );
    echo json_encode($ret);
    return;
}

$ret=array(
    'code'=>0,
    'order'=>$order,
    'message'=>'获取数据成功',
);
echo json_encode($ret);
return;
//获取订单信息 END