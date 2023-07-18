<?php
/**
 * 获取公告内容
 * User: zhanglimin
 * Date: 13-8-28
 * Time: 下午6:52
 */

$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";
$notice_id=isset($params['notice_id'])&&!empty($params['notice_id']) ? intval(trim($params['notice_id'])) : "";
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


$noticeInfo = NewNotice::model()->getInfo($notice_id);

if(!empty($noticeInfo)){
    $noticeInfo['category'] = NewNotice::$categorys[$noticeInfo['category']];
    $noticeInfo['booking_push_datetime'] = date("m-d H:i",strtotime($noticeInfo['booking_push_datetime']));

    if($noticeInfo['post_id'] != 0){
        $host = str_replace("api","www",$_SERVER['HTTP_HOST']);
        $url = "http://".$host."/v2/index.php?r=site/noticepost&id=".intval($noticeInfo['post_id']);
        $noticeInfo['url']= $url;
    }
}
$ret = array (
    'code'=>0,
    'info'=>$noticeInfo,
    'message'=>'读取成功');
echo json_encode($ret);
return;