<?php
/**
 * 客户端API：c.driver.comment.list 获取司机的评价列表，不需要token
 * 
 * @author sunhongjing 2013-10-11
 * @param
 * 
 * @return json 成功返回成功信息，异常返回错误代码，需要附带返回结果的例子
 * @example
 */

//验证参数格式

$pageNo = isset($params['pageNo']) ? intval($params['pageNo']) : 0;
$pageSize = isset($params['pageSize']) ? intval($params['pageSize']) : '';
$driverID = isset($params['driverID']) ? trim($params['driverID']) : '';
if (empty($pageSize) || empty($driverID)) {
	$ret = array('code' => 2 , 'message' => '参数有误');
	return json_encode($ret);return ;
}

$comments = CommentSms::model()->getCombinedList($pageNo , $pageSize , $driverID);
//Update driver comment list,zhongfuhai commented at 2015/4/28
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