<?php
/**
 * @deprecated 显赫确认已经不需要从客户端传在线时间到服务端
 * driver online time tongji
 * @author aiguoxin
 * @version 2014-04-14 
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';
$online_time = isset($params['online_time']) ? intval($params['online_time']) : 0;


// if(empty($token) || empty($driver_id)){
//     $ret=array('code'=>2 , 'message'=>'参数不正确!');
//     echo json_encode($ret);return;
// }

// // 验证token
// $driver = DriverStatus::model()->getByToken($token);
// if ($driver===null || $driver->token===null||$driver->token!==$token) {
//     $ret=array('code'=>1 , 'message'=>'token失效');
//     echo json_encode($ret);return;
// }
// $flag = DriverOnlineLog::model()->addDriverOnlineLog($driver_id,$online_time);
// if(!$flag){
// 	$ret=array('code'=>1 , 'message'=>'插入数据失败');
// 	echo json_encode($ret);return;
// }
//返回成功信息
$ret=array('code'=>0 , 'message'=>'成功');
echo json_encode($ret);return;
