<?php
/**
 * 投诉详细页
 * @return echo array('code'=>0,'message'=>'添加成功')
 * @modify  wanglonghuan 2013-12-25
 */
$params['complaint_id'] = ( empty($params['complaint_id']) || $params['complaint_id']<=0 ) ? 0: $params['complaint_id'];
$driver = DriverStatus::model()->getByToken($params['token']);
if ( empty($driver) ||  $driver->token===null || $driver->token!==$params['token'] ) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}
if(empty($params['complaint_id'])){
    $ret=array(
        'code'=>2,
        'message'=>'参数错误'
    );
    echo json_encode($ret);
    return;
}

$data = CustomerComplain::model()->getComplaintDetailById($params['complaint_id']);
$ret=array(
    'code'=>0,
    'data'=>$data,
    'message'=>'读取成功',
);
echo json_encode($ret);
?>