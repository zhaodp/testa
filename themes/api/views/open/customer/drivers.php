<?php
/**
 * 客户端API：c.my.drivers 获取我的司机,从redis中取。
 * @param string $token
 * @return json
 * @author AndyCong 2013-10-15
 */
//接收token
$token = isset($params['token']) ? $params['token'] : '';
$gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'google';
$polling_count = isset($params['polling_count']) ? $params['polling_count'] : 1;

//验证token
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
	$ret = array('code' => 1 , 'data' => '' , 'message' => '验证失败');
	echo json_encode($ret);return ;
}

//获取当前订单数据
$phone = isset($validate['phone']) ? $validate['phone'] :'';
$result = CustomerApiOrder::model()->getMyDrivers($phone , $gps_type);
$data = array(
    'drivers' => $result['drivers'],
    'polling_state' => $result['polling_state'],
    'polling_count' => $polling_count,
    'next' => 10,
);

if ($polling_count >= 50) {
	$data['polling_state'] = 2;
};

$ret = array('code' => 0 , 'data' => $data , 'message' => '成功',);
echo json_encode($ret);return;

