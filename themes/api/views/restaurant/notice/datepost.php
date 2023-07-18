<?php
/**
 * 语音公告添加
 * User: zhanglimin
 * Date: 13-8-31
 * Time: 下午3:41
 */

$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";

$city_ids=isset($params['city_ids'])&&!empty($params['city_ids']) ? trim($params['city_ids']) : "";

$title=isset($params['title'])&&!empty($params['title']) ? trim($params['title']) : "";

$category = isset($params['category'])&&!empty($params['category']) ? trim($params['category']) : "";

$deadline = isset($params['deadline'])&&!empty($params['deadline']) ? trim($params['deadline']) : "";

$audio_length=isset($params['audio_length'])&&!empty($params['audio_length']) ? trim($params['audio_length']) : "";

$audio_url=isset($params['audio_url'])&&!empty($params['audio_url']) ? trim($params['audio_url']) : "";

if(empty($token) || empty($title) || empty($audio_url)){
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
    'method'=>'notice_datepost',
    'params'=>array(
        'city_ids' => $city_ids,
        'title'=>$title,
        'category'=>$category,
        'deadline'=>$deadline,
        'audio_second'=>$audio_length,
        'audio_url'=>$audio_url,
        'source'=>'IOS',
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
