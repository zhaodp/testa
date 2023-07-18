<?php
/**
 * 生成数据表、数据导入操作 Model
 * @author AndyCong<congming@edaijia.cn>
 * @version web2.0 2013-01-24
 */
class DataImport {	
	/**
	 * 每天执行脚本操作
	 */
	public function actionCrontab() {
		$date = date('Y-m-d' , strtotime('-1 day'));
		self::actionDailyOrderDrvierReload();
		self::actionDailyOrderDriver($date);
	}
	
	/**
	 * 获取每天订单和司机数据
	 * @param array $condition
	 * @return array $result
	 */
	public function actionDailyOrderDriver( $date = null ) {
		$condition = array();
		if ($date == null) {
			$condition['start_time'] = strtotime(date('Y-m-d').' 07:00:00')-2*86400;
			$condition['end_time'] = strtotime(date('Y-m-d').' 07:00:00')-86400;
			$condition['date'] = date('Y-m-d');
		}else {
			$condition['start_time'] = strtotime($date.' 07:00:00');
			$condition['end_time'] = strtotime($date.' 07:00:00')+86400;
			$condition['date'] = $date;
		}
		echo "\n delete old data \n";
		$sql = "DELETE FROM t_daily_order_driver WHERE `current_day` = :current_day";
		$command = Yii::app()->dbreport->createCommand($sql);
		$current_day = str_replace('-' , '' , $date);
		$command->bindParam(":current_day" , $current_day);
		$delete = $command->execute();
		var_dump($delete);
		
		$total_income = 0;
		$total_cast   = 0;
		
		$limit = 1000;
		for ($j=0 ; $j < 200 ; $j++) {
			$offset = $j*$limit;
			$field = "o.order_id,o.order_number,o.source,o.city_id,case when o.vipcard = '' then 0 else vipcard end as vipcard,o.call_type,o.call_time,o.order_date,o.booking_time,o.reach_time,o.reach_distance,o.start_time,o.start_time,o.end_time,o.distance,o.charge,o.location_start,o.location_end,o.income,o.cast,o.status,o.user_id,o.name as customer_name,o.phone,o.driver,o.driver_id as driver_user";
			$sql = "SELECT ".$field." FROM t_order o WHERE o.booking_time >= :start_time AND o.booking_time < :end_time ORDER BY order_id ASC LIMIT ".$offset.",".$limit;
			$command = Order::getDbReadonlyConnection()->createCommand($sql);
		    $command->bindParam(":start_time", $condition['start_time']);
		    $command->bindParam(":end_time", $condition['end_time']);
		    $result = $command->queryAll();
		    echo "\n ".($j+1)."|count:".count($result)."|".date('Y-m-d H:i:s');
			if (empty($result)) {
				break;
			}
		    
			$i = 0;
		    foreach ($result as $key=>$val) {
		    	//获取用户信息
		    	$user_info = self::getUserInfo($val['user_id']);
		    	if ($user_info['create_time'] >= $condition['start_time'] && $user_info['create_time']< $condition['end_time']) {
		    		$val['is_new_user'] = 1;
		    	}else {
		    		$val['is_new_user'] = 0;
		    	}
		    	$val['customer_type'] = $user_info['type'];
		    	//获取司机信息
		    	$driver_info = self::getDriverInfo($val['driver_user'],$val['city_id']);
		    	if ($driver_info) {
			    	$val['driver_id'] = isset($driver_info['id']) ? $driver_info['id'] : 0;
			    	$val['driver_picture'] = isset($driver_info['picture']) ? $driver_info['picture'] : '';
			    	$val['city_id'] = $val['city_id'] != 0 ? $val['city_id'] : (isset($driver_info['city_id']) ? $driver_info['city_id'] : 0);
		    	} else {
		    		$val['driver_id'] = 0;
			    	$val['driver_picture'] = '';
			    	$val['city_id'] = 0;
		    	}
		    	
		    	$total_cast += $val['cast'];
		    	$total_income += $val['income'];
		    	
		    	//将记录信息写入t_daily_order_driver
		    	self::insertDailyOrderDriver($val,$condition);
		    	$i += 1;
		    }
		    // add by sunhongjing
		    echo "\n date:".$date."---".($j+1)."---order_total_number:".$i."---total_income:".$total_income."---total_cast:".$total_cast."---".round($total_cast/$total_income,2)."\n ";
		    sleep(2);
		}
	}
	
