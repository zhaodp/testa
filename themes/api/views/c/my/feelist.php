<?php
/**
 * Created by PhpStorm.
 * User: mtx
 * Date: 14-2-19
 * Time: 下午2:22
 * auther mengtianxue
 */

//参数有效性验证
$token = trim($params['token']);
$user_id = isset($params['user_id']) ? $params['user_id'] : 0;
$type = isset($params['type']) ? $params['type'] : 0;
$min_id = (isset($params['min_id']) && empty($params['min_id'])) ? 0 : $params['min_id'];
$count = isset($params['count']) ? $params['count'] : 10;

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
//vip流水
$isvip=false;
$vipPhone=VipPhone::model()->getPrimary($phone);
$vip=false;
if($vipPhone&&isset($vipPhone['vipid'])){
	$vip=Vip::model()->getPrimary($vipPhone['vipid']);
}
if($vipPhone && $vip && isset($vipPhone['vipid']) && !empty($vipPhone['vipid'])){
    $user_id=$vipPhone['vipid'];
    $vipType=$vipPhone['type'];
    $isvip=true;
}

if($isvip){
    $feesList = VipTrade::model()->getVipTradeListApi($user_id,$phone,$vipType, $min_id, $count, $type);
}else{
    //普通用户流水
    //接口如果没有传user_id
    if (empty($user_id)) {
        $customer = BCustomers::model()->getCustomerInfo(array('phone' => $phone));
        if ($customer['code'] === 0) {
            $customer_data = $customer['data'];
            $user_id = $customer_data->id;
        }
    }

    $feesList = BCustomers::model()->getCustomerTradeListApi($user_id, $min_id, $count, $type);
}

$ret = array(
    'code' => 0,
    'data' => $feesList,
    'message' => '获取成功'
);

echo json_encode($ret);
