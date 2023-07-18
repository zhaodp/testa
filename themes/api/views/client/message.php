<?php
/**
 * 推送消息状态接口
 * Created by JetBrains PhpStorm.
 * User: zhanglimin
 * Date: 13-4-28
 * Time: 上午11:24
 * To change this template use File | Settings | File Templates.
 */

$ret=array(
    'code'=>0,
    'message'=>'成功!'
);
echo json_encode($ret);
return ;
    $token  =  isset($params['token']) && !empty($params['token']) ? trim($params['token']) : "";

    $push_msg_id  =  isset($params['push_msg_id']) && !empty($params['push_msg_id']) ? trim($params['push_msg_id']) : "";

    $flag  =  isset($params['flag']) && !empty($params['flag']) ? trim($params['flag']) : "";

    if(empty($token) || empty($push_msg_id) || empty($flag) || $flag != 2 ) {
        $ret = array(
            'code'=>2,
            'message'=>'参数不正确!'
        );
        echo json_encode($ret);
        return ;
    }

    //这里是否都统一成最新的driver方法了,add by sunhongjing
    $check_token = DriverToken::model()->validateToken($token);

    if(empty($check_token))
    {
        $ret = array (
            'code'=>1,
            'message'=>'token失效!'
        );
        echo json_encode($ret);
        return ;
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
	Queue::model()->putin($task,'order');

    $ret = array(
        'code'=>0,
        'message'=>'成功!');
    echo json_encode($ret);
    return ;



