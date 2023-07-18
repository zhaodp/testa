<?php
class CronCommand extends CConsoleCommand {
	/**
	 * 
	 * 将客户端用户访问的日志导入数据库
	 * @param string $log
	 */
	public function actionClientLog($path, $date = null) {
		if (!$date) {
			$date = date('Ymd', time()-86400);
		}
		
		$command = "grep 'request:driver' $path/MRSS.HttpProxy_$date.log > $path/tmp.log";
		passthru($command, $ret);
		
		file_get_contents("$path/tmp.log");
		
		$handle = @fopen("$path/tmp.log", 'r');
		if ($handle) {
			$i = 0;
			while (($buffer = fgets($handle))!==false) {
				$agent = '';
				$date = substr($buffer, 0, 19);
				
				$ret = preg_match('%pargma:(.*)%', $buffer, $match);
				if ($ret==1) {
					parse_str($match[1]);
				}
				
				$attr = array (
					'uuid'=>$uuid, 
					'longitude'=>intval($longitude*10000), 
					'latitude'=>intval($latitude*10000), 
					'created'=>strtotime($date));
				
				//拆分agent
				if ($agent) {
					$agent = urldecode($agent);
					$params = explode('|', $agent);
					$device = array (
						'device'=>@$params[0], 
						'os'=>@$params[1], 
						'version'=>@$params[2]);
					$attr = array_merge($attr, $device);
				}
				
				LogRequestDriver::$db = Yii::app()->dbstat;
				$log = new LogRequestDriver();
				$log->attributes = $attr;
				$log->save();
			}
			if (!feof($handle)) {
				echo "Error: unexpected fgets() fail\n";
			}
			fclose($handle);
		}
	
	}
	
	/**
	 * 客户订单趋势导入统计数据库
	 * php protected/yiic cron customerbookingtotal
	 */
	public function actionCustomerBookingTotal() {
		$statImport = new StatImport();
		$statImport->actionCustomerBookingTotal();
	}
	
	/**
	 * 客户订单汇总
	 * php protected/yiic cron customerbooking  --booking_date=20120525
	 */
	public function actionCustomerBooking($booking_date = null) {
		$statImport = new StatImport();
		$statImport->actionCustomerBooking($booking_date);
	}
	
	/**
	 * 处理包含未完成订单的客户订单汇总
	 * php protected/yiic cron customerbookingreload
	 */
	public function actionCustomerBookingReload() {
		$statImport = new StatImport();
		$statImport->actionCustomerBookingReload();
	}
	
	public function actionAprilCustomer($city_id = 0, $days = 30, $activeMonth = 4) {
		$statImport = new StatImport();
		$statImport->actionAprilCustomer($city_id, $days, $activeMonth);
	}
	
	public function actionAprilCustomerReload() {
		$statImport = new StatImport();
		$statImport->actionAprilCustomerReload();
	}
	
	/**
	 * 初始化客户订单汇总
	 * php protected/yiic cron customerbookinginitialize
	 */
	public function actionCustomerBookingInitialize() {
		$statImport = new StatImport();
		$statImport->actionCustomerBookingInitialize();
	}
	
	/**
	 * 初始化客户订单汇总
	 * php protected/yiic cron orderdailyinitialize
	 */
	public function actionOrderDailyInitialize() {
		$statImport = new StatImport();
		$statImport->actionOrderDailyInitialize();
	}
	
	/**
	 * 
	 * 订单情况导入统计数据库
	 * php protected/yiic cron orderdaily --created_start=20120525
	 */
	public function actionOrderDaily($created_start = null) {
		$statImport = new StatImport();
		$statImport->actionOrderDaily($created_start);
	}
	
	/**
	 * 
	 * 未结订单情况更新统计数据库
	 * php protected/yiic cron orderdailyreload
	 */
	public function actionOrderDailyReload() {
		$statImport = new StatImport();
		$statImport->actionOrderDailyReload();
	}
	
	/**
	 * 
	 * 客户Life time value情况导入统计数据库
	 * php protected/yiic cron customermonthlyltv --month_first_day=20120401
	 */
	public function actionCustomerMonthlyLTV($month_first_day = null) {
		$statImport = new StatImport();
		$statImport->actionCustomerMonthlyLTV($month_first_day);
	}
	
	/**
	 * 
	 * 客户Life time value情况导入统计数据库
	 * php protected/yiic cron customerdailyltv --month_first_day=20120401
	 */
	public function actionCustomerDailyLTV($month_first_day = null) {
		$statImport = new StatImport();
		$statImport->actionCustomerDailyLTV($month_first_day);
	}
	
	/**
	 * 
	 * 客户活跃度导入统计数据库
	 * php protected/yiic cron customerdailyactiveinitialize
	 */
	public function actionCustomerDailyActiveInitialize() {
		$statImport = new StatImport();
		$statImport->actionCustomerDailyActiveInitialize();
	}
	
	/**
	 * 
	 * 客户活跃度导入统计数据库
	 * php protected/yiic cron customerdailyactive --created_start=20120401
	 */
	public function actionCustomerDailyActive($created_start = null) {
		$statImport = new StatImport();
		$statImport->actionCustomerDailyActive($created_start);
	}
	
	/**
	 * 
	 * 司机在线情况导入统计数据库
	 * php protected/yiic cron worklog --snap_time_start=20120425
	 */
	public function actionWorkLog($snap_time_start = null) {
		$statImport = new StatImport();
		$statImport->actionWorkLog($snap_time_start);
	}
	
	/**
	 * 新老客户情况导入统计数据库
	 * php protected/yiic cron customerdailyinitialize
	 */
	public function actionCustomerDailyInitialize() {
		$statImport = new StatImport();
		$statImport->actionCustomerDailyInitialize();
	}
	
	/**
	 * 
	 * 新老客户情况导入统计数据库
	 * php protected/yiic cron customerdaily --current_date=20120401
	 */
	public function actionCustomerDaily($current_date = null) {
		$statImport = new StatImport();
		$statImport->actionCustomerDaily($current_date);
	}
	
	/**
	 * 
	 * 司机每日在线情况导入统计数据库
	 * php protected/yiic cron DriverTrack
	 */
	public function actionDriverTrack() {
		$statImport = new StatImport();
		$statImport->actionDriverTrack();
	}
	
	/**
	 * 
	 * 初始化司机每日在线情况导入统计数据库
	 * php protected/yiic cron DriverTrackInitialize
	 */
	public function actionDriverTrackInitialize() {
		$statImport = new StatImport();
		$statImport->actionDriverTrackInitialize();
	}
	
	/**
	 * 
	 * 司机每日在线情况重新导入统计数据库
	 * php protected/yiic cron DriverTrackReloadByMonth
	 */
	public function actionDriverTrackReloadByMonth() {
		$statImport = new StatImport();
		$statImport->actionDriverTrackReloadByMonth();
	}
}