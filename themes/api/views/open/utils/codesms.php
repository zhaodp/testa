<?php
/**
 * 验证码专用短信接口
 * User: cuiluzhe
 * Date: 2015-03-31
 * Time: 15:24
 */

EdjLog::info('open.utils.codesms input ---- '.json_encode($params));
$phone      = isset($params['phone']) ? $params['phone'] : 0;
$message    = isset($params['message']) ? $params['message'] : '';
if(empty($phone) || empty($message)){
    $ret = array('code' => 2, 'message' => '参数错误');
    echo json_encode($ret);return;
}

$ret_sms = Sms::SendSMS($phone, $message, Sms::CHANNEL_ZCYZ);
if ($ret_sms) {
    $ret = array('code' => 0, 'message' => '验证码已成功发送。');
    echo json_encode($ret);return;
}
$ret = array('code' => 2, 'message' => '系统延迟，请稍后再试。');
echo json_encode($ret);return;

