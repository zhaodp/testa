<?php
/**
 * 客户端API：c.coupon.list 优惠券列表
 * @param token
 * @author 
 * @return json,成功信息，异常返回错误代码
 * 
 */

$token = isset($params['token']) ? $params['token'] : '';
$pageNO = isset($params['pageNO']) ? $params['pageNO'] : 0;
$pageSize = isset($params['pageSize']) ? $params['pageSize'] : 10;
#$sort = isset($params['sort']) ? $params['sort'] : 0;
//因为ios、android客户端传值sort参数不一致，暂时先在api屏蔽sort参数--zhangxiaoyin
$sort = 2;
$status = isset($params['status']) ? $params['status'] : 0;
$app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';
$channel_limited = isset($params['channel_limited']) ? $params['channel_limited'] : '';
if (empty($token)) {
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
        'message' => 'Token验证失败',
    );
    echo json_encode($ret);
    return;
}

$params = array();
$params['phone'] = $validate['phone'];
if(!empty($app_ver)){
    //add by aiguoxin
    CustomerMain::model()->updateAppversion($params['phone'],$app_ver);
}

$vip_phone = VipPhone::model()->getPrimary($params['phone']);
if($vip_phone){
	$ret=array(
	'code'=>0,
	'message'=>'获取成功',
	'data'=>array(),
	);
	echo json_encode($ret);
	return;
}
$params['pageNO'] = $pageNO;
$params['pageSize'] = $pageSize;
$params['sort'] = $sort;
$params['status'] = $status;
$params['bonus_use_limit'] =$channel_limited;
$params['app_ver'] =$app_ver< BonusCode::APP_VER?0:1;
$bonus_list = BBonus::model()->getCustomerBonus($params);



$ret = array(
    'code' => 0,
    'message' => '获取成功',
    'data' => $bonus_list
);
echo json_encode($ret);
