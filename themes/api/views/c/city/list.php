<?php
/**
 * 客户端API：c.city.list 获取城市列表
 * 调用的url:
 * @author sunhongjing 2013-10-14
 * @param $params    $params['_callback']
 * 
 * @return json
 * @see  app.city.list
 * @since
 */



//redis
$tmp = RCityList::model()->getOpenCityList(true); //参数 true 排除线上开通的默认城市
$city = array_flip($tmp);

$ret = array(
	'code'=>0,
	'cityList'=>$city,
	'message'=>'');


echo json_encode($ret);

