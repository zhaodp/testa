<?php
/**
 * 获取城市列表
 * @var $params['_callback']
 */

//$city = Dict::items('city');
//$subcity = array('0'=>'全部');
//$retcity = array_diff($city, $subcity);

//unset($city[0]);
//unset($city[2]);
//for($i=8;$i<=21;$i++){
//	unset($city[$i]);
//}

//redis
$city = RCityList::model()->getOpenCityList(true); //参数 true 排除线上开通的默认城市

$ret = array(
	'code'=>0,
	'cityList'=>$city,
	'message'=>'');


echo json_encode($ret);

