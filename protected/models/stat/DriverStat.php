<?php
/**
 * 司机数据统计 model
 * @author AndyCong<congming@edaijia.cn>
 * @version web2.0 2013-01-24
 */
class DriverStat {
	/**
	 * 获取司机排行榜数据并记录到t_driver_ranking_report
	 * @param date $date
	 */
	public function actionDriverRankingReport($date = null) {
		if ($date == null) {
			return false;
		}
		$sql = "DELETE FROM t_driver_ranking_report WHERE date = :date";
		$command = Yii::app()->dbreport->createCommand($sql);
		$date = str_replace('-','' , $date);
		$command->bindParam(":date" , $date);
		$command->execute();

		$result = self::getDriverRankingReport($date);
		$i = 0;
        foreach ($result as $val) {
			self::insertDriverRankingReport($val , $date);
			$i += 1;
		}
		echo "date:".$date."----number:".$i;
	}
	/**
	 * 获取司机排行榜记录信息
	 * @param string $date
	 * @return array $data
	 */
	private function getDriverRankingReport($date = null) {
		$sql = "SELECT current_day,driver_id,driver_user,driver,city_id,time_part,count(order_id) AS order_count,(SUM(IF((`source` IN (:source_callcenter)), 1, 0))+SUM(IF((`source` IN (:source_callcenter_input)), 1, 0))) as callcenter_count,(SUM(IF((`source` IN (:source_client_input)), 1, 0))+SUM(IF((`source` IN (:source_client)), 1, 0))) as app_count,SUM(IF((status = :order_complate), 1, 0)) as complate_count,SUM(IF((status = :order_not_confirm), 1, 0)) as not_confirm_count,SUM(IF((status in (:order_cancel , :order_confirm)), 1, 0)) as cancel_count,SUM(IF(status IN(1 , 4) , income , 0)) as income,CASE WHEN distance <= 10 THEN 10 WHEN (distance > 10 AND distance <= 20) THEN 20 WHEN (distance > 20 AND distance <= 30) THEN 30 ELSE 9999 END AS distance_area,";
		$sql .= ' SUM(IF((`status` IN(1,4) AND `source` IN (:source_client,:source_client_input)) , 1 , 0)) AS complate_app_count,';
		$sql .= ' SUM(IF((`status` IN(1,4) AND `source` IN (:source_callcenter,:source_callcenter_input)) , 1 , 0)) AS complate_callcenter_count,';

		$sql .= ' SUM(IF((`status` IN(2,3) AND `source` IN (:source_client,:source_client_input)) , 1 , 0)) AS cancel_app_count,';
		$sql .= ' SUM(IF((`status` IN(2,3) AND `source` IN (:source_callcenter,:source_callcenter_input)) , 1 , 0)) AS cancel_callcenter_count';

		$sql .= ' FROM t_daily_order_driver WHERE current_day=:current_day GROUP BY driver_id,time_part,distance_area';
		$command = Yii::app()->dbreport->createCommand($sql);

		$source_callcenter = join(',', Order::$callcenter_sources);
		$source_callcenter_input = join(',', Order::$callcenter_input_sources);
		$source_client_input = join(',', Order::$client_input_source);
		$source_client = join(',', Order::$client_source);
		$order_complate = Order::ORDER_COMPLATE;
		$order_not_confirm = Order::ORDER_NOT_COMFIRM;
		$order_cancel = Order::ORDER_CANCEL;
		$order_confirm = Order::ORDER_COMFIRM;

		$command->bindParam(":source_callcenter" , $source_callcenter );
		$command->bindParam(":source_callcenter_input" , $source_callcenter_input );
		$command->bindParam(":source_client_input" , $source_client_input );
		$command->bindParam(":source_client" , $source_client );
		$command->bindParam(":order_complate" , $order_complate );
		$command->bindParam(":order_not_confirm" , $order_not_confirm );
		$command->bindParam(":order_cancel" , $order_cancel );
		$command->bindParam(":order_confirm" , $order_confirm );
		$command->bindParam(":current_day" , $date);
		$data = $command->queryAll();
		return $data;
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
	 * 获取司机排行记录
	 * @param array $data
	 */
	private function getRecord($data = array()) {
		if (empty($data) || $date == null) {
			return false;
		}
		$sql = "SELECT id FROM t_driver_ranking_report WHERE date = :date AND driver_id = :driver_id AND time_part = :time_part";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":date" , $data['current_day']);
		$command->bindParam(":driver_id" , $data['driver_id']);
		$command->bindParam(":time_part" , $data['time_part']);
		$result = $command->queryRow();
		return $result;
	}
	/**
	 * 获取司机排行榜数据并记录到t_driver_ranking_report
	 * @param array $data
	 * @param string $date
	 */
	public function insertDriverRankingReport($data = array() , $date = null) {
		if (empty($data) || $date == null) {
			return false;
		}

		$sql = "INSERT INTO t_driver_ranking_report(date,year,month,day,driver_id,driver_user,driver,city_id,time_part,distance_area,order_count,callcenter_count,app_count,complate_count,not_confirm_count,cancel_count,income,created,complate_app_count,complate_callcenter_count,cancel_app_count,cancel_callcenter_count) VALUES(:date,:year,:month,:day,:driver_id,:driver_user,:driver,:city_id,:time_part,:distance_area,:order_count,:callcenter_count,:app_count,:complate_count,:not_confirm_count,:cancel_count,:income,:created,:complate_app_count , :complate_callcenter_count , :cancel_app_count , :cancel_callcenter_count)";
		$command = Yii::app()->dbreport->createCommand($sql);
		$year = substr($date , 0 , 4);
		$month = substr($date , 4 , 2);
		$day = substr($date , 6 , 2);
		$time = time();
		$driver_id = empty($data['driver_id']) ? 9999999 : $data['driver_id'];
		$command->bindParam(":date" , $date);
		$command->bindParam(":year" , $year);
		$command->bindParam(":month" , $month);
		$command->bindParam(":day" , $day);
		$command->bindParam(":driver_id" , $driver_id);
		$command->bindParam(":driver_user" , $data['driver_user']);
		$command->bindParam(":driver" , $data['driver']);
		$city_id = intval($data['city_id']);
		$command->bindParam(":city_id" , $city_id);
		$command->bindParam(":time_part" , $data['time_part']);
		$command->bindParam(":distance_area" , $data['distance_area']);
		$command->bindParam(":order_count" , $data['order_count']);
		$command->bindParam(":callcenter_count" , $data['callcenter_count']);
		$command->bindParam(":app_count" , $data['app_count']);
		$command->bindParam(":complate_count" , $data['complate_count']);

		$command->bindParam(":not_confirm_count" , $data['not_confirm_count']);
		$command->bindParam(":cancel_count" , $data['cancel_count']);

		$command->bindParam(":income" , $data['income']);
		$command->bindParam(":created" , $time);

		$command->bindParam(":complate_app_count" , $data['complate_app_count']);
		$command->bindParam(":complate_callcenter_count" , $data['complate_callcenter_count']);

		$command->bindParam(":cancel_app_count" , $data['cancel_app_count']);
		$command->bindParam(":cancel_callcenter_count" , $data['cancel_callcenter_count']);
		$command->execute();
		$command->reset();
	}

