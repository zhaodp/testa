<?php
/**
 * 客户端API：c.driver.info 获取司机信息，需不需要token?
 * @param string $from
 * @param string $driver_id
 * @param string $gps_type {gps,google,baidu}
 * @author 
 * @return json 成功返回成功信息，异常返回错误代码
 */

//获得司机信息 返回是否成功
$driver_id=$params['driver_id'];
$gps_type= isset($params['gps_type'])?$params['gps_type']:'google';

$driver_info = Helper::foramt_driver_detail( $driver_id , $gps_type );

if ( $driver_info ) {
	
	$ret=array(
			'code'=>0,
			'driverInfo'=>$driver_info,
			'message'=>'获取成功'
	);
} else {
	$ret=array(
			'code'=>1,
			'message'=>'获取失败'
	);
}

echo json_encode($ret);

