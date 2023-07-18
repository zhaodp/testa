<?php
/**
 * 获取收入和余额信息 API
 * Created by JetBrains PhpStorm.
 * User: zhanglimin
 * Date: 13-5-25
 * Time: 上午10:37
 * To change this template use File | Settings | File Templates.
 */

$token = isset($params['token']) && !empty($params['token']) ? trim($params['token']) : "";

$driver = DriverStatus::model()->getByToken($token);

if ($driver === null || $driver->token === null || $driver->token !== $token) {
    $ret = array(
        'code' => 1,
        'message' => '请重新登录'
    );
    echo json_encode($ret);
    return;
}

$ecoins = 0;
//可用e币
$driver_ext = DriverExt::model()->getExt($driver->driver_id);
if($driver_ext){
    $ecoins = $driver_ext['total_wealth'];
}

$balance = $driver->account['balance'];
//2014-09-03 防止redis访问失败,错误提示司机信息费错误,从DB拿司机信息费余额
if($balance <= 300){
	$driverId = $driver->driver_id;
	if(empty($driverId)){
		$criteria = new CDbCriteria();
		$criteria->compare('authtoken', $token);
		$driverToken = DriverToken::model()->find($criteria);
		if($driverToken){
			$driverId = $driverToken->driver_id;
		}
	}
	$driverBalance = DriverBalance::model()->getDriverBalance($driverId);
	if($driverBalance){
		$balance = $driverBalance->balance;
	}
}

$ret = array(
    'code' => 0,
    'info' => array(
        'service_times' => !empty($driver->service['service_times']) ? $driver->service['service_times'] : 0,
        'balance' => (int)$driver->account['balance'], //司机端 采用 int 来展示,但是信息费扣取之后可能变味 float,需要强制转 int
        'yesterday' => (int)$driver->account['yesterday'],
        'month' => (int)$driver->account['month'],
        'total' => (int)$driver->account['total'],
        'read_flag' => $driver->read_flag == null ? 1 : $driver->read_flag,
        'ecoins' => $ecoins,
    ),
    'message' => '成功'
);
echo json_encode($ret);
return;