	/**
	 * 更新司机排行榜数据信息
	 * @param array $data
	 * @param string $date
	 */
	public function updateDriverRankingReport($data = array() , $date = null) {
		if (empty($data) || $date == null) {
			return false;
		}
		$sql = "UPDATE t_driver_ranking_report SET city_id = :city_id,order_count = :order_count,callcenter_count = :callcenter_count,app_count = :app_count,complate_count = :complate_count,not_confirm_count = :not_confirm_count,cancel_count = :cancel_count,income = :income WHERE date = :date AND time_part= :time_part AND driver_id = :driver_id";
		$command = Yii::app()->dbreport->createCommand($sql);
		$city_id = intval($data['city_id']);
		$command->bindParam(":city_id" , $city_id);
		$command->bindParam(":order_count" , $data['order_count']);
		$command->bindParam(":callcenter_count" , $data['callcenter_count']);
		$command->bindParam(":app_count" , $data['app_count']);
		$command->bindParam(":complate_count" , $data['complate_count']);
		$command->bindParam(":not_confirm_count" , $data['not_confirm_count']);
		$command->bindParam(":cancel_count" , $data['cancel_count']);
		$command->bindParam(":income" , $data['income']);
		$command->bindParam(":date" , $data['current_day']);
		$command->bindParam(":time_part" , $data['time_part']);
		$command->bindParam(":driver_id" , $data['driver_id']);

		$command->execute();
		$command->reset();
	}

