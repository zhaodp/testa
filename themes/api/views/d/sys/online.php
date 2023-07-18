<?php
/**
 * 司机端API：d.sys.online 司机端系统接口，检查是否个推在线
 *
 * @author sunhongjing 2014-01-02
 * @param token
 * @param
 *
 * @return json 成功返回成功信息，异常返回错误代码，需要附带返回结果的例子
 * @example
 */
$token 		= isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";
$udid		= isset($params['udid'])&&!empty($params['udid']) ? trim($params['udid']) : "";
$driver_id 	= isset($params['driver_id'])&&!empty($params['driver_id']) ? strtoupper(trim($params['driver_id'])) : "";
$client_id	= isset($params['client_id'])&&!empty($params['client_id']) ? trim($params['client_id']) : "";

if( empty($token) || empty($client_id) ){
	$ret=array( 'code'=>2, 'message'=>'参数错误');
    echo json_encode($ret);
    return;
}

$driver = DriverStatus::model()->getByToken($token);

if (empty($driver) || $driver->token===null||$driver->token!==$token) {
    $ret=array( 'code'=>1, 'message'=>'token失效');
    echo json_encode($ret);
    return;
}

if( $client_id != $driver->client_id ){
	$params=array(
			'client_id'=>$client_id,
			'udid'=>$udid,
			'version'=>'driver',
			'city'=>$driver->city_id,
			'driver_id'=>$driver->driver_id,
			'created'=>date('Y-m-d H:i:s')
	);	
	//添加task队列
	$task=array(
			'method'=>'driver_push_register',
			'params'=>$params
	);
	Queue::model()->putin($task,'register');
	$driver->client_id = $client_id;
}

//添加task队列
$task = array(
    'method' => 'push_wakeup_msg',
    'params' => array(
        'client_id' =>$driver->client_id,
        'flag' =>'driver',
    ),
);
Queue::model()->putin($task,'test');

$ret = array( 'code'=>0,'message'=>'请耐心等待，如果2分钟内未收到推送消息，确认网络连接正常。必要时可重启手机，');
echo json_encode($ret);
return;



