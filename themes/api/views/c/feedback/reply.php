<?php
/**
 * 反馈回复
 * @param token
 * @author aiguoxin
 * @return json,成功信息，异常返回错误代码
 * 
 */

$token = isset($params['token']) ? $params['token'] : '';
$phone = isset($params['phone']) ? $params['phone'] : '';
$id = isset($params['id']) ? $params['id'] : 0;
$content = isset($params['content']) ? $params['content'] : '';

if (empty($token)) {
    $ret = array(
        'code' => 2,
        'message' => 'token参数错误'
    );
    echo json_encode($ret);
    return;
}

if (empty($phone)) {
    $ret = array(
        'code' => 2,
        'message' => 'phone参数错误'
    );
    echo json_encode($ret);
    return;
}

if (empty($id)) {
    $ret = array(
        'code' => 2,
        'message' => 'id参数错误'
    );
    echo json_encode($ret);
    return;
}

if (empty($content)) {
    $ret = array(
        'code' => 2,
        'message' => '回复内容不能为空'
    );
    echo json_encode($ret);
    return;
}

$dataFeedback = array();
$dataFeedback['phone'] = $phone;
$dataFeedback['id'] = $id;
$dataFeedback['content'] = $content;

$res = CustomerSuggestionReply::model()->addSuggestionReply($id,$content,CustomerSuggestionReply::ROLE_CUSTOMER,$phone);
if($res > 0){
    $task = array(
        'method' => 'replyFeedback',
        'params' => $dataFeedback
    );
    Queue::model()->putin($task, 'task');
}

$ret = array(
        'code' => 0,
        'message' => '回复成功',
    );

echo json_encode($ret);
