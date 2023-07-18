<?php
/**
 *  司机端e币月统计
 * @author aiguoxin
 * @version 2014-07-17
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$pageNo = ( empty($params['pageNo']) || $params['pageNo']<=0 ) ? 1 : $params['pageNo'];
$pageSize = ( empty($params['pageSize']) || $params['pageSize']<=0 ) ? 10 : $params['pageSize'];


if(empty($token)){
    $ret=array('code'=>2 , 'message'=>'token参数不正确!');
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

// $total_emoney=0;
$month_emoney=0;
$rank=0;
//可用e币
// $driver_ext = DriverExt::model()->getExt($driver_id);
// if($driver_ext){
//     $total_emoney = $driver_ext['total_wealth'];
// }
//本月排名
$stat_month=date("Y-m");
$rank = DriverWealthMonthStat::model()->getMonthCityRank($driver_id,$stat_month);
//本月e币
$monthWealth = DriverWealthMonthStat::model()->getWealthMonth($driver_id,$stat_month);
if($monthWealth){
    $month_emoney = $monthWealth['total'];
}
//分页获取
$list=DriverWealthDayStat::model()->getWealthList($driver_id,$pageNo,$pageSize);
$data = array();
foreach ($list as $driverWealthDayStat){
    $emoney = $driverWealthDayStat['total_wealth'];
    if($emoney != 0){
        $emoney = $emoney > 0 ? '+'.$emoney : $emoney;
    }
    $data[] = array(
        'emoney'=> $emoney,
        'date' => date("Y.m.d",strtotime($driverWealthDayStat['stat_day'])),
    );

}
//设置redis为已读
$driver->read_flag=1;

//返回成功信息
$ret=array('code'=>0 , 
	'message'=>'ok',
	// 'total'=>$total_emoney,
    'monthTotal'=>$month_emoney,
    'rank'=>$rank,
	'list'=>$data);
echo json_encode($ret);return;
