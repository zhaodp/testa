<?php
/**
 * 获取司机帐户余额 API 
 * @author sunhongjing 2013-04-23
 * 
 */
$driver_id = DriverToken::model()->getDriverIdByToken($params['token']);
if ( !empty($driver_id) ) {
		
	$driverBalance = null;

	$driverBalance = EmployeeAccount::model()->getDriverBalances($driver_id);
	
	
	$balance = 	!empty($driverBalance) ? $driverBalance : 0;
	
	if($driverBalance){
		$ret = array ('code'=>0, 'balance'=>$balance,'message'=>'读取成功');
	}else{
		$ret = array ('code'=>1, 'balance'=>0,'message'=>'读取信息费失败');
	}
		
} else {
	$ret = array ('code'=>1, 'message'=>'请重新登录');
}
echo json_encode($ret);