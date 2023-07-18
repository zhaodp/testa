<?php
/**
 * 信息上报接口
 * 需要写清楚注释，再清楚些 add by sunhongjing at 2013-5-19
 * Created by JetBrains PhpStorm.
 * User: zhanglimin
 * Date: 13-5-13
 * Time: 下午2:21
 * To change this template use File | Settings | File Templates.
 */

$phone = isset($params['phone']) ? trim($params['phone']) : '';
$content = isset($params['content']) ? trim($params['content']) : '';
$driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';
$created = isset($params['created']) ? trim($params['created']) : '';

if(empty($phone) || empty($content) || empty($driver_id) || empty($created)){
    $ret = array(
        'code'=>2,
        'message'=>'参数不正确!'
    );
    echo json_encode($ret);
    return;
}

$driver = DriverStatus::model()->get($driver_id);
if(empty($driver)){
    $ret = array(
        'code'=>2,
        'message'=>'司机工号有误!'
    );
    echo json_encode($ret);
    return;
}

//添加task队列
$task = array(
    'method' => 'push_upload_sms',
    'params' => array(
        'phone' =>$phone,
        'content' =>$content,
        'driver_id' =>$driver_id,
        'created' => $created , //确认时间
    ),
);

//Queue::model()->task($task);
//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
Queue::model()->putin($task,'task');


$ret = array(
    'code'=>0,
    'message'=>'成功!'
);
echo json_encode($ret);
return;