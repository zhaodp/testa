<?php
/**
 * 合力那边已经可以配置接口了，我们提供一个电话查询接口，定义返回值的规范，然后提供给他们做配置。
 * 在public接口里增加；
 * 查询的功能包括：
 * 3、分机查询：司机的id，返回司机工作电话号码（此功能为以后准备）
 * 
 * @author sunhongjing 2013-08-12
 * 
 */

$ret = array();

$phone = empty($params['phone']) ? '' : trim($params['phone']);
//$driver_id = empty($params['id']) ? '' : trim($params['id']);
$timestamp = empty($params['timestamp']) ? '' : trim($params['timestamp']);

//if ( ( empty($phone) && empty($driver_id) ) || empty($timestamp) ) {
if (  empty($phone) || empty($timestamp) ) {
	$ret=array('code'=>'-1' ,'message'=>'参数错误.');
	echo json_encode($ret);return ;
}

//超过10分钟，提示过期
if( ( time() - $timestamp ) > 600 ){
	$ret=array('code'=>'-2' ,'message'=>'链接超时.');
	echo json_encode($ret);return ;
}

//先不加缓存，调试通过后如果压力测试有性能问题再加。
//查询手机号，看是否是我们的司机

$driver = DriverStatus::model()->getByPhone($phone);


if( !empty($driver) ){
	$ret=array('code'=>'1' ,'driver_id'=>$driver ,'message'=>'成功');
}else{
	$ret=array('code'=>'1' ,'driver_id'=>'' ,'message'=>'成功');
}
echo json_encode($ret);return ;