<?php
/**
 * 客户端API：c.coupon.bind 绑定优惠券
 * @param token
 * @author
 * @return json,成功信息，异常返回错误代码
 *
 */

$token = isset($params['token']) ? $params['token'] : '';
$bonus_sn = isset($params['bonus_sn']) ? trim($params['bonus_sn']) : '';
$phone = isset($params['phone']) ? trim($params['phone']) : '';

if (empty($bonus_sn) && empty($phone)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}
//
//$validate = CustomerToken::model()->validateToken($token);
//if (!$validate) {
//    $ret = array(
//        'code' => 1,
//        'message' => '验证失败',
//    );
//    echo json_encode($ret);
//    return;
//}

$order_id = CustomerBonus::model()->phoneBonusCheckUser($phone, $bonus_sn);
$ret = array(
	'code' => 0,
	'status' => $order_id
);
echo json_encode($ret);