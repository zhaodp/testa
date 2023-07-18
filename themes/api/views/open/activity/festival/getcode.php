<?php
/**
 * 春节活动获取验证码
 * @author cuiluzhe 20150109
 */
Yii::import('application.models.redis.*');
$phone =  isset($params['phone']) ? $params['phone'] : '';

if (empty($phone)) {
    $ret = array('code' => 2, 'message' => '参数错误');
    echo json_encode($ret);
    return;
}

$is_phone = Common::checkPhone($phone);
if( ! $is_phone ){
    $ret = array('code' => 2, 'message' => '手机号有误，请更正');
    echo json_encode($ret);
    return;
}
$code = rand(1000, 9999); //生成随机验证码
$set_ret = RFestival::model()->setFestivalCustomerCode($phone, $code);
if(!$set_ret){
    $ret = array('code' => 2, 'message' => '获取失败,请稍后重新获取');
    echo json_encode($ret);
    return;
}else{
    $message = '您的验证码为：'.$code.'，验证码将在5分钟后失效。';
    $ret_sms = Sms::SendSMS($phone, $message, Sms::CHANNEL_ZCYZ);
    if (empty($ret_sms)) {
        $ret = array('code' => 2, 'message' => '系统延迟，请稍后再试。');
        echo json_encode($ret);
        return;
    }
    $ret = array('code' => 0, 'message' => '验证码已成功发送。');
    echo json_encode($ret);
    return;
}
