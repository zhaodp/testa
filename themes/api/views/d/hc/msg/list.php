<?php
/**
 * 获取司机 工单列表
 * @param token
 * @modify  wanglonghuan 2013-12-25
 */
$params['page'] = ( empty($params['page']) || $params['page']<=0 ) ? 1 : $params['page'];
$params['pageSize'] = ( empty($params['pageSize']) || $params['pageSize']<=0 ) ? 10 : $params['pageSize'];

$driver = DriverStatus::model()->getByToken($params['token']);
if ( empty($driver) ||  $driver->token===null || $driver->token!==$params['token'] ) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}

$ret['data'] = SupportTicket::model()->loadSTlist($driver->driver_id, $params['page'], $params['pageSize']);
$ret['data'] = json_decode($ret['data']);
$ret['code'] = 0;
$ret['message'] = "读取成功";
echo json_encode($ret);
?>