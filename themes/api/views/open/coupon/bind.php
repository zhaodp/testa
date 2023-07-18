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
//增加参数 appkey 用来后面一些其他操作  by liutuanwang @ 2014-06-18
$channel = isset($params['channel']) ? $params['channel'] : 0;

if (empty($bonus_sn) && empty($phone)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}

$ret = BonusLibrary::model()->BonusBinding($bonus_sn, $phone, $pwd, $channel);
echo json_encode($ret);