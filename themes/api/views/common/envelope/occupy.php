<?php
//使用红包
//check params
EdjLog::info('红包领取请求参数：'.serialize($params));
if(checkParams($params)){
    $ret=array('code'=>2, 'message'=>'参数不正确!');
    echo json_encode($ret);return;
}

//check driver has this envelope

//2.校验 driver token
$token  = $params['token'];
$driver_id = DriverToken::model()->getDriverIdByToken($token);
if(!$driver_id){
    $ret = array (
        'code'=>1,
        'message'=>'请重新登录');
    echo json_encode($ret);return;
}else{
    $params['driver_id'] = $driver_id;
}

$sn = $params['sn'];
$type = $params['type'];
//TODO ... 校验一下
$driver = Driver::model()->getDriverInfoByDriverId($driver_id);
$cityId = $driver['city_id'];
$order_id=isset($params['order_id'])?$params['order_id']:0;
//红包先占用,后充钱

$status = false;
$updateRet = EnvelopeExtend::model()->updateEnvelopeReceiveStatus($sn,$driver_id,1);
$cast = 0;
if(FinanceConstants::isSuccess($updateRet)){
    $cast = $updateRet['amount'];
    $status = OrderSettlement::model()->envelopeSettle($driver_id, $cityId, $sn, $cast,$order_id);
}
$ret = array(
    'code'  => 101,
    'message' => '系统忙碌,请稍候重试',
);

if($status){
    $format = '恭喜您获得信息费%s元';
    $ret = array(
        'code'  => 0,
        'message' => sprintf($format, $cast)
    );
}else{
    if(FinanceConstants::isNotSuccess($updateRet)){
        $ret = array(
            'code'  => $updateRet['code'],
            'message' => $updateRet['msg'],
        );
    }
}
EdjLog::info('envelope use return ---- '.json_encode($ret));
echo json_encode($ret);return;

function checkParams($params){
    return empty($params['token'])
        || empty($params['type'])
        || empty($params['sn']);
}