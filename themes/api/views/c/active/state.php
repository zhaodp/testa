<?php
/**
 * 客户端API：c.active.state 
 * @param token
 * @author aiguoxin
 * @return json,成功信息，异常返回错误代码
 * 
 */

$token = isset($params['token']) ? $params['token'] : '';
$lng = $params['longitude'];
$lat = $params['latitude'];
$state = isset($params['state']) ? $params['state'] : '';
$activeName = isset($params['activeName']) ? $params['activeName'] : CustomerActiveLog::ACTIVE_NAME_NANJING;


if (empty($token) || empty($lng) || empty($lat) || empty($state) || empty($activeName)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}

//security 
if($activeName != CustomerActiveLog::ACTIVE_NAME_NANJING){
    $ret = array(
        'code' => 2,
        'message' => '活动不存在'
    );
    echo json_encode($ret);
    return;
}

$cityName = GPS::model()->getCityByBaiduGPS($lng,$lat);

$citys = Dict::items('city');
$cityId = 0;
foreach($citys as $key=>$value) {
	if ($value==$cityName){
		$cityId = $key;
		break;
	}
}

//city validate
// if($cityId != 8){ //not nanjing
// 	$ret = array(
//         'code' => 3,
//         'message' => '你所在城市不在南京,无法操作'
//     );
//     echo json_encode($ret);
//     return;
// }

//login validate
$validate = CustomerToken::model()->validateToken($token);
if (!$validate) {
    $ret = array('code' => 1, 'message' => '验证失败');
    echo json_encode($ret);
    return;
}

//vip validate
$phone = $validate['phone'];
$vip = VipPhone::model()->getPrimary($phone);

if(!empty($vip)){
	$ret = array(
        'code' => 3,
        'message' => 'VIP用户无法操作'
    );
    echo json_encode($ret);
    return;
}

$bonus_sn = 5030464087;
$pwd = 0;

// set to cache
$key = CustomerActiveLog::CITY_ACTIVE_DISPLAY . $cityId.$phone.CustomerActiveLog::ACTIVE_NAME_NANJING;
Yii::app()->cache->set($key, '1', 3600);

$data = array(
    'state' => $state,
    'bonus_sn' => $bonus_sn, //coupon number
    'pwd' => $pwd, //coupon password
    'phone' => $phone,
    'cityId' => $cityId,
    'activeName' => $activeName,
);
//添加task队列更新数据库
$task=array(
    'method'=>'customer_active_display',
    'params'=>$data,
);


Queue::model()->putin($task,'coupon');

$ret = array(
        'code' => 0,
        'message' => '请求成功'
    );
echo json_encode($ret);
