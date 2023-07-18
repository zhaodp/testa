<?php
/**
 * 支持用的嗅探服务
 *
 * User: tuan
 * Date: 14/11/4
 * Time: 21:16
 */

$db = Yii::app()->db_readonly;
$status = $db->getActive();//看看db 连接是否ok
$code = 0;
if(!$status){
	$code  = -1;
}

$ret = array(
	'code'	=> $code,
);
echo json_encode($ret);
return;