<?php
/**
 * 客户端注册
 * User: zhanglimin
 * Date: 13-8-5
 * Time: 下午2:26
 */

//android客户端有bug,这里先屏蔽了 add by sunhongjing 2013-10-21
$ret=array(
    'code'=>0,
    'message'=>'成功!'
);
echo json_encode($ret);
return;
//------------------临时屏蔽 



$token = isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";

//android
$client_id = isset($params['client_id'])&&!empty($params['client_id']) ? trim($params['client_id']) : "";

//兼容ios
$deviceToken = isset($params['deviceToken'])&&!empty($params['deviceToken']) ? trim($params['deviceToken']) : "";

//唯一标识(udid设备号)
$udid=isset($params['udid'])&&!empty($params['udid']) ? trim($params['udid']) : "";

//1 安卓 0 ISO
$type = isset($params['type']) ? intval($params['type']) : 1;

//兼容ios
if( 0==$type ){
	$client_id = empty($deviceToken) ? $client_id : $deviceToken;
}

if( empty($client_id) || empty($udid) ){
    $ret = array (
        'code'=>2,
        'message'=>'参数不正确'
    );
    echo json_encode($ret);
    return;
}

$phone = "";
if(!empty($token)){
    $validate = CustomerToken::model()->validateToken($token);
    if (empty($validate)) {
        $ret = array (
            'code'=>1,
            'message'=>'token失效'
        );
        echo json_encode($ret);
        return;
    }
    $phone = $validate['phone'];
}

$data = array(
    'udid' => $udid,
    'client_id' => $client_id,
    'type' => $type,
    'phone'=>$phone,
);


//添加task队列更新数据库
$task=array(
    'method'=>'customer_client_register',
    'params'=>$data,
);


Queue::model()->putin($task,'apptest');

$ret=array(
    'code'=>0,
    'message'=>'成功!'
);
echo json_encode($ret);
return;
