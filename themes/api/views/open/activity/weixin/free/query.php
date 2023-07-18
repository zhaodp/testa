<?php
/*******查询用户是否存在********/

Yii::import('application.models.schema.activity.*');
$openid = isset($params['openid']) ? $params['openid'] : '';
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

if(empty($openid)){
    $ret = array('code'=>2, 'message'=>'没有该用户信息');
    echo json_encode($ret);
    return;
}

$user = RWxUser::model()->getUser($act_name,$openid);
if($user){
	$is_share=0; //是否分享过
	$user = get_object_vars($user);
	$openid = $user['openid'];
	$userInfo=RWxUser::model()->getUserInfo($act_name,$openid);
	if($userInfo){
		$is_share=1;
	}
    $ret = array('code'=>0, 'openid'=>$user['openid'],'headurl'=>$user['headurl'],'nickname'=>$user['nickname'],'is_share'=>$is_share);
    echo json_encode($ret);
    return;
}else{
    $ret = array('code'=>2, 'message'=>'没有该用户信息');
    echo json_encode($ret);
    return;
}


