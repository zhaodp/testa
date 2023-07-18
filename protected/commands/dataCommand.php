<?php

class dataCommand extends CConsoleCommand {

	public function actionExport() {

		$startDate = strtotime('2012-4-1');
		$endDate = strtotime('2012-10-1');
		$outFilePath = dirname(__FILE__) .  '/../../cache/'. 'data_20121107.txt';
		$cityPrefix = array_flip(Dict::items("city_prefix"));
		$city = Dict::items("city");
		$i = 0;
		for ($startTime = $startDate; $startTime < $endDate; $startTime += 86400)
		{
			$criteria = new CDbCriteria();
			$endTime = $startTime + 86400;
			$criteria->addCondition("booking_time>=$startTime");
			$criteria->addCondition("booking_time<=$endTime");
			$criteria->addCondition("status=1");

			$orders = Order::model()->findAll($criteria);

			if (!empty($orders))
			{
				foreach ($orders as $order)
				{
					$i ++;

					$phone = $order->phone;
					$criteriaCount  = new CDbCriteria();
					$criteriaCount->addCondition("phone='$phone'");
					$count = Order::model()->count($criteriaCount);
					if ($count == 1)
					{
						$driver_id = $order->driver_id;
						$prefix = strtoupper(substr($driver_id, 0, 2));
						$cityName = $city[$cityPrefix[$prefix]];
						$info = array();
						$info[0] = $order->name;
						$info[1] = $order->phone;
						$info[2] = $cityName;
						$info[3] = $order->income;
						$info[4] = $order->order_date;
						$info[5] = date('Y-m-d H:i:s', $order->booking_time);
						$content = implode('","', $info);
						$content = '"' . $content . '"';
						file_put_contents($outFilePath, $content . "\n", FILE_APPEND);
					}
					echo $i . ":" . date('Y-m-d H:i:s', $order->booking_time) . ":$count";
					if ($count == 1)
					{
						echo " ok";
					}
					echo  "\n";
				}

			}
		}
	}

	public function actionCustomerBonusUsed()
	{
		$data = CustomerBonus::model()->findAll("order_id > 0");
		if ($data)
		{
			foreach ($data as $row)
			{
				$order_id = $row->order_id;
				$order = Order::model()->find('order_id=:order_id', array(':order_id'=>$order_id));
				if ($order)
				{
					$used = $order->booking_time;
					$new = array(
						'used' => $used
					);
					$row->attributes = $new;
					$ret = $row->save();
					var_dump($ret);
				}
			}
		}
	}

	public function actionOrderUpdateLog($date, $save=0)
	{
		$begin_time = strtotime($date);
		$end_time = $begin_time + 86400;
		$sql = "status = 1 and call_time>=:begin_time and call_time < :end_time";
		$data = Order::model()->findAll($sql, array(':begin_time'=>$begin_time, ':end_time'=>$end_time));
		$number = 0;
		if ($data)
		{
			var_dump(count($data));
			foreach ($data as $row)
			{
				$order_id = $row->order_id;
				$driver_id = $row->driver_id;
				$end_time = $row->end_time;

				$sql = "order_id=:order_id and operator=:driver_id";
				$order_log = OrderLog::model()->find($sql, array(':order_id'=>$order_id, ':driver_id'=>$driver_id));

				if (!$order_log)
				{
					$number++;
					echo $order_id;
					$sql = "order_id=:order_id";
					$dataAccount = EmployeeAccount::model()->find($sql, array(':order_id'=>$order_id));
					if ($dataAccount)
					{
						$created = $end_time + 10;
						echo ":1";
					}
					else
					{
						$created = time();
					}
					echo "\n";
					if ($save==1)
					{
						$log = new OrderLog();
						$dataLog = $log->attributes;
						$dataLog['order_id'] = $order_id;
						$dataLog['operator'] = $driver_id;
						$dataLog['description'] = "报单";
						$dataLog['created'] = $created;
						$log->attributes = $dataLog;
						$log->save();
					}
				}
			}
			echo $number;
		}
	}

