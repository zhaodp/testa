<?php
/**
 * 提供给外界用来绑优惠码
 *
 * 外部提供的优惠码应该是可以直接给用户绑定的,这里默认不进行验证
 *
 * 如果 需要验证,那么就会走默认接口,发送的短信会为默认文案
 *
 * User: tuan
 * Date: 2/3/15
 * Time: 17:43
 */

EdjLog::info('open.utils.bind_bonus ---- '.json_encode($params));

$phone = isset($params['phone']) ? $params['phone'] : 0;
$bonusSn = isset($params['bonus_sn']) ? $params['bonus_sn'] : 0;
$num     = isset($params['num']) ? $params['num'] : 1;
$sms     = isset($params['sms']) ? $params['sms'] : '';
$isCheck  = isset($params['checked']) ? $params['checked'] : 0;
$bindStatus = 0;
if(empty($phone) || empty($bonusSn)){
    $ret = array(
        'code' => 2,
        'message' => '参数错误',
    );
    echo json_encode($ret);return;
}
if(!$isCheck && empty($sms)){
    $ret = array(
        'code' => 2,
        'message' => '参数错误',
    );
    echo json_encode($ret);return;
}
if($isCheck){
    $bindStatus = FinanceWrapper::bindBonusBySn($phone, $bonusSn);
}else{

    $bindStatus = FinanceWrapper::bindBonusGenerate($phone, $bonusSn, $num, $sms);
}
if($bindStatus){
    $ret = array(
        'code' => 0,
        'message' => '绑定成功',
    );
    echo json_encode($ret);return;
}else{
    $ret = array(
        'code' => -1,
        'message' => '绑定失败',
    );
    echo json_encode($ret);return;
}

