<?php
/*******分享活动********/

Yii::import('application.models.schema.activity.*');
$openid = isset($params['openid']) ? $params['openid'] : '';
$headurl = isset($params['headurl']) ? $params['headurl'] : '';
$nickname = isset($params['nickname']) ? $params['nickname'] : '';
$query = isset($params['query']) ? $params['query'] : '0'; //0默认没有分享，1分享过

$act_name =  isset($params['act_name']) ? $params['act_name'] : '';
if (empty($act_name)) {
    $ret = array('code' => 8, 'message' => '活动已结束，敬请关注e代驾微信后续活动！关注e代驾微信号');
    echo json_encode($ret);
    return;
}
// $over_date = strtotime(WxFreeUser::OVER_TIME);
// if(time()>$over_date){
//     $ret = array('code' => 8, 'message' => '活动已结束，敬请关注e代驾微信后续活动！关注e代驾微信号');
//     echo json_encode($ret);
//     return;
// }

if (empty($params['openid'])) {
    $ret = array('code' => 2, 'message' => '微信帐号不能空');
    echo json_encode($ret);
    return;
}


$wxUser = $openid; //微信id
$headurl= $headurl; //头像
$nickname= $nickname; //昵称

/****获取该用户余额及帮助列表****/
$begin = microtime(TRUE);

//新用户,返回1
$res = RWxUser::model()->keyExist($act_name.'_'.RWxUser::WX_ACT_USER.$wxUser,0);

$end = microtime(TRUE);
$time=($end-$begin)*1000;
EdjLog::info('share....'.$wxUser.'判断是否新用户耗费:'.$time.'ms');


if($query){ //查询是否分享过
    $begin = microtime(TRUE);

    if($res){ //新用户
        $ret = array('code'=>0, 'message'=>'新用户','money'=>WxFreeUser::INIT_MONEY,'is_draw'=>0,'headurl'=>$headurl,'nickname'=>$nickname, 'list'=>array(),'is_share'=>0);
    }else{
        $userInfo=RWxUser::model()->getUserInfo($act_name,$wxUser);
        $list = RWxUser::model()->getHelpList($act_name,$wxUser);
        $ret = array('code'=>0, 'message'=>'成功','money'=>$userInfo['money'],'is_draw'=>$userInfo['is_draw'],'headurl'=>$userInfo['head_url'],'nickname'=>$userInfo['nickname'],'list'=>$list,'is_share'=>1);
    }
    echo json_encode($ret);

    $end = microtime(TRUE);
    $time=($end-$begin)*1000;
    EdjLog::info('share....'.$wxUser.'查询用户是否分享过耗费:'.$time.'ms');
    return;
}else{
    $begin = microtime(TRUE);
    $userInfo=RWxUser::model()->getUserInfo($act_name,$wxUser);
    $end = microtime(TRUE);
    $time=($end-$begin)*1000;
    EdjLog::info('share....'.$wxUser.'从redis获取用户信息耗费:'.$time.'ms');
if(empty($userInfo)){ //新用户
    $begin = microtime(TRUE);
    //初始化
    try{
    	WxFreeUser::model()->initData($act_name,$wxUser,$headurl,$nickname);
        RWxUser::model()->addUser($act_name,$wxUser,$headurl,$nickname);
    }catch(Exception $e){
    	$ret = array('code'=>0, 'message'=>'已经分享过','money'=>WxFreeUser::INIT_MONEY,'is_draw'=>0,'headurl'=>$headurl,'nickname'=>$nickname, 'list'=>array());
    	echo json_encode($ret);
    	return;
    }
    $ret = array('code'=>0, 'message'=>'成功','money'=>WxFreeUser::INIT_MONEY,'is_draw'=>0,'headurl'=>$headurl,'nickname'=>$nickname, 'list'=>array());
    echo json_encode($ret);
    $end = microtime(TRUE);
    $time=($end-$begin)*1000;
    EdjLog::info('share....'.$wxUser.'初始化用户信息到db耗费:'.$time.'ms');
    return;
}else{
    $begin = microtime(TRUE);
    //老客户,获取用户金额，帮助用户列表
    $list = RWxUser::model()->getHelpList($act_name,$wxUser);
    $ret = array('code'=>0, 'message'=>'成功','money'=>$userInfo['money'],'is_draw'=>$userInfo['is_draw'],'headurl'=>$userInfo['head_url'],'nickname'=>$userInfo['nickname'],'list'=>$list);
    echo json_encode($ret);
    $end = microtime(TRUE);
    $time=($end-$begin)*1000;
    EdjLog::info('share....'.$wxUser.'从redis获取用户帮助列表耗费:'.$time.'ms');
    return;
    }
}