	/**
	 * 每天所有的成单数据
	 * Enter description here ...
	 * @param unknown_type $date
	 * php yiic.php data DailyDriverOrderReport --date=2012-12-11
	 */
	public function actionDailyDriverOrderReport($date=NULL){
		if(empty($date)){
			$dateBegin = strtotime(date('Y-m-d').' 15:00:00')-2*86400;
			$dateEnd = strtotime(date('Y-m-d').' 15:00:00')-86400;
		}else{
			$dateBegin = strtotime($date.' 15:00:00');
			$dateEnd = strtotime($date.' 15:00:00')+86400;
		}

		$order = new Order();

		$criteria = new CDbCriteria();
		$criteria->select="distinct driver_id";
		$criteria->params = array (
				':dateBegin' => $dateBegin,
				':dateEnd' => $dateEnd
		);
		$criteria->addCondition('status = 1');
		$test_driver_list = Common::getTestDriverIds();
		$criteria->addNotInCondition('driver_id', $test_driver_list );
		$criteria->addCondition('booking_time BETWEEN :dateBegin AND :dateEnd');
		$driver_list = $order->findAll($criteria);

		foreach ($driver_list as $list){
			//查询参数
			$params = array (
				':driver_id' => $list->driver_id,
				':dateBegin' => $dateBegin,
				':dateEnd' => $dateEnd,
				':source_callcenter'=>join(',', Order::$callcenter_sources),
				':source_callcenter_input'=>join(',', Order::$callcenter_input_sources),
				':source_client_input'=>join(',', Order::$client_input_source),
				':source_client'=>join(',', Order::$client_source),
				':order_conplate' => Order::ORDER_COMPLATE,
				':not_comfirm'=>Order::ORDER_NOT_COMFIRM,
			);

			//成单的或销单审核不通过统计
			$success_order_info = Order::model()->statDriverFailOrderCount($params);

			if($success_order_info){
				$success_order_info['current_day'] = date('Ymd',$dateBegin);

				//要传递的参数
				$params_value = array (
					':driver_id' => $success_order_info['driver_id'],
					':dateBegin' => $dateBegin,
					':dateEnd' => $dateEnd,
				);
				
				//保存信息
				$save = $this->dailyDriverOrderReportSave($success_order_info,$params_value);
				echo $save;
			}
		}
	}

	/**
	 * 保存信息
	 * Enter description here ...
	 * @param unknown_type $success_order_info  统计的信息
	 * @param unknown_type $params_value	需要查询时传递的参数
	 */
	public function dailyDriverOrderReportSave($success_order_info,$params_value){
		
		//检查是否存在
		$sql = 'driver_id = :driver_id and current_day = :current_day';
		$dailyDriverOrderReport = DailyDriverOrderReport::model()->find($sql,array(':driver_id'=>$success_order_info['driver_id'],':current_day'=>$success_order_info['current_day']));
		if(!$dailyDriverOrderReport){
			$dailyDriverOrderReport = new DailyDriverOrderReport();
		}
		$order_attr = $dailyDriverOrderReport->attributes;
		$order_attr = $success_order_info;
		$order_attr['city_id'] = $this->getDriverCity($order_attr['driver_id']);
		$order_attr['city_name'] = Dict::item('city', $order_attr['city_id']);
		$order_attr['init_count'] = $this->getStatusOrderCount($params_value,Order::ORDER_READY);
		$order_attr['cancel_count'] = $this->getStatusOrderCount($params_value,array(Order::ORDER_CANCEL,Order::ORDER_COMFIRM));
		$order_attr['current_month'] = date('Ym',$params_value[':dateBegin']);
		$order_attr['created'] = time();
		$dailyDriverOrderReport->attributes = $order_attr;
		$dailyDriverOrderReport::$db = Yii::app()->db;
		$dailyDriverOrderReport->save();
		return $order_attr['driver_id']."\n";
			
	}


	/**
	 * 通过传的条件和状态获取order的统计
	 * Enter description here ...
	 * @param unknown_type $params
	 * @param unknown_type $status
	 */
	public function getStatusOrderCount($params,$status = 1){
            $order_info = Order::model()->getStatusOrderCount($params, $status);
            return $order_info ? $order_info['order_count'] : 0;
	}

	/**
	 * 获取司机所在城市id
	 * Enter description here ...
	 * @param unknown_type $driver_id
	 */
	public function getDriverCity($driver_id){
		$driver = new Driver();
		$driver::$db = Yii::app()->db_readonly;
		$criteria = new CDbCriteria();
		$criteria->select="city_id";
		$criteria->addCondition('user = :user');
		$criteria->params = array(':user'=>$driver_id);
		$driverInfo = $driver->find($criteria);
		if($driverInfo){
			$city_id = $driverInfo->city_id;
		}else{
			$city_id = 0;
		}
		return $city_id;
	}

	/**
	 * 按月批量导入到每天的接单数据
	 * Enter description here ...
	 * @param unknown_type $date=2012-01
	 * php yiic.php data MonthlyDriverOrderReport --date=2012-11
	 */
	public function actionMonthlyDriverOrderReport($date=NULL){
		if(empty($date)){
			$dateBegin = strtotime(date('Y-m',strtotime("last month")));
		}else{
			$dateBegin = strtotime($date);
		}
		$mon = date('Y-m',$dateBegin);
		$day = date("t",$dateBegin);
		for ($d = 1; $d <= $day; $d++){
			$date_str = $mon.'-'.$d;
			$this->actionDailyDriverOrderReport($date_str);
		}
	}
}
