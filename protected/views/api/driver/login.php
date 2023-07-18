<?php
//用手机的imei,电话号码登录
$imei = $params['imei'];
$phone = $params['phone'];

$ret = Driver::authenticate($imei, $phone);

echo json_encode($ret);