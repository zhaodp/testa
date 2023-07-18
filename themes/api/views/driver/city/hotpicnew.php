<?php
/**
 * get city hot map which driver in
 * @author aiguoxin
 * @version 2014-04-22
 * 
 */
Yii::import('application.models.schema.report.*');
require_once 'ReportDemandHeatmap.php';
//接收并验证参数
// $token = isset($params['token']) ? trim($params['token']) : '';
$left_lng = isset($params['left_lng']) ? trim($params['left_lng']) : '';
$left_lat = isset($params['left_lat']) ? trim($params['left_lat']) : '';
$right_lng = isset($params['right_lng']) ? trim($params['right_lng']) : '';
$right_lat = isset($params['right_lat']) ? trim($params['right_lat']) : '';
$callback=isset($_GET["callback"])?$_GET["callback"]:"";

// if(empty($token) || empty($left_lng) || empty($left_lat) || empty($right_lng) || empty($right_lat)){
//     $ret=array('code'=>2 , 'message'=>'参数不正确!');
//     $json_str=json_encode($ret);
// 	if(isset($callback)&&!empty($callback)){
// 		$json_str=$callback.'('.$json_str.')';
// 	}
// 	echo $json_str;Yii::app()->end();
// }

// // //验证token
// $driver = DriverStatus::model()->getByToken($token);
// if ($driver===null || $driver->token===null||$driver->token!==$token) {
//     $ret=array('code'=>1 , 'message'=>'token失效');
//     $json_str=json_encode($ret);
// 	if(isset($callback)&&!empty($callback)){
// 		$json_str=$callback.'('.$json_str.')';
// 	}
// 	echo $json_str;Yii::app()->end();
// }

//get position point list
$list = ReportDemandHeatmap::model()->getBlockPoint($left_lng,$left_lat,$right_lng,$right_lat);

//返回成功信息
$ret=array('code'=>0 , 'message'=>'ok', 'data'=>$list);
$json_str=json_encode($ret);
if(isset($callback)&&!empty($callback)){
	$json_str=$callback.'('.$json_str.')';
}
echo $json_str;Yii::app()->end();?>