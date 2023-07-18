<?php
/**
 * 合力那边已经可以配置接口了，我们提供一个电话查询接口，定义返回值的规范，然后提供给他们做配置。
 * 在public接口里增加；
 * 查询的功能包括：
 * 1、红名单：VIP客户及绑定的客户电话，返回标识值
 * 
 * @author sunhongjing 2013-08-12
 * 
 */
$ret = array();

$phone = empty($params['phone']) ? '' : trim($params['phone']);
$timestamp = empty($params['timestamp']) ? '' : trim($params['timestamp']);

if ( empty($phone) || empty($timestamp) ) {
	$ret=array('code'=>'-1' ,'message'=>'参数错误.');
	echo json_encode($ret);return ;
}

//超过10分钟，提示过期
if( ( time() - $timestamp ) > 600 ){
	$ret=array('code'=>'-2' ,'message'=>'链接超时.');
	echo json_encode($ret);return ;
}

//先不加缓存，调试通过后如果压力测试有性能问题再加。
//查询手机号，看是否是Vip

$vip = VipPhone::model()->getPrimary($phone);

if( !empty($vip) ){
	$ret=array('code'=>'1' ,'vip'=>'1' ,'message'=>'成功');//是vip
}else{
	$ret=array('code'=>'1' ,'vip'=>'0' ,'message'=>'成功');//不是vip
}
echo json_encode($ret);return ;


