<?php
/**
 * 推送消息状态接口
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-07-23
 */

//不更新message_log BY AndyCong 2014-01-14

$push_msg_id = isset($params['push_msg_id']) ? $params['push_msg_id'] : 0;
$flag  =  isset($params['flag']) ? $params['flag'] : '';
$token  =  isset($params['token']) ? $params['token'] : '';

$p = array(
    'token' => $token,
    'method' => 'driver.push.callback',
    'push_msg_id' => $push_msg_id,
	'flag' => $flag,
	'log_time' => time(),
);
$task = array(
    'method' => 'order_receive_tmp_log',
    'params' => $p,
);
Queue::model()->putin($task,'test');

$ret = array('code'=>0 , 'message'=>'成功!');
echo json_encode($ret);return;


if(empty($token) || 0 == $push_msg_id || empty($flag) || $flag != 2 ) {
    $ret = array('code'=>2 , 'message'=>'参数不正确!');
    echo json_encode($ret);return;
}

$driver = DriverStatus::model()->getByToken($token);
if (empty($driver) || $driver->token===null || $driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}

//添加task队列
$task = array(
    'method' => 'update_msg_flag',
    'params' => array(
        'push_msg_id' =>$push_msg_id,
        'flag' =>$flag,
    ),
);

Queue::model()->putin($task,'dalmessage');
$ret = array('code'=>0 , 'message'=>'成功!');
echo json_encode($ret);
return;