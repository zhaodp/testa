<?php
/**
 * Created by PhpStorm.
 * User: aiguoxin
 * Date: 15/3/31
 * Time: 下午12:01
 */
$token = empty($params['token']) ? '' : trim($params['token']);
$bookingDate = empty($params['bookingDate']) ? '' : trim($params['bookingDate']);

if (empty($token)) {
    $ret['message'] = JsonResponse::validMsgFail;
    $ret['code'] = JsonResponse::EXPIRE_CODE;
    echo JsonResponse::fail($ret);
    return;
}

//验证是否登录
$driverManagerId = DriverStatus::model()->getDriverManagerToken($token);
if (empty($driverManagerId)) {
    $ret['message'] = JsonResponse::validMsgFail;
    $ret['code'] = JsonResponse::EXPIRE_CODE;
    echo JsonResponse::fail($ret);
    return;
}

$driverManager = DriverStatus::model()->get($driverManagerId);
if(empty($driverManager)){
    $ret['message'] = JsonResponse::validMsgFail;
    echo JsonResponse::fail($ret);
    return;
}

$cityId = $driverManager->city_id;
if(empty($bookingDate)){//默认当天，int类型20150411
    $bookingDate = date ("Ymd");
}
$bookingList=BookingExamDriver::model()->getBookingList($cityId,$bookingDate);
$ret['bookingList']=$bookingList;
$hourList=array();
$length = count(BookingHoursSetting::$columns)/2;
for($i=1;$i<=$length;$i++){
    $hours=BookingHoursSetting::model()->getHoursDesc($cityId,$i);
    $hourList[]=array(
        'id'=>$i,
        'hours'=>$hours,
    );
}
$ret['hourList']=$hourList;

echo JsonResponse::success($ret);
return;


