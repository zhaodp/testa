<?php

$splash = array (
	"splash_image_url"=>Yii::app()->params['app_splash'], 
	"splash_duration"=>5, 
	"start_at"=>"2013-11-14", 
	"end_at"=>"2013-11-16");

$ret = array (
	'code'=>0, 
	'splash'=>$splash, 
	'message'=>'获取成功');

echo json_encode($ret);