<?php
/**
 * 创建订单(仿滴滴打车 用音频约车)
 * User: zhanglimin
 * Date: 13-8-5
 * Time: 下午7:23
 */
$ret=array(
    'code'=>0,
    'message'=>'成功!'
);
echo json_encode($ret);
return;

$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";

$lng  =isset($params['lng'])&&!empty($params['lng']) ? trim($params['lng']) : "";

$lat  =isset($params['lat'])&&!empty($params['lat']) ? trim($params['lat']) : "";

$gps_type = isset($params['gps_type'])&&!empty($params['gps_type']) ? trim($params['gps_type']) : "wgs84";

$order_street_name = isset($params['order_street_name'])&&!empty($params['order_street_name']) ? trim($params['order_street_name']) : "";

$audio_url_path = isset($params['audio_url_path'])&&!empty($params['audio_url_path']) ? trim($params['audio_url_path']) : "";

if(
    empty($token) ||
    empty($order_street_name) ||
    empty($audio_url_path)
){
    $ret = array(
        'code'=>2,
        'message'=>'参数错误');
    echo json_encode($ret);return;
}

$validate = CustomerToken::model()->validateToken($token);

if(empty($validate)){
    $ret=array(
        'code'=>1,
        'message'=>'token失效!'
    );
    echo json_encode($ret);
    return;
}

$data = array(
    'lng'=>$lng,
    'lat'=>$lat,
    'gps_type'=>$gps_type,
    'address'=>$order_street_name,
    'phone'=>$validate['phone'],
    'url'=>$audio_url_path,
);

//添加task队列
$task=array(
    'method'=>'customer_gen_order',
    'params'=>$data,
);
Queue::model()->putin($task,'order');

$ret=array(
    'code'=>0,
    'message'=>'成功!'
);
echo json_encode($ret);
return;