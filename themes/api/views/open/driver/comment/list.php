<?php
//需要写清楚注释 add by sunhongjing at 2013-5-19 

//获得司机评价信息 返回是否成功
$pageNo = isset($params['pageNo'])?$params['pageNo']:0;
$pageSize = isset($params['pageSize'])?$params['pageSize']:10;
$driverID = $params['driverID'];

//修改司机评价短信model，调用commentSms中的getList,缓存2小时 add by sunhongjing at 2013-5-19
$comments = CommentSms::model()->getList($pageNo, $pageSize, $driverID);

//$comments = CommentSms::getListByDriverID($pageNo, $pageSize, $driverID);

if ($comments){
	$ret = array(
		'code'=>0,
		'commentList'=>$comments,
		'message'=>'读取成功');
} else {
	$ret = array(
		'code'=>1,
		'message'=>'读取失败');
}

echo json_encode($ret);