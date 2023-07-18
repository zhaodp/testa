<?php
/**
 * Created by PhpStorm.
 * User: aiguoxin
 * Date: 15/3/27
 * Time: 下午3:31
 */

//司管app当前版本
$appCode = empty($params['appCode']) ? '' : trim($params['appCode']);

$ret=array(
    'appCode' => '1',
    'appUrl'=>'http://d.edaijia.cn/driver/driverclient_2.5.7_20150317.apk'
);

echo JsonResponse::success($ret);
return;