	/**
	 * 获取订单所有订单信息
	 * @param array $condition
	 * @return array $data
	 */
	private function getDailyOrderDriver($condition = array()) {
		$field = "o.order_id,o.order_number,o.source,o.city_id,case when o.vipcard = '' then 0 else vipcard end as vipcard,o.call_type,o.call_time,o.order_date,o.booking_time,o.reach_time,o.reach_distance,o.start_time,o.start_time,o.end_time,o.distance,o.charge,o.location_start,o.location_end,o.income,o.cast,o.status,o.user_id,o.name as customer_name,o.phone,o.driver,o.driver_id as driver_user";
		$sql = "SELECT ".$field." FROM t_order o WHERE o.booking_time >= :start_time AND o.booking_time < :end_time";
		$command = Order::getDbReadonlyConnection()->createCommand($sql);
	    $command->bindParam(":start_time", $condition['start_time']);
	    $command->bindParam(":end_time", $condition['end_time']);
	    $data = $command->queryAll();
	    return $data;
	}
	
	/**
	 * 未完成订单重载操作
	 */
	public function actionDailyOrderDrvierReload() {
		$unfinished_order_date = self::getUnfinishedOrderDate(); 
		$prev_date = date('Y-m-d' , strtotime("-2 day"));
		self::actionDailyOrderDriver($prev_date);  //前天的数据重跑一次
		$current_time = time();
		foreach ($unfinished_order_date as $date) {
			if (($current_time - strtotime($date['current_day'])) <= 41*86400) {
				$orderid_arr = self::getUnfinishedOrderIDs($date['current_day']);
				$dbreadonly_orders = self::getUnfinishedOrders($orderid_arr);
				foreach ($dbreadonly_orders as $val) {
					self::updateDailyOrderDriver($val); //之前没有操作完成的订单更新
				}
			}
		}
	}
	
