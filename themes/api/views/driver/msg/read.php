<?php
/**
 * 设置公告己读
 * User: zhanglimin
 * Date: 13-10-11
 * Time: 上午11:13
 */

$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";
$notice_id=isset($params['notice_id'])&&!empty($params['notice_id']) ? intval(trim($params['notice_id'])) : "";
//add by aiguoxin type=0未读公告,type=1投诉
$type=isset($params['type'])&&!empty($params['type']) ? trim($params['type']) : "0";

if ( empty($token) || empty($notice_id) ) {
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

if($type == '0'){
    $conditions = array(
        'driver_id'=>$driver->driver_id,
        'flag'=>1, //0 未读 1 己读
    );
    $notice_ids = NoticeStatus::model()->getDriverNoticeIds($conditions);
    if(empty($notice_ids) || !in_array($notice_id,$notice_ids)){

        //添加task队列 设置当前己读
        $task=array(
            'method'=>'push_driver_new_notice_read',
            'params'=>array(
                'notice_id'=>$notice_id,
                'driver_id'=>$driver->driver_id,
            )
        );
        Queue::model()->task($task);
    }
}elseif ($type == '1') {
        //add by aiguoxin
        // $task=array(
        //     'method'=>'push_customer_complain_read',
        //     'params'=>array(
        //         'notice_id'=>$notice_id,
        //         'driver_id'=>$driver->driver_id,
        //     )
        // )
        CustomerComplain::model()->updateDriverRead($notice_id,$driver->driver_id);
}

$ret = array (
    'code'=>0,
    'message'=>'读取成功'
);
echo json_encode($ret);
return;