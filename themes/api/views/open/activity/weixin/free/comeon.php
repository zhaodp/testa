<?php
/*******给别人加油********/

Yii::import('application.models.schema.activity.*');
$wxUser = isset($params['openid']) ? $params['openid'] : ''; //分享用户
$helpUser = isset($params['helpopenid']) ? $params['helpopenid'] : ''; //帮助用户
$headurl = isset($params['headurl']) ? $params['headurl'] : ''; //帮助用户头像
$nickname = isset($params['nickname']) ? $params['nickname'] : ''; //帮助用户昵称
$query = isset($params['query']) ? $params['query'] : '0'; //0默认帮助，1查询
$act_name =  isset($params['act_name']) ? $params['act_name'] : '';
$over_date = strtotime(WxFreeUser::OVER_TIME);
if(time()>$over_date){
    $ret = array('code' => 8, 'message' => '活动已结束，敬请关注e代驾微信后续活动！关注e代驾微信号');
    echo json_encode($ret);
    return;
}
if (empty($act_name)) {
    $ret = array('code' => 8, 'message' => '活动已结束，敬请关注e代驾微信后续活动！关注e代驾微信号');
    echo json_encode($ret);
    return;
}
if (empty($wxUser)) {
    $ret = array('code' => 2, 'message' => '微信分享帐号不能空');
    echo json_encode($ret);
    return;
}

if (empty($helpUser)) {
    $ret = array('code' => 2, 'message' => '微信帮助帐号不能空');
    echo json_encode($ret);
    return;
}
$begin = microtime(TRUE);
//设置加油成功到缓存
$helpkey= $act_name.'_'.RWxUser::WX_HELP.$wxUser.$helpUser;
if($query){ //不为空则是查询
	$res = RWxUser::model()->getByKey($helpkey);
	$ret = array('code'=>'0','message'=>'没有帮助过','is_help'=>0);
	if($res){
		$ret = array('code'=>'0','message'=>'已经帮助过','is_help'=>1);
	}
	echo json_encode($ret);
	return;
}
$end = microtime(TRUE);
$time=($end-$begin)*1000;
EdjLog::info('comeon----user='.$wxUser.'|helper='.$helpUser.'查询redis看是否帮助过耗费:'.$time.'ms');

//不能给自己加油
if($wxUser == $helpUser){
	$ret = array('code'=>'2','message'=>'自己不能帮助自己');
	echo json_encode($ret);
	return;
}

$begin = microtime(TRUE);
//已经达到标准，不用再加油
$userInfo=RWxUser::model()->getUserInfo($act_name,$wxUser);
$end = microtime(TRUE);
$time=($end-$begin)*1000;
EdjLog::info('comeon----user='.$wxUser.'|helper='.$helpUser.'查询redis获取用户信息耗费:'.$time.'ms');

if(empty($userInfo)){
	$ret = array('code'=>'2','message'=>'用户不存在');
	echo json_encode($ret);
	return;
}
if($userInfo['money'] == WxFreeUser::DRAW_MONEY){
	$ret = array('code'=>'2','message'=>'用户不需要帮助了');
	echo json_encode($ret);
	return;
}

$begin = microtime(TRUE);
$res = RWxUser::model()->keyExist($helpkey,1);
$end = microtime(TRUE);
$time=($end-$begin)*1000;
EdjLog::info('comeon----user='.$wxUser.'|helper='.$helpUser.'查询redis是否帮助过keyExist耗费:'.$time.'ms');

//不成功返回0
if(empty($res)){
	$ret = array('code'=>'2','message'=>'不能重复帮助');
	echo json_encode($ret);
	return;
}

$key= $act_name.'_'.RWxUser::WX_ACT_USER.$wxUser;
$begin = microtime(TRUE);
//加油次数+1
$res = RWxUser::model()->automicIncr($key);
$end = microtime(TRUE);
$time=($end-$begin)*1000;
EdjLog::info('comeon----user='.$wxUser.'|helper='.$helpUser.'查询redis自增加油次数耗费:'.$time.'ms');

if($res){
	$begin = microtime(TRUE);
	$num = RWxUser::model()->getByKey($key);
	$end = microtime(TRUE);
	$time=($end-$begin)*1000;
	EdjLog::info('comeon----user='.$wxUser.'|helper='.$helpUser.'查询用户帮助第几次耗费:'.$time.'ms');

	if($num > $userInfo['help_num']){
		$ret = array('code'=>'2','message'=>'用户不需要帮助了');
		echo json_encode($ret);
		return;
	}
	$begin = microtime(TRUE);
	//判断帮助人是否存在,使用缓存中的值覆盖
	$user = RWxUser::model()->getUser($act_name,$helpUser);
	if($user){
		$user = get_object_vars($user);
		$headurl = $user['headurl'];
		$nickname = $user['nickname'];
	}
	RWxUser::model()->addUser($act_name,$helpUser,$headurl,$nickname);
	$end = microtime(TRUE);
	$time=($end-$begin)*1000;
	EdjLog::info('comeon----user='.$wxUser.'|helper='.$helpUser.'保存帮助信息到redis,免验证耗费:'.$time.'ms');

	//加油成功，更新一条日志
	$begin = microtime(TRUE);
	$res= WxFreeUser::model()->helpUser($act_name,$wxUser,$helpUser,$num,$headurl,$nickname);
	$end = microtime(TRUE);
	$time=($end-$begin)*1000;
	EdjLog::info('comeon----user='.$wxUser.'|helper='.$helpUser.'保存帮助信息到db总耗费:'.$time.'ms');

	if(empty($res)){
		$ret = array('code'=>'2','message'=>'不能重复帮助');
		echo json_encode($ret);
		return;
	}
	//更新缓存
	$begin = microtime(TRUE);
	RWxUser::model()->reloadHelpList($act_name,$wxUser);
	$end = microtime(TRUE);
	$time=($end-$begin)*1000;
	EdjLog::info('comeon----user='.$wxUser.'|helper='.$helpUser.'重新加载帮助列表到redis耗费:'.$time.'ms');

	$ret = array('code'=>'0','message'=>'帮助成功');
}else{
	$ret = array('code'=>'2','message'=>'帮助失败');
}
echo  json_encode($ret);
return;