	/**
	 * 获取未完成操作的订单
	 * @return array $data
	 */
	private function getUnfinishedOrderDate() {
		$sql = "SELECT current_day FROM t_daily_order_driver GROUP BY current_day HAVING SUM(IF((`status` IN ( :order_ready , :order_comfirm)) , 1 ,0)) > 0 ORDER BY current_day ASC";
		$command = Yii::app()->dbreport->createCommand($sql);
		$order_ready = Order::ORDER_READY;
		$order_comfirm = Order::ORDER_COMFIRM;
		$command->bindParam(":order_ready" , $order_ready);
		$command->bindParam(":order_comfirm" , $order_comfirm);
		$data = $command->queryAll();
		return $data;
	}
	/**
	 * 获取未完成订单ID
	 * @param string $date
	 * @return array $orderid_arr
	 */
	private function getUnfinishedOrderIDs($date = null) {
		$sql = "SELECT order_id FROM t_daily_order_driver WHERE current_day = :date AND `status` IN ( :order_ready , :order_comfirm )";
		$command = Yii::app()->dbreport->createCommand($sql);
		$order_ready = Order::ORDER_READY;
		$order_comfirm = Order::ORDER_COMFIRM;
		$command->bindParam(":date" , $date);
		$command->bindParam(":order_ready" , $order_ready);
		$command->bindParam(":order_comfirm" , $order_comfirm);
		$result = $command->queryAll();
		$orderid_arr = array();
		foreach ($result as $val) {
			$orderid_arr[] = $val['order_id'];
		}
		return $orderid_arr;
	}
	/**
	 * 从db_readonly库中获取未完成订单
	 * @param array $orderid_arr
	 * @return array $data
	 */
	private function getUnfinishedOrders($orderid_arr = array()) {
		if (empty($orderid_arr)) 
		{
			return false;
		}
		$str_orderid = implode(',' , $orderid_arr);
		$field = "o.order_id,o.order_number,o.source,o.city_id,case when o.vipcard = '' then 0 else vipcard end as vipcard,o.call_type,o.call_time,o.order_date,o.booking_time,o.reach_time,o.reach_distance,o.start_time,o.start_time,o.end_time,o.distance,o.charge,o.location_start,o.location_end,o.income,o.cast,o.status,o.user_id,o.name as customer_name,o.phone,o.driver,o.driver_id as driver_user";   //order表字段
		$sql = "SELECT ".$field." FROM t_order o WHERE order_id IN (:str_orderid)";
		$command = Order::getDbReadonlyConnection()->createCommand($sql);
		$command->bindParam(":str_orderid" , $str_orderid);
		$data = $command->queryAll();
		return $data;
	}
	/**
	 * 初始化订单司机记录
	 * @param string $month
	 * @param int $order
	 */
	public function actionDailyOrderDriverInitialize($month = null , $order = 0) {
		if ($month == null) {
//			$sql = "SELECT DISTINCT FROM_UNIXTIME( booking_time, '%Y-%m-%d' ) AS booking_date FROM t_order";
//			$command = Yii::app()->db_readonly->createCommand($sql);
//			$order_initialize_reports = $command->queryAll();
//			foreach($order_initialize_reports as $initialize) {
//				self::actionDailyOrderDriver($initialize['booking_date']);
//			}
            return false;
		}else {
			$arr = explode('-' , $month);
			$count = date('d' , strtotime ( $arr[0] .'-'. ($arr[1]+1) .'-' . '01') - 1);
			if ($count == '01') {
			    $count = 31;
		    }
		    if ($order == 0) {
		    	for ($i = 1;$i <=$count;$i++) {
					if ($i < 10) {
						$date = $month.'-0'.$i;
					}else {
						$date = $month.'-'.$i;
					}
					self::actionDailyOrderDriver($date);
				}
		    }elseif ($order == 1) {
				for ($i = 1;$i <10;$i++) {
					self::actionDailyOrderDriver($month.'-0'.$i);
				}
			}elseif ($order == 2) {
				for ($i = 10;$i <=20;$i++) {
					self::actionDailyOrderDriver($month.'-'.$i);
				}
			}elseif ($order == 3) {
				for ($i = 21;$i <=$count;$i++) {
					self::actionDailyOrderDriver($month.'-'.$i);
				}
			}
		}
	}
	/**
	 * 获取客户相关数据信息
	 * @param int $user_id
	 * @return array $result
	 */
	private function getUserInfo($user_id = 0) {
		if ($user_id == 0) {
			return false;
		}
		$sql = "SELECT id,type,create_time FROM t_customer_main WHERE id=:id";
		$command = Yii::app()->db_readonly->createCommand($sql);
		$command->bindParam(":id",$user_id);
		$result = $command->queryRow();
        return $result;
	}
	/**
	 * 获取客户相关数据信息
	 * @param string $driver_user
	 * @param int $city_id
	 * @return array $result
	 */
	private function getDriverInfo($driver_user = null,$city_id = 0) {
		if ($driver_user == null) {
			return false;
		}
		$sql = "SELECT id,user,picture,city_id FROM t_driver WHERE user=:user";
		$command = Yii::app()->db_readonly->createCommand($sql);
		$command->bindParam(":user",$driver_user);
		$result = $command->queryRow();
		if ($city_id == 0 && empty($result['city_id'])) {
//			$result['city_id'] = self::getCityId($driver_user);
            $city_arr = Dict::items('city_prefix');
            $city_arr = array_flip($city_arr);
            if (isset($city_arr[strtoupper(substr($driver_user,0,2))])) {
            	$city_code = $city_arr[strtoupper(substr($driver_user,0,2))];
            } else {
            	$city_code = 0;
            }
            $result['city_id'] = $city_code;
		}
		return $result;
	}
	/**
	 * 获取客户相关数据信息(已停用 使用缓存数据)
	 * @param array $result
	 */
	private function getCityId($driver_user = null) {
		if ($driver_user == null) {
			return '';
		}
		$sql = "SELECT code FROM t_dict WHERE name=:name AND dictname='city_prefix'";
		$command = Yii::app()->db_readonly->createCommand($sql);
		$command->bindParam(":name",strtoupper(substr($driver_user,0,2)));
		$data = $command->queryRow();
		$command->reset();
		
		$sql = "SELECT id FROM t_dict WHERE code = :code AND dictname='city'";
		$command = Yii::app()->db_readonly->createcommand($sql);
		$command->bindParam(":code",$data['code']);
		$city = $command->queryRow();
		return $city['id'];
	}
	
