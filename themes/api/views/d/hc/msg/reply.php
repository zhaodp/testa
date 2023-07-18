<?php
/**
 * 回复工单
 * @return echo array('code'=>0,'message'=>'回复成功')
 * @modify  wanglonghuan 2013-12-25
 */
$params['id'] = ( empty($params['id']) || $params['id']<=0 ) ? 0: $params['id'];
$params['message'] = ( empty($params['message']) || $params['message'] == '') ? '': $params['message'];

$driver = DriverStatus::model()->getByToken($params['token']);
if ( empty($driver) ||  $driver->token===null || $driver->token!==$params['token'] ) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}

if($params['id'] == 0 || $params['message'] == ''){
    $ret=array(
        'code'=>2,
        'message'=>'参数错误'
    );
    echo json_encode($ret);
    return;
}

//添加task队列 设置当前己读
$task=array(
    'method'=>'reply_support_ticket',
    'params'=>array(
        'ticket_id'=>$params['id'],
        'message' =>$params['message'],
        'reply_user' => $driver->driver_id,
    )
);
Queue::model()->putin($task,'support');

$ret=array(
    'code'=>0,
    'message'=>'已添加至回复列表。'
);
echo json_encode($ret);
?>