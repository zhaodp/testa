<?php
/**
 * 即使语音添加
 * User: zhanglimin
 * Date: 13-8-31
 * Time: 下午2:20
 */

$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";

$city_id=intval($params['city_id']);

$audio_length=isset($params['audio_length'])&&!empty($params['audio_length']) ? trim($params['audio_length']) : "";

$audio_url=isset($params['audio_url'])&&!empty($params['audio_url']) ? trim($params['audio_url']) : "";

if (empty($token) || empty($audio_length) || empty($audio_url)){
    $ret=array(
        'code'=>2,
        'message'=>'参数不正确!'
    );
    echo json_encode($ret);
    return;
}

$restaurantToken = RestaurantToken::model()->validateToken($token);

if(empty($restaurantToken)){
    $ret=array(
        'code'=>1,
        'message'=>'token失效!'
    );
    echo json_encode($ret);
    return;
}


//添加task队列
$task=array(
    'method'=>'notice_immediatepost',
    'params'=>array(
        'city_id' => $city_id,
        'audio_url' => $audio_url,
        'audio_second' => $audio_length ,
        'created' => date("Y-m-d H:i:s") ,
        'opt_user_id' => $restaurantToken->user_id , //用户ID
    )
);

Queue::model()->putin($task,'task');

$ret=array(
    'code'=>0,
    'message'=>'成功'
);
echo json_encode($ret);
return;