	/**
	 * 获取司机日志信息、判定司机是否为新注册、是否为解约  注：t_driver_log表中driver_id要加上索引
	 * @param array $result
	 * @param int $key
	 */
	private function getDriverLogInfo($driver_id=null,$date=null,$mark=0) {
		if ($driver_id == '') {
			return 0;
		}
		if ($mark == 2) {
			$order_by = "ORDER BY created ASC";
		}elseif ($mark == 3) {
			$order_by = "ORDER BY created DESC";
		}
		$sql = "SELECT from_unixtime(created,'%Y-%m-%d') AS createtime FROM t_driver_log WHERE driver_id='".$driver_id."' AND last_record LIKE '%\"mark\":\"".$mark."\"%' ".$order_by;
		$command = Yii::app()->db_readonly->createCommand($sql);
		$result = $command->queryRow();
		$command->reset();
		if (!$result) {
			return 0;
		}else {
			if ($date == $result['createtime']) {
				return 1;
			}else {
				return 0;
			}
		}
	}
	/**
	 * 将日常订单司机记录信息写入表中
	 * @param string $table
	 * @param array $data
	 */
	private function insertDailyOrderDriver($data=array(),$condition = array()) {
		if (empty($data) || empty($condition)) {
			return false;
		}
		
		//过滤测试工号 BY AndyCong 2013-06-20
		$test_driver_ids = Common::getTestDriverIds();
		if (in_array(strtoupper($data['driver_user']) , $test_driver_ids)) {
			return true;
		}
		//过滤测试工号 BY AndyCong 2013-06-20 END
		
		$sql = "INSERT INTO t_daily_order_driver(order_id,order_number,source,city_id,call_type,call_time,order_date,booking_time,reach_time,reach_distance,start_time,end_time,time_part,current_month,current_day,year,month,day,distance,charge,location_start,location_end,income,cast,coupon,status,user_id,customer_name,customer_type,phone,vipcard,is_new_user,is_active,driver,driver_id,driver_user,driver_phone,driver_imei,driver_picture,is_new_driver,is_left,created) VALUES (:order_id,:order_number,:source,:city_id,:call_type,:call_time,:order_date,:booking_time,:reach_time,:reach_distance,:start_time,:end_time,:time_part,:current_month,:current_day,:year,:month,:day,:distance,:charge,:location_start,:location_end,:income,:cast,:coupon,:status,:user_id,:customer_name,:customer_type,:phone,:vipcard,:is_new_user,:is_active,:driver,:driver_id,:driver_user,:driver_phone,:driver_imei,:driver_picture,:is_new_driver,:is_left,:created)";
		
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":order_id", $data['order_id']);
		$command->bindParam(":order_number", $data['order_number']);
		$source = intval($data['source']);
		$command->bindParam(":source", $source);
		$city_id = intval($data['city_id']);
		$command->bindParam(":city_id", $city_id);
		$command->bindParam(":call_type", $data['call_type']);
		$command->bindParam(":call_time", $data['call_time']);
		$command->bindParam(":order_date", $data['order_date']);
		$command->bindParam(":booking_time", $data['booking_time']);
		$command->bindParam(":reach_time", $data['reach_time']);
		$command->bindParam(":reach_distance", $data['reach_distance']);
		$command->bindParam(":start_time", $data['start_time']);
		$command->bindParam(":end_time", $data['end_time']);
		
		if ($data['booking_time'] >= $condition['start_time'] && $data['booking_time'] < $condition['start_time']+15*3600) {
			$time_part = 7;
		}elseif ($data['booking_time'] >= $condition['start_time']+15*3600 && $data['booking_time'] < $condition['start_time']+16*3600) {
			$time_part = 22;
		}elseif ($data['booking_time'] >= $condition['start_time']+16*3600 && $data['booking_time'] < $condition['start_time']+17*3600) {
			$time_part = 23;
		}
		elseif ($data['booking_time'] >= $condition['start_time']+17*3600 && $data['booking_time'] < $condition['start_time']+24*3600) {
			$time_part = 24;
		}
		$command->bindParam(":time_part", $time_part);
		
		$arr_date = explode('-',$condition['date']);
		$current_month = $arr_date[0].$arr_date[1];
		$current_day = $arr_date[0].$arr_date[1].$arr_date[2];
		$month = intval($arr_date[1]);
		$day = intval($arr_date[2]);
		$command->bindParam(":current_month", $current_month);
		$command->bindParam(":current_day", $current_day);
		$command->bindParam(":year", $arr_date[0]);
		$command->bindParam(":month", $month);
		$command->bindParam(":day", $day);
		
