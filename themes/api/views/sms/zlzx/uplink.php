<?php
$params = $_GET;
$error = '';
if (empty($params['name']) || empty($params['src']) || empty($params['dest']) || empty($params['content']) || empty($params['time'])) {
	$error = mb_convert_encoding('error:参数有误','gbk','utf-8');
	echo $error;
	exit;
} 
$params['channel'] = 'zlzx';
$params['created'] = date('Y-m-d H:i:s');
$model = new SmsUplink();
$model->attributes = $params;
if ($model->save()) {
	echo 'success';
} else {
	$error = mb_convert_encoding('error:记录失败','gbk','utf-8');
	echo $error;
}
exit;