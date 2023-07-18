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

$tmp = RCityList::model()->getOpenCityList(true);
$city = array();
//这里先转化一下，方便后续根据城市名称查编码，如果不需要编码，也需要遍历
foreach ($tmp as $key => $value) {
//    array_push($city, $value=>$key);
    $city[$value] = $key;
}

$ret = array(
	'code'=>0,
	'cityList'=>$city,
	'message'=>'');

echo json_encode($ret);

