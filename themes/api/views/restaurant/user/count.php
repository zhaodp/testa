<?php
/**
 * 统计（今日、累积）商家
 * User: zhanglimin
 * Date: 13-9-24
 * Time: 下午5:02
 */

$token = isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";

if(empty($token)){
    $ret = array (
        'code'=>2,
        'message'=>'参数不正确'
    );
    echo json_encode($ret);
    return;
}

$restaurantToken = RestaurantToken::model()->validateToken($token);
if (empty($restaurantToken)) {
    $ret = array (
        'code'=>1,
        'message'=>'token失效'
    );
    echo json_encode($ret);
    return;
}

$count = Restaurant::model()->getCount($restaurantToken->user_id);

//返回数据
$ret = array (
    'code'=>0,
    'all'=>$count['all'],
    'today'=>$count['today'],
    'message'=>'获取成功');
echo json_encode($ret);
return;