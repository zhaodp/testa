<?php
/**
 * 客户端API：c.my.infoview 我的账号信息，验证客户信息，如果客户已登录，则返回用户信息(需要缓存)，否则返回失败
 *
 * @param string $token
 * @param string $from
 *
 * @author sunhongjing 2013-10-11
 *
 * @return json
 */
//验证用户token，如果有token，如果客户已登录，则返回用户信息(需要缓存)，否则返回失败
$token = trim($params['token']);

$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    $ret = array('code' => 1, 'message' => '验证失败');
    echo json_encode($ret);
    return;
}

//获取客户手机号
$phone = trim($validate['phone']);
$customer_info = CustomerMain::model()->getCustomerInfo($phone);
$name = $customer_info->name;
$backup_phone = $customer_info->backup_phone;
$address = $customer_info->address;
$car_num = $customer_info->car_num;
$gender = intval($customer_info->gender);
$id = $customer_info->id;
$vip = $customer_info->vip;
$balance = 0;
$company='';
$viptype="";
if($vip){
    //获取vip主、副卡信息
    $data=VipPhone::model()->getPrimary($phone);
    if($data && isset($data['vipid']) && !empty($data['vipid'])){
        $viptype=$data['type'];
        $vipid=$data['vipid'];
        $vip_info=Vip::model()->getPrimary($vipid);
        if($data['type']>VipPhone::TYPE_VICE){
            /*$name = $vip_info->name;
            $company = $vip_info->company;
            $phone = $vip_info->phone;
            $backup_phone = $vip_info->send_phone;
            $credit = $vip_info->credit;
            $city_id = $vip_info->city_id;
            $id = $vip_info->id;*/
            $vip =1;
            $balance = $vip_info->balance;
        }else{
            /*$name = $data['name'];*/
            $phone = $phone;
            /*$id = $vip_info->id;*/
            $vip =1;
            $balance = 0;
        }
    }
}else{
    $user_account = BCustomers::model()->getAccount(array('user_id' => $id));
    if($user_account['code'] === 0){
        $user_account_data = $user_account['data'];
        $balance = $user_account_data->amount;
    }
}


//客户信息
$customer_data = array(
    'id' => $id,
    'phone' => $phone,
    'name' => $name,
    'company' => $company,
    'gender' => $gender,
    'backup_phone' => $backup_phone,
    'car_num' => $car_num,
    'address' => $address,
    'balance' => $balance,
    'vip' => $vip,
    'viptype' => $viptype,//0-vip副卡,1-vip主卡
);

//返回数据信息
$ret = array('code' => 0, 'data' => $customer_data, 'message' => '操作成功');
echo json_encode($ret);
return;