<?php
$token = $params['token'];

//查找司机登录信息
$ret = DriverToken::validateToken($token);

if(!$ret){
	$message = array('code'=>1004,'message'=>'登录已过期');
	echo json_encode($message);
	Yii::app()->end();
}

$json = json_encode(array (
	'min_fee'=>Yii::app()->params['minFee'], 
	'first_fee'=>Yii::app()->params['firstFee'],
	'second_fee'=>Yii::app()->params['secondFee'],
	'third_fee'=>Yii::app()->params['thirdFee'],
	'min_distince'=>Yii::app()->params['minDistance'],
	'fee_step'=>Yii::app()->params['feeStep'], 
	'distance_step'=>Yii::app()->params['distanceStep'], 
	'distance_fee_step'=>Yii::app()->params['distanceFeeStep'],
	'before_waiting_fee'=>Yii::app()->params['beforeWaitingFee'],
	'on_waiting_fee'=>Yii::app()->params['onWaitingFee'],
	'before_waiting_step'=>Yii::app()->params['beforeWaitingStep'],
	'on_waiting_step'=>Yii::app()->params['onWaitngStep'],
	'min_fee_hour'=>Yii::app()->params['minFeeHour'],
	'first_fee_hour'=>Yii::app()->params['firstFeeHour'],
	'second_fee_hour'=>Yii::app()->params['secondFeeHour'],
	'third_fee_hour'=>Yii::app()->params['thirdFeeHour']));

echo $json;
