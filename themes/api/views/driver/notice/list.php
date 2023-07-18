<?php
/*
 * modify  zhanglimin 2013-06-08
 * 切换验证token方式
 */
$params['pageNo'] = ( empty($params['pageNo']) || $params['pageNo']<=0 ) ? '1' : $params['pageNo'];

$driver = DriverStatus::model()->getByToken($params['token']);
if ( empty($driver) ||  $driver->token===null || $driver->token!==$params['token'] ) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}


$notice = array();
$notice['category'] = empty($params['category']) ? 0 : trim($params['category']);
$notice['city_id'] = $driver->city_id;
$notice['pageSize'] = empty($params['pageSize']) ? 5 : trim($params['pageSize']);
//$notice['id'] = empty($params['id'])  ? '' : $params['id'];

$notice['offset'] = $params['pageSize'] * ($params['pageNo'] - 1);
$noticeList = Notice::model()->getNoticeListByClient($notice);

$ret = array (
    'code'=>0,
    'noticeList'=>$noticeList,
    'message'=>'读取成功');
echo json_encode($ret);
return;
