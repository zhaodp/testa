<?php
/**
 * 活动API：验证手机号是否绑定过优惠劵
 * @param token
 * @author
 * @return json,成功信息，异常返回错误代码
 *
 */
$phone = isset($params['phone']) ? trim($params['phone']) : '';
if (empty($phone)) {
    $ret = array('code' => 0, 'message' => '您的手机号绑定过优惠劵');
    echo json_encode($ret);return;
}
$bind_result = CustomerBonus::model()->checkBind($phone);
if($bind_result){
    $ret = array('code' => 0, 'message' => '您的手机号绑定过优惠劵');
    echo json_encode($ret);return;
}
$ret = array('code' => 2, 'message' => '您的手机号未绑定过优惠劵');
echo json_encode($ret);return;