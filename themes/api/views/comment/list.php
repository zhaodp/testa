<?php
//获得留言墙信息 返回是否成功

$pageNo = $params['pageNo'];
$pageSize = $params['pageSize'];

//需要优化，而且这里读的还是老表，应该读新表了。需要更改成新表，接口返回数据不变，并增加缓存。add by sunhongjing
//$comments = Comments::getList($pageNo, $pageSize);
$comments = CommentSms::model()->getList($pageNo, $pageSize);


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