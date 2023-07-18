<?php
/**
*	开机图片
*/


$data = array (
	"splash_image_url"=>"", //http://pic.edaijia.cn/client/spring_start.png
	"splash_duration"=>2, 
	"start_at"=>"2015-02-17 00:00:00", 
	"end_at"=>"2015-02-20 23:59:59");

$ret = array (
	'code'=>0, 
	'data'=>$data, 
	'message'=>'获取成功');

echo json_encode($ret);