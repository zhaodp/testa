<?php
/**
 * 软件上传接口 
 * @author aiguoxin
 * @version 2014-06-16
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$list = isset($params['list']) ? trim($params['list']) : '';

//test
// $list = array(
// 	array('package'=>'test',
// 		 'name'=>'test'),
	
// 	array('package'=>'test1',
// 		 'name'=>'test1'),
// );
// $list = json_encode($list);

if(empty($token)){
    $ret=array('code'=>2 , 'message'=>'参数不正确!');
    echo json_encode($ret);return;
}

if(empty($list)){
    $ret=array('code'=>2 , 'message'=>'软件列表为空!');
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

//解析url中引号
$list = htmlspecialchars_decode($list,ENT_QUOTES);

//解析list
$de_json = json_decode($list,TRUE);
$count_json = count($de_json);
for ($i = 0; $i < $count_json; $i++){
	$package = $de_json[$i]['package'];
	$name = $de_json[$i]['name'];
	//save to t_driver_software
	DriverSoftware::model()->addDriverSoftware($driver_id,$name,$package);
}


//返回成功信息
$ret=array('code'=>0 , 
	'message'=>'ok');
echo json_encode($ret);return;
