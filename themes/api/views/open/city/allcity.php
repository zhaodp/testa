<?php
/**
 * 获取所有城市列表
 */

$allcity = Dict::items('city');

$city = array();

foreach ($allcity as $key => $value) {
    if($key == '0'){
        continue;
    }
    $city[$value] = $key;
}

$ret = array(
	'code'=>0,
	'cityList'=>$city,
	'message'=>'');
echo json_encode($ret);

