<?php
class StatImport {
	/**
	 * 客户订单趋势导入统计数据库
	 * 
	 */
	public function actionCustomerBookingTotal() {
		$sql = "SELECT phone, MAX( driver_id ) AS driver_id, MIN( FROM_UNIXTIME( booking_time,  '%Y-%m-%d' ) ) AS first_booking, MAX( FROM_UNIXTIME( booking_time, '%Y-%m-%d' ) ) AS last_booking, COUNT( DISTINCT FROM_UNIXTIME( booking_time,  '%Y%m%d' ) ) AS booking_times
			FROM  `t_order` 
			WHERE `status` IN ( 1, 4 ) 
			GROUP BY phone";
		
		$command = Order::getDbMasterConnection()->createCommand($sql);
		$order_reports = $command->queryAll();
		
		$command->reset();
		$sql = "INSERT INTO t_customer_booking_total_report(telephone, city_id, first_booking, last_booking, booking_times ) VALUES (:telephone, :city_id, :first_booking, :last_booking, :booking_times)";
		$connection = Yii::app()->dbstat;
		
		foreach($order_reports as $data) {
			$command = $connection->createCommand($sql);
			
			$employee = Employee::model()->find('user=:user', array (
				':user'=>$data['driver_id']));
			$telephone = empty($data['phone']) ? '' : $data['phone'];
			$city_id = empty($employee) ? 1 : $employee->city_id;
			$first_booking = empty($data['first_booking']) ? '' : $data['first_booking'];
			$last_booking = empty($data['last_booking']) ? '' : $data['last_booking'];
			$booking_time = empty($data['booking_times']) ? '' : $data['booking_times'];
			
			$command->bindParam(":telephone", $telephone);
			$command->bindParam(":city_id", $city_id);
			$command->bindParam(":first_booking", $first_booking);
			$command->bindParam(":last_booking", $last_booking);
			$command->bindParam(":booking_times", $booking_time);
			
			$command->execute();
			$command->reset();
		}
	}
	
	/**
	 * 客户订单汇总
	 * 
	 */
	public function actionCustomerBooking($booking_date = null) {
		if (empty($booking_date)) {
			$booking_date = date('Y-m-d', time()-24*3600);
		} else {
			$booking_date = date('Y-m-d', strtotime($booking_date));
		}
		
		$connection = Yii::app()->dbstat;
		$sql = "DELETE FROM t_customer_booking_report WHERE booking_date = :booking_date";
		$command = $connection->createCommand($sql);
		$command->bindParam(":booking_date", $booking_date);
		
		$command->execute();
		$command->reset();
		$customer_booking = self::getCustomerBooking($booking_date);
		
		$sql = "INSERT INTO t_customer_booking_report(telephone, city_id, booking_date, by_application, by_callcenter, unfinished ) VALUES (:telephone, :city_id, :booking_date, :by_application, :by_callcenter, :unfinished)";
		
		foreach($customer_booking as $data) {
			
			$command = $connection->createCommand($sql);
			
			$telephone = $city_id = $booking_date = $by_application = $by_callcenter = $unfinished = 0;
			$telephone = empty($data['telephone']) ? '-' : $data['telephone'];
			$city_id = $data['city_id'];
			$booking_date = empty($data['booking_date']) ? '2012-01-01' : $data['booking_date'];
			$by_application = $data['by_application'];
			$by_callcenter = $data['by_callcenter'];
			$unfinished = $data['unfinished'];
			
			$command->bindParam(":telephone", $telephone);
			$command->bindParam(":city_id", $city_id);
			$command->bindParam(":booking_date", $booking_date);
			$command->bindParam(":by_application", $by_application);
			$command->bindParam(":by_callcenter", $by_callcenter);
			$command->bindParam(":unfinished", $unfinished);
			$command->execute();
			$command->reset();
		}
	}
	
	/**
	 * 处理包含未完成订单的客户订单汇总
	 * 
	 */
	public function actionCustomerBookingReload() {
		$connection = Yii::app()->dbstat;
		$sql = "SELECT DISTINCT booking_date FROM t_customer_booking_report WHERE unfinished > 0";
		$command = $connection->createCommand($sql);
		$order_unfinished_reports = $command->queryAll();
		
		foreach($order_unfinished_reports as $unfinished) {
			self::actionCustomerBooking($unfinished['booking_date']);
		}
	}
	
