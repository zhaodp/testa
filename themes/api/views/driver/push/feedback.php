<?php
/**
 * 推送消息状态接口
 * Created by JetBrains PhpStorm.
 * User: zhanglimin
 * Date: 13-5-25
 * Time: 下午2:57
 * To change this template use File | Settings | File Templates.
 */

$token  =  isset($params['token']) && !empty($params['token']) ? trim($params['token']) : "";

$push_msg_id  =  isset($params['push_msg_id']) && !empty($params['push_msg_id']) ? trim($params['push_msg_id']) : "";

$flag  =  isset($params['flag']) && !empty($params['flag']) ? trim($params['flag']) : "";

//$p = array(
//    'token' => $token,
//    'push_msg_id' => $push_msg_id,
//	'flag' = > $flag,
//);
//$task = array(
//    'method' => 'order_receive_tmp_log',
//    'params' => $p,
//);
//Queue::model()->putin($task,'test');

//不更新message_log BY AndyCong 2014-01-14
$ret = array('code'=>0 , 'message'=>'成功!');
echo json_encode($ret);return;

if(empty($token) || empty($push_msg_id) || empty($flag) || $flag != 2 ) {
    $ret = array(
        'code'=>2,
        'message'=>'参数不正确!'
    );
    echo json_encode($ret);
    return;
}

$driver = DriverStatus::model()->getByToken($token);

if (empty($driver) || $driver->token===null||$driver->token!==$token) {
    $ret=array(
        'code'=>1,
        'message'=>'token失效'
    );
    echo json_encode($ret);
    return;
}

//添加task队列
$task = array(
    'method' => 'set_message_flag',
    'params' => array(
        'push_msg_id' =>$push_msg_id,
        'flag' =>$flag,
    ),
);

//Queue::model()->task($task);
//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
Queue::model()->putin($task,'dalmessage');

$ret = array(
    'code'=>0,
    'message'=>'成功!');
echo json_encode($ret);
return;




