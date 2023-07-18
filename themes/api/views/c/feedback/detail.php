<?php
/**
 * 反馈详情
 * @param token
 * @author aiguoxin
 * @return json,成功信息，异常返回错误代码
 * 
 */

$token = isset($params['token']) ? $params['token'] : '';
$phone = isset($params['phone']) ? $params['phone'] : '';
$id = isset($params['id']) ? $params['id'] : 0;

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

$data = array();

$list = CustomerSuggestionReply::model()->findAllBySuggestionId($id);

foreach ($list as $message) {
    $data[]=array(
        'content'=>$message['content'],
        'user'=>$message['role'],
        'updateTime'=>date("m月d日 H:i分",strtotime($message['create_time'])),
        );
}

$ret = array(
        'code' => 0,
        'message' => '请求成功',
        'list'=>$data,
    );
echo json_encode($ret);
