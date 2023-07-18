<?php
/*
 * modify  zhanglimin 2013-06-08
 * 切换验证token方式
 */
$driver = DriverStatus::model()->getByToken($params['token']);
if (empty($driver) ||  $driver->token===null||$driver->token!==$params['token']) {
    $ret=array(
        'code'=>1,
        'message'=>'请重新登录'
    );
    echo json_encode($ret);
    return;
}

$notice['category'] = 0;
$notice['city_id'] = $driver->city_id;
$notice['driver_id'] = $driver_id;
$notice['pageSize'] = $params['pageSize'];
$notice['offset'] = $params['pageSize'] * ($params['pageNo'] - 1);

$noticeList = Notice::model()->getNewestList($notice);
if ($noticeList != 0) {
    $ret = array (
        'code'=>0,
        'noticeList'=>$noticeList,
        'is_newest'=>1,
        'message'=>'读取成功');
} else {
    $noticeList = Notice::model()->getNoticeListByClient($notice);
    $ret = array (
        'code'=>0,
        'noticeList'=>$noticeList,
        'is_newest'=>0,
        'message'=>'读取成功');
}
echo json_encode($ret);
return;