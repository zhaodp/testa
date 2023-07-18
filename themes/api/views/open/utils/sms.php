<?php
/**
 * 用来封装发送短信的东西
 *该接口默认改用国都的通道,发送短信
 *
 * User: tuan
 * Date: 2/3/15
 * Time: 14:12
 */

EdjLog::info('open.utils.sms input ---- '.json_encode($params));
$phone      = isset($params['phone']) ? $params['phone'] : 0;
$message    = isset($params['message']) ? $params['message'] : '';
if(empty($phone) || empty($message)){
    $ret = array(
        'code' => 2,
        'message' => '参数错误',
    );
    echo json_encode($ret);return;
}

$ret = Sms::SendForActive($phone, $message);
if($ret){
    $ret = array(
        'code' => 0,
        'message' => '发送成功',
    );
    echo json_encode($ret);return;
}else{
    $ret = array(
        'code' => -1,
        'message' => '发送失败',
    );
    echo json_encode($ret);return;
}

