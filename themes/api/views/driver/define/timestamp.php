<?php
/**
 * 返回服务端时间
 */
$ret = array(
		'code'=>0,
		'timestamp'=>date('YmdHis',time()),
		'message'=>'success');

echo CJSON::encode($ret);