<?php
/**
 * 获取司机 被投诉信息
 * @param token
 * @modify  wanglonghuan 2014-1-16
 */
$params['page'] = ( empty($params['page']) || $params['page']<=0 ) ? 1 : $params['page'];
$params['pageSize'] = ( empty($params['pageSize']) || $params['pageSize']<=0 ) ? 10 : $params['pageSize'];
$params['time_type'] = ( empty($params['time_type']) || $params['time_type']<0 || $params['time_type']>1 ) ? 0 : $params['time_type'];


$driver = DriverStatus::model()->getByToken($params['token']);
if ( empty($driver) ||  $driver->token===null || $driver->token!==$params['token'] ) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}
if(empty($params['pageSize']) || empty($params['page'])){
    $ret=array(
        'code'=>2,
        'message'=>'参数错误'
    );
    echo json_encode($ret);
    return;
}

$ret['data'] =CustomerComplain::model()->getComplainListByDriver($driver->driver_id,$params['page'],$params['pageSize'],$params['time_type'],true);
$ret['code'] = 0;
$ret['message'] = "读取成功";
echo json_encode($ret);
?>