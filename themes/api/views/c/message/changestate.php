<?php
/**
 * 客户端API：c.message.changestate 
 * @param token
 * @author aiguoxin
 * @return json,成功信息，异常返回错误代码
 * 
 */

$token = isset($params['token']) ? $params['token'] : '';
$messageid = isset($params['messageid']) ? $params['messageid'] : '';
$state = isset($params['state']) ? $params['state'] : '';

//login validate
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    $ret = array('code' => 1, 'message' => '验证失败');
    echo json_encode($ret);
    return;
}

if(empty($messageid)){
    $ret = array('code' => 2, 'message' => '消息不能为空');
    echo json_encode($ret);
    return;
}

if(empty($state)){
    $ret = array('code' => 2, 'message' => '状态不能为空');
    echo json_encode($ret);
    return;
}

$phone = $validate['phone'];
if(empty($phone)){
    $ret = array('code' => 2, 'message' => '客户电话为空');
    echo json_encode($ret);
    return;
}

CustomerMessage::model()->updateState($phone,$messageid,$state);

$ret = array(
            'code' => 0,
            'message' => '更新成功',
        );
echo json_encode($ret);
