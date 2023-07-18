<?php
/**
 *  司机端当天e币统计
 * @author aiguoxin
 * @version 2014-07-18
 * 
 */
//接收并验证参数
$token = isset($params['token']) ? trim($params['token']) : '';
$date = isset($params['date']) ? trim($params['date']) : '';


if(empty($token)){
    $ret=array('code'=>2 , 'message'=>'token参数不正确!');
    echo json_encode($ret);return;
}

if(empty($date)){
    $ret=array('code'=>2 , 'message'=>'date参数不正确!');
    echo json_encode($ret);return;
}

//日期格式转换'2014.09.08'转'2014-09-08'
$date = strtr($date,array('.'=>'-'));

// //验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver===null||$driver->token===null||$driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}
$driver_id=$driver->driver_id;

// test
// $driver_id='BJ9030';

$today_emoney=0;
$rank=0;
$income=0;

//收入
$income = Order::model()->getIncomeByDay($driver_id,$date);
if($income == null){
    $income = 0;
}
//当天本市排名
$rank=DriverWealthDayStat::model()->getDayCityRank($driver_id,$date);

//各项列表
$list = array();
$count = 0;//条数计算
//当天e币
$wealth_log_list = DriverWealthLog::model()->getWealth($driver_id,$date);
foreach ($wealth_log_list as $log) {
    if($log['sum'] == 0 || $log['type'] == DriverWealthLog::REWARD_PUNISH_TYPE){
        continue;
    }
    $today_emoney += $log['sum'];
    $count = $log['sum']/DriverWealthLog::$typeWealth[$log['type']];
    if($log['type']==DriverWealthLog::HOT_ONLINE_TYPE){//转成小时
        $count = round($count*15/60,1).'小时';
    }
    //todo name需要调整
    $name = DriverWealthLog::$typeName[$log['type']].':'.$count;
    //恶劣天气奖励
    if($log['type']==DriverWealthLog::DRIVER_REWARD 
        || $log['type']==DriverWealthLog::INVEST_TYPE){
        $name=$log['des'];
    }
    $emoney = $log['sum'];
    $list[] = array(
        'name'=>$name,
        'emoney'=>$emoney,
    );

}

//奖惩类型单独处理
$reward_list = DriverWealthLog::model()->getWealthListByType($driver_id,$date,DriverWealthLog::REWARD_PUNISH_TYPE);
foreach($reward_list as $reward){
    $name = $reward['des'];
    $emoney = $reward['wealth'];
    $list[] = array(
        'name'=>$name,
        'emoney'=>$emoney,
    );
}

//返回成功信息
$ret=array('code'=>0 , 
	'message'=>'ok',
    'income'=>$income,
	'total'=>$today_emoney,
    'date'=>date("Y年m月d日",strtotime($date)),
    'rank'=>$rank,
	'list'=>$list);
echo json_encode($ret);return;
