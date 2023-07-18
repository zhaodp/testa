<?php
/**
 * 订单数据统计 model
 * @author AndyCong<congming@edaijia.cn>
 * @version web2.0 2013-01-30
 */
class OrderStat {
	
	public static $data_source = array(
		'order_all_count' => '订单数',
		'order_complate_count' => '报单数',
		'income_complate' => '总收入',
		'income_company' => '公司总收入',
		'average_price' => '客单价',
	);
	/**
	 * 获取订单趋势数据
	 * @param date $date
	 * @param string $type
	 */
	public function actionOrderTrendReport($date = null) {
		if ($date == null) {
			return false;
		}
		$date = str_replace('-' , '' , $date);
		$sql = "DELETE FROM t_order_trend_report WHERE date = :date";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":date" , $date);
		$command->execute();
		
		//增加查询cast字段。add by sunhongjing 2013-04-23
		$sql = "SELECT current_day,COUNT(id) AS order_all_count,COUNT(IF(`status`=1 OR `status` = 4,TRUE,NULL)) AS order_complate_count,SUM(IF(`status`=1 OR `status` = 4,income,0)) AS income_complate,SUM(IF(`status`=1 OR `status` = 4,abs(cast),0)) AS income_company,city_id,time_part,source,CASE WHEN distance <= 10 THEN 10 WHEN (distance > 10 AND distance <= 20) THEN 20 WHEN (distance > 20 AND distance <= 30) THEN 30 ELSE 9999 END AS distance_area FROM t_daily_order_driver WHERE current_day= :current_day GROUP BY city_id,source,time_part,distance_area";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":current_day",$date);
		$result = $command->queryAll();
		$order_all_count = 0;
		$order_complate_count = 0;
		$income_complate = 0;
		$income_company = 0;
		foreach ($result as $val) {
			$order_all_count += $val['order_all_count'];
			$order_complate_count += $val['order_complate_count'];
			$income_complate += $val['income_complate'];
			$income_company += $val['income_company'];
			self::insertOrderTrendRepord($val);
		}
		
		echo "date:".$date."----order_all_count:".$order_all_count."----order_complate_count:".$order_complate_count."----income_complate:".$income_complate."----income_company:".$income_company;
	}
	
	/**
	 * 记录订单趋势数据
	 * @param array $data
	 * @param date $date
	 */
	public function insertOrderTrendRepord($data = array()) {
		if (empty($data)) {
			return false;
		}
		$sql = "INSERT INTO t_order_trend_report(date,city_id,source,time_part,order_all_count,order_complate_count,income_complate,income_company,source_cost,distance_area,year,month,day,created) VALUES (:date,:city_id,:source,:time_part,:order_all_count,:order_complate_count,:income_complate,:income_company,:source_cost,:distance_area,:year,:month,:day,:created)";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":date" , $data['current_day']);
		$city_id = intval($data['city_id']);
		$command->bindParam(":city_id" , $city_id);
		$source = intval($data['source']);
		$command->bindParam(":source" , $data['source']);
		$command->bindParam(":time_part" , $data['time_part']);
		$command->bindParam(":order_all_count" , $data['order_all_count']);
		$command->bindParam(":order_complate_count" , $data['order_complate_count']);
		$command->bindParam(":income_complate" , $data['income_complate']);
		
		//信息费按从订单表中的取值计算，丛铭你咋在这里计算信息费呢，太不靠谱了
		// modify by sunhongjing 2013-04-23
