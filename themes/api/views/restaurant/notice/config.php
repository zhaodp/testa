<?php
/**
 * 公告类型配置文件
 * User: zhanglimin
 * Date: 13-8-30
 * Time: 上午11:47
 */

$token=isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";

if (empty($token) ){
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

$userInfo = Yii::app()->db_readonly->createCommand()
    ->select('city')
    ->from('t_admin_user')
    ->where('user_id=:user_id', array(':user_id'=>$restaurantToken->user_id))
    ->queryRow();
if(empty($userInfo)){
    $ret=array(
        'code'=>2,
        'message'=>'获取信息失败!'
    );
    echo json_encode($ret);
    return;
}

//所有城市
$citys= Dict::items('city');
//去掉全部
unset($citys[0]);
if($userInfo['city'] == 0){
    $city_limit = $citys;
}else{
    $city_limit = array(
        $userInfo['city'] => $citys[$userInfo['city']],
    );
}

//公告分类
$categorys = NewNotice::$categorys;

$ret=array(
    'code'=>0,
    'city_limit'=>$city_limit,
    'citys'=>$citys,
    'categorys'=>$categorys,
    'message'=>'获取成功!'
);
echo json_encode($ret);
return;