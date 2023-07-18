<?php
/**
 *
 * 接口用来返回用户用户的基本信息
 *
 * 现在包括 用户类型,用户 id,
 * 如果是 vip 那么 userId 为 vip 卡号
 *
 * 如果用户不存在返回 type 为0
 *
 *
 * User: tuan
 * Date: 2/3/15
 * Time: 20:09
 */

EdjLog::info('open.utils.user_info ---- '.json_encode($params));

$phone = isset($params['phone']) ? $params['phone'] : 0;

if(empty($phone)){
    $ret = array(
        'code' => 2,
        'message' => '参数错误',
    );
    echo json_encode($ret);return;
}

$code = 0;
$customer_info = CustomerMain::model()->getCustomerInfo($phone);
$userId = 0;
$isVip  = false;
$type   = 1;
if($customer_info){
    $userId = $customer_info->id;
    $isVip  = $customer_info->vip;
}else{
    $customerMain = CustomerMain::model()->getCustomer($phone);
    if($customerMain){
        $userId = $customerMain['id'];
    }else{
        $type = 0;
    }
}

$isVip = CustomerMain::model()->isVip($phone);
if($isVip){
    $vipPhone = VipPhone::model()->getPrimary($phone);
    if($vipPhone){
        $userId = $vipPhone['vipid'];
    }else{
        $code = 1;
    }
}
$isDriver = Driver::model()->isDriver($phone);
$isNewCustomer = CustomerOrderReport::model()->isNewCustomer($phone);//是否新客
$ret = array(
    'code' => $code,
    'type' => $type,
    'isVip' => $isVip,
    'isDriver' => $isDriver,
    'isNewCustomer' => $isNewCustomer,
    'userId' => $userId
);

echo json_encode($ret);return;
