<?php
$user = isset($params['user'])?$params['user']:'';
//$user = Yii::app()->user->name;

echo json_encode(array (
	"hello $user,waitting 10s,ok!".date(Yii::app()->params['formatDateTime'], time())));