<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 13-11-9
 * Time: 下午12:14
 * auther mengtianxue
 */
$token = $params['token'];
$order_id = isset($params['order_id']) ? trim($params['order_id']) : '';
$content = isset($params['content']) ? trim($params['content']) : '';

//参数有效性验证
if (empty($token) || empty($order_id) || empty($content)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}

//校验token
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    $ret = array(
        'code' => 1,
        'message' => '验证失败'
    );
    echo json_encode($ret);
    return;
}

$complain = array();
$complain['order_id'] = $order_id;
$complain['content'] = $content;

$task = array(
    'method' => 'complain',
    'params' => $complain
);
Queue::model()->putin($task, 'task');

$ret = array(
    'code' => '0',
    'message' => '投诉成功'
);
echo json_encode($ret);
return;
