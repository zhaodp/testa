<?php
/**
 * 通过传过来的参数,给客户端来返回又拍云下载地址
 * User: zhanglimin
 * Date: 13-8-5
 * Time: 下午6:50
 */

$token = isset($params['token'])&&!empty($params['token']) ? trim($params['token']) : "";

$md5_file = isset($params['md5_file'])&&!empty($params['md5_file']) ? trim($params['md5_file']) : "";

$filesize = isset($params['filesize'])&&!empty($params['filesize']) ? trim($params['filesize']) : 0;

$source =  isset($params['source'])&&!empty($params['source']) ? strtolower(trim($params['source'])) : "restaurant";

if($source == "restaurant"){
  $type =  "image/jpeg";
}elseif($source == "audio"){
  $type =  "audio/amr";
}else{
    $ret = array (
        'code'=>2,
        'message'=>'source来源不正确'
    );
    echo json_encode($ret);
    return;
}

if(empty($token) || empty($md5_file) || empty($filesize) || $filesize < 0){
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

$info = UpyunUpload::model($source)->getUrlInfo($md5_file , $filesize , $type);
if(!$info['flag']){
    $ret = array (
        'code'=>2,
        'message'=>$info['msg'],
    );
    echo json_encode($ret);
    return;
}
$ret=array(
    'code'=>0,
    'info'=>$info['data'],
    'message'=>'成功!'
);
echo json_encode($ret);
return;