		$command->bindParam(":distance", $data['distance']);
		$command->bindParam(":charge", $data['charge']);
		$command->bindParam(":location_start", $data['location_start']);
		$command->bindParam(":location_end", $data['location_end']);
		$command->bindParam(":income", $data['income']);
		$command->bindParam(":cast", $data['cast']);
		$coupon = isset($data['coupon'])?$data['coupon']:0;
		$command->bindParam(":coupon", $coupon);
		$command->bindParam(":status", $data['status']);
		$command->bindParam(":user_id", $data['user_id']);
		$command->bindParam(":customer_name", $data['customer_name']);
		$customer_type = $data['customer_type']?$data['customer_type']:0;
		$command->bindParam(":customer_type", $customer_type);
		$command->bindParam(":phone", $data['phone']);
		$command->bindParam(":vipcard", $data['vipcard']);
		$command->bindParam(":is_new_user", $data['is_new_user']);
		$is_active = 0;
		$command->bindParam(":is_active", $is_active);
		$command->bindParam(":driver", $data['driver']);
		$command->bindParam(":driver_id", $data['driver_id']);
		$command->bindParam(":driver_user", $data['driver_user']);
		$command->bindParam(":driver_phone", $data['driver_phone']);
		$command->bindParam(":driver_imei", $data['driver_imei']);
		$command->bindParam(":driver_picture", $data['driver_picture']);
		$is_new_driver = 0;
		$is_left = 0;
		$command->bindParam(":is_new_driver", $is_new_driver);
		$command->bindParam(":is_left", $is_left);
		$command->bindParam(":created", time());
		
		$command->execute();
		$command->reset();
	}
	
	/**
	 * 更新t_daily_order_driver记录
	 * @param array $data
	 */
	private function updateDailyOrderDriver($data=array()) {
		if (empty($data)) {
			return false;
		}
		$sql = "UPDATE t_daily_order_driver SET order_number=:order_number,source=:source,call_type=:call_type,call_time=:call_time,order_date=:order_date,booking_time=:booking_time,reach_time=:reach_time,reach_distance=:reach_distance,start_time=:start_time,end_time=:end_time,distance=:distance,charge=:charge,location_start=:location_start,location_end=:location_end,income=:income,cast=:cast,status=:status,user_id=:user_id,customer_name=:customer_name,customer_type=:customer_type,phone=:phone,vipcard=:vipcard,driver=:driver,driver_user=:driver_user,driver_phone=:driver_phone,driver_imei=:driver_imei WHERE order_id=:order_id";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":order_number", $data['order_number']);
		$source = intval($data['source']);
		$command->bindParam(":source", $source);
		$command->bindParam(":call_type", $data['call_type']);
		$command->bindParam(":call_time", $data['call_time']);
		$command->bindParam(":order_date", $data['order_date']);
		$command->bindParam(":booking_time", $data['booking_time']);
		$command->bindParam(":reach_time", $data['reach_time']);
		$command->bindParam(":reach_distance", $data['reach_distance']);
		$command->bindParam(":start_time", $data['start_time']);
		$command->bindParam(":end_time", $data['end_time']);
		$command->bindParam(":distance", $data['distance']);
		$command->bindParam(":charge", $data['charge']);
		$command->bindParam(":location_start", $data['location_start']);
		$command->bindParam(":location_end", $data['location_end']);
		$command->bindParam(":income", $data['income']);
		$command->bindParam(":cast", $data['cast']);
		$command->bindParam(":status", $data['status']);
		$command->bindParam(":user_id", $data['user_id']);
		$command->bindParam(":customer_name", $data['customer_name']);
		$command->bindParam(":customer_type", $data['customer_type']);
		$command->bindParam(":phone", $data['phone']);
		$command->bindParam(":vipcard", $data['vipcard']);
		$command->bindParam(":driver", $data['driver']);
		$command->bindParam(":driver_user", $data['driver_user']);
		$command->bindParam(":driver_phone", $data['driver_phone']);
		$command->bindParam(":driver_imei", $data['driver_imei']);
		$command->bindParam(":order_id", $data['order_id']);
		$result = $command->execute();
		$command->reset();
	}
}
