<?php
/**
 *  司机端代驾分详情 
 * @author aiguoxin
 * @version 2014-05-27
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';

if(empty($token)){
    $ret=array('code'=>2 , 'message'=>'参数不正确!');
    echo json_encode($ret);return;
}

// //验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver===null||$driver->token===null||$driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}
$driver_id=$driver->driver_id;

// $driver_id='BJ9030';

$driverExt = DriverExt::model()->getDriverExt($driver_id);
if(empty($driverExt)){
	$ret=array('code'=>2 , 'message'=>'找不到司机EXT表信息');
	echo json_encode($ret);return;

}
$current_start = $driverExt['startTime'];

$next_year = $current_start+366*24*60*60;

//返回成功信息
$ret=array('code'=>0 , 
	'message'=>'ok',
	'score'=>$driverExt['score'],
	'driver_count'=>$driverExt['year_driver_count'],
	'next_calculate_time'=>$next_year);
echo json_encode($ret);return;
