<?php
/**
 * 客户端API：c.active.display 
 * @param token
 * @author aiguoxin
 * @return json,成功信息，异常返回错误代码
 * 
 */

$token = isset($params['token']) ? $params['token'] : '';
$lng = $params['longitude'];
$lat = $params['latitude'];

if (empty($token) || empty($lng) || empty($lat)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
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
//         'message' => '你所在城市不在南京,不展示'
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
        'message' => 'VIP用户不展示'
    );
    echo json_encode($ret);
    return;
}

// $activeLog = CustomerActiveLog::model()->isDisplay($phone,$cityId);
// if(!empty($activeLog)){
// 	$ret = array(
//         'code' => 3,
//         'message' => '已经出现过一次弹窗'
//     );
//     echo json_encode($ret);
//     return;
// }



//football
//活动添加截止时间限制
$current_time = date("Y-m-d H:m:s");

$display_activity = Yii::app()->params['display_activity'];
$end_time=$display_activity['end_time'];
if($current_time > $end_time){
    $ret = array(
        'code' => 3,
        'message' => '活动已结束'
    );
    echo json_encode($ret);
    return;
}

$today = date("Y-m-d");

$key = CustomerActiveLog::CITY_ACTIVE_DISPLAY . $phone.CustomerActiveLog::ACTIVE_NAME_NANJING;
if($today == '2014-07-13'){
    $key = CustomerActiveLog::CITY_ACTIVE_DISPLAY . $today.$phone.CustomerActiveLog::ACTIVE_NAME_NANJING;
}
$activeLog = Yii::app()->cache->get($key);
if($activeLog){
    $ret = array(
        'code' => 3,
        'message' => '已经出现过一次弹窗'
    );
    echo json_encode($ret);
    return;
}else{
    Yii::app()->cache->set($key, '1', 3600*24*7);
}


$ret = array(
        'code' => 0,
        'display' => 0,
        'message' => '请求成功',
        'activeName' => CustomerActiveLog::ACTIVE_NAME_NANJING,
        'activeMessage' => '猜世界杯比分，每天赢39元代驾卡！',
  		'shareText' => '我和e代驾对赌，每天有人帮我猜对球赛比分，都能拿他们的代驾卡→http://h5.edaijia.cn/worldcup/index.html?'.strrev($phone+01234567899)
    );
echo json_encode($ret);
