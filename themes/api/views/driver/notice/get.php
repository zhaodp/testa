<?php
$driver_id = DriverToken::model()->getDriverIdByToken($params['token']);
if ($driver_id) {
	$notice = Notice::model()->getNoticeByClient($params['notice_id']);
	
	$ret = array (
		'code'=>0, 
		'notice'=>$notice,
		'message'=>'读取成功');
} else {
	$ret = array (
		'code'=>1, 
		'message'=>'请重新登录');
}
echo json_encode($ret);