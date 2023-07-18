<?php
/**
 * 需要写清楚注释，写库走队列 add by sunhongjing at 2013-5-19
 * 
 * Created by JetBrains PhpStorm.
 * User: zhanglimin
 * Date: 13-4-22
 * Time: 下午1:40
 * To change this template use File | Settings | File Templates.
 */
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

//以下代码由李玉卿更改，由原来直接读写数据库改为走队列,2013-05-19，利民以后要注意了，API绝对不能写库。
switch ($version) {
	case 'driver' :
		$driver=DriverStatus::model()->get($driver_id);
		$token=$driver->token;
		if (empty($token)) {
			$ret=array('code'=>1,'message'=>'token失效');
			echo json_encode($ret);return;
		}

        DriverStatus::model()->set_client_id_app_ver($driver_id, $client_id, $app_ver);

		$params=array(
				'client_id'=>$client_id,
				'udid'=>$udid,
				'version'=>$version,
				'city'=>$driver->city_id,
				'driver_id'=>$driver_id,
				'created'=>date('Y-m-d H:i:s')
		);
		
		//添加task队列
		$task=array(
				'method'=>'driver_push_register',
				'params'=>$params
		);
		//Queue::model()->putin($params=null,$queue_type=null)第二个参数是队列名称,可接受值见Queue::$queue_type_list;
		Queue::model()->putin($task,'register');
		
		//先把client_id更新到redis里 add by sunhongjing 2013-12-31
		$driver->client_id = $client_id;
		
		$ret=array('code'=>0,'message'=>'上传成功');	
		break;
		
	case 'customer' :
		$ret=array('code'=>0,'message'=>'上传成功');
		break;
	default :
		$ret=array('code'=>2,'message'=>'版本参数不正确!');
		break;
}
echo json_encode($ret);return;