//		if ($data['time_part'] == 7) {
//			$income_company = intval($data['order_complate_count'])*5;
//		}elseif ($data['time_part'] == 22) {
//			$income_company = intval($data['order_complate_count'])*10;
//		}elseif ($data['time_part'] == 23) {
//			$income_company = intval($data['order_complate_count'])*15;
//		}elseif ($data['time_part'] == 24) {
//			$income_company = intval($data['order_complate_count'])*20;
//		}
		
		//为兼容之前老数据，对公司信息费取绝对值处理，因为丛2013-04-07号之前cast表中纪录d信息费为负值，add by sunhongjing 2013-04-23
		$command->bindParam(":income_company" , abs($data['income_company']));
		$command->bindParam(":source_cost" , $data['source_cost']);
		$command->bindParam(":distance_area" , $data['distance_area']);
		
		$year = substr($data['current_day'] , 0 , 4);
		$month = substr($data['current_day'] , 4 , 2);
		$day = substr($data['current_day'] , 6 , 2);
		
		$command->bindParam(":year" , $year);
		$command->bindParam(":month" , $month);
		$command->bindParam(":day" , $day);
		$created = time();
		$command->bindParam(":created" , $created);
		
		$command->execute();
		$command->reset();
	}
	
	/**
	 * 更新订单趋势数据
	 * @param array $data
	 * @param date $date
	 */
	public function updateOrderTrendRepord($data = array() , $date = null) {
		if (empty($data) || $date == null) {
			return false;
		}
		$sql = "UPDATE t_order_trend_report SET order_all_count = :order_all_count, order_complate_count = :order_complate_count , income_complate = :income_complate , income_company = :income_company , source_cost = :source_cost WHERE date = :date AND city_id = :city_id AND source = :source AND time_part = :time_part";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":order_all_count" , $data['order_all_count']);
		$command->bindParam(":order_complate_count" , $data['order_complate_count']);
		$command->bindParam(":income_complate" , $data['income_complate']);
		if ($data['time_part'] == 7) {
			$income_company = intval($data['order_complate_count'])*5;
		}elseif ($data['time_part'] == 22) {
			$income_company = intval($data['order_complate_count'])*10;
		}elseif ($data['time_part'] == 23) {
			$income_company = intval($data['order_complate_count'])*15;
		}elseif ($data['time_part'] == 24) {
			$income_company = intval($data['order_complate_count'])*20;
		}
		$command->bindParam(":income_company" , $income_company);
		$command->bindParam(":source_cost" , $data['source_cost']);
		$command->bindParam(":date" , $data['current_day']);
		$city_id = intval($data['city_id']);
		$command->bindParam(":city_id" ,$city_id);
		$source = intval($data['source']);
		$command->bindParam(":source" , $source);
		$command->bindParam(":time_part" , $data['time_part']);
		
		$command->execute();
		$command->reset();
	}
	
	/**
	 * 校验订单趋势记录是否存在
	 * @param array $data
	 */
	public function checkOrderTrend(&$data) {
		$sql = "SELECT id FROM t_order_trend_report WHERE date = :date AND city_id = :city_id AND source = :source AND time_part = :time_part";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":date" , $data['date']);
		$city_id = intval($data['city_id']);
		$command->bindParam(":city_id" , $city_id);
		$source = intval($data['source']);
		$command->bindParam(":source" , $source);
		$command->bindParam(":time_part" , $data['time_part']);
		$result = $command->queryRow();
		if (empty($result)) {
			return false;
		}else {
			return true;
		}
	}
	
	/**
	 * 订单周报统计
	 * @param string $date
	 * @param string $type
	 */
	public function actionOrderWeeklyReport($date = null) {
		if ($date == null) {
			return false;
		}
		$date = str_replace('-' , '' , $date);
		$sql = "DELETE FROM t_order_week_report WHERE date = :date";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":date" , $date);
		$command->execute();
		
		$weekarray=array("日","一","二","三","四","五","六");		
		$time = strtotime($date);
		$week_cn = $weekarray[date('w' , $time)];
		$week_en = date('D' , $time);
		$prev_date = date("Ymd" , ($time-7*86400));
		$result = self::getOrderWeeklyReport($date);
		foreach ($result as $val) {
			$val['week_cn'] = $week_cn;
			$val['week_en'] = $week_en;
			$last_week_data = self::getLastOrderWeeklyReport($val , $prev_date);
			if ($last_week_data) {
				$val['last_all_count'] = $last_week_data['all_count'];
				$val['last_complate_count'] = $last_week_data['complate_count'];
				$val['last_cancel_count'] = $last_week_data['cancel_count'];
				$val['last_income_complate'] = $last_week_data['income_complate'];
				$val['last_income_company'] = $last_week_data['income_company'];
			} else {
				$val['last_all_count'] = 0;
				$val['last_complate_count'] = 0;
				$val['last_cancel_count'] = 0;
				$val['last_income_complate'] = 0;
				$val['last_income_company'] = 0;
			}
			self::insertOrderWeeklyReport($val , $date);
		}
	}
	
	/**
	 * 获取订单周报数据
	 * @param string $date
	 * @return array $data
	 */
	private function getOrderWeeklyReport($date = null) { 
		if ($date < '20130416') {
			$sql = "SELECT current_day,COUNT(id) AS all_count,COUNT(IF(`status`=1 OR `status` = 4,TRUE,NULL)) AS complate_count,COUNT(IF(`status`=0,TRUE,NULL)) AS ready_count,COUNT(IF(`status`=3,TRUE,NULL)) AS cancel_count,SUM(IF(`status`=1 OR `status` = 4,income,0)) AS income_complate,city_id,time_part,source,SUM(CASE WHEN time_part=7 AND `status` IN(1,4) THEN 5 WHEN time_part=22 AND `status` IN(1,4) THEN 10 WHEN time_part=23 AND `status` IN(1,4) THEN 15 WHEN time_part=24 AND `status` IN(1,4) THEN 20 ELSE 0 END) AS income_company FROM t_daily_order_driver WHERE current_day= :current_day GROUP BY city_id,source,time_part";
		} else {
			$sql = "SELECT current_day,COUNT(id) AS all_count,COUNT(IF(`status`=1 OR `status` = 4,TRUE,NULL)) AS complate_count,COUNT(IF(`status`=0,TRUE,NULL)) AS ready_count,COUNT(IF(`status`=3,TRUE,NULL)) AS cancel_count,SUM(IF(`status`=1 OR `status` = 4,income,0)) AS income_complate,city_id,time_part,source,SUM(IF(`status`=1 OR `status` = 4,abs(cast),0)) AS income_company FROM t_daily_order_driver WHERE current_day= :current_day GROUP BY city_id,source,time_part";
		}
	    
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":current_day" , $date);
		$data = $command->queryAll();
		foreach ($data as $key=>$val) {
			$customers = self::getNewCustomerReport($val);
			$data[$key]['repeat'] = $customers['repeat'];
			$data[$key]['fresh_callcenter'] = $customers['fresh_callcenter'];
			$data[$key]['fresh_app'] = $customers['fresh_app'];
			$data[$key]['fresh_order_count'] = $customers['fresh_order_count'];
		}
		return $data;
	}
	
	/**
	 * 获取新客数据
	 * @param array $data
	 * @return array $arr
	 */
	private function getNewCustomerReport($data = array()) {
		$sql = "SELECT DISTINCT(phone) AS phone FROM t_daily_order_driver WHERE current_day = :current_day AND city_id = :city_id AND source = :source AND time_part = :time_part AND status IN(1 , 4)";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":current_day" , $data['current_day']);
		$command->bindParam(":city_id" , $data['city_id']);
		$command->bindParam(":source" , $data['source']);
		$command->bindParam(":time_part" , $data['time_part']);
		$result = $command->queryAll();
		$arr = array();
		$arr['repeat'] = 0;
		$arr['fresh_callcenter'] = 0;
		$arr['fresh_app'] = 0;
		$arr['fresh_order_count'] = 0;
		foreach ($result as $val) {
			$customers = self::getNewCustomerOrderNum($val['phone'] , $data['current_day'] , $data);
			$arr['repeat'] += $customers['repeat'];
			$arr['fresh_callcenter'] += $customers['fresh_callcenter'];
			$arr['fresh_app'] += $customers['fresh_app'];
			$arr['fresh_order_count'] += $customers['fresh_order_count'];
		}
		return $arr;
	}
	
	/**
	 * 获取新客下单数
	 * @param string $phone
	 * @param string $date
	 * @param array $data
	 * @return int $count
	 */
	private function getNewCustomerOrderNum($phone = null , $date = null , $data = array()) {
		if ($phone == null || $date == null) {
			return false;
		}
		$sql = "SELECT COUNT(order_id) AS cnt FROM t_daily_order_driver WHERE current_day < :date AND phone = :phone AND status IN(1 , 4)";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":date" , $date);
		$command->bindParam(":phone" , $phone);
		$result = $command->queryRow();
		$arr = array();
		$repeat = 0;
		$fresh_order_count = 0;
		$fresh_callcenter = 0;
		$fresh_app = 0;
		if ($result['cnt'] == 0) {
			$sql = "SELECT COUNT(order_id) AS cnt FROM t_daily_order_driver WHERE current_day = :current_day AND city_id = :city_id AND source = :source AND time_part = :time_part AND phone = :phone AND status IN(1 , 4)";
			$command = Yii::app()->dbreport->createCommand($sql);
			$command->bindParam(":current_day" , $data['current_day']);
			$command->bindParam(":city_id" , $data['city_id']);
			$command->bindParam(":source" , $data['source']);
			$command->bindParam(":time_part" , $data['time_part']);
			$command->bindParam(":phone" , $phone);
			$result_fresh = $command->queryRow();
			if ($data['source'] == 0 || $data['source'] == 2) {
				$fresh_app = 1;
			} else {
				$fresh_callcenter = 1;
			}
			$fresh_order_count = $result_fresh['cnt'];
		} else {
			$repeat = 1;
		}
		$arr = array(
		    'repeat' => $repeat,
		    'fresh_callcenter' => $fresh_callcenter,
		    'fresh_app' => $fresh_app,
		    'fresh_order_count' => $fresh_order_count,
		);
		return $arr;
	}
	
	/**
	 * 记录订单周报统计 t_order_week_report
	 * @param array $data
	 * @param string $type
	 */
	public function insertOrderWeeklyReport($data = array()) {
		if (empty($data)) {
			return false;
		}
		$sql = "INSERT INTO t_order_week_report(date,week_cn,week_en,city_id,source,time_part,all_count,complate_count,ready_count,cancel_count,income_complate,income_company,last_all_count,last_complate_count,last_cancel_count,last_income_complate,last_income_company,`repeat`,fresh_callcenter,fresh_app,fresh_order_count,created) VALUES(:date,:week_cn,:week_en,:city_id,:source,:time_part,:all_count,:complate_count,:ready_count,:cancel_count,:income_complate,:income_company,:last_all_count,:last_complate_count,:last_cancel_count,:last_income_complate,:last_income_company,:repeat,:fresh_callcenter,:fresh_app,:fresh_order_count,:created)";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":date" , $data['current_day']);
		$command->bindParam(":week_cn" , $data['week_cn']);
		$command->bindParam(":week_en" , $data['week_en']);
		$command->bindParam(":city_id" , $data['city_id']);
		$command->bindParam(":source" , $data['source']);
		$command->bindParam(":time_part" , $data['time_part']);
		$command->bindParam(":all_count" , $data['all_count']);
		$command->bindParam(":complate_count" , $data['complate_count']);
		$command->bindParam(":ready_count" , $data['ready_count']);
		$command->bindParam(":cancel_count" , $data['cancel_count']);
		$command->bindParam(":income_complate" , $data['income_complate']);
		$command->bindParam(":income_company" , $data['income_company']);
		$command->bindParam(":last_all_count" , $data['last_all_count']);
		$command->bindParam(":last_complate_count" , $data['last_complate_count']);
		$command->bindParam(":last_cancel_count" , $data['last_cancel_count']);
		$command->bindParam(":last_income_complate" , $data['last_income_complate']);
		$command->bindParam(":last_income_company" , $data['last_income_company']);
		$command->bindParam(":repeat" , $data['repeat']);
		$command->bindParam(":fresh_callcenter" , $data['fresh_callcenter']);
		$command->bindParam(":fresh_app" , $data['fresh_app']);
		$command->bindParam(":fresh_order_count" , $data['fresh_order_count']);
		$created = time();
		$command->bindParam(":created" , $created);
		$command->execute();
		$command->reset();
	}
	
	/**
	 * 更新订单周报统计 t_order_week_report
	 * @param array $data
	 * @param string $type
	 */
	public function updateOrderWeeklyReport($data = array()) {
		if (empty($data)) {
			return false;
		}
		$sql = "UPDATE t_order_week_report SET complate_count = :complate_count , ready_count = :ready_count , cancel_count = :cancel_count , income_complate = :income_complate , income_company = :income_company WHERE date = :date AND city_id = :city_id AND source = :source AND time_part = :time_part";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":complate_count" , $data['complate_count']);
		$command->bindParam(":ready_count" , $data['ready_count']);
		$command->bindParam(":cancel_count" , $data['cancel_count']);
		$command->bindParam(":income_complate" , $data['income_complate']);
		$command->bindParam(":income_company" , $data['income_company']);
		$command->bindParam(":date" , $data['current_day']);
		$command->bindParam(":city_id" , $data['city_id']);
		$command->bindParam(":source" , $data['source']);
		$command->bindParam(":time_part" , $data['time_part']);
	}
	
	/**
	 * 校验订单周报记录是否存在
	 * @param array $data
	 * @return boolean
	 */
	public function checkOrderWeeklyReport($data = array()) {
		if (empty($data)) {
			return false;
		}
		$sql = "SELECT id FROM t_order_week_report WHERE date = :date AND city_id = :city_id AND source = :source AND time_part = :time_part";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":date" , $data['current_day']);
		$command->bindParam(":city_id" , $data['city_id']);
		$command->bindParam(":source" , $data['source']);
		$command->bindParam(":time_part" , $data['time_part']);
		$result = $command->queryRow();
		if (!empty($result)) {
			return true;
		}else {
			return false;
		}
	}
	
	/**
	 * 获取上周同期数据
	 * @param array $data
	 * @param string $date
	 * @return array $result
	 */
	private function getLastOrderWeeklyReport($data = array() , $date = null) {
		if (empty($data) || $date == null) {
			return false;
		}
		$sql = "SELECT all_count,complate_count,ready_count,cancel_count,income_complate,income_company FROM t_order_week_report WHERE date = :date AND city_id = :city_id AND source = :source AND time_part = :time_part AND week_cn = :week_cn";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":date" , $date);
		$command->bindParam(":city_id" , $data['city_id']);
		$command->bindParam(":source" , $data['source']);
		$command->bindParam(":time_part" , $data['time_part']);
		$command->bindParam(":week_cn" , $data['week_cn']);
		$result = $command->queryRow();
		return $result;
	}
	
	/**
	 * 订单统计重新加载
	 */
	public function actionOrderReportReload() {
		//优化 重新load前天数据 BY AndyCong
		$date = date('Y-m-d' , strtotime('-2 day'));
		self::actionOrderTrendReport($date);
		self::actionOrderWeeklyReport($date);
		//优化 重新load前天数据 BY AndyCong END
		
//		$unfinished_order_date = self::getUnfinishedOrderDate(); 
//		$current_time = time();
//		foreach ($unfinished_order_date as $date) {
//			if (($current_time - strtotime($date['current_day'])) <= 31*86400) {
//				self::actionOrderTrendReport($date['current_day']);
//				self::actionOrderWeeklyReport($date['current_day']);
//			}
//		}
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
	 * 订单统计初始化
	 * @param string $month
	 */
	public function actionOrderReportInitialize($month = null) {
		if ($month == null) {
			return false;
		}else {
			$arr = explode('-' , $month);
		    $count = date('d' , strtotime ( $arr[0] .'-'. ($arr[1]+1) .'-' . '01') - 1);
		    if ($count == '01') {
				$count = 31;
			}
		    for ($i = 1;$i <= $count;$i++) {
				if ($i < 10) {
					$date = $month.'-0'.$i;
				}else {
					$date = $month.'-'.$i;
				}
				self::actionOrderTrendReport($date);
				self::actionOrderWeeklyReport($date);
			}
		}
	}
	/**
	 * 每天定时执行脚本，跑前一天的数据
	 */
	public function actionCrontab() {
		$date = date('Y-m-d' , strtotime('-1 day'));
		self::actionOrderReportReload();
		self::actionOrderTrendReport($date);
		self::actionOrderWeeklyReport($date);
	}
	/**
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 * ++++++++++++++++++++++++++++++上边是统计method++++++++++++++++++++++++++++++
	 * ++++++++++++++++++++++++++++++下边是展示method++++++++++++++++++++++++++++++
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 */
	
	/**
	 * 获取订单趋势数据做展示
	 * @param array $condition
	 * @param int $pages
	 * @return array
	 */
	public function getOrderTrendData($condition = array() , $pages = 31, $data_source='order_complate_count') {
		$sql = "SELECT date,SUM(order_all_count) AS order_all_count,SUM(order_complate_count) AS order_complate_count,SUM(income_complate) AS income_complate,SUM(income_company) AS income_company,";
		
		//报单数
		$sql .= "SUM(IF(time_part=7,order_complate_count,0)) AS chart_order_complate_count_seven,";
		$sql .= "SUM(IF(time_part=22,order_complate_count,0)) AS chart_order_complate_count_twentytwo,";
		$sql .= "SUM(IF(time_part=23,order_complate_count,0)) AS chart_order_complate_count_twentythree,";
		$sql .= "SUM(IF(time_part=24,order_complate_count,0)) AS chart_order_complate_count_twentyfour,";
		//订单数
		$sql .= "SUM(IF(time_part=7,order_all_count,0)) AS chart_order_all_count_seven,";
		$sql .= "SUM(IF(time_part=22,order_all_count,0)) AS chart_order_all_count_twentytwo,";
		$sql .= "SUM(IF(time_part=23,order_all_count,0)) AS chart_order_all_count_twentythree,";
		$sql .= "SUM(IF(time_part=24,order_all_count,0)) AS chart_order_all_count_twentyfour,";
		//总收入
		$sql .= "SUM(IF(time_part=7,income_complate,0)) AS chart_income_complate_seven,";
		$sql .= "SUM(IF(time_part=22,income_complate,0)) AS chart_income_complate_twentytwo,";
		$sql .= "SUM(IF(time_part=23,income_complate,0)) AS chart_income_complate_twentythree,";
		$sql .= "SUM(IF(time_part=24,income_complate,0)) AS chart_income_complate_twentyfour,";
		//公司收入
		$sql .= "SUM(IF(time_part=7,income_company,0)) AS chart_income_company_seven,";
		$sql .= "SUM(IF(time_part=22,income_company,0)) AS chart_income_company_twentytwo,";
		$sql .= "SUM(IF(time_part=23,income_company,0)) AS chart_income_company_twentythree,";
		$sql .= "SUM(IF(time_part=24,income_company,0)) AS chart_income_company_twentyfour,";
		
		$sql .= "city_id,source,time_part , (SUM(IF((source IN (:source_callcenter)), order_complate_count , 0))+SUM(IF((source IN (:source_callcenter_input)), order_complate_count , 0))) as callcenter_count,(SUM(IF((source IN (:source_client_input)), order_complate_count , 0))+SUM(IF((source IN (:source_client)), order_complate_count, 0))) as app_count FROM t_order_trend_report WHERE date BETWEEN :start_time AND :end_time";
		if (!empty($condition['city_id'])) {
			$sql .= " AND city_id = :city_id";
			$city_arr = Dict::items('city');
			$city = $city_arr[$condition['city_id']];
		}else {
			$city = '全部';
		}
		if (!empty($condition['time_part'])) {
			$sql .= " AND time_part = :time_part";
		}
		if (!empty($condition['source'])) {
			$sql .= " AND source = :source";
		}
		if (!empty($condition['distance_area'])) {
			$sql .= " AND distance_area = :distance_area";
		}
		$sql .= " GROUP BY date ORDER BY date DESC";
		$command = Yii::app()->dbreport->createCommand($sql);
		$start_time = str_replace('-' , '' , $condition['start_time']);
		$end_time = str_replace('-' , '' , $condition['end_time']);
		$command->bindParam(":start_time" , $start_time);
		$command->bindParam(":end_time" ,$end_time);
                $source_callcenter = join(',', Order::$callcenter_sources);
		$source_callcenter_input = join(',', Order::$callcenter_input_sources);
		$source_client_input = join(',', Order::$client_input_source);
		$source_client = join(',', Order::$client_source);

		$command->bindParam(":source_callcenter" , $source_callcenter);
		$command->bindParam(":source_callcenter_input" ,$source_callcenter_input);
		$command->bindParam(":source_client_input" , $source_client_input);
		$command->bindParam(":source_client" ,$source_client);
		if (!empty($condition['city_id'])) {
			$command->bindParam(":city_id" , $condition['city_id']);
		}
		if (!empty($condition['time_part'])) {
			$command->bindParam(":time_part" , $condition['time_part']);
		}
		if (!empty($condition['source'])) {
			$command->bindParam(":source" , $condition['source']);
		}
		if (!empty($condition['distance_area'])) {
			$command->bindParam(":distance_area" , $condition['distance_area']);
		}
		$result = $command->queryAll();
		foreach ($result as $key=>$val) {
			$result[$key]['city'] = $city;
            $result[$key]['city_id'] = $condition['city_id'];
		}
		$chart = $this->getChartData($result,$condition['data_source']);
		$dataProvider = new CArrayDataProvider($result, array (
			'id'=>'order_trend', 
			'keyField'=>'date', 
			'pagination'=>array (
				'pageSize'=>$pages)
			)
		);
		
		$data['dataProvider'] = $dataProvider;
		$data['chart'] = $chart;
		return $data;
	}
	
	/**
	 * 获取图表数据
	 * @param unknown_type $data
	 * @param unknown_type $field
	 * @return string
	 */
	public function getChartData(&$result, $data_source) {
		$data = array();
		$date = '';
		$order_count = '';
		$chart_seven = '';
		$chart_twentytwo = '';
		$chart_twentythree = '';
		$chart_twentyfour = '';
		if ($data_source != 'average_price') {
			foreach ($result as $val) {
				$date = date("m-d" , strtotime($val['date'])).",".$date;
				$order_count = $val[$data_source].",".$order_count;
				$chart_seven = $val['chart_'.$data_source.'_seven'].",".$chart_seven;
				$chart_twentytwo = $val['chart_'.$data_source.'_twentytwo'].",".$chart_twentytwo;
				$chart_twentythree = $val['chart_'.$data_source.'_twentythree'].",".$chart_twentythree;
				$chart_twentyfour = $val['chart_'.$data_source.'_twentyfour'].",".$chart_twentyfour;
			}
		} else {
			//number_format($data["income_complate"]/$data["order_complate_count"],2)
			foreach ($result as $val) {
				$date = date("m-d" , strtotime($val['date'])).",".$date;
				if ($val['order_complate_count']>0) {
					$order_count = number_format($val['income_complate']/$val['order_complate_count'],2).",".$order_count;
				} else {
					$order_count = "0,".$order_count;;
				}
				if ($val['chart_order_complate_count_seven'] > 0) {
					$chart_seven = number_format($val['chart_income_complate_seven']/$val['chart_order_complate_count_seven'],2).",".$chart_seven;
				} else {
					$chart_seven = "0,".$chart_seven;
				}
				if ($val['chart_order_complate_count_twentytwo']>0) {
					$chart_twentytwo = number_format($val['chart_income_complate_twentytwo']/$val['chart_order_complate_count_twentytwo'],2).",".$chart_twentytwo;
				} else {
					$chart_twentytwo = "0,".$chart_twentytwo;
				}
				if ($val['chart_order_complate_count_twentythree']>0) {
					$chart_twentythree = number_format($val['chart_income_complate_twentythree']/$val['chart_order_complate_count_twentythree'],2).",".$chart_twentythree;
				} else {
					$chart_twentythree = "0,".$chart_twentythree;
				}
				if ($val['chart_order_complate_count_twentyfour'] >0) {
					$chart_twentyfour = number_format($val['chart_income_complate_twentyfour']/$val['chart_order_complate_count_twentyfour'],2).",".$chart_twentyfour;
				} else {
					$chart_twentyfour = "0,".$chart_twentyfour;
				}
			}		
		}
		$date = substr($date , 0 , strlen($date)-1);
		$data['date'] = "'" . str_replace(",", "','", $date) . "'";
		$data['order_count'] = substr($order_count , 0 , strlen($order_count)-1);
		$data['chart_seven'] = substr($chart_seven , 0 , strlen($chart_seven)-1);
		$data['chart_twentytwo'] = substr($chart_twentytwo , 0 , strlen($chart_twentytwo)-1);
		$data['chart_twentythree'] = substr($chart_twentythree , 0 , strlen($chart_twentythree)-1);
		$data['chart_twentyfour'] = substr($chart_twentyfour , 0 , strlen($chart_twentyfour)-1);
		$data['subtitle'] = self::$data_source[$data_source];
		return $data;
	}
	
	/**
	 * 获取周报数据做展示
	 * @param array $condition
	 * @return object $dataProvider
	 */
	public function getOrderWeeklyData($condition = array()) {
		$sql = "SELECT `date` ,  week_cn , SUM(all_count) AS all_count , SUM(complate_count) AS complate_count , SUM(ready_count) AS ready_count , SUM(cancel_count) AS cancel_count , SUM(income_complate) AS income_complate , SUM(income_company) AS income_company , SUM(last_all_count) AS last_all_count , SUM(last_complate_count) AS last_complate_count , SUM(last_cancel_count) AS last_cancel_count , SUM(last_income_complate) AS last_income_complate , SUM(last_income_company) AS last_income_company , (SUM(IF((source IN (:source_callcenter)), complate_count , 0))+SUM(IF((source IN (:source_callcenter_input)), complate_count , 0))) as callcenter_count,(SUM(IF((source IN (:source_client_input)), complate_count , 0))+SUM(IF((source IN (:source_client)), complate_count , 0))) as app_count  FROM t_order_week_report WHERE `date` BETWEEN :start_time AND :end_time";
		if (!empty($condition['city_id'])) {
			$sql .= " AND city_id = :city_id";
			$city_arr = Dict::items('city');
			$city = $city_arr[$condition['city_id']];
			$city_id = $condition['city_id'];
		}else {
			$city = '全部';
			$city_id = 0;
		}
		if (!empty($condition['source'])) {
			$sql .= " AND source = :source";
		}
		if (!empty($condition['time_part'])) {
			$sql .= " AND time_part = :time_part";
		}
		$sql .= " GROUP BY `date` ORDER BY `date` DESC";
		$command = Yii::app()->dbreport->createCommand($sql);
		$start_time = str_replace('-' , '' , $condition['start_time']);
		$end_time = str_replace('-' , '' , $condition['end_time']);
		$command->bindParam(":start_time" , $start_time);
		$command->bindParam(":end_time" , $end_time);

                $source_callcenter = join(',', Order::$callcenter_sources);
		$source_callcenter_input = join(',', Order::$callcenter_input_sources);
		$source_client_input = join(',', Order::$client_input_source);
		$source_client = join(',', Order::$client_source);

		$command->bindParam(":source_callcenter" , $source_callcenter);
		$command->bindParam(":source_callcenter_input" ,$source_callcenter_input);
		$command->bindParam(":source_client_input" , $source_client_input);
		$command->bindParam(":source_client" ,$source_client);
		if (!empty($condition['city_id'])) {
			$command->bindParam(":city_id" , $condition['city_id']);
		}
		if (!empty($condition['source'])) {
			$command->bindParam(":source" , $condition['source']);
		}
		if (!empty($condition['time_part'])) {
			$command->bindParam(":time_part" , $condition['time_part']);
		}
		$result = $command->queryAll();
		foreach ($result as $key=>$val) {
			$result[$key]['city'] = $city;
			$result[$key]['city_id'] = $city_id;
		}
		$dataProvider = new CArrayDataProvider($result , array (
			'id'=>'order_trend', 
			'keyField'=>'date', 
			'pagination'=>false,
			));
		$chart = self::getWeeklyChartData();
		$data = array();
		$data['dataProvider'] = $dataProvider;
		$data['chart'] = $chart;
		return $data;	
	}
	
	/**
	 * 获取周报图表数据
	 * @return array $data
	 */
	private function getWeeklyChartData() {
		$date_arr = array();
		$week_num = date('w' , strtotime('-1 day'));
		$date_arr['本周']['end_time'] = date('Ymd' , strtotime('-1 day'));
		if ($week_num == 6) {
			$date_arr['本周']['start_time'] = date('Ymd' , strtotime('-7 day'));
		}else {
			$date_arr['本周']['start_time'] = date('Ymd' , strtotime('-'.($week_num+1).' day'));
		}
		$time = strtotime($date_arr['本周']['start_time']);
		$date_arr['上周']['end_time'] = date('Ymd' , $time-86400);
		$date_arr['上周']['start_time'] = date('Ymd' , $time-7*86400);
		
		$date_arr[date('m-d' , $time-14*86400) ."~". date('m-d' , $time-8*86400)]['end_time'] = date('Ymd' , $time-8*86400);
		$date_arr[date('m-d' , $time-14*86400) ."~". date('m-d' , $time-8*86400)]['start_time'] = date('Ymd' , $time-14*86400);
		
		$date_arr[date('m-d' , $time-21*86400) ."~". date('m-d' , $time-15*86400)]['end_time'] = date('Ymd' , $time-15*86400);
		$date_arr[date('m-d' , $time-21*86400) ."~". date('m-d' , $time-15*86400)]['start_time'] = date('Ymd' , $time-21*86400);
		
		$date_arr[date('m-d' , $time-28*86400) ."~". date('m-d' , $time-22*86400)]['end_time'] = date('Ymd' , $time-22*86400);
		$date_arr[date('m-d' , $time-28*86400) ."~". date('m-d' , $time-22*86400)]['start_time'] = date('Ymd' , $time-28*86400);
		
		$date = '';
		$complate_count = '';
		$callcenter_count = '';
		$app_count = '';
		$repeat_customer = '';
		$fresh_customer = '';
		$fresh_order_count = '';
		foreach ($date_arr as $key=>$val) {
			$date = $key.",".$date;
			$sql = "SELECT SUM(complate_count) AS complate_count , (SUM(IF((source IN (:source_callcenter)), complate_count , 0))+SUM(IF((source IN (:source_callcenter_input)), complate_count , 0))) as callcenter_count,(SUM(IF((source IN (:source_client_input)), complate_count , 0))+SUM(IF((source IN (:source_client)), complate_count , 0))) as app_count,SUM(`repeat`) AS repeat_customer,SUM(fresh_callcenter+fresh_app) AS fresh_customer , SUM(fresh_order_count) AS fresh_order_count FROM t_order_week_report WHERE `date` BETWEEN :start_time AND :end_time";
			$command = Yii::app()->dbreport->createCommand($sql);

                        $source_callcenter = join(',', Order::$callcenter_sources);
		        $source_callcenter_input = join(',', Order::$callcenter_input_sources);
		        $source_client_input = join(',', Order::$client_input_source);
		        $source_client = join(',', Order::$client_source);

			$command->bindParam(":source_callcenter" , $source_callcenter);
			$command->bindParam(":source_callcenter_input" , $source_callcenter_input);
			$command->bindParam(":source_client_input" , $source_client_input);
			$command->bindParam(":source_client" , $source_client);
			$command->bindParam(":start_time" , $val['start_time']);
			$command->bindParam(":end_time" , $val['end_time']);
			$result = $command->queryRow();
			$complate_count = $result['complate_count'] .','.$complate_count;
			$callcenter_count = $result['callcenter_count'] .','.$callcenter_count;
			$app_count = $result['app_count'] .','.$app_count;
			$repeat_customer = $result['repeat_customer'] .','.$repeat_customer;
			$fresh_customer = $result['fresh_customer'] .','.$fresh_customer;
			$fresh_order_count = $result['fresh_order_count'] .','.$fresh_order_count;
		}
		$data = array();
		$date = substr($date , 0 , strlen($date)-1);
		$data['date'] = "'" . str_replace(",", "','", $date) . "'";
		$data['complate_count'] = substr($complate_count , 0 , strlen($complate_count)-1);
		$data['callcenter_count'] = substr($callcenter_count , 0 , strlen($callcenter_count)-1);
		$data['app_count'] = substr($app_count , 0 , strlen($app_count)-1);
		$data['repeat_customer'] = substr($fresh_customer , 0 , strlen($fresh_customer)-1);
		$data['fresh_customer'] = substr($fresh_customer , 0 , strlen($fresh_customer)-1);
		$data['fresh_order_count'] = substr($fresh_order_count , 0 , strlen($fresh_order_count)-1);
		return $data;
	}
	
	/**
	 * 获取月报数据
	 * @return array $data
	 */
	public function getOrderMonthlyData($condition=null) {
		$sql = "SELECT year FROM t_daily_order_driver GROUP BY year ORDER BY year DESC";
		$command = Yii::app()->dbreport->createCommand($sql);
		$result = $command->queryAll();
		$data = array();
		foreach ($result as $key=>$val) {
			$sql = "SELECT month , COUNT(IF(`status`=1 OR `status` = 4,TRUE,NULL)) AS order_complate_count FROM t_daily_order_driver WHERE year = :year ";
			if (isset($condition['city_id']) && $condition['city_id']!=0) {
				$sql .= ' AND city_id= :city_id';
			}
			$sql .= " GROUP BY month ORDER BY month ASC";
			$command = Yii::app()->dbreport->createCommand($sql);
			$command->bindParam(":year" , $val['year']);
			if (isset($condition['city_id']) && $condition['city_id']!=0) {
				$command->bindParam(":city_id" , $condition['city_id']);
			}
			$result_year_data = $command->queryAll();
			$month = '';
			$order_complate_count = '';
			foreach ($result_year_data as $data_key=>$data_val) {
				$month = $month.','.$data_val['month']."月份";
				$order_complate_count = $order_complate_count.','.$data_val['order_complate_count'];
			}
			$month = substr($month , 1);
			$data[$val['year']]['month'] = "'" . str_replace(",", "','", $month) . "'";
			$data[$val['year']]['order_complate_count'] = substr($order_complate_count , 1);
		}
		return $data;
	}

    /**
     * 获取月报数据
     * @return array $data
     */
    public function getOrderMonthlyDataNew($city_id,$flush = false) {
        $redis = RedisHAProxy::model();

        $data_key = 'ORDER_MONTH_STAT_DATA_'.$city_id;

        $data_stat = $redis->get($data_key);
        $data = array();
        if($data_stat){
            $data_stat = json_decode($data_stat,1);

            //print_r($data_stat);die;
            foreach($data_stat as $year => $value){
                foreach($value as $month => $count){
                    $month_data[$year][] = '\''.$month.'月份\'';
                    $count_data[$year][] = $count;
                }
//                $year_now = date('Y');
//                $month_now = date('n');
//                if($year == $year_now){
//                    $new_data = $this->getYearData($year,$month_now);
//                    if($new_data){
//                        $month_data[$year][] = '\''.$month_now.'月份\'';
//                        $count_data[$year][] = isset($new_data[$year_now][$month_now]) ? $new_data[$year_now][$month_now] : 0;
//                    }
//                }


                $data[$year]['month'] = implode(',',$month_data[$year]);
                $data[$year]['order_complate_count'] = implode(',',$count_data[$year]);
            }

        }
        krsort($data);
        return $data;
    }

    private function getYearData($update_year,$update_month,$city_id){
        $sql = "SELECT  COUNT(IF(`status` = 1 OR `status` = 4,TRUE,NULL)) AS order_complate_count FROM t_daily_order_driver WHERE year = :year and month = :month";
        if (isset($city_id) && $city_id!=0) {
            $sql .= ' AND city_id= :city_id';
        }

        $command = Yii::app()->dbreport->createCommand($sql);
        $command->bindParam(":year" , $update_year);
        $command->bindParam(':month', $update_month);
        if (isset($city_id) && $city_id!=0) {
            $command->bindParam(":city_id" , $city_id);
        }
        $result_year_data = $command->queryRow();
        if($result_year_data){
            $old_data[$update_year][$update_month] = $result_year_data['order_complate_count'];
            return $old_data;
        }
        else return false;
    }


    /**
     * @param $city_id
     * @param string $type 分为两种type  1 init 初始化数据 2 update 每月更新上一月统计数据和当月数据
     */
    public function setOrderMonthlyData($city_id, $type = ''){
        $redis = RedisHAProxy::model();
        $data_key = 'ORDER_MONTH_STAT_DATA_'.$city_id;
        if($type == 'init') {
            $data_key = 'ORDER_MONTH_STAT_DATA_'.$city_id;
            if($city_id){
                $where = ' where city_id = '.$city_id;
            }
            else{
                $where = ' where 1 ';
            }
            $sql = "SELECT year FROM t_daily_order_driver {$where} GROUP BY year ORDER BY year DESC";
            $command = Yii::app()->dbreport->createCommand($sql);
            $date_arr = $command->queryAll();
            $data = array();
            foreach ($date_arr as $val) {
                $sql = "SELECT month , COUNT(IF(`status`=1 OR `status` = 4,TRUE,NULL)) AS order_complate_count FROM t_daily_order_driver WHERE year = :year ";
                if (isset($city_id) && $city_id!=0) {
                    $sql .= ' AND city_id= :city_id';
                }
                $sql .= " GROUP BY month ORDER BY month ASC";
                $command = Yii::app()->dbreport->createCommand($sql);
                $command->bindParam(":year" , $val['year']);
                if (isset($city_id) && $city_id!=0) {
                    $command->bindParam(":city_id" , $city_id);
                }
                $result_year_data = $command->queryAll();

                foreach ($result_year_data as $data_val) {
                    $data[$val['year']][$data_val['month']] = $data_val['order_complate_count'];
                }
            }
            $res = $redis->set($data_key,json_encode($data));
            echo '---city_id :'.$city_id.'----- type:init---result:';
            var_dump($res);
            echo "\n";
            return $res;
        }

        if($type == 'update'){
            $old_data = $redis->get($data_key);
            if(!$old_data){
                return $this->setOrderMonthlyData($city_id,'init');
            }

            $old_data = json_decode($old_data,1);

            if(in_array(date('d'),array('01','02','03','04','05'))){
                $last_month = strtotime('-1 month');
                $update_year = date('Y',$last_month);
                $update_month = date('n',$last_month);
                $data = $this->getYearData($update_year,$update_month,$city_id);
                if($data){
                    echo '---city_id :'.$city_id.'---type:update --- date'.$update_year.'-'.$update_month.'---'."\n";

                }
                if($data !== false){
                    $old_data[$update_year][$update_month] = $data[$update_year][$update_month];
                }
            }
            $this_year = date('Y');
            $this_month = date('n');
            $data = $this->getYearData($this_year,$this_month,$city_id);

            if($data !== false){
                $old_data[$this_year][$this_month] = $data[$this_year][$this_month];
            }

            $res = $redis->set($data_key,json_encode($old_data));
            echo '---city_id :'.$city_id.'---type:update --- date'.$this_year.'-'.$this_month.'---result:';
            var_dump($res);
            echo "\n";
            return $res;
        }



    }
	
	/**
	 * 获取新客数据
	 * @param array $condition
	 * @return array $data
	 */
	public function getNewCustomer($condition = array()) {
		$sql = "SELECT DISTINCT(phone) AS phone , city_id FROM t_daily_order_driver WHERE city_id=1 AND status IN(1,4) AND current_day = :current_day ORDER BY order_id DESC";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":current_day" , $condition['date']);
		$result = $command->queryAll();
		$data = array();
		$city_arr = Dict::items('city');
		foreach ($result as $key=>$val) {
			$is_new_customer = self::checkNewCustomer($val['phone'] , $condition['date']);
			if ($is_new_customer) {
				$data[] = array(
				    'city' => $city_arr[$val['city_id']],
					'phone' => '`'.$val['phone'],
				);
			}
		}
		return $data;
	}
	
	/**
	 * 校验是否为新客
	 * @param string $phone
	 * @param int $time
	 * @return boolean $data
	 */
	private function checkNewCustomer($phone = null , $current_day = '') {
		if ($phone == null || empty($current_day)) {
			return false;
		}
		$sql = "SELECT count(order_id) AS count FROM t_daily_order_driver WHERE phone = :phone AND status IN(1 , 4) AND current_day < :current_day";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":current_day" , $current_day);
		$command->bindParam(":phone" , $phone);
		$result = $command->queryRow();
		if ($result['count'] == 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 获取地区分布图表数据
	 * @return array $data
	 */
	public function getAreaDistributeData() {
		$hour = date('H' , time());
		$min = date('i' , time());
		if (intval($hour) > 16 && intval($min) > 30) {
			$number = -1;
		} else {
			$number = -2;
		}
		$date = date('Ymd' , strtotime($number.' day'));
		$date_arr = array();
		$date_arr[] = date('Ymd' , strtotime($number.' day'));
		$date_arr[] = date('Ymd' , strtotime(($number - 7).' day'));
		$date_arr[] = date('Ymd' , strtotime(($number - 30).' day'));
		$date_arr[] = date('Ymd' , strtotime(($number - 90).' day'));
		$date_arr[] = date('Ymd' , strtotime(($number - 180).' day'));
		$date_arr[] = date('Ymd' , strtotime(($number - 365).' day'));
		$data = array();
		foreach ($date_arr as $key=>$val) {
			$arr = self::getAreaDistributeDataByDate($val , $date , $key);
			if ($arr) {
				$data[] = $arr;
			}
		}
		return $data;
	}
	
	/**
	 * 通过日期获取地区分布图表数据
	 * @return array $data
	 */
	private function getAreaDistributeDataByDate($start_date = null , $end_date = null , $key = 0) {
		if ($start_date == null || $end_date == null) {
			return false;
		}
		$total_count = Yii::app()->dbreport->createCommand()
		                  ->select('count(id) as cnt')
		                  ->from('t_daily_order_driver')
		                  ->where('current_day between :start_date and :end_date' , array(':start_date' => $start_date , ':end_date' => $end_date))
		                  ->queryRow();
		$sql = "SELECT COUNT(id) AS cnt , city_id FROM t_daily_order_driver where city_id > 0 AND status IN(1,4) AND `current_day` BETWEEN :start_date AND :end_date GROUP BY city_id";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":start_date" , $start_date);
		$command->bindParam(":end_date" , $end_date);
		$result = $command->queryAll();
		$command->reset();
		$sql = "SELECT COUNT(id) AS cnt , city_id FROM t_daily_order_driver where city_id > 0 AND status IN(2 , 3) AND `current_day` BETWEEN :start_date AND :end_date GROUP BY city_id";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":start_date" , $start_date);
		$command->bindParam(":end_date" , $end_date);
		$result_cancel = $command->queryAll();
		switch ($key) {
			case 0:
				$chart_key = $start_date."-昨天数据";
				break;
			case 1:
				$chart_key = $start_date.'~'.$end_date."-7天数据";
				break;
			case 2:
				$chart_key = $start_date.'~'.$end_date."-30天数据";
				break;
			case 3:
				$chart_key = $start_date.'~'.$end_date."-90天数据";
				break;
			case 4:
				$chart_key = $start_date.'~'.$end_date."-180天数据";
				break;
			case 5:
				$chart_key = $start_date.'~'.$end_date."-365天数据";
				break;
		}
		$data = array();
		if (0 == $total_count['cnt']) {
			return $data;
		}
		$city_arr = Dict::items('city');
		$count = 0;
		foreach ($result as $result_key=>$result_val) {
			$data[$chart_key][0][$city_arr[$result_val['city_id']]] = $result_val['cnt'];
		}
		foreach ($result_cancel as $cancel_key=>$cancel_val) {
			$data[$chart_key][1][$city_arr[$cancel_val['city_id']]] = $cancel_val['cnt'];
		}
		return $data;
	}
	
	public function getOrderDistributeData() {
		$sql = "SELECT * FROM t_daily_order_driver WHERE current_day = 20130101 AND city_id=1";
		$command = Yii::app()->dbreport->createCommand($sql);
//		$command->bindParam(":date" , $date);
		$result = $command->queryAll();
		return $result;
		var_dump($result);exit;
	}
	
	/**
	* 计算指定时间段内的自然周起始及终止日期
	*/
	public function getWeekByDate($start, $end) {
		$w = date('w', strtotime($start));
		if ($w == 0) {
			$start_ts = strtotime($start);
		} else {
			$surplus = 7-$w;
			$start_ts = strtotime($start)+$surplus*86400;
		}
		$end_ts = strtotime($end);
		$week_arr = array();
		while($start_ts < $end_ts) {
			$tmp['0'] = date('Ymd', $start_ts);
			$tmp['1'] = date('Ymd', $start_ts+6*86400);
			if (strtotime($tmp['1']) > $end_ts) {
				break;
			}
			$week_arr[] = $tmp;
			$start_ts = $start_ts+7*86400;
		}
		return $week_arr;
	}
	
	/**
	 * 订单周报统计数据(chart)
	 * @param array $condition
	 */
	public function getWeeklyChartDataNew($condition) {
		$start_time = str_replace('-' , '' , $condition['start_time']);
		$end_time = str_replace('-' , '' , $condition['end_time']);
		//新需求要求统计一段时间内的自然周的数据
		$week_arr = $this->getWeekByDate($start_time, $end_time);
		$chart = array();
		//图表X轴
		$date = array();
		$order_count = array();
		$chart_seven = array();
		$chart_twentytwo = array();
		$chart_twentythree = array();
		$chart_twentyfour = array();
		//默认副标题
		$subtitle = '单数';
		$data_source = $condition['data_source'];
		foreach ($week_arr as $_date) {
			$condition['start_time'] = $_date[0];
			$condition['end_time'] = $_date[1];
			$sql = "SELECT SUM(all_count) AS order_all_count, SUM(complate_count) AS order_complate_count,SUM(income_complate) AS income_complate,SUM(income_company) AS income_company, SUM(last_complate_count) AS last_complate_count,";
			//报单数
			$sql .= "SUM(IF(time_part=7,complate_count,0)) AS chart_order_complate_count_seven,";
			$sql .= "SUM(IF(time_part=22,complate_count,0)) AS chart_order_complate_count_twentytwo,";
			$sql .= "SUM(IF(time_part=23,complate_count,0)) AS chart_order_complate_count_twentythree,";
			$sql .= "SUM(IF(time_part=24,complate_count,0)) AS chart_order_complate_count_twentyfour,";
			//订单数
			$sql .= "SUM(IF(time_part=7,all_count,0)) AS chart_order_all_count_seven,";
			$sql .= "SUM(IF(time_part=22,all_count,0)) AS chart_order_all_count_twentytwo,";
			$sql .= "SUM(IF(time_part=23,all_count,0)) AS chart_order_all_count_twentythree,";
			$sql .= "SUM(IF(time_part=24,all_count,0)) AS chart_order_all_count_twentyfour,";
			//总收入
			$sql .= "SUM(IF(time_part=7,income_complate,0)) AS chart_income_complate_seven,";
			$sql .= "SUM(IF(time_part=22,income_complate,0)) AS chart_income_complate_twentytwo,";
			$sql .= "SUM(IF(time_part=23,income_complate,0)) AS chart_income_complate_twentythree,";
			$sql .= "SUM(IF(time_part=24,income_complate,0)) AS chart_income_complate_twentyfour,";
			//公司收入
			$sql .= "SUM(IF(time_part=7,income_company,0)) AS chart_income_company_seven,";
			$sql .= "SUM(IF(time_part=22,income_company,0)) AS chart_income_company_twentytwo,";
			$sql .= "SUM(IF(time_part=23,income_company,0)) AS chart_income_company_twentythree,";
			$sql .= "SUM(IF(time_part=24,income_company,0)) AS chart_income_company_twentyfour,";
			$sql .= "last_income_complate FROM t_order_week_report WHERE date BETWEEN :start_time AND :end_time";
			if (!empty($condition['city_id'])) {
				$sql .= " AND city_id = :city_id";
				$city_arr = Dict::items('city');
				$city = $city_arr[$condition['city_id']];
			}else {
				$city = '全部';
			}
			if (!empty($condition['time_part'])) {
				$sql .= " AND time_part = :time_part";
			}
			if (!empty($condition['source'])) {
				$sql .= " AND source = :source";
			}
			if (!empty($condition['distance_area'])) {
				$sql .= " AND distance_area = :distance_area";
			}
			$sql .= "";
			$command = Yii::app()->dbreport->createCommand($sql);
			$start_time = str_replace('-' , '' , $condition['start_time']);
			$end_time = str_replace('-' , '' , $condition['end_time']);
			$command->bindParam(":start_time" , $start_time);
			$command->bindParam(":end_time" ,$end_time);
			if (!empty($condition['city_id'])) {
				$command->bindParam(":city_id" , $condition['city_id']);
			}
			if (!empty($condition['time_part'])) {
				$command->bindParam(":time_part" , $condition['time_part']);
			}
			if (!empty($condition['source'])) {
				$command->bindParam(":source" , $condition['source']);
			}
			if (!empty($condition['distance_area'])) {
				$command->bindParam(":distance_area" , $condition['distance_area']);
			}
			$_result = $command->queryRow();
			$date[] = "{$_date[0]}-{$_date[1]}";
			if ($data_source == 'average_price') {
				$order_count[] = floatval($_result['income_complate']/$_result['order_complate_count']);
				$chart_seven[] = floatval($_result['chart_income_complate_seven']/$_result['chart_order_complate_count_seven']);
				$chart_twentytwo[] = floatval($_result['chart_income_complate_twentytwo']/$_result['chart_order_complate_count_twentytwo']);
				$chart_twentythree[] = floatval($_result['chart_income_complate_twentythree']/$_result['chart_order_complate_count_twentythree']);
				$chart_twentyfour[] = floatval($_result['chart_income_complate_twentyfour']/$_result['chart_order_complate_count_twentyfour']);			
			} else {
				$order_count[] = floatval($_result[$data_source]);
				$chart_seven[] = floatval($_result['chart_'.$data_source.'_seven']);
				$chart_twentytwo[] = floatval($_result['chart_'.$data_source.'_twentytwo']);
				$chart_twentythree[] = floatval($_result['chart_'.$data_source.'_twentythree']);
				$chart_twentyfour[] = floatval($_result['chart_'.$data_source.'_twentyfour']);
			}
		}
		$chart = array(
			'subtitle' => self::$data_source[$data_source],
			'date' => $date,
			'order_count' => $order_count,
			'chart_seven' => $chart_seven,
			'chart_twentytwo' => $chart_twentytwo,
			'chart_twentythree' => $chart_twentythree,
			'chart_twentyfour' => $chart_twentyfour,
		);
		return $chart;
	}
	
	/**
	 * 订单周报统计数据(dataProvider/chart)
	 * @param array $condition
	 */
	public function getWeeklyDataNew($condition) {
		$result = $this->getWeekDataByCondition($condition);
		$dataProvider = new CArrayDataProvider($result , array (
			'id'=>'order_trend', 
			'keyField'=>'date', 
			'pagination'=>false,
			));
		$chart = self::getWeeklyChartDataNew($condition);
		$data = array();
		$data['dataProvider'] = $dataProvider;
		$data['chart'] = $chart;
		return $data;	
	}
    /**
     * 订单周报统计数据下载到execl中
     * @param array $condition
     */
    public function getWeeklyDataNewToExecl($condition) {
        $result = $this->getWeekDataByCondition($condition);
        return $result;
    }
	
	/**
	 * 订单周报统计数据(dataProvider)
	 * @param array $condition
	 */
	public function getWeekDataByCondition($condition) {
		$sql = "SELECT date,week_cn,SUM(all_count) AS all_count,SUM(complate_count) AS complate_count,SUM(income_complate) AS income_complate,SUM(income_company) AS income_company, SUM(last_complate_count) AS last_complate_count,last_income_complate,";
		//报单数
		$sql .= "SUM(IF(time_part=7,complate_count,0)) AS chart_order_complate_count_seven,";
		$sql .= "SUM(IF(time_part=22,complate_count,0)) AS chart_order_complate_count_twentytwo,";
		$sql .= "SUM(IF(time_part=23,complate_count,0)) AS chart_order_complate_count_twentythree,";
		$sql .= "SUM(IF(time_part=24,complate_count,0)) AS chart_order_complate_count_twentyfour,";
		//订单数
		$sql .= "SUM(IF(time_part=7,all_count,0)) AS chart_order_all_count_seven,";
		$sql .= "SUM(IF(time_part=22,all_count,0)) AS chart_order_all_count_twentytwo,";
		$sql .= "SUM(IF(time_part=23,all_count,0)) AS chart_order_all_count_twentythree,";
		$sql .= "SUM(IF(time_part=24,all_count,0)) AS chart_order_all_count_twentyfour,";
		//总收入
		$sql .= "SUM(IF(time_part=7,income_complate,0)) AS chart_income_complate_seven,";
		$sql .= "SUM(IF(time_part=22,income_complate,0)) AS chart_income_complate_twentytwo,";
		$sql .= "SUM(IF(time_part=23,income_complate,0)) AS chart_income_complate_twentythree,";
		$sql .= "SUM(IF(time_part=24,income_complate,0)) AS chart_income_complate_twentyfour,";
		//公司收入
		$sql .= "SUM(IF(time_part=7,income_company,0)) AS chart_income_company_seven,";
		$sql .= "SUM(IF(time_part=22,income_company,0)) AS chart_income_company_twentytwo,";
		$sql .= "SUM(IF(time_part=23,income_company,0)) AS chart_income_company_twentythree,";
		$sql .= "SUM(IF(time_part=24,income_company,0)) AS chart_income_company_twentyfour,";
		
		$sql .= "city_id,source,time_part , (SUM(IF((source IN (:source_callcenter)), complate_count , 0))+SUM(IF((source IN (:source_callcenter_input)), complate_count , 0))) as callcenter_count,(SUM(IF((source IN (:source_client_input)), complate_count , 0))+SUM(IF((source IN (:source_client)), complate_count, 0))) as app_count FROM t_order_week_report WHERE date BETWEEN :start_time AND :end_time";
		if (!empty($condition['city_id'])) {
			$sql .= " AND city_id = :city_id";
			$city_arr = Dict::items('city');
			$city = $city_arr[$condition['city_id']];
		}else {
			$city = '全部';
		}
		if (!empty($condition['time_part'])) {
			$sql .= " AND time_part = :time_part";
		}
		if (!empty($condition['source'])) {
			$sql .= " AND source = :source";
		}
		if (!empty($condition['distance_area'])) {
			$sql .= " AND distance_area = :distance_area";
		}
		$sql .= " GROUP BY date ORDER BY date DESC";
		$command = Yii::app()->dbreport->createCommand($sql);
		$start_time = str_replace('-' , '' , $condition['start_time']);
		$end_time = str_replace('-' , '' , $condition['end_time']);
		$command->bindParam(":start_time" , $start_time);
		$command->bindParam(":end_time" ,$end_time);

                $source_callcenter = join(',', Order::$callcenter_sources);
		$source_callcenter_input = join(',', Order::$callcenter_input_sources);
		$source_client_input = join(',', Order::$client_input_source);
		$source_client = join(',', Order::$client_source);

		$command->bindParam(":source_callcenter" , $source_callcenter);
		$command->bindParam(":source_callcenter_input" ,$source_callcenter_input);
		$command->bindParam(":source_client_input" , $source_client_input);
		$command->bindParam(":source_client" ,$source_client);
		if (!empty($condition['city_id'])) {
			$command->bindParam(":city_id" , $condition['city_id']);
		}
		if (!empty($condition['time_part'])) {
			$command->bindParam(":time_part" , $condition['time_part']);
		}
		if (!empty($condition['source'])) {
			$command->bindParam(":source" , $condition['source']);
		}
		if (!empty($condition['distance_area'])) {
			$command->bindParam(":distance_area" , $condition['distance_area']);
		}
		$result = $command->queryAll();
		foreach ($result as $key=>$val) {
			$result[$key]['city'] = $city;
		}
		return $result;
	}
}
