<?php
/**
 * 反馈列表
 * @param token
 * @author aiguoxin
 * @return json,成功信息，异常返回错误代码
 * 
 */

$token = isset($params['token']) ? $params['token'] : '';
$phone = isset($params['phone']) ? $params['phone'] : '';

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

$data = array();

$list = CustomerSuggestion::model()->getMsgList($phone);

foreach ($list as $message) {
    //计算回复数目
    $replyNum = CustomerSuggestionReply::model()->countBySuggestionId($message['id']);
    $data[]=array(
        'id'=>$message['id'],
        'content'=>$message['title'],
        'replyNum'=>$replyNum,
        'status'=>$message['status'],
        'updateTime'=>date("m月d日 H:i分",strtotime($message['update_time'])),
        'type'=>$message['type'] == CustomerSuggestion::TYPE_FEEDBACK ? '反馈':'投诉',
        );
}

$ret = array(
        'code' => 0,
        'message' => '请求成功',
        'list'=>$data,
    );
echo json_encode($ret);
