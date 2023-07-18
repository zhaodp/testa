<?php
// 注册新的push通道client_id接口

$client_id=isset($params['client_id'])&&!empty($params['client_id']) ? trim($params['client_id']) : "";
//唯一标识(token或udid设备号)
$udid=isset($params['udid'])&&!empty($params['udid']) ? trim($params['udid']) : "";
//driver:司机客户端、customer:用户客户端
$version=isset($params['version'])&&!empty($params['version']) ? trim($params['version']) : "driver";
$city=isset($params['city'])&&!empty($params['city']) ? trim($params['city']) : "";
//司机工号
$driver_id=isset($params['driver_user'])&&!empty($params['driver_user']) ? trim($params['driver_user']) : "";

$app_ver = isset($params['app_ver']) ? $params['app_ver'] : '';

if (empty($client_id)||empty($udid)||empty($driver_id)) {
	$ret=array('code'=>2,'message'=>'参数不正确!');
	echo json_encode($ret);return;
}

if ( !in_array($version, array( 'driver','customer') ) ) {
	$ret=array('code'=>2,'message'=>'版本参数不正确!');
	echo json_encode($ret);return;
}


DriverStatus::model()->set_newpush_client($driver_id, $client_id);

$ret=array('code'=>0,'message'=>'上传成功');


echo json_encode($ret);return;


