<?php
/**
 * 客户端API：c.sendlogin 用户直接通过发送短信登录，获取发送的目标号码和短信内容
 * 调用的url:
 */
Yii::import('application.models.redis.*');
//用手机号码预登录 发送短信 返回是否成功
$phone = empty($params['phone']) ? '' : trim($params['phone']);
$udid = empty($params['udid']) ? '' : trim($params['udid']);
$macaddress = empty($params['macaddress']) ? '' : trim($params['macaddress']);
$device_type = empty($params['os']) ? 'unknown' : strtoupper(trim($params['os']));
$business = empty($params['business']) ? CustomerToken::EDJ_TOKEN_FROM : trim($params['business']);

if (empty($phone) || empty($udid)) {
    $ret = array('code' => 2, 'message' => '参数错误');
    echo json_encode($ret);
    return;
}

//增加对手机号格式的验证，add by sunhongjing 2014-01-04
$is_phone = Common::checkPhone($phone);
if( ! $is_phone ){
	$ret = array('code' => 2, 'message' => '手机号有误，请更正');
    echo json_encode($ret);
    return;
}


//一键登录相关 bidong 2014-1-18
$quickPwd=Common::makeRandCode(2);
$quickSms=$quickPwd."，直接发送短信以绑定手机号，10分钟内发送有效【e代驾】";
$quickLoginData = array('channel' => '106911892930', 'sms' =>$quickSms ,'limit'=>'60');
//保存一键注册短信内容
$customerLogic = new CustomerLogic();
$customerLogic->setQuickLoginSms($phone,$quickSms,$business);
//一键登录相关 bidong 2014-1-18

//返回json数据
$ret = array(
    'code' => 0,
    'data'=>$quickLoginData,
    );
echo json_encode($ret);
return;





