<?php
/**
 * 客户端API：c.message.list 
 * @param token
 * @author aiguoxin
 * @return json,成功信息，异常返回错误代码
 * 
 */

$token = isset($params['token']) ? $params['token'] : '';

//login validate
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    $ret = array('code' => 1, 'message' => '验证失败');
    echo json_encode($ret);
    return;
}

$phone = $validate['phone'];
if(empty($phone)){
    $ret = array('code' => 2, 'message' => '客户电话为空');
    echo json_encode($ret);
    return;
}

$data = array();
$version=CustomerMain::model()->getAppversion($phone);
$list = CustomerMessage::model()->getMsgList($phone,$version);
$unread = 0;//未读消息数
foreach ($list as $message) {
    $data[]=array(
        'message_id'=>$message['id'],
        'state'=>$message['state'],
        'type'=>$message['type'],
        'title'=>$message['title'],
        'content'=>$message['content'],
        'time'=>strtotime($message['create_time']),
        'action_url'=>$message['action_url'],
        'content_id'=>$message['content_id'],
        );
    if($message['state'] == 0){
        $unread++;
    }
}


$ret = array(
        'code' => 0,
        'message' => '请求成功',
        'unread' => $unread,
        'data'=>$data,
    );
echo json_encode($ret);
