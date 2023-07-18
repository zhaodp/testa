<?php
/**
 * 客户端API：c.coupon.valid 验证优惠券
 * @param token
 * @author 
 * @return json,成功信息，异常返回错误代码
 * 
 */

$token = isset($params['token']) ? $params['token'] : '';
$bonus_sn = isset($params['bonus_sn']) ? trim($params['bonus_sn']) : '';

if(empty($token) && empty($bonus_sn)){
	$ret = array(
		'code'=>2,
		'message'=>'参数错误'
	);
	echo json_encode($ret);
	return;
}

$validate = CustomerToken::model()->validateToken($token);
if (!$validate){
	$ret = array(
		'code'=>1,
		'message'=>'验证失败',
	);
    echo json_encode($ret);
    return;
}

//优惠劵检查是否有密码
$checkBonusUse = BonusLibrary::model()->checkBonusUse($bonus_sn, 0, 0);
if ($checkBonusUse && $checkBonusUse['status'] == 2) {
    $ret = array(
        'code' => 0,
        'message' => '优惠劵有密码', //
    );
} else {
    $ret = array(
        'code' => 2,
        'message' => '没有密码', //
    );
}
echo json_encode($ret);