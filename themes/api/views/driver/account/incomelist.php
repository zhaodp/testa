<?php
/**
 * 客户端API：c.coupon.list 优惠券列表
 * @param token
 * @author 
 * @return json,成功信息，异常返回错误代码
 * 
 */

$token = isset($params['token']) ? $params['token'] : '';
$pageNO = isset($params['page_num']) ? $params['page_num'] : 1;
$pageSize = isset($params['page_size']) ? $params['page_size'] : 10;
$year = isset($params['year']) ? $params['year'] : 0;
$month = isset($params['month']) ? $params['month'] : 0;


if (empty($token)) {
    $ret = array(
        'code' => 2,
        'message' => '参数错误'
    );
    echo json_encode($ret);
    return;
}

//验证token
$driver = DriverStatus::model()->getByToken($token);
if ($driver===null||$driver->token===null||$driver->token!==$token) {
    $ret=array('code'=>1 , 'message'=>'token失效');
    echo json_encode($ret);return;
}

$table_date=date("Ym");
//组合表名
if($year && $month){
    //默认当前月份
    if($month<10){
        $month='0'.$month;//补0
    }
    $table_date=$year.$month;
}

$list = EmployeeAccount::model()->getAccountIncomeList($driver->driver_id,$pageNO,$pageSize,$table_date);

$ret = array(
    'code' => 0,
    'balance'=>$driver->account['balance'],
    'yesterday' => $driver->account['yesterday'],
    'month' => $driver->account['month'],
    'total' => $driver->account['total'],
    'message' => '获取成功',
    'list' => $list
);
echo json_encode($ret);
