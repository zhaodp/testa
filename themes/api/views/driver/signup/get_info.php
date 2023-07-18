<?php

$city_id = isset($_GET['city_id']) ? (int)$_GET['city_id'] : '';
$send_times = isset($_GET['send_times']) ? (int)$_GET['send_times'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : '';


if(!$send_times ){
    $this->renderError(__LINE__, '请求错误');
}
$id = (int)$id;
$city_id = (int)$city_id;
//getDriverByScendTimes($send_times, $city_id = '', $start_id = '')
$res = DriverRecruitment::model()->getDriverByScendTimes($send_times,$city_id,$id);
if($res){
    $this->outputJson($res);
}else{
    $this->outputJson(null,'error','1');
}



