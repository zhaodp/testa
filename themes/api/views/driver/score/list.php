<?php
/**
 *  司机端代驾分明细 
 * @author aiguoxin
 * @version 2014-05-27
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$params['page_num'] = ( empty($params['page_num']) || $params['page_num']<=0 ) ? 1 : $params['page_num'];
$params['page_size'] = ( empty($params['page_size']) || $params['page_size']<=0 ) ? 20 : $params['page_size'];


if(empty($token)){
    $ret=array('code'=>2 , 'message'=>'参数不正确!');
    echo json_encode($ret);return;
}

// //验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver===null||$driver->token===null||$driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}
$driver_id=$driver->driver_id;

// test
// $driver_id='BJ9030';

if(empty($params['page_num']) || empty($params['page_size'])){
    $ret=array(
        'code'=>2,
        'message'=>'参数错误'
    );
    echo json_encode($ret);
    return;
}
$driverExt = DriverExt::model()->getDriverExt($driver_id);
if(empty($driverExt)){
    $ret=array('code'=>2 , 'message'=>'找不到司机EXT表信息');
    echo json_encode($ret);return;

}

$total=DriverPunishLog::model()->getPunishCount($driver_id);
$list=DriverPunishLog::model()->getPunishListByDriver($driver_id,$params['page_num'],$params['page_size']);


//返回成功信息
$ret=array('code'=>0 , 
	'message'=>'ok',
    'score'=>$driverExt['score'],
	'total'=>$total,
	'list'=>$list);
echo json_encode($ret);return;
