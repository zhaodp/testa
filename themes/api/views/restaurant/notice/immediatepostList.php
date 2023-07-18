<?php
/**
 * 即使语音列表
 * User: zhanglimin
 * Date: 13-8-31
 * Time: 下午3:03
 */

$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";

$pageSize=isset($params['pageSize'])&&!empty($params['pageSize']) ? intval(trim($params['pageSize'])) : 10;

$pageNo=isset($params['pageNo'])&&!empty($params['pageNo']) ? intval(trim($params['pageNo'])) : 1;


if (empty($token)){
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


$arr = array();
$arr['opt_user_id'] = $restaurantToken->user_id;
$arr['pageSize'] = $pageSize;
$arr['offset'] = $pageSize * ($pageNo - 1);
$list = AudioPromptlyLog::model()->getList($arr);

$ret = array (
    'code'=>0,
    'list'=>$list,
    'message'=>'读取成功'
);

echo json_encode($ret);
return;
