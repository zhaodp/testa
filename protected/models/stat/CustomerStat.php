<?php
/**
 * 客户统计
 * @author AndyCong<congming@edaijia.cn>
 * @version web2.0 2013-03-15
 */
class CustomerStat {
	/**
	 * 初始化
	 * @param string $month
	 */
	public function actionCustomerReportInitialize($month = null) {
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
				self::actionVipFitReport($date);
				self::actionFreshOldReport($date);
			}
		}
	}
	
	/**
	 * 数据重载
	 */
	public function actionCustomerReportReload() {
		$date = date('Y-m-d' , strtotime('-2 day'));
		self::actionVipFitReport($date);
		self::actionFreshOldReport($date);
		
//		$unfinished_order_date = self::getUnfinishedOrderDate(); 
//		$current_time = time();
//		foreach ($unfinished_order_date as $date) {
//			if (($current_time - strtotime($date['current_day'])) <= 31*86400) {
//				self::actionVipFitReport($date['current_day']);
//				self::actionFreshOldReport($date['current_day']);
//			}
//		}
	}
	
	/**
	 * 定时执行脚本
	 */
	public function actionCrontab() {
		$date = date('Y-m-d' , strtotime('-1 day'));
		self::actionCustomerReportReload();
		self::actionVipFitReport($date);
		self::actionFreshOldReport($date);
	}
	
	/**
	 * 获取未完成订单的日期
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
	 * vip及散客数据统计
	 * @param string $date
	 */
	private function actionVipFitReport($date = null) {
		if ($date == null) {
			return false;
		}
		$date = str_replace('-' , '' , $date);
		$sql = "DELETE FROM t_customer_vip_fit_report WHERE date = :date";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":date" , $date);
		$command->execute();
		$sql = "SELECT current_day AS date , SUM(IF(vipcard!=0 , 1 , 0)) AS vip_order_num , SUM(IF(vipcard!=0 , income , 0)) AS vip_cost , SUM(IF(vipcard='0' , 1 , 0)) AS fit_order_num , SUM(IF(vipcard!=0 , income , 0)) AS vip_cost , SUM(IF(vipcard=0 , income , 0)) AS fit_cost , city_id , source , time_part FROM t_daily_order_driver WHERE current_day = :date AND status IN(1 , 4) GROUP BY city_id,source,time_part";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":date" , $date);
		$result = $command->queryAll();
		$vip_num = 0;
		$fit_num = 0;
		foreach ($result as $key=>$val) {
			$sql = "SELECT COUNT(DISTINCT(phone)) AS vip_num FROM t_daily_order_driver WHERE current_day = ".$date." AND status IN(1 , 4) AND city_id = ".$val['city_id']." AND source=".$val['source']." AND time_part=".$val['time_part']." AND vipcard!=0";
			$command = Yii::app()->dbreport->createCommand($sql);
			$result_vip_num = $command->queryRow();
			$val['vip_num'] = $result_vip_num['vip_num'];
			$vip_num += $val['vip_num'];
			$sql = "SELECT COUNT(DISTINCT(phone)) AS fit_num FROM t_daily_order_driver WHERE current_day = ".$date." AND status IN(1 , 4) AND city_id = ".$val['city_id']." AND source=".$val['source']." AND time_part=".$val['time_part']." AND vipcard=0";
			$command = Yii::app()->dbreport->createCommand($sql);
			$result_fit_num = $command->queryRow();
			$val['fit_num'] = $result_fit_num['fit_num'];
			$fit_num += $val['fit_num'];
			self::insertVipFitReport($val , $date);
		}
		echo "date:".$date."----vip_number:".$vip_num."----fit_number:".$fit_num;
	}
	
	/**
	 * 记录VIP及散客数据
	 * @param array $data
	 * @param string $date
	 */
	private function insertVipFitReport($data = array() , $date = null) {
		if (empty($data) && $date == null && $data['date'] != $date) {
			return false;
		}
		$sql = "INSERT INTO t_customer_vip_fit_report(`date` , `city_id` , `source` , `time_part` , `vip_num` , `vip_order_num` , `vip_cost` , `fit_num` , `fit_order_num` , `fit_cost`) VALUES(:date,:city_id,:source,:time_part,:vip_num,:vip_order_num,:vip_cost,:fit_num,:fit_order_num,:fit_cost)";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":date" , $data['date']);
		$command->bindParam(":city_id" , $data['city_id']);
		$command->bindParam(":source" , $data['source']);
		$command->bindParam(":time_part" , $data['time_part']);
		$command->bindParam(":vip_num" , $data['vip_num']);
		$command->bindParam(":vip_order_num" , $data['vip_order_num']);
		$command->bindParam(":vip_cost" , $data['vip_cost']);
		$command->bindParam(":fit_num" , $data['fit_num']);
		$command->bindParam(":fit_order_num" , $data['fit_order_num']);
		$command->bindParam(":fit_cost" , $data['fit_cost']);
		$command->execute();
		$command->reset();
	}
	
	
	private function actionFreshOldReport($date = null) {
		if ($date == null) {
			return false;
		}
		$date = str_replace('-' , '' , $date);
		$sql = "DELETE FROM t_customer_fresh_report WHERE date = :date";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":date" , $date);
		$command->execute();
		$sql = "SELECT DISTINCT(phone) AS phone , current_day AS date , city_id , source FROM t_daily_order_driver WHERE current_day = :date AND status IN(1 , 4) ORDER BY city_id ASC , order_id ASC";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":date" , $date);
		$result = $command->queryAll();
		$data = array();
		$fresh_app = 0;
		$fresh_callcenter = 0;
		foreach ($result as $key=>$val) {
			$is_fresh = self::checkCustomerFreshOld($val);   //校验是否为新用户
			$order_num = self::getOrderNumByPhone($val);
			$first = self::checkCustomerFirst($val);
			$data[$val['city_id']]['date'] = $val['date'];
			$data[$val['city_id']]['city_id'] = $val['city_id'];
			if (!isset($data[$val['city_id']]['fresh_callcenter'])) {
				$data[$val['city_id']]['fresh_callcenter'] = 0;
			}
			if (!isset($data[$val['city_id']]['fresh_app'])) {
				$data[$val['city_id']]['fresh_app'] = 0;
			}
			if (!isset($data[$val['city_id']]['fresh_callcenter_order'])) {
				$data[$val['city_id']]['fresh_callcenter_order'] = 0;
			}
			if (!isset($data[$val['city_id']]['fresh_app_order'])) {
				$data[$val['city_id']]['fresh_app_order'] = 0;
			}
			if (!isset($data[$val['city_id']]['repeat_callcenter'])) {
				$data[$val['city_id']]['repeat_callcenter'] = 0;
			}
			if (!isset($data[$val['city_id']]['repeat_callcenter_order'])) {
				$data[$val['city_id']]['repeat_callcenter_order'] = 0;
			}
			if (!isset($data[$val['city_id']]['repeat_app'])) {
				$data[$val['city_id']]['repeat_app'] = 0;
			}
			if (!isset($data[$val['city_id']]['repeat_app_order'])) {
				$data[$val['city_id']]['repeat_app_order'] = 0;
			}
			if (!isset($data[$val['city_id']]['callcenter_sms_num'])) {
				$data[$val['city_id']]['callcenter_sms_num'] = 0;
			}
			if (!isset($data[$val['city_id']]['first_app'])) {
				$data[$val['city_id']]['first_app'] = 0;
			}
			if (!isset($data[$val['city_id']]['first_callcenter'])) {
				$data[$val['city_id']]['first_callcenter'] = 0;
			}
			$data[$val['city_id']]['first_app'] += $first['first_app'];
			$data[$val['city_id']]['first_callcenter'] += $first['first_callcenter'];
			if ($is_fresh) {
				if ($val['source'] == 0 || $val['source'] == 2) {
					$data[$val['city_id']]['fresh_app'] += 1;
					$fresh_app += 1;
				} else {
					$data[$val['city_id']]['fresh_callcenter'] += 1;
					$data[$val['city_id']]['callcenter_sms_num'] += 1;
					$fresh_callcenter += 1;
				}
				$data[$val['city_id']]['fresh_callcenter_order'] += $order_num['callcenter_count'];
				$data[$val['city_id']]['fresh_app_order'] += $order_num['app_count'];
			} else {
				if ($val['source'] == 0 || $val['source'] == 2) {
					$data[$val['city_id']]['repeat_app'] += 1;
				} else {
					$data[$val['city_id']]['repeat_callcenter'] += 1;
					$is_callcenter_sms = self::checkCallcenterSms($val);
					if ($is_callcenter_sms) {
						$data[$val['city_id']]['callcenter_sms_num'] += 1;
					}
				}
				$data[$val['city_id']]['repeat_callcenter_order'] += $order_num['callcenter_count'];
				$data[$val['city_id']]['repeat_app_order'] += $order_num['app_count'];
			}
		}
		self::insertFreshReport($data);
		echo "date:".$date."----fresh_app:".$fresh_app."----fresh_callcenter:".$fresh_callcenter;
	}
	
	/**
	 * 校验是否是新客
	 * @param array $data
	 * @return boolean
	 */
	private function checkCustomerFreshOld($data = array()) {
		if (empty($data)) {
			return false;
		}
		$sql = "SELECT COUNT(order_id) AS cnt FROM  t_daily_order_driver WHERE phone = :phone AND current_day < :date AND status IN(1,4)";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":phone" , $data['phone']);
		$command->bindParam(":date" , $data['date']);
		$count = $command->queryRow();
		if ($count['cnt'] > 0) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * 校验是否为每天发送短信的400用户
	 * @param array $data
	 * @return boolean
	 */
	private function checkCallcenterSms($data = array()) {
		if (empty($data)) {
			return false;
		}
		$sql = "SELECT SUM(IF(source IN(0,2) , 1 ,0)) AS app_count, SUM(IF(source IN(1,3) , 1 ,0)) AS callcenter_count FROM t_daily_order_driver WHERE phone= :phone AND status IN(1 , 4) AND current_day < :date";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":phone" , $data['phone']);
		$command->bindParam(":date" , $data['date']);
		$result = $command->queryRow();
		if ($result['app_count'] == 0 && $result['callcenter_count'] <= 3) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 通过电话号获取当天的订单数（app单数、400单数）
	 * @param array $data
	 * @return array $result
	 */
	private function getOrderNumByPhone($data = array()) {
		if (empty($data)) {
			return false;
		}
		$sql = "SELECT SUM(IF(source IN(0,2) , 1 ,0)) AS app_count, SUM(IF(source IN(1,3) , 1 ,0)) AS callcenter_count FROM t_daily_order_driver WHERE phone= :phone AND status IN(1 , 4) AND current_day = :date";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":phone" , $data['phone']);
		$command->bindParam(":date" , $data['date']);
		$result = $command->queryRow();
		return $result;
	}
	
	public function checkCustomerFirst($data = array()) {
		if (empty($data)) {
			return false;
		}
		if ($data['source'] == 0 || $data['source'] == 2) {
			$condition = " AND source IN(0 , 2)";
		} else {
			$condition = " AND source IN(1 , 3)";
		}
		$sql = "SELECT COUNT(order_id) AS cnt FROM t_daily_order_driver WHERE phone = :phone AND current_day < :date AND status IN(1,4)".$condition;
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":phone" , $data['phone']);
		$command->bindParam(":date" , $data['date']);
		$result = $command->queryRow();
		$arr = array('first_app' => 0 , 'first_callcenter' => 0);
		if ($result['cnt'] == 0 && ($data['source'] == 0 || $data['source'] == 2)) {
			$arr['first_app'] = 1;
		}
		if ($result['cnt'] == 0 && ($data['source'] == 1 || $data['source'] == 3)) {
			$arr['first_callcenter'] = 1;
		}
		return $arr;
	}
	
	/**
	 * 记录新老客数据
	 * @param array $data
	 */
	private function insertFreshReport($data = array()) {
		if (empty($data)) {
			return false;
		}
		$sql = "INSERT INTO t_customer_fresh_report(`date` , `city_id` , `fresh_callcenter` , `fresh_callcenter_order` , `fresh_app` , `fresh_app_order` , `repeat_callcenter` , `repeat_callcenter_order` , `repeat_app` , `repeat_app_order` , `callcenter_sms_num` , `first_app` , `first_callcenter`) VALUES(:date , :city_id , :fresh_callcenter , :fresh_callcenter_order , :fresh_app , :fresh_app_order , :repeat_callcenter , :repeat_callcenter_order , :repeat_app , :repeat_app_order , :callcenter_sms_num , :first_app , :first_callcenter)";
		foreach ($data as $key=>$val) {
			$command = $command = Yii::app()->dbreport->createCommand($sql);
			$command->bindParam(":date" , $val['date']);
			$command->bindParam(":city_id" , $val['city_id']);
			$command->bindParam(":fresh_callcenter" , $val['fresh_callcenter']);
			$command->bindParam(":fresh_callcenter_order" , $val['fresh_callcenter_order']);
			$command->bindParam(":fresh_app" , $val['fresh_app']);
			$command->bindParam(":fresh_app_order" , $val['fresh_app_order']);
			$command->bindParam(":repeat_callcenter" , $val['repeat_callcenter']);
			$command->bindParam(":repeat_callcenter_order" , $val['repeat_callcenter_order']);
			$command->bindParam(":repeat_app" , $val['repeat_app']);
			$command->bindParam(":repeat_app_order" , $val['repeat_app_order']);
			$command->bindParam(":callcenter_sms_num" , $val['callcenter_sms_num']);
			$command->bindParam(":first_app" , $val['first_app']);
			$command->bindParam(":first_callcenter" , $val['first_callcenter']);
			$command->execute();
			$command->reset();
		}
	}
	
	/**
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 * ++++++++++++++++++++++++++++++上边是统计method++++++++++++++++++++++++++++++
	 * ++++++++++++++++++++++++++++++下边是展示method++++++++++++++++++++++++++++++
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 */
	
	public function getFreshRepeatData($condition = array() , $pages = 31) {
		$sql = "SELECT date , SUM(fresh_callcenter+fresh_app+repeat_callcenter+repeat_app) AS total , SUM(fresh_callcenter+fresh_app) AS fresh_total , SUM(fresh_callcenter) AS fresh_callcenter , SUM(fresh_app) AS fresh_app , SUM(repeat_callcenter+repeat_app) AS repeat_total , SUM(repeat_callcenter) AS repeat_callcenter , SUM(repeat_app) AS repeat_app , SUM(callcenter_sms_num) AS callcenter_sms_num , SUM(first_app) AS first_app , SUM(first_callcenter) AS first_callcenter FROM t_customer_fresh_report WHERE date >= :start_time AND date <= :end_time";
		$condition['start_time'] = str_replace('-' , '' , $condition['start_time']);
		$condition['end_time'] = str_replace('-' , '' , $condition['end_time']);
		if (!empty($condition['city_id'])) {
			$sql .= " AND city_id = :city_id";
			$city_arr = Dict::items('city');
			$city = $city_arr[$condition['city_id']];
		}else {
			$city = '全部';
		}
		$sql .= " GROUP BY date ORDER BY date DESC";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":start_time" , $condition['start_time']);
		$command->bindParam(":end_time" , $condition['end_time']);
		if (!empty($condition['city_id'])) {
			$command->bindParam(":city_id" , $condition['city_id']);
		}
		$result = $command->queryAll();
		foreach ($result as $key=>$val) {
			$result[$key]['city'] = $city;
		}
        //print_r($condition);
        //print_r($result);die;
		$dataProvider = new CArrayDataProvider($result, array (
			'id'=>'fresh-repeat', 
			'keyField'=>'date', 
			'pagination'=>array (
				'pageSize'=>$pages)
			)
		);
		return $dataProvider;
	}
	
	public function getVipFitData($condition = array() , $pages = 31) {
		$sql = "SELECT date , SUM(vip_num) AS vip_num , SUM(vip_order_num) AS vip_order_num , SUM(vip_cost) AS vip_cost , SUM(fit_num) AS fit_num , SUM(fit_order_num) AS fit_order_num , SUM(fit_cost) AS fit_cost FROM t_customer_vip_fit_report WHERE date >= :start_time AND date <= :end_time";
		$condition['start_time'] = str_replace('-' , '' , $condition['start_time']);
		$condition['end_time'] = str_replace('-' , '' , $condition['end_time']);
		if (!empty($condition['city_id'])) {
			$sql .= " AND city_id = :city_id";
			$city_arr = Dict::items('city');
			$city = $city_arr[$condition['city_id']];
		}else {
			$city = '全部';
		}
		$sql .= " GROUP BY date ORDER BY date DESC";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":start_time" , $condition['start_time']);
		$command->bindParam(":end_time" , $condition['end_time']);
		if (!empty($condition['city_id'])) {
			$command->bindParam(":city_id" , $condition['city_id']);
		}
		$result = $command->queryAll();
		foreach ($result as $key=>$val) {
			$result[$key]['city'] = $city;
		}
		$dataProvider = new CArrayDataProvider($result, array (
			'id'=>'vip-fit', 
			'keyField'=>'date', 
			'pagination'=>array (
				'pageSize'=>$pages)
			)
		);
		return $dataProvider;
	}
	
	/**
	 * 插入营销短信
	 * @param array $data
	 */
	public function insertMarketSms($data = array()) {
		$sql = "INSERT INTO t_market_sms(`pre_send_time` , `phone` , `content` , `user_id` , `created`) VALUES(:pre_send_time , :phone , :content , :user_id , :created)";
		if (is_array($data['sms_phones'])) {
			$phone_arr = $data['sms_phones'];
		} else {
			$phone_str = $data['sms_phones'];
		    $phone_arr = preg_split('/[\r\n]+/', $phone_str);
		}
		$time = date("Y-m-d H:i:s" , time());
		foreach ($phone_arr as $phone) {
			if (!empty($phone)) {
				$command = Yii::app()->dbreport->createCommand($sql);
				$command->bindParam(":pre_send_time" , $data['pre_send_time']);
				$command->bindParam(":phone" , $phone);
				$command->bindParam(":content" , $data['content']);
				$command->bindParam(":user_id" , $data['user_id']);
				$command->bindParam(":created" , $time);
				$command->execute();
				$command->reset();
			}
		}
		return true;
	}
	
	
	public function getSmsList($condition = array()) {
		$sql = "SELECT * FROM t_market_sms WHERE 1=1";
		$str_condition = '1=1';
		if ($condition['status'] !== '') {
			$sql .= ' AND status='.$condition['status'];
			$str_condition .= ' AND status='.$condition['status'];
		}
		$sql .= ' ORDER BY id DESC';
		$count = Yii::app()->dbreport->createCommand()
		            ->select('*')
		            ->from('t_market_sms')
		            ->where($str_condition)
		            ->query()
		            ->count();
		//sql数据转化成Provider格式 源自：http://blog.yiibook.com/?p=420   Yii手册CSqlDataProvider
		$dataProvider = new CSqlDataProvider($sql, array(
	            'keyField'=>'phone',   //必须指定一个作为主键
	            'totalItemCount'=>$count,    //分页必须指定总记录数
	            'db'=>Yii::app()->dbreport,
	            'pagination'=>array(
			        'pageSize'=>50,
			    ),
	    ));
	    //sql数据转化成Provider格式 END
	    return $dataProvider;
	}
	/**
	 * 获取<=4次连续使用400的客户
	 * @param string $start_time
	 * @param string $end_time
	 * @return array $data
	 */
	public function getCallcenterCustomer($start_time = null , $end_time = null) {
		if ($start_time == null || $end_time == null) {
			return false;
		}
		$sql = "SELECT DISTINCT(phone) AS phone FROM t_order WHERE status IN(1,4) AND source IN(1,3) AND booking_time BETWEEN  :start_time AND :end_time";
		$command = Order::getDbReadonlyConnection()->createCommand($sql);
		$command->bindParam(":start_time" , $start_time);
		$command->bindParam(":end_time" , $end_time);
		$result = $command->queryAll();
		$data = self::checkCallcenterCustomer($result , $start_time);
		return $data;
	}
	
	/**
	 * 校验满足<=4次连续使用400的客户
	 * @param array $result
	 * @param string $start_time
	 * @return array $data
	 */
	private function checkCallcenterCustomer($result = array() , $start_time = null) {
		$data = array();
		$sql = "SELECT SUM(IF(source IN(0,2) , 1 ,0)) AS app_count, SUM(IF(source IN(1,3) , 1 ,0)) AS callcenter_count FROM t_order WHERE phone = :phone AND status IN(1 , 4) AND booking_time < :start_time";
		foreach ($result as $key=>$val) {
			if (strlen($val['phone']) >= 11) {
				$command = Order::getDbReadonlyConnection()->createCommand($sql);
				$command->bindParam(":phone" , $val['phone']);
				$command->bindParam(":start_time" , $start_time);
				$count = $command->queryRow();
				$command->reset();
				if ($count['app_count'] == 0 && $count['callcenter_count'] <= 3) {
					$data[] = array(
					     'phone' => '`'.$val['phone'],
					);
				}
			}
		}
		return $data;
	}
	
	/**
	 * 获取app新客数据
	 * @param string $start_time
	 * @param string $end_time
	 * @return array $data
	 */
	public function getAppNewCustomer($start_time = null , $end_time = null) {
		if ($start_time == null || $end_time == null) {
			return false;
		}
		$sql = "SELECT DISTINCT(phone) AS phone FROM t_order WHERE status IN(1,4) AND source IN(0,2) AND booking_time BETWEEN  :start_time AND :end_time";
		$command = Order::getDbReadonlyConnection()->createCommand($sql);
		$command->bindParam(":start_time" , $start_time);
		$command->bindParam(":end_time" , $end_time);
		$result = $command->queryAll();
		$data = self::checkAppNewCustomer($result , $start_time);
		return $data;
	}
	
	/**
	 * 校验满足app新客数据
	 * @param array $result
	 * @param string $start_time
	 */
	private function checkAppNewCustomer($result = array() , $start_time = null) {
		$data = array();
		$sql = "SELECT count(order_id) AS cnt FROM t_order WHERE phone=:phone AND status IN(1 , 4) AND booking_time < :start_time";
		foreach ($result as $key=>$val) {
			if (strlen($val['phone']) >= 11) {
				$command = Order::getDbReadonlyConnection()->createCommand($sql);
				$command->bindParam(":phone" , $val['phone']);
				$command->bindParam(":start_time" , $start_time);
				$count = $command->queryRow();
				$command->reset();
				if ($count['cnt'] == 0) {
					$data[] = array(
					     'phone' => '`'.$val['phone'],
					);
				}
			}
		}
		return $data;
	}
}
