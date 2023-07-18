<?php
/**
 * 获取白名单
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-07-18
 */

$token = isset($params['token']) ? $params['token'] : '';
//验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver) {
	$whitelist = CustomerWhiteList::model()->cache_list();
	$ret = array('code' => 0 , 'whitelist' => $whitelist , 'message' => '获取成功');
} else {
	$ret = array ('code'=>1 , 'message'=>'token失效');
}
echo json_encode($ret);
return ;
