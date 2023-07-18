<?php
//需要写清楚注释 add by sunhongjing at 2013-5-19 

//获得司机评价信息 返回是否成功
$pageNo = isset($params['pageNo'])?$params['pageNo']:0;
$pageSize = isset($params['pageSize'])?$params['pageSize']:10;
$app_ver = isset($params['app_ver'])?$params['app_ver']:'0';
$driverID = $params['driverID'];
$token = isset($params['token']) ? trim($params['token']) : '';

//修改司机评价短信model，调用commentSms中的getList,缓存2小时 add by sunhongjing at 2013-5-19
$comments = CommentSms::model()->getList($pageNo, $pageSize, $driverID);

//$comments = CommentSms::getListByDriverID($pageNo, $pageSize, $driverID);

if ($comments){
	if($app_ver < '2.4.0'){
	    $ret = array(
		    'code'=>0,
		    'commentList'=>$comments,
		    'message'=>'读取成功');
	}else{
		if(empty($token)){
		    $ret=array('code'=>2 , 'message'=>'参数不正确!');
		    echo json_encode($ret);return;
		}

		// //验证token
		$driver = DriverStatus::model()->getByToken($token);
		if ($driver===null||$driver->token===null||$driver->token!==$token) {
		    $ret=array('code'=>1 , 'message'=>'token失效');
		    echo json_encode($ret);return;
		}
		
	    $total = $comments['total'];
	    unset($comments['total']);
	    $ret = array(
		    'code'=>0,
		    'commentList'=>$comments,
		    'total'=>$total,
		    'message'=>'读取成功');
	}
} else {
    $ret = array(
	    'code'=>2,
	    'message'=>'读取失败');
}

echo json_encode($ret);
