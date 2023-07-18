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
$pwd = isset($params['pwd']) ? trim($params['pwd']) : 0;
$app_ver=isset($params['app_ver']) ? trim($params['app_ver']) : '';
if (empty($bonus_sn) && empty($phone)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}

$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    $ret = array(
        'code' => 1,
        'message' => '验证失败',
    );
    echo json_encode($ret);
    return;
}

$ret = BonusLibrary::model()->BonusBinding($bonus_sn, $phone, $pwd,0,$app_ver<BonusCode::APP_VER?0:1);
echo json_encode($ret);