<?php
/**
 * @毕东 跟踪确认此接口是否在使用， 了解清楚当时的业务需求，确认此接口是否有保留价值。add by sunhongjing 2013-06-06
 * 
 * @var unknown_type
 */
$token = $params['token'];
$phone = $params['phone'];
$type = $params['type'];
$bonus = $params['bonus'];
$fee = $params['fee'];
$password = isset($params['password']) ? $params['password'] : 0;

if ($token == 0 && $fee == 0) {
    //优惠劵绑定
    $bonusBinding = BonusLibrary::model()->BonusBinding($bonus, $phone, $password);
    $ret = array(
        'code' => 0,
        'token' => $token,
        'recharge' => $bonusBinding['code'],
        'message' => $bonusBinding['message']);
} else {
    $validate = CustomerToken::validateToken($token);

    if (!$validate){
        $ret = array(
            'code' => 1,
            'message' => 'token已失效请重新进行预注册');
    }
}
echo json_encode($ret);