<?php
/**
 * 软件列表 
 * @author aiguoxin
 * @version 2014-06-16
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';

// if(empty($token)){
//     $ret=array('code'=>2 , 'message'=>'参数不正确!');
//     echo json_encode($ret);return;
// }

// //验证token
// $driver = DriverStatus::model()->getByToken($token);
// if ($driver===null||$driver->token===null||$driver->token!==$token) {
//     $ret=array('code'=>1 , 'message'=>'token失效');
//     echo json_encode($ret);return;
// }


$list=Software::model()->getSoftwareList();


//返回成功信息
$ret=array('code'=>0 , 
	'message'=>'ok',
	'list'=>$list);
echo json_encode($ret);return;
