<?php
/**
 * 新销单类型
 * User: zhanglimin
 * Date: 13-11-8
 * Time: 下午6:50
 */

$token = isset($params['token']) && !empty($params['token']) ? trim($params['token']) : "";

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


$qx_type= Dict::items('qx_o_type'); //取消订单
ksort($qx_type);
//change by aiguoxin add new code=16 客户不接电话
$qx_type_step1 = array_slice($qx_type,0,6,true);

$qx_type_step2 = array_slice($qx_type,4,1,true);

$ts_o_type= Dict::items('ts_o_type'); //投诉类型
ksort($ts_o_type);

$ts_o_type_step1 = array_slice($ts_o_type,0,2,true);

$ts_o_type_step2 = array_slice($ts_o_type,0,5,true);

$ts_o_type_step3 = array_slice($ts_o_type,5,5,true);

$ret = array (
    'code'		=> 0,
    'qx_type_step1' => (object)$qx_type_step1,
    'qx_type_step2' => (object)$qx_type_step2,

    'ts_o_type_step1' => (object)$ts_o_type_step1,
    'ts_o_type_step2' => (object)$ts_o_type_step2,
    'ts_o_type_step3' => (object)$ts_o_type_step3,

    'message'	=> '读取成功'
);

echo json_encode($ret);
return;
