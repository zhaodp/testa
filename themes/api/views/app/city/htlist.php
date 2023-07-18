<?php
/**
 * 获取城市列表
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-08-15
 */

$city = RCityList::model()->getOpenCityList(true);
$city = array_values($city);
$ret = array(
	'code'=>0,
	'cityList'=>$city,
	'message'=>'');
echo json_encode($ret);