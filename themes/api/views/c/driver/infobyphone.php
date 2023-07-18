<?php
/**
 * 洗车专用接口
 * 洗车司机端在e代驾以客户的方式登录
 * 通过用户token,判断是不是司机,获取司机信息
 * @param token
 * @return json
 */

$token = isset($params['token']) ? trim($params['token']) : '';

if(empty($token)) {
    $ret = array('code' => 2, 'message' => '参数有误');
    echo json_encode($ret);
    return ;
}

//验证token
$validate = CustomerToken::model()->validateToken($token);
if(!$validate) {
    $ret = array('code' => 1, 'message' => '验证失败');
    echo json_encode($ret);
    return ;
}

$ret = array(
    'code' => 0,
    'message' => '获取成功'
);

$driver = Driver::model()->getDriverByPhone($validate['phone']);
if(!empty($driver)) {
    $ret['data'] = array();
    $driver_info = Helper::foramt_driver_detail($driver['user'],'',0,'driver');
    if(!empty($driver_info)) {
        $ret['data'] = $driver_info;
    }
    $ret['data']['driverId'] = $driver['user'];
}

echo json_encode($ret);
return;