	/**
	 * 数据重载
	 */
	public function actionDriverReportReload() {
		//优化 重新load前天数据 BY AndyCong
		$date = date('Y-m-d' , strtotime('-2 day'));
		self::actionDriverRankingReport($date);
		//优化 重新load前天数据 BY AndyCong END

//		$unfinished_order_date = self::getUnfinishedOrderDate();
//		$current_time = time();
//		foreach ($unfinished_order_date as $date) {
//			if (($current_time - strtotime($date['current_day'])) <= 31*86400) {
//				self::actionDriverRankingReport($date['current_day']);
//			}
//		}
	}

	/**
	 * 初始化
	 * @param string $month
	 */
	public function actionDriverReportInitialize($month = null) {
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
				self::actionDriverRankingReport($date);
			}
		}
	}

	/**
	 * 定时执行脚本
	 */
	public function actionCrontab() {
		$date = date('Y-m-d' , strtotime('-1 day'));
		self::actionDriverReportReload();
		self::actionDriverRankingReport($date);
	}
    /**
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 * ++++++++++++++++++++++++++++++上边是统计method++++++++++++++++++++++++++++++
	 * ++++++++++++++++++++++++++++++下边是展示method++++++++++++++++++++++++++++++
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 */

	/**
	 * 获取排行榜数据做展示
	 * @param array $date
	 * @return array $result
	 */
	public function getDriverRankingData($condition = array()) {
		$field = 'date,driver,driver_user,SUM(order_count) AS order_count,SUM(callcenter_count) AS callcenter_count,SUM(app_count) AS app_count,SUM(complate_count) AS complate_count,SUM(not_confirm_count) AS not_confirm_count,SUM(cancel_count) AS cancel_count,SUM(income) AS income';
		$sql = "SELECT ".$field." FROM t_driver_ranking_report WHERE date >= :start_time AND date <= :end_time";
		if (!empty($condition['time_part'])) {
			$sql .= ' AND time_part = :time_part';
		}
		if (!empty($condition['city_id'])) {
			$sql .= ' AND city_id = :city_id';
		}
		if (!empty($condition['driver_user'])) {
			$sql .= ' AND driver_user = :driver_user';
			$condition['driver_user'] = strtoupper($condition['driver_user']);
		}
		if (!empty($condition['distance_area'])) {
			$sql .= ' AND distance_area = :distance_area';
		}
		$sql .= ' GROUP BY driver_id ORDER BY income DESC,order_count DESC,app_count DESC,callcenter_count DESC';
        $command = Yii::app()->dbreport->createCommand($sql);
		$start_time = str_replace('-' , '' , $condition['start_time']);
		$end_time = str_replace('-' , '' , $condition['end_time']);
		$command->bindParam(":start_time" , $start_time);
		$command->bindParam(":end_time" , $end_time);
		if (!empty($condition['time_part'])) {
			$command->bindParam(":time_part" , $condition['time_part']);
		}
		if (!empty($condition['city_id'])) {
			$command->bindParam(":city_id" , $condition['city_id']);
		}
		if (!empty($condition['driver_user'])) {
			$command->bindParam(":driver_user" , $condition['driver_user']);
		}
		if (!empty($condition['distance_area'])) {
			$command->bindParam(":distance_area" , $condition['distance_area']);
		}
		$result = $command->queryAll();
		$dataProvider = new CArrayDataProvider($result, array (
			'id'=>'driver_ranking',
			'keyField'=>'date',
			'pagination'=>array (
				'pageSize'=>30)
			)
		);
		$data['dataDriverRanking'] = $dataProvider;
		$driverRankCount = self::getDriverRankingCount($condition);
		$data['driverRankCount'] = $driverRankCount;
		return $data;
	}



    public function getDriverRankingDataArray($condition = array()) {
        $field = '*';
        $sql = "SELECT ".$field." FROM t_driver_ranking_report WHERE date >= :start_time AND date <= :end_time";
        $param=array();
        if (!empty($condition['time_part'])) {
            $sql .= ' AND time_part = :time_part';
            $param[':time_part']=$condition['time_part'];
        }
        if (!empty($condition['city_id'])) {
            $sql .= ' AND city_id = :city_id';
            $param[':city_id']=$condition['city_id'];
        }
        if (!empty($condition['driver_user'])) {
            $sql .= ' AND driver_user = :driver_user';
            $condition['driver_user'] = strtoupper($condition['driver_user']);
            $param[':driver_user']=$condition['driver_user'];
        }
        if (!empty($condition['distance_area'])) {
            $sql .= ' AND distance_area = :distance_area';
            $param[':distance_area']=$condition['distance_area'];
        }
        $sql .= ' GROUP BY driver_id ORDER BY income DESC,order_count DESC,app_count DESC,callcenter_count DESC';


        $command = Yii::app()->dbreport->createCommand($sql);
        $start_time = str_replace('-' , '' , $condition['start_time']);
        $end_time = str_replace('-' , '' , $condition['end_time']);

        $param[':start_time']=$start_time;
        $param[':end_time']=$end_time;

        $result = $command->queryAll(true,$param);

        return $result;
    }


	/**
	 * 获取全部订单数、接单司机总数、平均接单数
	 * @param array $condition
	 * @return string $rank_str
	 */
	public function getDriverRankingCount($condition = array()) {
		$sql = "SELECT SUM(complate_count+not_confirm_count) as order_count,COUNT(distinct(driver_id)) as driver_number FROM t_driver_ranking_report WHERE date >= :start_time AND date <= :end_time";
		if (!empty($condition['time_part'])) {
			$sql .= ' AND time_part = :time_part';
		}
		if (!empty($condition['city_id'])) {
			$sql .= ' AND city_id = :city_id';
		}
		$command = Yii::app()->dbreport->createCommand($sql);
		$start_time = str_replace('-' , '' , $condition['start_time']);
		$end_time = str_replace('-' , '' , $condition['end_time']);
		$command->bindParam(":start_time" , $start_time);
		$command->bindParam(":end_time" , $end_time);
		if (!empty($condition['time_part'])) {
			$command->bindParam(":time_part" , $condition['time_part']);
		}
		$city = Dict::items('city');
		if (!empty($condition['city_id'])) {
			$command->bindParam(":city_id" , $condition['city_id']);
			$city_name = $city[$condition['city_id']];
		}else {
			$city_name = '全部';
		}
		$count = $command->queryRow();

		if ($count['order_count'] > 0 && $count['driver_number'] > 0) {
			$rank_str ='<h3>
				'.$city_name.'订单总数:<font color="red">'.$count['order_count'].'</font>单，
				接单司机人数：<font color="red">'.$count['driver_number'].'</font>,
				平均接单数：<font color="red">'.sprintf("%.2f",$count['order_count']/$count['driver_number']).'</font>单/人
			</h3><br />';
		}else {
			$rank_str = '';
		}
		return $rank_str;
	}

	/**
	 * 获取司机订单数量通过司机工号
	 * @param string $driver_id
	 * @return int
	 * @author AndyCong<congming@edaijia.cn>
	 * @version 2013-05-08
	 */
	public function getOrderCountByDriver($driver_id = '') {
		if (empty($driver_id)) {
			return false;
		}
		$sql = "SELECT SUM(complate_count) AS cnt FROM t_driver_ranking_report WHERE driver_user = :driver_id";
		$command = Yii::app()->dbreport->createCommand($sql);
		$command->bindParam(":driver_id" , $driver_id);
		$result = $command->queryRow();
		return $result['cnt'];
	}

    /**
     * 订单 司机接单 统计
     * @param $city_id
     * @param $start_time
     * @param $end_time
     * @return string
     * @author bidong
     */
    public function getDriverRankString($city_id, $start_time, $end_time)
    {
        $start_time = str_replace('-', '', $start_time);
        $end_time = str_replace('-', '', $end_time);
        $whereStr='date >= :start_time AND date <= :end_time';
        $param=array(':start_time'=>$start_time,':end_time'=>$end_time);

        if($city_id){
            $whereStr.=' and  city_id = :city_id';
            $param[':city_id']=$city_id;
        }

        $count = Yii::app()->dbreport->createCommand()
                    ->select('SUM(complate_count+not_confirm_count) as order_count,COUNT(distinct(driver_id)) as driver_number')
                    ->from('{{driver_ranking_report}}')
                    ->where($whereStr,$param)
                    ->queryRow();

        $rank_str = '';
        $city_arr =  Dict::items('city');
        if(isset($city_arr[$city_id])){
            $city_name=$city_arr[$city_id];
            if ($count['order_count'] > 0 && $count['driver_number'] > 0) {
                $rank_str = '<h4>'.$city_name.'.'.substr($start_time,-4).'日 总报单：<font color="red">'.$count['order_count'].'</font>，
				接单司机：<font color="red">'.$count['driver_number'].'</font>，
				人均接单：<font color="red">'.sprintf("%.2f", $count['order_count']/$count['driver_number']).'</font>单/人</h4>';
            }
        }

        return $rank_str;
    }

    /**
	 * 司机消单排行统计
	 * @param array $condition
	 * @return object
	 */
	public function getDriverCancelStat($condition = array()) {
		$start_time = str_replace('-' , '' , htmlspecialchars($condition['start_time']));
		$end_time = str_replace('-' , '' , htmlspecialchars($condition['end_time']));
		$field = 'date,driver,driver_user,SUM(order_count) AS order_count,SUM(cancel_count) AS cancel_count,SUM(cancel_count)/SUM(order_count) AS cancel_rate,SUM(app_count) AS app_count,SUM(cancel_app_count) AS cancel_app_count,SUM(cancel_app_count)/SUM(app_count) AS cancel_app_rate,SUM(callcenter_count) AS callcenter_count,SUM(cancel_callcenter_count) AS cancel_callcenter_count,SUM(cancel_callcenter_count)/SUM(callcenter_count) AS cancel_callcenter_rate,'.$start_time.' AS start_time,'.$end_time.' AS end_time, SUM(alert_num) AS alert_num, SUM(alert_num) / SUM(cancel_count) AS alert_rate'; // 添加恶意消单和恶意消单率 duke 201403 13
		$sql = "SELECT ".$field." FROM t_driver_ranking_report WHERE date >= '".$start_time."' AND date <= '".$end_time."'";
		if (!empty($condition['city_id'])) {
			$sql .= ' AND city_id = '.intval($condition['city_id']);
		}
		if (!empty($condition['driver_user'])) {
			$sql .= ' AND driver_user = "'.strtoupper(htmlspecialchars($condition['driver_user'])).'"';
		}
        if (!empty($condition['processed'])) {
            if(intval($condition['processed'])==1){
                $sql .= ' AND processed = '.intval($condition['processed']);
            }else{
                $sql .= ' AND processed = 0';
            }
        }
		$sql .= ' GROUP BY driver_id HAVING 1=1 ';
		$sql_count_area = $this->_getCountArea($condition['count_area'],$condition['count_num']);
		$sql_rate_area = $this->_getRateArea($condition['rate_area'],$condition['rate_num']);
		$sql_rate_alert = $this->_getRateAlert($condition['rate_alert'],$condition['alert_num']);
		$sql .= $sql_count_area.$sql_rate_area.$sql_rate_alert;

		$orderby = $this->_getGroupCondition($condition['sortby']);
		$sql .= ' ORDER BY '.$orderby;
		$count = Yii::app()->dbreport->createCommand($sql)->query()->count();
		//sql数据转化成Provider格式 源自：http://blog.yiibook.com/?p=420   Yii手册CSqlDataProvider
		$dataProvider = new CSqlDataProvider($sql, array(
	            'keyField'=>'driver_user',
	            'totalItemCount'=>$count,
	            'db'=>Yii::app()->dbreport,
	            'pagination'=>array(
			        'pageSize'=>100,
			    ),
	    ));
	    $data = array();
	    $data['dataProvider'] = $dataProvider;
	    $data['cancel_count'] = $this->getCancelCount($condition);
		return $data;
	}

	private function _getCountArea($count_area , $count_num) {
		if (empty($count_area) || empty($count_num)) {
			return '';
		}
		$sql_count_area = ' AND order_count '.$count_area.$count_num;
		return $sql_count_area;
	}

	private function _getRateArea($rate_area , $rate_num) {
		if (empty($rate_area) || empty($rate_num)) {
			return '';
		}
		$sql_rate_area = ' AND cancel_rate*100 '.$rate_area.$rate_num;
		return $sql_rate_area;
	}

	private function _getRateAlert($rate_area , $rate_num) {
		if (empty($rate_area) || empty($rate_num)) {
			return '';
		}
		$sql_rate_alert = ' AND alert_rate*100 '.$rate_area.$rate_num;
		return $sql_rate_alert;
	}

	/**
	 * 获取排序字段
	 *
	 * @param unknown_type $sortby
	 * @return unknown
	 */
	private function _getGroupCondition($sortby) {
		switch ($sortby) {
			case '':
				$orderby = ' cancel_rate DESC , order_count DESC,app_count DESC,callcenter_count DESC';
				break;
			case '1':
				$orderby = ' cancel_app_rate DESC , app_count DESC';
				break;
			case '2':
				$orderby = ' cancel_callcenter_rate DESC , callcenter_count DESC';
				break;
			case '3':
				$orderby = 'order_count DESC , cancel_rate DESC';
				break;
			case '4':
				$orderby = ' app_count DESC , cancel_app_rate DESC';
				break;
			case '5':
				$orderby = ' callcenter_count DESC , cancel_callcenter_rate DESC';
				break;
			case '6':
				$orderby = ' cancel_count DESC , order_count ASC';
				break;
			case '7':
				$orderby = ' cancel_app_count DESC , app_count ASC';
				break;
			case '8':
				$orderby = ' cancel_callcenter_count DESC , callcenter_count ASC';
				break;
			case '9':
				$orderby = ' alert_rate DESC , cancel_rate ASC';
				break;
			default:
				$orderby = ' cancel_rate DESC , order_count DESC';
				break;
		}
		return $orderby;
	}

	/**
	 * 获取全部订单数、接单司机总数、平均接单数
	 * @param array $condition
	 * @return string $rank_str
	 */
	public function getCancelCount($condition = array()) {
		$cancel_str = '';
		$sql = "SELECT SUM(order_count) AS order_count,SUM(complate_count) AS complate_count,SUM(cancel_count) AS cancel_count,SUM(cancel_count)/SUM(order_count) AS cancel_rate,COUNT(distinct(driver_id)) as driver_number FROM t_driver_ranking_report WHERE date >= :start_time AND date <= :end_time";
		if (!empty($condition['city_id'])) {
			$sql .= ' AND city_id = :city_id';
		}
		$command = Yii::app()->dbreport->createCommand($sql);
		$start_time = str_replace('-' , '' , $condition['start_time']);
		$end_time = str_replace('-' , '' , $condition['end_time']);
		$command->bindParam(":start_time" , $start_time);
		$command->bindParam(":end_time" , $end_time);
		$city = Dict::items('city');
		if (!empty($condition['city_id'])) {
			$command->bindParam(":city_id" , $condition['city_id']);
			$city_name = $city[$condition['city_id']];
		}else {
			$city_name = '全部';
		}
		$count = $command->queryRow();

		if ($count['order_count'] > 0 && $count['driver_number'] > 0) {
			$cancel_str ='
				'.$city_name.'订单总数:<font color="red">'.$count['order_count'].'</font>单，
				报单总数：<font color="red">'.$count['complate_count'].'</font>,
				销单总数：<font color="red">'.$count['cancel_count'].'</font>,
				总销单率：<font color="red">'.sprintf("%.2f%%",$count['cancel_rate']*100).'</font>';
		}
		return $cancel_str;
	}




	/**
	 * 通过订单号 获取信息
	 * @param   type    $varname    description
	 * @param   type    $varname    description
	 * @param   type    $varname    description
	 * @return  type    description
	 * @access  public or private
	 * @static  makes the class property accessible without needing an instantiation of the class
	 */
	public function getInfoByOrderid($id)
	{

		$command = Yii::app()->dbreport->createCommand();
        $command->select('id,order_id,alert_level');
        $command->from('t_daily_order_driver');
        $command->where('order_id=:order_ids', array(':order_ids'=>$id));
        $data = $command->queryRow();

		if (!empty($data))
		{
			return $data;
		}
		return false;


	} // end func
}
