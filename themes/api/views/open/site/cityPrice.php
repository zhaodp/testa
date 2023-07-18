<?php
/**
 * 官网API：开通城市和价格表
 * @return json
 * @author duke 2014-10-08
 */

$identify = isset($params['identify']) ? $params['identify'] : '';
$showNew = (isset($params['show_type']) && strtolower($params['show_type']) == 'new') ? 'new' : 'old';

if( empty($identify) ){
    $ret = array('code' => 2 ,'message' => '参数有误');
    echo json_encode($ret);return ;
}


$res = CityConfig::model()->h5citylist($showNew);


if($res){
    $ret = array('code' => 1 , 'data' => $res , 'message' => '');
}else{
    $ret = array('code' => 0 , 'data' => '' , 'message' => '');
}

echo json_encode($ret);return;

