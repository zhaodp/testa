<?php
/**
 * 客户端API：用户删除订单
 * @param token
 * @param order_id
 * @param pageNo
 * @param
 * @author bidong 2014-1-14
 * @return json
 */

//加缓存 一天
//$cache_key = 'ORDER_COMPLATE_' . $phone . '_' . $pageNo;
$token = trim($params['token']);
$order_id=trim($params['orderId']);

//验证token 获取用户手机
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    $ret = array(
        'code' => 1,
        'message' => '验证失败',
    );
    echo json_encode($ret);
    return;
}
$phone = trim($validate['phone']);

if(empty($order_id)){
    $ret = array(
        'code' => 2,
        'message' => '参数缺失',
    );
    echo json_encode($ret);
    return;
}

$ret = array(
    'code' => 2,
    'message' => '删除失败',
);

//逻辑删除数据
$params = array(
    'phone'		=>	$phone,
    'order_id'		=>	$order_id
);

//task 添加或修改token
$task = array(
    'method'=>'customerDelOrder',
    'params'=>$params
);
Queue::model()->putin($task,'test');

//删除redis
$result= ROrderHistory::model()->delOrderId($phone, $order_id);
if($result){
    $ret = array(
        'code' => 0,
        'message' => '删除成功',
    );
}


echo json_encode($ret);