	/**
	 * 四月新进客户订单统计
	 * 
	 */
	public function actionAprilCustomer($city_id = 0, $days = 30, $activeMonth = 201204) {
		$connection = Yii::app()->dbstat;
		
		$firstBookingDateFrom = date("Y-m-d", strtotime($activeMonth.'01'));
		$firstBookingDateTo = date("Y-m-d", strtotime(($activeMonth+1).'01')-24*3600);
		if ($city_id==0) {
			$sql = "SELECT r.telephone, r.booking_date, (r.by_application + r.by_callcenter) AS booking_times FROM `t_customer_booking_report` AS r JOIN (
					SELECT telephone, MIN(booking_date) AS cc FROM `t_customer_booking_report` GROUP BY telephone HAVING cc BETWEEN '$firstBookingDateFrom' AND '$firstBookingDateTo') AS t ON t.telephone = r.telephone
					ORDER BY r.telephone, r.booking_date";
		} else {
			$sql = "SELECT r.telephone, r.booking_date, (r.by_application + r.by_callcenter) AS booking_times FROM `t_customer_booking_report` AS r JOIN (
					SELECT telephone, MIN(booking_date) AS cc FROM `t_customer_booking_report` GROUP BY telephone HAVING cc BETWEEN '$firstBookingDateFrom' AND '$firstBookingDateTo') AS t ON t.telephone = r.telephone
					WHERE  r.city_id = $city_id
					ORDER BY r.telephone, r.booking_date";
		}
		
		if ($activeMonth==0) {
			if ($city_id==0) {
				$sql = "SELECT r.telephone, r.booking_date, (r.by_application + r.by_callcenter) AS booking_times FROM `t_customer_booking_report` AS r JOIN (
					SELECT telephone, MIN(booking_date) AS cc FROM `t_customer_booking_report` GROUP BY telephone HAVING cc < '2012-04-01') AS t ON t.telephone = r.telephone
					WHERE r.booking_date > '2012-04-01'
					ORDER BY r.telephone, r.booking_date";
			} else {
				$sql = "SELECT r.telephone, r.booking_date, (r.by_application + r.by_callcenter) AS booking_times FROM `t_customer_booking_report` AS r JOIN (
					SELECT telephone, MIN(booking_date) AS cc FROM `t_customer_booking_report` GROUP BY telephone HAVING cc < '2012-04-01') AS t ON t.telephone = r.telephone
					WHERE r.booking_date > '2012-04-01' AND city_id = $city_id
					ORDER BY r.telephone, r.booking_date";
			}
		}
		//echo $sql; die();
		/*
		$sql = "SELECT r.telephone, r.booking_date, (r.by_application + r.by_callcenter) AS booking_times FROM `t_customer_booking_report` AS r JOIN (
				SELECT telephone, MIN(booking_date) AS cc FROM `t_customer_booking_report` GROUP BY telephone HAVING cc BETWEEN '2012-06-01' AND '2012-06-30') AS t ON t.telephone = r.telephone
				WHERE r.city_id = 1
				ORDER BY r.telephone, r.booking_date";
		
		$sql = "SELECT r.telephone, r.booking_date, (r.by_application + r.by_callcenter) AS booking_times FROM `t_customer_booking_report` AS r JOIN (
				SELECT telephone, MIN(booking_date) AS cc FROM `t_customer_booking_report` GROUP BY telephone HAVING cc < '2012-04-01') AS t ON t.telephone = r.telephone
				WHERE r.booking_date > '2012-04-01' AND city_id = 3
				ORDER BY r.telephone, r.booking_date";
		*/
		$command = $connection->createCommand($sql);
		$orders = $command->queryAll();
		$booking_times = array ();
		$phone = '';
		foreach($orders as $order) {
			if ($phone!=$order['telephone']) {
				$phone = $order['telephone'];
				$last_date = date_create($order['booking_date']);
				date_add($last_date, date_interval_create_from_date_string($days.' days'));
				$booking_times[$order['telephone']]['last_date'] = date_format($last_date, 'Y-m-d');
				$booking_times[$order['telephone']]['booking_times'] = $order['booking_times'];
			} else {
				if ($order['booking_date']<=$booking_times[$order['telephone']]['last_date']) {
					$booking_times[$order['telephone']]['booking_times'] = $booking_times[$order['telephone']]['booking_times']+$order['booking_times'];
				}
			}
		}
		$monthly_orders = array ();
		foreach($booking_times as $key=>$data) {
			$monthly_orders[$data['booking_times']] = $monthly_orders[$data['booking_times']]+1;
		}
		ksort($monthly_orders);
		//print_r($monthly_orders);
		$delsql = 'DELETE FROM t_new_customer_booking WHERE `month` = %d AND `city` = %d AND `range` = %d';
		
		$delstr = sprintf($delsql, $activeMonth, $city_id, $days);
		
		$command = $connection->createCommand($delstr);
		$command->execute();
		
		$sql = 'INSERT INTO 
					t_new_customer_booking(`month`, `city`, `times`, `customers`, `range`) 
				VALUES(%d, %d, %d, %d, %d)';
		
		foreach($monthly_orders as $key=>$value) {
			$sqlstr = sprintf($sql, $activeMonth, $city_id, $key, $value, $days);
			
			$command = $connection->createCommand($sqlstr);
			$command->execute();
		}
	}
	
	public function actionAprilCustomerReload() {
		$month = array (
			0, 
			201204, 
			201205, 
			201206, 
			201207);
		$days = array (
			30, 
			60, 
			90, 
			120, 
			150);
		$city = array (
			0, 
			1, 
			3);
		
		foreach($month as $mval) {
			foreach($days as $dval) {
				foreach($city as $cval) {
					self::actionAprilCustomer($cval, $dval, $mval);
				}
			}
		}
	}
	
	/**
	 * 初始化客户订单汇总
	 * 
	 */
	public function actionCustomerBookingInitialize() {
		$connection = Order::getDbMasterConnection();
		$sql = "SELECT DISTINCT FROM_UNIXTIME( booking_time, '%Y-%m-%d' ) AS booking_date FROM t_order";
		$command = $connection->createCommand($sql);
		$order_initialize_reports = $command->queryAll();
		
		foreach($order_initialize_reports as $initialize) {
			self::actionCustomerBooking($initialize['booking_date']);
		}
	}
	
	/**
	 * 初始化客户订单汇总
	 * 
	 */
	public function actionOrderDailyInitialize() {
		$connection = Order::getDbMasterConnection();
		$sql = "SELECT DISTINCT FROM_UNIXTIME( booking_time, '%Y%m%d' ) AS booking_date FROM t_order WHERE FROM_UNIXTIME( booking_time, '%Y-%m-%d' ) BETWEEN '2012-04-01' AND DATE_SUB( CURDATE() , INTERVAL 1 DAY ) ORDER BY booking_date";
		$command = $connection->createCommand($sql);
		$order_initialize_reports = $command->queryAll();
		
		//Yii::app()->dbstat->createCommand('TRUNCATE TABLE `t_daily_report_order`')->execute();
		

		foreach($order_initialize_reports as $initialize) {
			self::actionOrderDaily($initialize['booking_date']);
		}
	}
	
	/**
	 * 
	 * 订单情况导入统计数据库
	 * 
	 */
	public function actionOrderDaily($created_start = null) {
		if (!$created_start) {
			$created_start = date('Ymd', time()-86400);
			$created_end = date('Ymd');
		} else {
			$created_end = date('Ymd', strtotime($created_start)+86400);
		}
		
		$sql = "DELETE FROM t_daily_report_order 
				WHERE `current_date` = :current_date";
		$params = array (
			":current_date"=>$created_start);
		Yii::app()->dbstat->createCommand($sql)->execute($params);
		
		$citys = Dict::items('city');
		foreach($citys as $city_id=>$name) {			
			$by_call_center_canceled = self::getDailyOrder($city_id, array_merge(
				Order::$callcenter_sources, 
				Order::$callcenter_input_sources), array (
				Order::ORDER_CANCEL), $created_start, $created_end);
			$by_call_center_finished = self::getDailyOrder($city_id, array_merge(
				Order::$callcenter_sources, 
				Order::$callcenter_input_sources), array (
				Order::ORDER_COMPLATE, 
				Order::ORDER_NOT_COMFIRM), $created_start, $created_end);
			$by_call_center_unfinished = self::getDailyOrder($city_id, array_merge(
				Order::$callcenter_sources, 
				Order::$callcenter_input_sources), array (
				Order::ORDER_READY), $created_start, $created_end);
			$by_client_canceled = self::getDailyOrder($city_id, array_merge(
				Order::$client_source, 
				Order::$client_input_source), array (
				Order::ORDER_CANCEL), $created_start, $created_end);
			$by_client_finished = self::getDailyOrder($city_id, array_merge(
				Order::$client_source, 
				Order::$client_input_source), array (
				Order::ORDER_COMPLATE, 
				Order::ORDER_NOT_COMFIRM), $created_start, $created_end);
			$by_client_unfinished = self::getDailyOrder($city_id, array_merge(
				Order::$client_source, 
				Order::$client_input_source), array (
				Order::ORDER_READY), $created_start, $created_end);
			
			//$by_call_center = $by_call_center_finished + $by_call_center_unfinished;
			//$by_client = $by_client_finished + $by_client_unfinished;
			$canceled = $by_call_center_canceled+$by_client_canceled;
			$unfinished = $by_client_unfinished+$by_call_center_unfinished;
			
			$sql = "SELECT by_callcenter + by_application AS last_weekday 
					FROM t_daily_report_order 
					WHERE `current_date` = DATE_SUB(:current_date, INTERVAL 7 DAY) 
					AND city_id = :city_id";
			
			$params = array (
				":current_date"=>$created_start, 
				":city_id"=>$city_id);
			
			$last_weekday = Yii::app()->dbstat->createCommand($sql)->queryScalar($params);
			//$last_weekday = !$order_reports['last_weekday'] ? 0 : $order_reports['last_weekday'];
			
			$sql = "INSERT INTO t_daily_report_order(`current_date`, city_id, by_callcenter, by_application, canceled, last_weekday, unfinished) "." VALUES(:current_date, :city_id, :by_callcenter, :by_application, :canceled, :last_weekday, :unfinished) ";
			
			$params = array (
				":current_date"=>$created_start, 
				":city_id"=>$city_id, 
				":by_callcenter"=>$by_call_center_finished, 
				":by_application"=>$by_client_finished, 
				":canceled"=>$canceled, 
				":last_weekday"=>$last_weekday, 
				":unfinished"=>$unfinished);
			
			Yii::app()->dbstat->createCommand($sql)->execute($params);
		}
	}
	
	public function actionTransmissionDriverImei() {
		$sql = "SELECT
					imei,
					user,
					city_id
				FROM  `t_driver`
				WHERE user <> '' AND city_id <> 0 AND mark = 0 AND imei <> ''";

		$command = Yii::app()->db->createCommand($sql);

		$employee = $command->queryAll();
		$query = Yii::app()->dbstat;
		$query->createCommand('delete from `t_imei_city`')->execute();
		foreach($employee as $data) {
			$sql = "select imei from t_imei_city where imei='".$data['imei']."'";
			$command = $query->createCommand($sql);
			if(count($command) > 0) {
				continue;
			}
			else {
				$sql = "INSERT INTO t_imei_city(imei, user, city_id) VALUES('".$data['imei']."', '".$data['user']."', '".$data['city_id']."')";
				$query->createCommand($sql)->execute();
			}
			
		}
	}
	/**
	 * 每日在线司机数入统计数据库
	 * 
	 */
	public function actionDriverActive($day = '') {
		$day = $day=='' ? date('Y-m-d', time()-24*3600) : date('Y-m-d', strtotime($day));
		$month = $day=='' ? date('Ym', time()-24*3600) : date('Ym', strtotime($day));
		$sql = "INSERT INTO t_daily_driver_online_by_track(driver_count, `current_date`, city_id)
				SELECT 
					COUNT( imei ) AS driver_count, 
					insert_date, 
					city_id
				FROM (
					SELECT 
						DISTINCT t.imei, 
						LEFT( t.insert_time, 10 ) AS insert_date, 
						d.city_id
					FROM 
						`t_employee_track_$month` AS t JOIN t_imei_city AS d ON LTRIM(RTRIM(d.imei)) = LTRIM(RTRIM(t.imei))
					WHERE 
						t.state =0
						AND LEFT( t.insert_time, 10 ) = '$day'
					GROUP BY 
						insert_date, 
						t.imei, 
						d.city_id
				) AS total
				GROUP BY 
					city_id";
		$query = Yii::app()->dbstat;
		$query->createCommand($sql)->execute();
	}
	
	/**
	 * 每日平均订单数入统计数据库
	 * 
	 */
	public function actionDriverOrderDaily($day = '') {
		
		$month = $day=='' ? date('Ym') : date('Ym', strtotime($day));
		$day = $day=='' ? date('Y-m-d', time()-24*3600) : date('Y-m-d', strtotime($day));
		
		$sql = "SELECT 
					SUM(IF((t1 = -5), t1, 0)) / -5  + SUM(IF((t1 = -10), t1, 0)) / -10 + SUM(IF((t1 = -15), t1, 0)) / -15 + SUM(IF((t1 = -20), t1, 0)) /-20 AS x1, 
					SUM(t0) AS x2,
					COUNT(DISTINCT user) AS userCount,
					FROM_UNIXTIME(created, '%Y-%m-%d') AS current_day, 
					city_id
				FROM  `t_view_employee_account` 
				WHERE city_id <> 0 AND FROM_UNIXTIME(created, '%Y-%m-%d') = '$day'
				GROUP BY current_day, city_id";
		
		$command = Yii::app()->db_readonly->createCommand($sql);
		
		$monthdailyorder = $command->queryAll();
		
		$sql = "SELECT 
					driver_count, 
					`current_date`, 
					city_id 
				FROM 
					t_daily_driver_online_by_track
				WHERE
					`current_date` = '$day'";
		$query = Yii::app()->dbstat;
		$statcommand = $query->createCommand($sql);
		
		$monthdailydriver = $statcommand->queryAll();
		
		foreach($monthdailyorder as $data) {
			$onlineDriver = 0;
			foreach($monthdailydriver as $driver) {
				if ($data['current_day']==$driver['current_date']&&$data['city_id']==$driver['city_id']) {
					$onlineDriver = $driver['driver_count'];
				}
			}
			
			$sql = "INSERT INTO  
						t_daily_driver_order(`current_date`, city_id, order_driver_count, order_count, order_money, online_driver_count)
					VALUES ('".$data['current_day']."', '".$data['city_id']."', '".$data['userCount']."', '".$data['x1']."', '".$data['x2']."', '".$onlineDriver."')";
			
			$query->createCommand($sql)->execute();
		}
	}
	
	/**
	 * 每日平均订单数入统计数据库 -- 月度更新
	 * @param $month = 201205
	 */
	public function actionDriverOrderDailyReload($month = '') {
		$month = $month=='' ? date('Ym', time()-20*24*3600) : $month;
		
		$sql = "SELECT 
					SUM(IF((t1 = -5), t1, 0)) / -5  + SUM(IF((t1 = -10), t1, 0)) / -10 + SUM(IF((t1 = -15), t1, 0)) / -15 + SUM(IF((t1 = -20), t1, 0)) /-20 AS x1, 
					SUM(t0) AS x2,
					COUNT(DISTINCT user) AS userCount,
					FROM_UNIXTIME(created, '%Y-%m-%d') AS current_day, 
					city_id
				FROM  `t_view_employee_account` 
				WHERE city_id <> 0 AND FROM_UNIXTIME(created, '%Y%m') = $month
				GROUP BY current_day, city_id";
		
		$command = Yii::app()->db_readonly->createCommand($sql);
		
		$monthdailyorder = $command->queryAll();
		
		foreach($monthdailyorder as $data) {
			$sql = "UPDATE 
						t_daily_driver_order 
					SET 
						order_driver_count = '".$data['userCount']."',
						order_count = '".$data['x1']."',
						order_money = '".$data['x2']."',
					WHERE 
						`current_date` = '".$data['current_day']."' 
						AND city_id = '".$data['city_id']."'  ";
			
			Yii::app()->dbstat->createCommand($sql)->execute();
		}
	}
	
	/**
	 * 
	 * 客户Life time value情况导入统计数据库
	 * 
	 */
	public function actionCustomerDailyLTV($month_first_day = null) {
		
		$connection = Yii::app()->dbstat;
		$sql = "INSERT INTO t_daily_report_customer_fee(`current_day`, `current_month`, city_id, name, telephone, by_callcenter, fee_callcenter, by_application, fee_application) VALUES(:current_day, :current_month, :city_id, :name, :telephone, :by_callcenter, :fee_callcenter, :by_application, :fee_application)";
		
		$get_sql = 'SELECT telephone FROM t_daily_report_customer_fee WHERE current_day = :current_day AND telephone = :telephone';
		
		$update_sql = 'UPDATE t_daily_report_customer_fee SET by_callcenter = :by_callcenter, fee_callcenter = :fee_callcenter, by_application = :by_application, fee_application = :fee_application WHERE current_day = :current_day AND telephone = :telephone';
		
//		$command = Yii::app()->db_readonly->createCommand("SELECT code, name FROM t_dict WHERE dictname = 'city_prefix'");
//
//		$city_prefixs = $command->queryAll();
		
		if (empty($month_first_day))
			$month_first_day = date('Ym')."01";
		
		$month = date('Ym', strtotime($month_first_day));
		$format_date = date('Y-m',strtotime($month_first_day));
		for ($i = 1; $i <= date('t', strtotime($month_first_day)); $i++){
			if ($i < 10) {
				$current_day = $month . '0' . $i;
                $f_date = $format_date .'-0'.$i;
			} else {
				$current_day = $month . $i;
                $f_date = $format_date."-".$i;
			}
			
			$dailyLTV = self::getDailyCustomerLTV(array (
				Order::ORDER_COMPLATE, 
				Order::ORDER_NOT_COMFIRM), $f_date);
			$j = 0;
			
			if( !empty( $dailyLTV['records'] ) ){
				
				foreach($dailyLTV['records'] as $record) {
					
					$by_callcenter = isset($record['callcenter_times']) ? $record['callcenter_times'] : 0;
					$fee_callcenter = isset($record['callcenter_fee']) ? $record['callcenter_fee'] : 0;
					$by_application = isset($record['app_times']) ? $record['app_times'] : 0;
					$fee_application = isset($record['app_fee']) ? $record['app_fee'] : 0;
					$name = empty($record['name']) ? '-' : $record['name'];
					$city_id = empty($record['city_id']) ? 1 : $record['city_id'];
					$phone = empty($record['phone']) ? '-' : $record['phone'];
					
					$get_command = $connection->createCommand($get_sql);
					$get_command->bindParam(":current_day", $current_day);
					$get_command->bindParam(":telephone", $phone);
					$exist = $get_command->query();
					$existphone = '';
					foreach ($exist as $value){
						if (isset($value['telephone']))
							$existphone = $value['telephone'];
					}
					if ($existphone == $phone){
						$update_command = $connection->createCommand($update_sql);
						
						$update_command->bindParam(":current_day", $current_day);
						$update_command->bindParam(":telephone", $phone);
						$update_command->bindParam(":by_callcenter", $by_callcenter);
						$update_command->bindParam(":fee_callcenter", $fee_callcenter);
						$update_command->bindParam(":by_application", $by_application);
						$update_command->bindParam(":fee_application", $fee_application);
						
						$update_command->execute();
					} else {
						$command = $connection->createCommand($sql);
						
						$command->bindParam(":current_day", $current_day);
						$command->bindParam(":current_month", $dailyLTV['month']);
						$command->bindParam(":city_id", $city_id);
						$command->bindParam(":name", $name);
						$command->bindParam(":telephone", $phone);
						$command->bindParam(":by_callcenter", $by_callcenter);
						$command->bindParam(":fee_callcenter", $fee_callcenter);
						$command->bindParam(":by_application", $by_application);
						$command->bindParam(":fee_application", $fee_application);
						
						$command->execute();
					}
	//				$j++;
	//				if ($j > 5){
	//					$j = 0;
	//					break;
	//				}
				}
			}
			
			echo $current_day. "\n";
		}
	}
	
	/**
	 * 
	 * 客户Life time value情况导入统计数据库
	 * 
	 */
	public function actionCustomerMonthlyLTV($month_first_day = null) {
		
		$connection = Yii::app()->dbstat;
		$sql = "INSERT INTO t_monthly_report_customer_fee(`current_month`, city_id, name, telephone, by_callcenter, fee_callcenter, by_application, fee_application) "." VALUES(:current_month, :city_id, :name, :telephone, :by_callcenter, :fee_callcenter, :by_application, :fee_application)";
		
		$command = $connection->createCommand($sql);
		
		if (empty($month_first_day)) {
			$month_first_day = date('Ym')."01";
			$month = date('Ym');
		}
		
		if (!empty($month_first_day)) {
			$delcommand = $connection->createCommand("DELETE FROM t_monthly_report_customer_fee WHERE `current_month` = :current_month");
			$month = date('Ym', strtotime($month_first_day));
			$delcommand->bindParam(":current_month", $month);
			$delcommand->execute();
		}
		
//		$monthlyLTV = self::getMonthlyCustomerLTV(array (
//			Order::ORDER_COMPLATE, 
//			Order::ORDER_NOT_COMFIRM), $month);

		$monthlyLTV = self::getMonthlyCustomerLTVFromDaily($month);
		
		foreach($monthlyLTV as $record) {
			
			$by_callcenter = isset($record['callcenter_times']) ? $record['callcenter_times'] : 0;
			$fee_callcenter = isset($record['callcenter_fee']) ? $record['callcenter_fee'] : 0;
			$by_application = isset($record['app_times']) ? $record['app_times'] : 0;
			$fee_application = isset($record['app_fee']) ? $record['app_fee'] : 0;
			$name = empty($record['name']) ? '-' : $record['name'];
			$city_id = empty($record['city_id']) ? 1 : $record['city_id'];
			$phone = empty($record['phone']) ? '-' : $record['phone'];
			$command->bindParam(":current_month", $month);
			$command->bindParam(":city_id", $city_id);
			$command->bindParam(":name", $name);
			$command->bindParam(":telephone", $phone);
			$command->bindParam(":by_callcenter", $by_callcenter);
			$command->bindParam(":fee_callcenter", $fee_callcenter);
			$command->bindParam(":by_application", $by_application);
			$command->bindParam(":fee_application", $fee_application);
			
			$command->execute();
		}
	}
	
	/**
	 * 
	 * 未结订单情况更新统计数据库
	 * 
	 */
	public function actionOrderDailyReload() {
		$command = Yii::app()->dbstat->createCommand();
		$order_reports = $command->select('current_date')->from('t_daily_report_order')->where('unfinished > 0')->queryAll();
		foreach($order_reports as $key=>$data) {
			self::actionOrderDaily(date('Ymd', strtotime($data['current_date'])));
		}
	}
	
	/**
	 * 
	 * 客户活跃度导入统计数据库
	 * 
	 */
	public function actionCustomerDailyActiveInitialize() {
		$connection = Yii::app()->db;
		$sql = "SELECT DISTINCT FROM_UNIXTIME( created, '%Y%m%d' ) AS created_date FROM t_log_request_driver WHERE FROM_UNIXTIME( created, '%Y-%m-%d' ) BETWEEN '2012-04-01' AND DATE_SUB( CURDATE() , INTERVAL 1 DAY ) ORDER BY created_date";
		$command = $connection->createCommand($sql);
		$customer_initialize_reports = $command->queryAll();
		
		Yii::app()->dbstat->createCommand('TRUNCATE TABLE `t_daily_report_customer_active`')->execute();
		
		foreach($customer_initialize_reports as $initialize) {
			self::actionCustomerDailyActive($initialize['created_date']);
		}
	}
	
	/**
	 *
	 * 客户活跃度导入统计数据库
	 *
	 */
	public function actionCustomerDailyActive($created_start = null) {
		if (!$created_start) {
			$table_date = date('Ym',time()-86400);
			$current_date = date('Y-m-d', time()-86400);
			$created_start = $current_date . " 07:00";
			$created_end = date('Y-m-d') . " 07:00";
		} else {
			$table_date = date('Ym',strtotime($created_start));
			$current_date = $created_start;
			$created_start = $current_date . " 07:00";
			$created_end = date('Y-m-d', strtotime($created_start) + 86400) . " 07:00";
		}
		$connection_readonly = Yii::app()->dbstat_readonly;
		$connection = Yii::app()->dbstat;

		//导入t_api_log_日期  数据
		$str = "";//记录macaddress
		$status = "";//记录status
		$actives=0; //活跃用户
		$fresh_actives=0;  //新用户
		$repeat_active=0;  //老用户
		$table_name = 't_api_log_'.$table_date;
		$source = $connection_readonly->createCommand()
		->select('COUNT(1) as count , macaddress, method, created, source')
		->from($table_name)
		->where("macaddress !=  '' and created between :startTime and :endTime")
		->group('macaddress, method')
		->queryAll(true,array(":startTime"=>$created_start,":endTime"=>$created_end));
			
		if(count($source) > 0){
			foreach($source as $source)
			{
				//检查当前信息是否存在
				$table_log_name = 't_api_log_count_'.$table_date;
				$sql = 'SELECT *
								FROM '.$table_log_name.' 
								WHERE macaddress = :macaddress
								AND created = :created';
				$commandExistence = $connection->createCommand($sql);
				$commandExistence->bindParam(":macaddress",$source['macaddress']);
				$commandExistence->bindParam(":created",$source['created']);
				$isExistence = $commandExistence->queryRow();

				if(!$isExistence){
					if($str != $source['macaddress']){
						$str = $source['macaddress'];
						$status = $this->isCustomerMacaddress($source);
						$status!= 0 ? $repeat_active ++ : $fresh_actives++;
					}
					$sql = 'INSERT INTO t_api_log_count_'.$table_date.'(method, macaddress, source, status, count_day, created) VALUES(:method, :macaddress, :source, :status, :count_day, :created)';
					$command = $connection->createCommand($sql);
					$command->bindParam(":method", $source['method']);
					$command->bindParam(":macaddress", $source['macaddress']);
					$command->bindParam(":source", $source['source']);
					$command->bindParam(":status", $status);
					$command->bindParam(":count_day", $source['count']);
					$command->bindParam(":created", $source['created']);
					$command->execute();
					$command->reset();
					echo $source['method']."\n";
				}
			}
		}
			
		$actives = $fresh_actives + $repeat_active;
		//导入t_daily_report_customer_active
		$sql = "delete from t_daily_report_customer_active where `current_date`=:current_date;
				INSERT INTO t_daily_report_customer_active(`current_date`, active_customer, fresh, `repeat`) 
				VALUES(:current_date, :active_customer, :fresh, :repeat)";

		$command = $connection->createCommand($sql);
		$command->bindParam(":current_date",$current_date);
		$command->bindParam(":active_customer",$actives);
		$command->bindParam(":fresh",$fresh_actives);
		$command->bindParam(":repeat",$repeat_active);
		$command->execute();
	}

	/**
	 * 查询macaddress是否存在
	 * Enter description here ...
	 * @param unknown_type $data
	 */
	public static function isCustomerMacaddress($data){
		$connection = Yii::app()->dbstat;
		$sql = "SELECT count(1) as count FROM t_customer_macaddress where macaddress = :macaddress";
		$command = $connection->createCommand($sql);
		$command->bindParam(":macaddress",$data['macaddress']);
		$isnot = $command->queryRow();
		if($isnot["count"]>0){
			return 1;
		}else{
			if(isset($data)){
				$sql = 'INSERT INTO t_customer_macaddress(macaddress, source, created) VALUES(:macaddress,  :source, :created)';
				$command_m = $connection->createCommand($sql);
				$command_m->bindParam(":source", $data['source']);
				$command_m->bindParam(":macaddress", $data['macaddress']);
				$command_m->bindParam(":created", $data['created']);
				$command_m->execute();
				$command_m->reset();
			}
			return 0;
		}
	}
	
	/**
	 * 
	 * 司机在线情况导入统计数据库
	 * 
	 */
	public function actionWorkLog($snap_time_start = null) {
		if (!$snap_time_start) {
			$snap_time_start = date('Ymd', time()-86400);
			$snap_time_end = date('Ymd');
		} else {
			$snap_time_end = date('Ymd', strtotime($snap_time_start)+86400);
		}
		$citys = Dict::items('city');
		
		foreach($citys as $code=>$name) {
			$worklog = self::getDailyWorklog($code, $snap_time_start, $snap_time_end);
			
			$connection = Yii::app()->dbstat;
			$sql = "INSERT INTO t_daily_report_worklog_timeline(`current_date`, city_id, online, inservice, offline, timeline) VALUES(:current_date, :city_id, :online, :inservice, :offline, :timeline)";
			
			$command = $connection->createCommand($sql);
			
			$online = empty($worklog['daily_report']['online']) ? '0' : $worklog['daily_report']['online'];
			$offline = empty($worklog['daily_report']['offline']) ? '0' : $worklog['daily_report']['offline'];
			$inservice = empty($worklog['daily_report']['inservice']) ? '0' : $worklog['daily_report']['inservice'];
			$timeline = empty($worklog['daily_report']['timeline']) ? '' : $worklog['daily_report']['timeline'];
			$command->bindParam(":current_date", $worklog['daily_report']['current_date']);
			$command->bindParam(":city_id", $worklog['daily_report']['city_id']);
			$command->bindParam(":online", $online);
			$command->bindParam(":inservice", $inservice);
			$command->bindParam(":offline", $offline);
			$command->bindParam(":timeline", $timeline);
			
			$command->execute();
			
			$sql = "INSERT INTO t_minutely_report_worklog_timeline(`current_datetime`, city_id, online, inservice, offline) VALUES(:current_datetime, :city_id, :online, :inservice, :offline)";
			
			$command_minutely = $connection->createCommand($sql);
			foreach($worklog['minutely_report'] as $key=>$value) {
				
				$minutely_online = empty($value['online']) ? 0 : $value['online'];
				$minutely_inservice = empty($value['inservice']) ? 0 : $value['inservice'];
				$minutely_offline = empty($value['offline']) ? 0 : $value['offline'];
				
				$command_minutely->bindParam(":current_datetime", date('Y-m-d H:i:s', strtotime($value['current_datetime'])));
				$command_minutely->bindParam(":city_id", $value['city_id']);
				$command_minutely->bindParam(":online", $minutely_online);
				$command_minutely->bindParam(":inservice", $minutely_inservice);
				$command_minutely->bindParam(":offline", $minutely_offline);
				
				$command_minutely->execute();
			}
		}
	}
	
	/**
	 * 新老客户情况导入统计数据库
	 * 
	 */
	public function actionCustomerDailyInitialize() {
		$connection = Order::getDbMasterConnection();
		$sql = "SELECT DISTINCT FROM_UNIXTIME( booking_time, '%Y%m%d' ) AS booking_date FROM t_order WHERE FROM_UNIXTIME( booking_time, '%Y-%m-%d' ) BETWEEN '2012-04-01' AND DATE_SUB( CURDATE() , INTERVAL 1 DAY ) ORDER BY booking_date";
		$command = $connection->createCommand($sql);
		$order_initialize_reports = $command->queryAll();
		
		Yii::app()->dbstat->createCommand('TRUNCATE TABLE `t_daily_report_customer`')->execute();
		
		foreach($order_initialize_reports as $initialize) {
			self::actionCustomerDaily($initialize['booking_date']);
		}
	}
	
	/**
	 * 
	 * 新老客户情况导入统计数据库
	 * 
	 */
	public function actionCustomerDaily($current_date = null) {
		if (!$current_date) {
			$current_date_start = date('Ymd', time()-86400);
			$current_date_end = date('Ymd');
		} else {
			$current_date_start = $current_date;
			$current_date_end = date('Ymd', strtotime($current_date)+86400);
		}
		
		$citys = Dict::items('city');
		
		foreach($citys as $code=>$name) {
			$customer = self::getDailyCustomer($code, $current_date_start, $current_date_end);
			
			$connection = Yii::app()->dbstat;
			$sql = "INSERT INTO t_daily_report_customer(`current_date`, city_id, `repeat`, fresh) VALUES(:current_date, :city_id, :repeat, :fresh)";
			
			$command = $connection->createCommand($sql);
			
			$command->bindParam(":current_date", $customer['current_date']);
			$command->bindParam(":city_id", $code);
			$command->bindParam(":repeat", $customer['repeat']);
			$command->bindParam(":fresh", $customer['fresh']);
			
			$command->execute();
		}
	}
	
	private function getCityUser($city_id) {
		$sql = "SELECT 
					user
				FROM 
					t_employee
				WHERE
					city_id=:city_id
					and user<>''";
					
		$command = Yii::app()->db->createCommand($sql);
		$command->bindParam(":city_id", $city_id);
		$users = $command->queryAll();
		$driver_ids = "";
		if(!empty($users)) {
			foreach($users as $u) {
				$driver_ids .= "'".$u['user']."',";
			}
		}
		$driver_ids = substr($driver_ids,0,-1);
		
		return $driver_ids;
	}
	
	private function getDailyOrder($city_id, $source, $status, $created_start, $created_end) {
		
		$driver_ids = empty($city_id)?'0':$this->getCityUser($city_id);
		$driver_ids = empty($driver_ids)?'0':$driver_ids;

		$status = implode(",",$status);
		$source = implode(",",$source);			

		$sql = "SELECT 
					imei,
					booking_time,
					phone
				FROM 
					t_order
				WHERE
					driver_id in(".$driver_ids.")
					and status in(".$status.")
					and source in(".$source.")
					and (booking_time between :start and :end)
				GROUP BY
					imei,
					booking_time,
					phone";

		$command = Yii::app()->dborder->createCommand($sql);
		

		$command->bindParam(":start", $created_start);
		$command->bindParam(":end", $created_end);
		
		$result = count($command->queryAll());
		
		return $result;	
		
		/*
		$criteria = new CDbCriteria();
		$criteria->select = 't.imei, t.booking_time, t.phone';
		$criteria->join = 'JOIN t_employee AS e ON t.driver_id = e.user';
		$criteria->addCondition("e.city_id=:city_id");
		$criteria->addInCondition('t.status', $status);
		$criteria->addInCondition('t.source', $source);
		//$criteria->addBetweenCondition("FROM_UNIXTIME(t.booking_time, '%Y%m%d %H:%i')", $created_start." 09:00", $created_end." 09:00");
		$criteria->addBetweenCondition("t.booking_time", strtotime($created_start." 07:00:00"), strtotime($created_end." 07:00:00"));
		$criteria->params[':city_id'] = $city_id;
		$criteria->group = 't.imei, t.booking_time, t.phone';
		//$criteria->order = "t.booking_time ASC";
		//if ($status == array(1,4)) {print_r($criteria);die();}
		$result=Order::model()->count($criteria);
		return $result;
		*/
	}
	
	private function getDailyWorklog($city_id, $snap_time_start, $snap_time_end) {
		$criteria = new CDbCriteria();
		$criteria->select = "state, city_id, total, FROM_UNIXTIME(snap_time, '%Y%m%d %H:%i') AS snap_time";
		$criteria->addCondition("RIGHT(FROM_UNIXTIME(snap_time, '%i'), 1) IN (5, 0)");
		$criteria->addCondition("city_id=:city_id");
		//$criteria->addBetweenCondition("FROM_UNIXTIME(snap_time, '%Y%m%d %H:%i')", $snap_time_start." 09:00", $snap_time_end." 09:00");
		$criteria->addBetweenCondition("snap_time", strtotime($snap_time_start." 07:00:00"), strtotime($snap_time_end." 07:00:00"));
		$criteria->params[':city_id'] = $city_id;
		$criteria->order = "snap_time ASC";
		
		$worklog = WorkLog::model()->findAll($criteria);
		
		$minutely_report = array ();
		$daily_report = array (
			'city_id'=>$city_id, 
			'current_date'=>$snap_time_start);
		$last_time = '';
		foreach($worklog as $key=>$data) {
			switch ($data->attributes['state']) {
				case 0 :
					$daily_report['online'] .= $data->attributes['total'].",";
					$minutely_report[$data->attributes['snap_time']]['online'] = $data->attributes['total'];
					break;
				case 1 :
					$daily_report['inservice'] .= $data->attributes['total'].",";
					$minutely_report[$data->attributes['snap_time']]['inservice'] = $data->attributes['total'];
					break;
				case 2 :
					$daily_report['offline'] .= $data->attributes['total'].",";
					$minutely_report[$data->attributes['snap_time']]['offline'] = $data->attributes['total'];
					break;
			}
			
			if ($last_time=='') {
				$last_time = $data->attributes['snap_time'];
				$daily_report['timeline'] .= date('H', strtotime($last_time));
			} else {
				if (strtotime($data->attributes['snap_time'])>=strtotime($last_time)+3600) {
					$daily_report['timeline'] .= date('H', strtotime($data->attributes['snap_time'])).",";
					$last_time = $data->attributes['snap_time'];
				} else {
					$daily_report['timeline'] .= ',';
				}
			}
			
			$minutely_report[$data->attributes['snap_time']]['city_id'] = $city_id;
			$minutely_report[$data->attributes['snap_time']]['current_datetime'] = $data->attributes['snap_time'];
		}
		
		return array (
			'daily_report'=>$daily_report, 
			'minutely_report'=>$minutely_report);
	}
	
	private function getDailyCustomerLTV($status,  $date = null) {
		$criteria = new CDbCriteria();
		$criteria->select = "source, phone, COUNT(phone) as call_time, MAX(driver_id) as driver_id, SUM(income) as income";
		if (empty($date)) {
			$criteria->addCondition("booking_time < :booking_time");
			$criteria->params[':booking_time'] = strtotime('2012-02-01 00:00:00');
			$month = '201201';
			$date = '20120201';
		} else {
            $criteria->addBetweenCondition("booking_time", strtotime($date." 00:00:00"), strtotime($date." 23:59:59"));
			//$criteria->addCondition("FROM_UNIXTIME(booking_time, '%Y%m%d') = :booking_time");
			//$criteria->params[':booking_time'] = $date;
			//$month = substr($date, 0, 7);
            $month = str_replace('-','',substr($date, 0, 7));
		}
		$criteria->addInCondition('status', $status);
		$criteria->group = 'phone, source';
		$phones = Order::model()->findAll($criteria);
		$return = array (
			'month'=>$month,
			'date'=>$date);
		$unfinished = 0;
		$phone = '';
		$booking_time_start = strtotime($date);
		$booking_time_end = $booking_time_start + 24 * 3600;
		
		foreach($phones as $key=>$data) {
			
			if ($phone==trim($data->phone)) {
				if (in_array($data->source, Order::$callcenter_sources)
				    || in_array($data->source, Order::$callcenter_input_sources)){
						if (!isset($return['records'][trim($data->phone)]['callcenter_times'])) {
							$return['records'][trim($data->phone)]['callcenter_times'] = $data->call_time;
						} else {
							$return['records'][trim($data->phone)]['callcenter_times'] += $data->call_time;
						}
						if (!isset($return['records'][trim($data->phone)]['callcenter_fee'])) {
							$return['records'][trim($data->phone)]['callcenter_fee'] = $data->income;
						} else {
							$return['records'][trim($data->phone)]['callcenter_fee'] += $data->income;
						}
				} else {
					if (!isset($return['records'][trim($data->phone)]['app_times'])) {
							$return['records'][trim($data->phone)]['app_times'] = $data->call_time;
						} else {
							$return['records'][trim($data->phone)]['app_times'] += $data->call_time;
						}
						if (!isset($return['records'][trim($data->phone)]['app_fee'])) {
							$return['records'][trim($data->phone)]['app_fee'] = $data->income;
						} else {
							$return['records'][trim($data->phone)]['app_fee'] += $data->income;
						}
				}
			} else {
				$phone = trim($data->phone);
				if ($data->source == Order::SOURCE_CALLCENTER || $data->source == Order::SOURCE_CALLCENTER_INPUT){
					$return['records'][trim($data->phone)]['callcenter_times'] = $data->call_time;
					$return['records'][trim($data->phone)]['callcenter_fee'] = $data->income;
				} else {
				
					$return['records'][trim($data->phone)]['app_times'] = $data->call_time;
					$return['records'][trim($data->phone)]['app_fee'] = $data->income;
				}
				$return['records'][trim($data->phone)]['phone'] = trim($data->phone);

//				$order = Order::model()->find(array (
//					'select'=>'driver_id', 
//					'condition'=>"phone=:phone AND booking_time BETWEEN :booking_time_start AND :booking_time_end", 
//					'params'=>array (
//						':phone'=>$data->attributes['phone'],
//						':booking_time_start'=>$booking_time_start,
//						':booking_time_end'=>$booking_time_end)));
//				
//				Driver::$db = Yii::app()->db_readonly;
//				$driver = Driver::model()->find(array (
//					'select'=>'city_id', 
//					'condition'=>'user=:driver_id', 
//					'params'=>array (
//						':driver_id'=>$order->driver_id)));
//				$city_id =  empty($driver) ? '-' : $driver->city_id;

				$return['records'][trim($data->attributes['phone'])]['city_id'] = DriverStatus::model()->getItem($data->driver_id,'city_id');
			}
		
		}
		
		return $return;
	}
	
	private function getMonthlyCustomerLTVFromDaily($month = null){
		if ($month == null){
			$month = date('Ym');
		} 

		$sql = 'SELECT 
					telephone AS phone,
					city_id,
					name,
					SUM(by_callcenter) AS callcenter_times, 
					SUM(fee_callcenter) AS callcenter_fee,
					SUM(by_application) AS app_times,
					SUM(fee_application) AS app_fee
				FROM 
					t_daily_report_customer_fee
				WHERE
					current_month = :current_month
				GROUP BY
					telephone,
					city_id,
					name';
		
		$command = Yii::app()->dbstat->createCommand($sql);
		
		$command->bindParam(":current_month", $month);
		
		$record = $command->queryAll();
		
		return $record;		
	}
	
	private function getMonthlyCustomerLTV($status, $month = null) {
		$criteria = new CDbCriteria();
		$criteria->select = "source, phone, COUNT(phone) as call_time, SUM(income) as income";
		if (empty($month)) {
			$criteria->addCondition("booking_time < :booking_time");
			$criteria->params[':booking_time'] = strtotime('2012-02-01 00:00:00');
			$month = '201201';
		} else {
			$criteria->addCondition("FROM_UNIXTIME(booking_time, '%Y%m') = :booking_time");
			$criteria->params[':booking_time'] = $month;
		}
		$criteria->addInCondition('status', $status);
		$criteria->group = 'phone, source';
		$phones = Order::model()->findAll($criteria);
		$return = array (
			'month'=>$month);
		$unfinished = 0;
		$phone = '';
		foreach($phones as $key=>$data) {
			if ($phone==trim($data->attributes['phone'])) {
				if(in_array($data->attributes['source'], Order::$callcenter_sources)
				    || in_array($data->attributes['source'], Order::$callcenter_input_sources)) {
						if (!isset($return['records'][trim($data->attributes['phone'])]['callcenter_times'])) {
							$return['records'][trim($data->attributes['phone'])]['callcenter_times'] = $data->attributes['call_time'];
						} else {
							$return['records'][trim($data->attributes['phone'])]['callcenter_times'] += $data->attributes['call_time'];
						}
						if (!isset($return['records'][trim($data->attributes['phone'])]['callcenter_fee'])) {
							$return['records'][trim($data->attributes['phone'])]['callcenter_fee'] = $data->attributes['income'];
						} else {
							$return['records'][trim($data->attributes['phone'])]['callcenter_fee'] += $data->attributes['income'];
						}
				}
                                elseif(in_array($data->attributes['source'], Order::$client_source)
				    || in_array($data->attributes['source'], Order::$client_input_source)){
						if (!isset($return['records'][trim($data->attributes['phone'])]['app_times'])) {
							$return['records'][trim($data->attributes['phone'])]['app_times'] = $data->attributes['call_time'];
						} else {
							$return['records'][trim($data->attributes['phone'])]['app_times'] += $data->attributes['call_time'];
						}
						if (!isset($return['records'][trim($data->attributes['phone'])]['app_fee'])) {
							$return['records'][trim($data->attributes['phone'])]['app_fee'] = $data->attributes['income'];
						} else {
							$return['records'][trim($data->attributes['phone'])]['app_fee'] += $data->attributes['income'];
						}
				}
			} else {
				$phone = trim($data->attributes['phone']);
				if(in_array($data->attributes['source'], Order::$callcenter_sources)
				    || in_array($data->attributes['source'], Order::$callcenter_input_sources)) {
						$return['records'][trim($data->attributes['phone'])]['callcenter_times'] = $data->attributes['call_time'];
						$return['records'][trim($data->attributes['phone'])]['callcenter_fee'] = $data->attributes['income'];
				}
                                elseif(in_array($data->attributes['source'], Order::$client_source)
				    || in_array($data->attributes['source'], Order::$client_input_source)){
						$return['records'][trim($data->attributes['phone'])]['app_times'] = $data->attributes['call_time'];
						$return['records'][trim($data->attributes['phone'])]['app_fee'] = $data->attributes['income'];
				}
				$return['records'][trim($data->attributes['phone'])]['phone'] = trim($data->attributes['phone']);
				$order = Order::model()->find(array (
					'select'=>'driver_id', 
					'condition'=>'phone=:phone', 
					'params'=>array (
						':phone'=>$data->attributes['phone']), 
					'order'=>'booking_time DESC'));
				$employee = Employee::model()->find(array (
					'select'=>'city_id', 
					'condition'=>'user=:driver_id', 
					'params'=>array (
						':driver_id'=>$order->attributes['driver_id'])));
				$return['records'][trim($data->attributes['phone'])]['city_id'] = empty($employee) ? '-' : $employee->attributes['city_id'];
			}
		
		}
		return $return;
	}
	
	private function getDailyCustomer($city_id, $current_date_start, $current_date_end) {
		$driver_ids = empty($city_id)?'0':$this->getCityUser($city_id);
		$driver_ids = empty($driver_ids)?0:$driver_ids;
					
		$sql = "SELECT 
					phone
				FROM 
					t_order
				WHERE
					driver_id in(".$driver_ids.")
					and (booking_time between :start and :end)
				GROUP BY
					phone
				order by
					booking_time ASC";			
		
		$command = Yii::app()->dborder->createCommand($sql);
		$command->bindParam(":start", $current_date_start);
		$command->bindParam(":end", $current_date_end);

		$orders = $command->queryAll();

		/*
		$criteria = new CDbCriteria();
		$criteria->select = 't.phone';
		$criteria->join = 'JOIN t_employee AS e ON t.driver_id = e.user';
		$criteria->addCondition("e.city_id=:city_id");
		//$criteria->addBetweenCondition("FROM_UNIXTIME(t.booking_time, '%Y%m%d %H:%i')", $current_date_start." 09:00", $current_date_end." 09:00");
		$criteria->addBetweenCondition("t.booking_time", strtotime($current_date_start." 09:00:00"), strtotime($current_date_end." 09:00:00"));
		$criteria->params[':city_id'] = $city_id;
		$criteria->group = "t.phone";
		$criteria->order = "t.booking_time ASC";
		
		$orders = Order::model()->findAll($criteria);
		*/
		$return = array (
			"current_date"=>$current_date_start, 
			"repeat"=>0, 
			"fresh"=>0);
		$condition = "phone=:phone AND source IN (0,2) AND FROM_UNIXTIME(booking_time, '%Y%m%d %H:%i') < :current_date_start";
		foreach($orders as $data) {
			$customer_phone = empty($data->attributes['phone']) ? '' : $data->attributes['phone'];
			$params[':phone'] = $customer_phone;
			$params[':current_date_start'] = $current_date_start." 09:00";
			
			if (Order::model()->exists($condition, $params)) {
				$return['repeat']++;
			} else {
				$return['fresh']++;
			}
		}
		
		return $return;
	}
	
	public function actionDriverTrackInitialize() {
		$cur_date = date('Y-m-d', time()-24*3600);
		$cur_time = strtotime(time()-24*3600);
		$last_time = strtotime('2012-04-30 23:59:59');
		$employees = Driver::model()->findAll();
		foreach($employees as $employee) {
			while ($cur_time>$last_time) {
				$cur_date = date('Y-m-d', $cur_time);
				self::insertDailyDriverTrack($employee->user, $employee->city_id, $cur_date);
				$cur_time = $cur_time-24*3600;
			}
		}
	}
	
	public function actionDriverTrackReloadByMonth($month = 0) {
		if ($month==0) {
			$last_date = date_create(date('Y-m').'-01');
			$days = -1;
			date_add($last_date, date_interval_create_from_date_string($days.' days'));
			$last_date_format = date_format($last_date, 'Y-m-d');
		}
		
		$cur_time = strtotime($last_date_format);
		$last_time = strtotime(date('Y-m', strtotime($last_date_format)).'-01 00:00:00');
		$employees = Driver::model()->findAll();
		foreach($employees as $employee) {
			while ($cur_time>$last_time) {
				$cur_date = date('Y-m-d', $cur_time);
				self::insertDailyDriverTrack($employee->user, $employee->city_id, $cur_date);
				$cur_time = $cur_time-24*3600;
			}
		}
	}
	
	public function actionMonthlyDriverTrackOrderReload() {
		$month = date('Ym', time()-31*24*3600);
		$employees = Driver::model()->findAll();
		foreach($employees as $employee) {
			self::insertMonthlyDriverTrackOrder($employee->user, $month);
		}
	}
	
	public function actionMonthlyDriverTrackOrderInitialize() {
		$month_array = array (
			'201205', 
			'201206', 
			'201207', 
			'201208');
		$employees = Driver::model()->findAll();
		foreach($employees as $employee) {
			foreach($month_array as $val) {
				self::insertMonthlyDriverTrackOrder($employee->user, $val);
			}
		}
	}
	
	public function insertMonthlyDriverTrackOrder($driverID = 'BJ9000', $month = '201204') {
		$driverAccount = self::getDriverAccountByMonth($driverID, $month);
		$driverTrack = self::getDriverTrackByMonth($driverID, $month);
		
		$connection = Yii::app()->dbstat;
		$delsql = 'DELETE FROM t_monthly_report_driver_order WHERE driver_id=:driverId AND log_month=:log_month';
		$command = $connection->createCommand($delsql);
		$command->bindParam(":driver_id", $driverID);
		$command->bindParam(":log_month", $month);
		
		$command->execute();
		
		$sql = "INSERT INTO 
					t_monthly_report_driver_order(city_id, driver_id, imei, log_month, free_hour, busy_hour, finished_order_count, canceled_order_count, cash, cast, tax, fine, vip, cast_in, insurance) 
				VALUES
					(:city_id, :driver_id, :imei, :log_month, :free_hour, :busy_hour, :finished_order_count, :canceled_order_count, :cash, :cast, :tax, :fine, :vip, :cast_in, :insurance)";
		
		$command = $connection->createCommand($sql);
		
		$command->bindParam(":city_id", $driverTrack['city_id']);
		$command->bindParam(":driver_id", $driverTrack['driver_id']);
		$command->bindParam(":imei", $driverTrack['imei']);
		$command->bindParam(":log_month", $driverTrack['log_month']);
		$command->bindParam(":free_hour", $driverTrack['free_hour']);
		$command->bindParam(":busy_hour", $driverTrack['busy_hour']);
		$command->bindParam(":finished_order_count", $driverTrack['finished_order_counts']);
		$command->bindParam(":canceled_order_count", $driverTrack['canceled_order_counts']);
		$command->bindParam(":cash", $driverAccount['x0']);
		$command->bindParam(":cast", $driverAccount['x1']);
		$command->bindParam(":tax", $driverAccount['x2']);
		$command->bindParam(":fine", $driverAccount['x4']);
		$command->bindParam(":vip", $driverAccount['x3']);
		$command->bindParam(":cast_in", $driverAccount['x5']);
		$command->bindParam(":insurance", $driverAccount['x6']);
		
		$command->execute();
	
	}
	
	private function getDriverTrackByMonth($driverID = 'BJ9000', $month = '201204') {
		$sql = 'SELECT 
					city_id, 
					driver_id, 
					imei, 
					log_month, 
					SUM(free_minute) AS free_minutes, 
					SUM(busy_minute) AS busy_minutes, 
					SUM(finished_order_count) AS finished_order_counts, 
					SUM(canceled_order_count) AS canceled_order_counts
				FROM 
					t_hourly_report_driver_order
				WHERE
					driver_id=:driver_id
					AND log_month=:log_month
				GROUP BY
					city_id, 
					driver_id, 
					imei, 
					log_month';
		
		$command = Yii::app()->dbstat->createCommand($sql);
		
		$command->bindParam(":driver_id", $driverID);
		$command->bindParam(":log_month", $month);
		
		$record = $command->query();
		
		$record['free_hour'] = round($record['free_minutes']/60, 2);
		$record['busy_hour'] = round($record['busy_minutes']/60, 2);
		
		return $record;
	}
	
	private function getDriverAccountByMonth($driverID = 'BJ9000', $month = '201204') {
		$sql = "SELECT 
					SUM(t0) AS x0,
					SUM(t1) AS x1, 
					SUM(t2) AS x2, 
					SUM(t3) AS x3, 
					SUM(t4) AS x4, 
					SUM(t5) AS x5, 
					SUM(t6) AS x6,
					user,
					FROM_UNIXTIME(created, '%Y-%m') AS current_month
				FROM  `t_view_employee_account` 
				WHERE
					user = :user
					AND create BETWEEN :month_from AND :month_end
				GROUP BY current_month, user";
		
		$command = Yii::app()->db_readonly->createCommand($sql);
		
		$last_date = date_create($month.'01');
		$days = 31;
		date_add($last_date, date_interval_create_from_date_string($days.' days'));
		$last_date_format = date_format($last_date, 'Ym');
		
		$month_from = strtotime($month.'01 00:00:00');
		
		$month_end = strtotime($last_date_format.'01 00:00:00');
		
		$command->bindParam(":user", $driverID);
		$command->bindParam(":month_from", $month_from);
		$command->bindParam(":month_end", $month_end);
		
		$record = $command->query();
		
		return $record;
	}
	
	public function actionDriverTrack($driverID = 'BJ9000', $cityID = 1, $cur_date = '') {
		if ($cur_date=='') {
			$cur_date = date('Y-m-d', time()-24*3600);
		}
		self::insertDailyDriverTrack($driverID, $cityID, $cur_date);
	}
	
	private function insertDailyDriverTrack($driverID = 'BJ9000', $cityID = 1, $cur_date = '2012-05-08') {
		$driverTracks = self::getDailyDriverTrack($driverID, $cityID, $cur_date);
		
		$connection = Yii::app()->dbstat;
		
		$delsql = 'DELETE FROM t_hourly_report_driver_order WHERE driver_id=:driverId AND log_date=:log_date';
		$command = $connection->createCommand($delsql);
		$log_date = date('Ymd', strtotime($cur_date));
		$command->bindParam(":driver_id", $driverID);
		$command->bindParam(":log_date", $log_date);
		
		$command->execute();
		
		$sql = "INSERT INTO 
					t_hourly_report_driver_order(city_id, driver_id, imei, log_date_hour, log_month, log_date, log_hour, free_minute, busy_minute, finished_order_count, canceled_order_count) 
				VALUES
					(:city_id, :driver_id, :imei, :log_date_hour, :log_month, :log_date, :log_hour, :free_minute, :busy_minute, :finished_order_count, :canceled_order_count)";
		
		$command = $connection->createCommand($sql);
		foreach($driverTracks as $key=>$value) {
			
			$log_date_hour = date('YmdH', strtotime($cur_date)+$key*3600);
			$log_month = date('Ym', strtotime($cur_date));
			$log_date = date('Ymd', strtotime($cur_date));
			$log_hour = $key;
			
			$command->bindParam(":city_id", $value['city_id']);
			$command->bindParam(":driver_id", $value['driver_id']);
			$command->bindParam(":imei", $value['imei']);
			$command->bindParam(":log_date_hour", $log_date_hour);
			$command->bindParam(":log_month", $log_month);
			$command->bindParam(":log_date", $log_date);
			$command->bindParam(":log_hour", $log_hour);
			$command->bindParam(":free_minute", $value['free_minute']);
			$command->bindParam(":busy_minute", $value['busy_minute']);
			$command->bindParam(":finished_order_count", $value['finishedOrderCount']);
			$command->bindParam(":canceled_order_count", $value['canceledOrderCount']);
			
			$command->execute();
		}
	}
	
	private function getDailyDriverTrack($driverID = 'BJ9000', $cityID = 1, $cur_date = '2012-05-06') {
		$current_date_start = strtotime($cur_date.' 00:00:00');
		$current_date_end = $current_date_start+24*3600;
		$criteria = new CDbCriteria();
		$criteria->select = 'imei';
		$criteria->addCondition("driver_id=:driver_id");
		$criteria->addBetweenCondition("booking_time", $current_date_start, $current_date_end);
		$criteria->params[':driver_id'] = $driverID;
		$criteria->group = "driver_id";
		
		$order = Order::model()->find($criteria);
		
		if ($order) {
			$imei = $order->imei;
		} else {
			$criteria = new CDbCriteria();
			$criteria->select = 'imei, MIN(booking_time) AS booking_time';
			$criteria->addCondition("driver_id=:driver_id");
			$criteria->params[':driver_id'] = $driverID;
			$criteria->group = "imei";
			
			$orders = Order::model()->findAll($criteria);
			foreach($orders as $order) {
				if ($order->booking_time<$current_date_end) {
					$imei = $order->imei;
				}
			}
		}
		
		$finishedOrderCount = 0;
		$canceledOrderCount = 0;
		$hourlyDriver = array ();
		for($i = 0; $i<24; $i++) {
			$free_minute = 0;
			$busy_minute = 0;
			
			if (strlen($i)==1) {
				$hour = '0'.$i;
			} else {
				$hour = $i;
			}
			$booking_start = $current_date_start+$i*3600;
			$booking_end = $booking_start+3600;
			
			$employeeTrack = self::getHourlyDriverMinute($imei, $cur_date.' '.$hour, date('Ym', $current_date_start));
			
			if ($employeeTrack) {
				$free_minute = $employeeTrack['free_minute'];
				$busy_minute = $employeeTrack['busy_minute'];
			}
			
			$finishedOrderCount = self::getHourlyDriverOrder($driverID, array (
				Order::ORDER_COMPLATE, 
				Order::ORDER_NOT_COMFIRM), $booking_start, $booking_end);
			$canceledOrderCount = self::getHourlyDriverOrder($driverID, array (
				Order::ORDER_CANCEL, 
				Order::ORDER_COMFIRM), $booking_start, $booking_end);
			$hourlyDriver[$hour] = array (
				'driver_id'=>$driverID, 
				'city_id'=>$cityID, 
				'imei'=>$imei, 
				'free_minute'=>$free_minute, 
				'busy_minute'=>$busy_minute, 
				'finishedOrderCount'=>$finishedOrderCount, 
				'canceledOrderCount'=>$canceledOrderCount);
		}
		
		return $hourlyDriver;
	}
	
	private function getHourlyDriverMinute($imei = '353419034936711', $insertDateHour = '2012-05-01 00', $cur_month = '201205') {
		$sql = "SELECT 
					state, 
					UNIX_TIMESTAMP(insert_time) AS insert_time, 
					FROM_UNIXTIME(UNIX_TIMESTAMP(insert_time), '%H') AS insert_hour 
				FROM 
					t_employee_track_".$cur_month.' 
				WHERE 
					imei=:imei 
					AND LEFT(insert_time, 13) = :insert_date_hour 
				ORDER BY 
					insert_time';
		
		$command = Yii::app()->dbstat->createCommand($sql);
		$command->bindParam(":imei", $imei);
		$command->bindParam(":insert_date_hour", $insertDateHour);
		
		$employeeTracks = $command->queryAll();
		
		$ret = array (
			'free_minute'=>0, 
			'busy_minute'=>0);
		$zero_time = strtotime($insertDateHour.':00:00');
		$last_state = 0;
		if ($employeeTracks) {
			foreach($employeeTracks as $employeeTrack) {
				
				if ($employeeTrack['insert_time']-$zero_time<300) {
					if ($last_state==Employee::EMPLOYEE_WORK) {
						$ret['busy_minute'] = $ret['busy_minute']+$employeeTrack['insert_time']-$zero_time;
					}
					if ($last_state==Employee::EMPLOYEE_IDLE) {
						$ret['free_minute'] = $ret['free_minute']+$employeeTrack['insert_time']-$zero_time;
					}
					if ($employeeTrack['state']==Employee::EMPLOYEE_IDLE&&$last_state==Employee::EMPLOYEE_GETOFF) {
						$ret['free_minute'] = $ret['free_minute']+$employeeTrack['insert_time']-$zero_time;
					}
				}
				
				$zero_time = $employeeTrack['insert_time'];
				$last_state = $employeeTrack['state'];
			
			}
			$ret['busy_minute'] = round($ret['busy_minute']/60);
			$ret['free_minute'] = round($ret['free_minute']/60);
			if ($ret['busy_minute']>55)
				$ret['busy_minute'] = 60;
			if ($ret['free_minute']>55)
				$ret['free_minute'] = 60;
		
		//die();
		}
		return $ret;
	}
	
	private function getHourlyDriverOrder($driverID, $status, $booking_start, $booking_end) {
		$criteria = new CDbCriteria();
		$criteria->select = 'order_id';
		$criteria->addCondition("driver_id=:driver_id");
		$criteria->addInCondition('status', $status);
		$criteria->addBetweenCondition("booking_time", $booking_start, $booking_end);
		$criteria->params[':driver_id'] = $driverID;
		$criteria->group = 'order_id';
		return Order::model()->count($criteria);
	}
	
	private function getCustomerBooking($booking_date = null) {
		$db_connection = Order::getDbMasterConnection();
		
		$booking_time_start = strtotime($booking_date);
		$booking_time_end = $booking_time_start+24*3600;
		
		$sql = "SELECT phone, MAX( driver_id ) AS driver_id, FROM_UNIXTIME( booking_time,  '%Y-%m-%d' ) AS booking_date, COUNT( order_id ) AS booking_times
				FROM  `t_order` 
				WHERE phone IS NOT NULL AND booking_time BETWEEN :booking_time_start AND :booking_time_end 
				AND `status` IN ( :order_complate, :order_not_confirm ) 
				AND source IN ( :source, :source_input ) 
				GROUP BY phone, booking_date";
		$command = $db_connection->createCommand($sql);
		$order_complate = Order::ORDER_COMPLATE;
		$order_not_confirm = Order::ORDER_NOT_COMFIRM;
		$source = join(',', Order::$client_source);
		$source_input = join(',', Order::$client_input_source);
		
		$command->bindParam(":order_complate", $order_complate);
		$command->bindParam(":order_not_confirm", $order_not_confirm);
		$command->bindParam(":source", $source);
		$command->bindParam(":source_input", $source_input);
		$command->bindParam(":booking_time_start", $booking_time_start);
		$command->bindParam(":booking_time_end", $booking_time_end);
		$order_app_reports = $command->queryAll();
		$command->reset();
		
		$command = $db_connection->createCommand($sql);
		
		$source = join(',', Order::$callcenter_sources);
		$source_input = join(',', Order::$callcenter_input_sources);
		$command->bindParam(":order_complate", $order_complate);
		$command->bindParam(":order_not_confirm", $order_not_confirm);
		$command->bindParam(":source", $source);
		$command->bindParam(":source_input", $source_input);
		$command->bindParam(":booking_time_start", $booking_time_start);
		$command->bindParam(":booking_time_end", $booking_time_end);
		$order_callcenter_reports = $command->queryAll();
		$command->reset();
		
		$sql = "SELECT phone, FROM_UNIXTIME( booking_time,  '%Y-%m-%d' ) AS booking_date, COUNT( order_id ) AS booking_times
				FROM  `t_order` 
				WHERE phone IS NOT NULL 
				AND booking_time BETWEEN :booking_time_start AND :booking_time_end 
				AND `status` IN ( :order_ready, :order_confirm ) 
				GROUP BY phone, booking_date";
		
		$command = $db_connection->createCommand($sql);
		$order_ready = Order::ORDER_READY;
		$order_confirm = Order::ORDER_COMFIRM;
		$command->bindParam(":order_ready", $order_ready);
		$command->bindParam(":order_confirm", $order_confirm);
		$command->bindParam(":booking_time_start", $booking_time_start);
		$command->bindParam(":booking_time_end", $booking_time_end);
		$order_unfinished_reports = $command->queryAll();
		$command->reset();
		
		$customer_booking = array ();
		foreach($order_app_reports as $key=>$data) {
			$customer_booking[$key]['telephone'] = $data['phone'];
			$customer_booking[$key]['booking_date'] = $data['booking_date'];
			$customer_booking[$key]['by_application'] = $data['booking_times'];
			$customer_booking[$key]['by_callcenter'] = 0;
			$customer_booking[$key]['unfinished'] = 0;
			$employee = Employee::model()->find("user=:user", array (
				':user'=>$data['driver_id']));
			$customer_booking[$key]['city_id'] = !$employee ? 1 : $employee->city_id;
			
			if (!empty($order_unfinished_reports)) {
				foreach($order_unfinished_reports as $unfinished) {
					if ($data['booking_date']==$unfinished['booking_date']&&$data['phone']==$unfinished['phone']) {
						$customer_booking[$key]['unfinished'] = $unfinished['booking_times'];
						break;
					}
				}
			}
			if (!empty($order_callcenter_reports)) {
				foreach($order_callcenter_reports as $k=>$callcenter) {
					if ($data['booking_date']==$callcenter['booking_date']&&$data['phone']==$callcenter['phone']&&strtolower(substr($callcenter['driver_id'], 2))==strtolower(substr($data['driver_id'], 2))) {
						$customer_booking[$key]['by_callcenter'] = $callcenter['booking_times'];
						$order_callcenter_reports[$k] = 0;
						break;
					}
				}
			}
		}
		if (!empty($order_callcenter_reports)) {
			foreach($order_callcenter_reports as $callcenter) {
				if ($callcenter!=0) {
					$key = count($customer_booking)+1;
					$customer_booking[$key]['telephone'] = $callcenter['phone'];
					$customer_booking[$key]['booking_date'] = $callcenter['booking_date'];
					$customer_booking[$key]['by_application'] = 0;
					$customer_booking[$key]['by_callcenter'] = $callcenter['booking_times'];
					$customer_booking[$key]['unfinished'] = 0;
					$employee = Employee::model()->find("user=:user", array (
						':user'=>$callcenter['driver_id']));
					$customer_booking[$key]['city_id'] = !$employee ? 1 : $employee->city_id;
					if (!empty($order_unfinished_reports)) {
						foreach($order_unfinished_reports as $unfinished) {
							if ($callcenter['booking_date']==$unfinished['booking_date']&&$callcenter['phone']==$unfinished['phone']) {
								$customer_booking[$key]['unfinished'] = $unfinished['booking_times'];
								break;
							}
						}
					}
				}
			}
		}
		return $customer_booking;
	}
}
