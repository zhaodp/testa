<?php
/*******支取代驾费用********/

Yii::import('application.models.schema.activity.*');
$act_name =  isset($params['act_name']) ? $params['act_name'] : '';
if (empty($act_name)) {
    $ret = array('code' => 8, 'message' => '活动已结束，敬请关注e代驾微信后续活动！关注e代驾微信号');
    echo json_encode($ret);
    return;
}
$over_date = strtotime(WxFreeUser::OVER_TIME);
if(time()>$over_date){
    $ret = array('code' => 8, 'message' => '活动已结束，敬请关注e代驾微信后续活动！关注e代驾微信号');
    echo json_encode($ret);
    return;
}

if (empty($params['openid'])) {
    $ret = array('code' => 2, 'message' => '微信帐号不能空');
    echo json_encode($ret);
    return;
}

$wxUser = $params['openid'];
$phone = $params['phone'];

if (empty($phone)) {
    $ret = array('code' => 2, 'message' => '领取的手机号不能为空');
    echo json_encode($ret);
    return;
}

//增加对手机号格式的验证
$is_phone = Common::checkPhone($phone);
if(!$is_phone){
	$ret = array('code' => 2, 'message' => '领取的手机号格式不正确');
    echo json_encode($ret);
    return;
}

//vip不能领取
if(CustomerMain::model()->isVip($phone)){
    $ret = array('code'=>'2','message'=>'vip客户不参与此次活动,请换个手机号');
    echo json_encode($ret);
    return;
}

//一个手机号只能领取一次
$phonekey= $act_name.'_'.RWxUser::WX_DRAW.$phone;
$res = RWxUser::model()->keyExist($phonekey,0);
//不成功返回0
if(empty($res)){
    $ret = array('code'=>'2','message'=>'手机号:'.$phone.'不能多次领取');
    echo json_encode($ret);
    return;
}


$userInfo=RWxUser::model()->getUserInfo($act_name,$wxUser);
if(empty($userInfo)){
    $ret = array('code'=>'2','message'=>'用户不存在');
    echo json_encode($ret);
    return;
}
if($userInfo['money'] < WxFreeUser::DRAW_MONEY){
    $ret = array('code'=>'2','message'=>'没有达到38领取标准');
    echo json_encode($ret);
    return;
}

$res = WxFreeUser::model()->drawMoney($act_name,$wxUser,$phone);

$message='领取成功';
if($res==1){
	$message='用户不存在';
}elseif ($res==2) {
	$message='没有到达38,不能支取';
}elseif ($res==3) {
	$message='不能重复领取';
}elseif ($res==4) {
    $message='手机号:'.$phone.'不能多次领取';
}
$ret = array('code'=>$res,'message'=>$message);
echo json_encode($ret);
return;