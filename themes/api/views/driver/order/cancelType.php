<?php
//需要写清楚注释 add by sunhongjing at 2013-5-19
/*
 * modify  zhanglimin 2013-06-07
 * 切换验证token方式
 */

$driver = DriverStatus::model()->getByToken($params['token']);
if (empty($driver) ||  $driver->token===null||$driver->token!==$params['token']) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    die();
}

$cancelType = Dict::items('cancel_type');
$complaintType = Dict::items('cancel_c_type');
$array = $arrayType = array();
foreach($complaintType as $value){
    $array[] = $value;
}

foreach($cancelType as $valueType){
    $arrayType[] = $valueType;
}

$ret = array (
    'code'=>0,
    'cancel'=>$arrayType,
    'complaint'=>$array,
    'message'=>'读取成功');

echo json_encode($ret);
exit();