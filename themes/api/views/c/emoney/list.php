<?php
/**
 * 客户端API：c.emoney.list 客户端e币列表
 * @param token
 * @author 
 * @return json,成功信息，异常返回错误代码
 * 
 */

$token = isset($params['token']) ? $params['token'] : '';
$pageNO = isset($params['page_num']) ? $params['page_num'] : 0;
$pageSize = isset($params['page_size']) ? $params['page_size'] : 10;

if(empty($token)){
    $ret = array(
        'code' => 2,
        'message' => 'token不能为空'
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

$phone=$validate['phone'];

$list = CustomerWealthLog::model()->getListByphone($phone,$pageNO,$pageSize);

$ret = array(
    'code' => 0,
    'message' => '获取成功',
    'total'=> CustomerMain::model()->getWealth($phone),
    'list' => $list
);
echo json_encode($ret);
