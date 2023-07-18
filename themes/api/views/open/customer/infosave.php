<?php
/**
 * 客户端API：c.my.infosave 获取周边最近的空闲司机列表，验证token。写操作走队列。
 * @param string $token
 * @param string $from
 *
 * @author sunhongjing 2013-10-11
 *
 * @return json
 */

//参数有效性验证
$token = trim($params['token']);
$name = isset($params['name']) ? trim($params['name']) : '';
$gender = isset($params['gender']) ? intval(trim($params['gender'])) : 0;
$backup_phone = empty($params['backup_phone']) ? '' : trim($params['backup_phone']);
$address = empty($params['address']) ? '' : trim($params['address']);
$car_num = empty($params['car_num']) ? '' : trim($params['car_num']);

if ($gender > 2) {
    $ret = array('code' => 2, 'message' => '传入的性别有误');
    echo json_encode($ret);
    return;
}

if (!empty($backup_phone)) {
    $is_phone = Common::checkPhone($backup_phone);
    if (!$is_phone) {
        $ret = array('code' => 2, 'message' => '传入手机号有误');
        echo json_encode($ret);
        return;
    }
}

if (!empty($name)) {
    $len = 20;
    if ($len < mb_strlen($name)) {
        $ret = array('code' => 2, 'message' => '姓名太长');
        echo json_encode($ret);
        return;
    }

    $is_hk = Common::hasSensitiveWords($name, $len);
    if ($is_hk) {
        $ret = array('code' => 2, 'message' => '姓名中包含非法字符');
        echo json_encode($ret);
        return;
    }
}


//验证用户token，如果有token，如果客户已登录，则返回用户信息(需要缓存)，否则返回失败
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    $ret = array('code' => 1, 'message' => '验证失败');
    echo json_encode($ret);
    return;
}
$phone = trim($validate['phone']);

$data = array();
$data['phone'] = $phone;
$data['name'] = $name; //过滤name,非法字符，超长字符都要干掉
$data['gender'] = $gender;
$data['backup_phone'] = $backup_phone; //超长的干掉，验证11位数字
$data['address'] = $address;
$data['car_num'] = $car_num;

//这里能不能走队列？add by sunhongjing

$updateCustomer = CustomerMain::model()->updateCustomerInfo($data);
if (!$updateCustomer) {
    $ret = array('code' => 2, 'message' => '您没有修改任何信息，请重新编辑!');
    echo json_encode($ret);
    return;
}
//返回数据信息
$ret = array('code' => 0, 'message' => '操作成功');
echo json_encode($ret);
return;
