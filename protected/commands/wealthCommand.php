 <?php
/**
*	aiguoxin
*	司机财富系统
**/
class wealthCommand extends LoggerExtCommand
{
	

	/**
	*	周全勤奖 
	*	一周内每天都高峰上线2.5个小时以上且至少有5单,80e
	*	每周一结算上周 
	*/
	public function actionWeekStat(){
        $today = date("Y-m-d");
        $yesterday = date("Y-m-d 23:59:59",strtotime("-1 day"));
        $weekago= date("Y-m-d 00:00:00",strtotime("-7 day"));
        $stat_day = date("Y-m-d",strtotime("-1 day"));
        echo 'today='.$today.',yesterday='.$yesterday.',weekago='.$weekago.PHP_EOL;
        EdjLog::info('today='.$today.',yesterday='.$yesterday.',weekago='.$weekago);

        //compute one week hot noline > 2.5h
        $sql = "select count(1) as total, driver_id from t_driver_wealth_log  
         		where  wealth>10 and type=3 and create_time>=:start_time and create_time<=:end_time group by driver_id";
        $command = Yii::app()->dbreport->createCommand($sql);
        $command->bindParam(":start_time", $weekago);
        $command->bindParam(":end_time",$yesterday);
        $driver_list = $command->queryAll();

        echo 'driver list='.count($driver_list).PHP_EOL;
       	EdjLog::info('driver list='.count($driver_list));
        //find 7 days online
        foreach ($driver_list as $driver) {
        	$driver_id = $driver['driver_id'];
	        $daysCount = $driver['total'];
	        echo 'driver='.$driver_id.',daysCount='.$daysCount.PHP_EOL;
	        EdjLog::info('driver='.$driver_id.',daysCount='.$daysCount);
	        if($daysCount == 7){
	        	//different 7 days,find report order >= 5
	        	$sql = "select count(1) as total,city_id from t_order 
	        	where  driver_id=:driver_id and status=1 
	        	and booking_time>UNIX_TIMESTAMP(:start_time) and booking_time<UNIX_TIMESTAMP(:end_time)";
	       		$command = Order::getDbReadonlyConnection()->createCommand($sql);
		        $command->bindParam(":start_time", $weekago);
		        $command->bindParam(":end_time",$yesterday);
		        $command->bindParam(":driver_id",$driver_id);
		        $driverInfo = $command->queryRow();
		        $orderCount = $driverInfo['total'];
		       	echo 'driver='.$driver_id.',orderCount='.$orderCount.PHP_EOL;
		       	EdjLog::info('driver='.$driver_id.',orderCount='.$orderCount);
		        if($orderCount >= 5){
		        	//add wealth 80 for driver
		        	DriverExt::model()->addWealth($driver_id,DriverWealthLog::WEEK_WEALTH);
		        	DriverWealthLog::model()->addLog($driver_id,DriverWealthLog::WEEK_TYPE,DriverWealthLog::WEEK_WEALTH,$driverInfo['city_id']);
		       		//增加总数,周全勤在每天统计之后,计算到周天
                    DriverWealthDayStat::model()->updateDayStatForWeek($driver_id,$stat_day);
                    echo 'driver='.$driver_id.'week online wealth success'.PHP_EOL;
		       		EdjLog::info('driver='.$driver_id.'week online wealth success');
		        }
	        }

        }

	}

	/**
	*	每天定时任务	
	*
	*/
	public function actionDayStat(){
        $today = date("Y-m-d");
        $yesterday_start = date("Y-m-d 07:00:00",strtotime("-1 day"));
        $yesterday_end = date("Y-m-d 07:00:00");
       
		//1. quick receive order
		echo $today.' start to quick receive order'.PHP_EOL;
        EdjLog::info($today.' start to quick receive order');
		$this->actionQuickReceiveOrder($today,$yesterday_start,$yesterday_end);

		//2. hot time online
		echo $today.' start to hot online'.PHP_EOL;
        EdjLog::info($today.' start to hot online');
		$this->actionHotOnline($today,$yesterday_start,$yesterday_end);

		//3. reach in time and long distance
		echo $today.' start to long distance'.PHP_EOL;
        EdjLog::info($today.' start to long distance');
		$this->actionReachInTimeAndLongDistance($today,$yesterday_start,$yesterday_end);

		//4 .five star
		echo $today.' start to five star'.PHP_EOL;
        EdjLog::info($today.' start to five star');
		$this->actionFiveStar($today,$yesterday_start,$yesterday_end);

		//5. reject or cancel
		echo $today.' start to cancel'.PHP_EOL;
        EdjLog::info($today.' start to cancel');
		$this->actionCancel($today,$yesterday_start,$yesterday_end);

		//6. reject
		echo $today.' start to reject'.PHP_EOL;
        EdjLog::info($today.' start to reject');
		$this->actionReject();


		//7. driver stat
		echo $today.' start to every driver day stat'.PHP_EOL;
        EdjLog::info($today.' start to every driver day stat');
		$this->actionDayAll($today,$yesterday_start,$yesterday_end);

		//8. driver month stat
		echo $today.' start to every driver month stat'.PHP_EOL;
        EdjLog::info($today.' start to every driver month stat');
		$this->actionMonthAll();

		//周全勤，每周一运行
		$week_day = date('w');
		if($week_day == 1){
			$this->actionWeekStat();
			echo $today.' start to every driver week stat'.PHP_EOL;
        	EdjLog::info($today.' start to every driver week stat');
		}else{
			echo $today.' 今天不是周一，周统计不运行'.PHP_EOL;
        	EdjLog::info($today.' 今天不是周一，周统计不运行');
		}
	}


	/**
	*	快速接单奖励,5秒内接单,3e
	*
	*/
	public function actionQuickReceiveOrder($today,$yesterday_start,$yesterday_end){

		
        echo 'today='.$today.',yesterday_start='.$yesterday_start.',yesterday_end='.$yesterday_end.PHP_EOL;
        EdjLog::info('today='.$today.',yesterday_start='.$yesterday_start.',yesterday_end='.$yesterday_end);

        //从订单日志表获取昨天报单
	    $sql="select * from t_order_log where created>=UNIX_TIMESTAMP(:start_time) 
                and created<=UNIX_TIMESTAMP(:end_time) and description='报单'";

		$command = Yii::app()->db_readonly->createCommand($sql);
        $command->bindParam(":start_time", $yesterday_start);
        $command->bindParam(":end_time",$yesterday_end);
        $order_list = $command->queryAll();

        echo 'order_list list='.count($order_list).PHP_EOL;
       	EdjLog::info('order_list='.count($order_list));

        foreach ($order_list as $order_log) {
            $t_order_ext = OrderExt::model()->getPrimary($order_log['order_id']);
            if(empty($t_order_ext)){
                continue;
            }
            $driver_receive_time = $t_order_ext['driver_receive_time'];
            if(0<$driver_receive_time && $driver_receive_time<=5){
        	$order = Order::model()->getOrderById($order_log['order_id']);
        	$driver_id=$order['driver_id'];
        	$total_wealth=DriverWealthLog::QUICK_RECEIVE_ORDER_WEALTH;
        	//add wealth 3 for driver
        	DriverExt::model()->addWealth($driver_id,$total_wealth);
        	DriverWealthLog::model()->addLog($driver_id,DriverWealthLog::QUICK_ACCEPT_TYPE,$total_wealth,$order['city_id']);
       		echo 'driver='.$driver_id.',wealth='.$total_wealth.'day quick receive order wealth success'.PHP_EOL;
       		EdjLog::info('driver='.$driver_id.',wealth='.$total_wealth.'day quick receive order wealth success');
            }
        }
	}

	/**
	*	高峰上线,8点到12点上线,每15分钟1e
	*
	*/
	public function actionHotOnline($today,$yesterday_start,$yesterday_end){
		//8-12,高峰期最多4个小时，也就是16个15分钟
		$max_wealth = DriverWealthLog::HOT_ONLINE_WEALTH*4*4;
        $sql="select sum(hot_time) as total,driver_id from t_driver_online_log 
        where create_time>=:start_time and create_time<=:end_time group by driver_id";
		$command = Yii::app()->db_readonly->createCommand($sql);
        $command->bindParam(":start_time", $yesterday_start);
        $command->bindParam(":end_time",$yesterday_end);
        $log_list = $command->queryAll();
        echo count($log_list).PHP_EOL;
        foreach ($log_list as $log) {
        	$driver_id=$log['driver_id'];
        	$total_wealth = round($log['total']/(15*60*1000))*DriverWealthLog::HOT_ONLINE_WEALTH;
        	//晚上8-12点高峰在线，最大值不能超过
        	if($total_wealth > $max_wealth){
        		$total_wealth = $max_wealth;
        	}
        	echo 'total_time='.$log['total'].',total_wealth='.$total_wealth.PHP_EOL;
        	$city_id = 0;
        	$driver = Driver::model()->getDriver($driver_id);
        	if($driver){
        		$city_id = $driver['city_id'];
        	}
        	DriverExt::model()->addWealth($driver_id,$total_wealth);
        	DriverWealthLog::model()->addLog($driver_id,DriverWealthLog::HOT_ONLINE_TYPE,$total_wealth,$city_id);
       		echo 'driver='.$driver_id.'hot_time online wealth success'.PHP_EOL;
       		EdjLog::info('driver='.$driver_id.'hot_time online wealth success');
        }
	}

	/**
	*	准时抵达,10分钟内到达,或行进速度200m每分以上,5e
	*   远距离,3公里以上赶路,10e
	*/
	public function actionReachInTimeAndLongDistance($today,$yesterday_start,$yesterday_end){
        echo 'today='.$today.',yesterday_start='.$yesterday_start.',yesterday_end='.$yesterday_end.PHP_EOL;
        EdjLog::info('today='.$today.',yesterday_start='.$yesterday_start.',yesterday_end='.$yesterday_end);

                $sql="select * from t_order_log where created>=UNIX_TIMESTAMP(:start_time) 
                        and created<=UNIX_TIMESTAMP(:end_time) and description='报单'";

		$command = Yii::app()->db_readonly->createCommand($sql);
        $command->bindParam(":start_time", $yesterday_start);
        $command->bindParam(":end_time",$yesterday_end);
        $order_list = $command->queryAll();

        echo 'order_list list='.count($order_list).PHP_EOL;
       	EdjLog::info('order_list='.count($order_list));

        foreach ($order_list as $order_log) {
        	$order_id=$order_log['order_id'];
        	$order = Order::model()->getOrderById($order_id);
        	if(empty($order)){
        		continue;
        	}
        	$driver_id = $order['driver_id'];
        	//find order_ext
        	$t_order = OrderExt::model()->getPrimary($order_id);
            if(empty($t_order)){
                continue;
            }
        	$total_wealth=DriverWealthLog::REACH_INTIME_WEALTH;
        	
        	//reach in time 单位秒
        	if(($t_order['driver_ready_time'] <= 600 && $t_order['driver_ready_time'] <= 600)
        		|| ($t_order['driver_ready_distance']*1000)/($t_order['driver_ready_time']/60) >= 200){
	        	DriverExt::model()->addWealth($driver_id,$total_wealth);
	        	DriverWealthLog::model()->addLog($driver_id,DriverWealthLog::REACH_INTIME_TYPE,$total_wealth,$order['city_id']);
	       		echo 'driver='.$driver_id.',wealth='.$total_wealth.'day receive order in time wealth success'.PHP_EOL;
	       		EdjLog::info('driver='.$driver_id.',wealth='.$total_wealth.'day quick receive order wealth success');
        	}

        	//long distance
        	if($t_order['driver_ready_distance'] >= 3){
        		$total_wealth = DriverWealthLog::LONG_DISTANCE_WEALTH;
	        	DriverExt::model()->addWealth($driver_id,$total_wealth);
	        	DriverWealthLog::model()->addLog($driver_id,DriverWealthLog::LONG_DISTANCE_TYPE,$total_wealth,$order['city_id']);
	       		echo 'driver='.$driver_id.',wealth='.$total_wealth.'day receive order in time wealth success'.PHP_EOL;
	       		EdjLog::info('driver='.$driver_id.',wealth='.$total_wealth.'day quick receive order wealth success');
        	}
        }
	}


	/**
	*	五星评价 获得5星好评 10e
	*
	*/
	public function actionFiveStar($today,$yesterday_start,$yesterday_end){
		
        $sql="select count(id) as total,driver_id from t_comment_sms 
        where created>=:start_time and created<=:end_time and level=5 group by driver_id";
		$command = Yii::app()->db_readonly->createCommand($sql);
        $command->bindParam(":start_time", $yesterday_start);
        $command->bindParam(":end_time",$yesterday_end);
        $log_list = $command->queryAll();
        echo count($log_list).PHP_EOL;
        foreach ($log_list as $log) {
        	$driver_id=$log['driver_id'];
        	$total_wealth = $log['total']*DriverWealthLog::FIVE_STAR_WEALTH;
        	echo 'total_time='.$log['total'].',total_wealth='.$total_wealth.PHP_EOL;
        	$city_id = 0;
        	$driver = Driver::model()->getDriver($driver_id);
        	if($driver){
        		$city_id = $driver['city_id'];
        	}
        	DriverExt::model()->addWealth($driver_id,$total_wealth);
        	DriverWealthLog::model()->addLog($driver_id,DriverWealthLog::FIVE_STAR_TYPE,$total_wealth,$city_id);
       		echo 'driver='.$driver_id.'five star wealth success'.PHP_EOL;
       		EdjLog::info('driver='.$driver_id.'five star wealth success');
        }
	}


	/**
	*	销单或拒单 -20e,销单2，3,4;并排除渠道channel=01004 or channel=01007（远程叫单） or (source=1 and channel=0)
	*
	*/
	public function actionCancel($today,$yesterday_start,$yesterday_end){
        $max=0;
        while (true) {
            $sql="select order_id,driver_id,city_id,status,source,channel from t_order where order_id>:max and booking_time>=UNIX_TIMESTAMP(:start_time) 
            and booking_time<=UNIX_TIMESTAMP(:end_time) and status in(2,4) and channel not in('01004','01007') order by order_id limit 3000";
            $command = Order::getDbReadonlyConnection()->createCommand($sql);
            $command->bindParam(":start_time", $yesterday_start);
            $command->bindParam(":end_time",$yesterday_end);
            $command->bindParam(":max",$max);
            $order_list = $command->queryAll();
            // echo count($order_list).PHP_EOL;
            if($order_list){
                foreach ($order_list as $order) {
                	$driver_id=$order['driver_id'];
                    $order_id = $order['order_id'];
                    $max = $order_id;
                    ######防止重复处理一个订单
                    if(DriverStatus::model()->single_get('reject_wealth'.$order_id)){
                        echo 'order_id='.$order_id.',已经处理过'.PHP_EOL;
                        EdjLog::info('order_id='.$order_id.',已经处理过');
                        continue;                    
                    }else{
                        DriverStatus::model()->single_set('reject_wealth'.$driver_id,1,3600*24);
                    }
                    //400不扣
                    if(in_array($order['source'], Order::$callcenter_sources)){
                        echo 'order_id='.$order_id.',driver_id='.$driver_id.'source='.$order['source'].'不扣e币'.PHP_EOL;
                        EdjLog::info('order_id='.$order_id.',driver_id='.$driver_id.'source='.$order['source'].'不扣e币');
                        continue;
                    }

                    //电话单
                    if($order['channel'] == '0' and $order['source']==1){
                        echo 'order_id='.$order_id.',driver_id='.$driver_id.'source=0,channel=1不扣e币'.PHP_EOL;
                        EdjLog::info('order_id='.$order_id.',driver_id='.$driver_id.'source=0,channel=1不扣e币');
                        continue;
                    }
                    //公司规定组长需要让单给组员，这就造成了一个问题，组长发扬精神销单但是会计算销单率，
                    //所以希望在销单率计算和e币扣分时希望去掉组长本人销单的情况 显赫需求
                    if($driver_id == ROrder::model()->getGroup($order_id,'leader')){//组长单，则不计算
                        echo 'order_id='.$order_id.',driver_id='.$driver_id.'是组长销单，不扣e币'.PHP_EOL;
                        EdjLog::info('order_id='.$order_id.',driver_id='.$driver_id.'是组长销单，不扣e币');
                        continue;
                    }

                    //新增，为了深圳某天不扣e币
                    if(!$this->canSend($today,$driver_id)){
                        echo 'order_id='.$order_id.',driver_id='.$driver_id.'特殊情况，不扣e币'.PHP_EOL;
                        EdjLog::info('order_id='.$order_id.',driver_id='.$driver_id.'特殊情况，不扣e币');
                        continue;
                    }

                	//status=8 reject order
                	$total_wealth = DriverWealthLog::CANCEL_WEALTH;
                	$type = DriverWealthLog::CANCEL_TYPE;
                	DriverExt::model()->addWealth($driver_id,$total_wealth);
                	DriverWealthLog::model()->addLog($driver_id,$type,$total_wealth,$order['city_id']);
               		echo 'order_id='.$order_id.'driver='.$driver_id.'cancel wealth success'.PHP_EOL;
               		EdjLog::info('order_id='.$order_id.'driver='.$driver_id.'cancel wealth success');
                }
            }else{
                break;
            }
        }
	}

    public function actionTest($driver_id){
        $today = date("Y-m-d");
        $res =  $this->canSend($today,$driver_id);
        if($res){
            echo 'driver='.$driver_id.'扣'.PHP_EOL;
        }else{
            echo 'driver='.$driver_id.'不扣'.PHP_EOL;
        }
    }

    /***
     * @param $today
     * @param $driver_id
     * @param $city_id
     * return boolean
     */
    public function canSend($today,$driver_id){
        $cityArray = array(6);//深圳
        $dateArray = array('2015-04-17','2015-04-18');
        $flag = true;
        $driver = DriverStatus::model()->get($driver_id);
        if($driver){
            $city_id = $driver->city_id;
            if(in_array($city_id,$cityArray) && in_array($today,$dateArray)){
                $flag = false;
                echo 'today='.$today.',driver_id='.$driver_id.'特殊情况，不扣e币'.PHP_EOL;
                EdjLog::info('today='.$today.',driver_id='.$driver_id.'特殊情况，不扣e币');
            }
        }
        return $flag;
    }

	/**
	*	@author aiguoxin	
	*	司机昨天拒单数
	*/
	public function actionReject(){
		$max=0;
        while (true) {
            $sql = "SELECT id,user,city_id FROM t_driver WHERE id>:max and mark != 3 LIMIT 1000";
            //test
            // $sql = "SELECT id,user,city_id,phone,ext_phone FROM t_driver WHERE user in('BJ9010','BJ9017','BJ9036','BJ9035','BJ9005')";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":max",$max);
            $driver_list = $command->queryAll();
            if ($driver_list) {
                foreach ($driver_list as $driver) {
                    $flag = false;
                    $max = $driver['id'];
                    $driver_id = $driver['user'];
                    $count = DriverInspireData::model()->getLastdayRejectCountByDriverId($driver_id);
                    if($count > 0){
                    	$type = DriverWealthLog::REJECT_TYPE;
	                    $total_wealth = DriverWealthLog::REJECT_WEALTH*$count;
	                    DriverExt::model()->addWealth($driver_id,$total_wealth);
			        	DriverWealthLog::model()->addLog($driver_id,$type,$total_wealth,$driver['city_id']);
			       		echo 'driver='.$driver_id.'reject wealth success'.PHP_EOL;
			       		EdjLog::info('driver='.$driver_id.'reject wealth success');
		       		}else{
						echo 'driver='.$driver_id.'reject wealth success'.PHP_EOL;
			       		EdjLog::info('driver='.$driver_id.'reject wealth success');
		       		}
                }
            }else{
                break;
            }
        }
	}

	/**
	*	每个司机都有一个后台每日e币明细,每天7点结算
	*
	*/
	public function actionDayAll($today,$yesterday_start,$yesterday_end){
	
        $max=0;
        //find all driver
		while (true) {
            $sql = "SELECT id,user,city_id FROM t_driver WHERE id>:max and mark != 3 LIMIT 5000";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":max",$max);
            $driver_list = $command->queryAll();
            echo count($driver_list);
            if($driver_list){
            	foreach ($driver_list as $driver) {
            		$max = $driver['id'];
            		$driver_id = $driver['user'];
            		echo $driver_id.PHP_EOL;
					$sql="select sum(wealth) as sum, count(1) as total, type from t_driver_wealth_log 
				        where driver_id=:driver_id and create_time>=:start_time and create_time<=:end_time group by type";
					$command = Yii::app()->dbreport->createCommand($sql);
			        $command->bindParam(":start_time", $yesterday_start);
			        $command->bindParam(":end_time",$yesterday_end);
			        $command->bindParam(":driver_id",$driver_id);
			        $wealth_list = $command->queryAll();
			        //init data
			        $five_star_count=0;
			        $reach_count=0;
			        $receive_count=0;
			        $group_count=0;
			        $hotline_count=0;
			        $long_distance_count=0;
			        $week_count=0;
			        $cancel_count=0;
			        $reject_count=0;
			        $reward_count=0;
                    $day_order_count=0;
                    $invest_count=0;
                    $reward_punish_count=0;//不是个数,是总财富
			        $stat_day=date("Y-m-d",strtotime("-1 day"));
			        $city_id=$driver['city_id'];

			        $total_wealth =0;
			        foreach ($wealth_list as $wealth) {
                        $total = $wealth['total'];
	        			$count=$wealth['sum']/DriverWealthLog::$typeWealth[$wealth['type']];
	        			$total_wealth += $wealth['sum'];
	        			switch ($wealth['type']) {
	        				case DriverWealthLog::WEEK_TYPE:
	        					$week_count = $count;
	        					break;
	        				case DriverWealthLog::QUICK_ACCEPT_TYPE:
	        					$receive_count = $count;
	        					break;
	        				case DriverWealthLog::HOT_ONLINE_TYPE:
	        					$hotline_count = $count;
	        					break;
	        				case DriverWealthLog::REACH_INTIME_TYPE:
	        					$reach_count = $count;
	        					break;
	        				case DriverWealthLog::FIVE_STAR_TYPE:
	        					$five_star_count = $count;
	        					break;
	        				case DriverWealthLog::LONG_DISTANCE_TYPE:
	        					$long_distance_count =$count;
	        					break;
	        				case DriverWealthLog::GROUP_TYPE:
	        					$group_count = $count;
	        					break;
	        				case DriverWealthLog::REJECT_TYPE:
	        					$reject_count = $count;
	        					break;
	        				case DriverWealthLog::CANCEL_TYPE:
	        					$cancel_count = $count;
	        					break;
                            case DriverWealthLog::DRIVER_REWARD:
                                $reward_count = $count;
                            case DriverWealthLog::DAY_ORDER_INFO_TYPE:
                                $day_order_count = $count;
                                break;
                            case DriverWealthLog::INVEST_TYPE:
                                $invest_count=$total;
                                break;
                            case DriverWealthLog::REWARD_PUNISH_TYPE:
                                $reward_punish_count=$count;
                                break;
	        				default:
	        					# code...
	        					break;
	        			}
			        }
			        
			     	$param=array('driver_id'=>$driver_id,
			     		'five_star_count'=>$five_star_count,
			     		'reach_count'=>$reach_count,
			     		'receive_count'=>$receive_count,
			     		'group_count'=>$group_count,
			     		'hotline_count'=>$hotline_count,
			     		'long_distance_count'=>$long_distance_count,
			     		'week_count'=>$week_count,
			     		'cancel_count'=>$cancel_count,
			     		'reject_count'=>$reject_count,
			     		'reward_count'=>$reward_count,
                        'day_order_count'=>$day_order_count,
                        'invest_count'=>$invest_count,
                        'reward_punish_count'=>$reward_punish_count,
			     		'stat_day'=>$stat_day,
			     		'city_id'=>$city_id,
			     		'total_wealth'=>$total_wealth);
			     	//如果都为空，则不统计该用户信息
			     	if($reward_punish_count== 0 && $five_star_count == 0 && $reach_count == 0 && $receive_count == 0 && $group_count == 0
			     		&& $hotline_count == 0 && $long_distance_count ==0 && $week_count == 0 && $day_order_count == 0
			     		&& $cancel_count ==0 && $reject_count ==0 && $reward_count ==0 &&$invest_count == 0 && $total_wealth == 0){
			     		// echo 'driver_id='.$driver_id.' 都为空，过滤'.PHP_EOL;
			     		continue;
			     	}
			     	//更新redis为未读
			     	$driver = DriverStatus::model()->get($driver_id);
			     	$driver->read_flag = 0;
                    $day_wealth=DriverWealthDayStat::model()->getWealth($driver_id,$stat_day);
			     	if($day_wealth){
                        DriverWealthDayStat::model()->updateDayStat($param);
                        echo 'update driver_id='.$driver_id.' day is ok'.PHP_EOL;
                    }else{
                        DriverWealthDayStat::model()->addDayStat($param);
                        echo 'save driver_id='.$driver_id.' day is ok'.PHP_EOL;
                    }
			     	echo 'driver_id='.$driver_id.' is ok'.PHP_EOL;
            	}
            }else {
            	break;
            }
        }
	}

	/**
	*	每个司机都有一个后台每月e币明细,每天7点结算
	*
	*/
	public function actionMonthAll(){
		$first_day_month = date("Y-m-01");
		$today = date("Y-m-d");
		$current_month = date("Y-m");
		$max=0;
        //find all driver
		while (true) {
            $sql = "SELECT id,user,city_id FROM t_driver WHERE id>:max and mark != 3 LIMIT 5000";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":max",$max);
            $driver_list = $command->queryAll();
            if($driver_list){
            	foreach ($driver_list as $driver) {
            		$max = $driver['id'];
            		$driver_id = $driver['user'];
            		$sql="select sum(total_wealth) as total ,
                    sum(five_star_count) as total_five_star_count, 
            		sum(reach_count) as total_reach_count,
            		sum(receive_count) as total_receive_count, 
            		sum(group_count) as total_group_count, 
            		sum(hotline_count) as total_hotline_count, 
            		sum(long_distance_count) as total_long_distance_count, 
            		sum(week_count) as total_week_count,
            		sum(cancel_count) as total_cancel_count, 
            		sum(reject_count) as total_reject_count, 
            		sum(reward_count) as total_reward_count,
                    sum(day_order_count) as total_day_order_count
                    from t_driver_wealth_day_stat 
            		where driver_id=:driver_id and stat_day>=:start_time and stat_day<=:end_time";
					$command = Yii::app()->dbreport->createCommand($sql);
			        $command->bindParam(":start_time", $first_day_month);
			        $command->bindParam(":end_time",$today);
			        $command->bindParam(":driver_id",$driver_id);
			        $wealth = $command->queryRow();
			        //add or update driver month info
			        if(empty($wealth)){
			     		echo 'driver_id='.$driver_id.' not find day wealth'.PHP_EOL;
			        	continue;
			        }
			        //init data
			        $stat_month=date("Y-m");
			        $city_id=$driver['city_id'];
			        $total = $wealth['total'];
			        $param=array('driver_id'=>$driver_id,
			        	'total'=>$total,
			     		'five_star_count'=>$wealth['total_five_star_count'],
			     		'reach_count'=>$wealth['total_reach_count'],
			     		'receive_count'=>$wealth['total_receive_count'],
			     		'group_count'=>$wealth['total_group_count'],
			     		'hotline_count'=>$wealth['total_hotline_count'],
			     		'long_distance_count'=>$wealth['total_long_distance_count'],
			     		'week_count'=>$wealth['total_week_count'],
			     		'cancel_count'=>$wealth['total_cancel_count'],
			     		'reject_count'=>$wealth['total_reject_count'],
			     		'reward_count'=>$wealth['total_reward_count'],
                        'day_order_count'=>$wealth['total_day_order_count'],
			     		'stat_month'=>$stat_month,
			     		'city_id'=>$city_id);
			        $wealthTotal=DriverWealthMonthStat::model()->getWealthMonth($driver_id,$stat_month);
			     	if($wealthTotal){
			   			DriverWealthMonthStat::model()->updateMonthStat($param);
			   			echo 'update driver_id='.$driver_id.' month stat is ok'.PHP_EOL;
			     	}else{
			   			DriverWealthMonthStat::model()->addMonthStat($param);
			 			echo 'save driver_id='.$driver_id.' month stat is ok'.PHP_EOL;
			     	}
            	}
            }else{
            	break;
            }
        }        $firstday = date('Y-m-01'); 
        $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day")); 
        $currentday=date('Y-m-d');
	}

    /**
    *   清空城市财富
    *
    */
    public function actionInit($city_id){
        
        $max=0;
        while (true) {
            $sql = "SELECT id,user FROM t_driver WHERE id>:max and city_id=:city_id LIMIT 1000";
            $command = Yii::app()->db_readonly->createCommand($sql);
            $command->bindParam(":max",$max);
            $command->bindParam(":city_id",$city_id);
            $driver_list = $command->queryAll();
            if ($driver_list) {
                foreach ($driver_list as $driver) {
                    $max = $driver['id'];
                    //更新司机总财富为0,t_driver_ext表
                    $update_sql="update t_driver_ext set total_wealth=0  where driver_id=:driver_id";
                    Yii::app()->db->createCommand($update_sql)->execute(array(
                        ':driver_id' => $driver['user'],
                    ));

                    //清空财富日志相关表
                    $update_sql="delete from t_driver_wealth_log  where driver_id=:driver_id";
                    Yii::app()->dbreport->createCommand($update_sql)->execute(array(
                        ':driver_id' => $driver['user'],
                    ));

                    $update_sql="delete from t_driver_wealth_day_stat  where driver_id=:driver_id";
                    Yii::app()->dbreport->createCommand($update_sql)->execute(array(
                        ':driver_id' => $driver['user'],
                    ));

                    $update_sql="delete from t_driver_wealth_month_stat  where driver_id=:driver_id";
                    Yii::app()->dbreport->createCommand($update_sql)->execute(array(
                        ':driver_id' => $driver['user'],
                    ));
                    echo 'clean driver_id='.$driver['user'].' wealth ok'.PHP_EOL;
                }
            }
        }
        echo 'clean city_id='.$city_id.' t_driver_ext ok'.PHP_EOL;
    }

    /*
    *   每个月1号执行一遍，皇冠限制
    */
    public function actionCrownLimit(){
        $this->actionCityCrownClientLimit();
        $this->actionCityCrownV2Limit();
    }


    /**
    *   司机端每天皇冠兑换数量限制
    *   每个月1号运行一次进行设置,个数通过上个月最后一个周五上线司机数而定
    */
    public function actionCityCrownClientLimit(){
        //获取所有开通城市
        $firstday = date('Y-m-01'); 
        $currentday=date('Y-m-d');
        // if($firstday != $currentday){
        //     echo "today is not first day,exit".PHP_EOL;
        //     EdjLog::info("today is not first day,exit");
        //     return;
        // }
        $last_friday='';
        $i = 1;
        while(date("w",strtotime("$firstday -$i day")) != 5){
            $i = $i+1;
            $last_friday = date("Y-m-d",strtotime("$firstday -$i day"));
        }
        echo 'last_friday='.$last_friday.PHP_EOL;
        EdjLog::info('last_friday='.$last_friday); 

        $num = 5; //皇冠数量,默认最少
        $citys = RCityList::model()->getOpenCityList();
        foreach($citys as $key=>$value) {
            $city_id = $key;
            //调用建平接口
            $count = DailyOnlineReport::model()->getOnlineDriverNumForCityByDay($city_id, $last_friday);
            if($count>4000){
                $num=200;
            }elseif (2500<$count && $count<=4000) {
                $num=100;
            }elseif (1500<$count && $count<=2500) {
                $num=60;
            }elseif (500<$count && $count<=1500) {
                $num=30;
            }elseif (200<$count && $count<=500) {
                $num=15;
            }elseif (50<$count && $count<=200) {
                $num=10;
            }elseif ($count<=50) {
                $num=5;
            }
            //更新redis各个城市皇冠数量
            $res = DriverStatus::model()->setCityMonthCrownLimit($city_id,$num,32*24*3600);
            if($res){
                echo "city_id=".$city_id.',num='.$num.',司机端皇冠每天兑换限额设置成功'.PHP_EOL;
                EdjLog::info("city_id=".$city_id.',num='.$num.',司机端皇冠每天兑换限额设置成功');
            }else{
                echo "city_id=".$city_id.',num='.$num.',司机端皇冠每天兑换限额设置失败'.PHP_EOL;
                EdjLog::info("city_id=".$city_id.',num='.$num.',司机端皇冠每天兑换限额设置失败');
            }
        }

    }


    /**
    *   V2后台司管奖励皇冠数量每月限制
    *   上个月最后一个周五日成单数
    */
    public function actionCityCrownV2Limit(){
         //获取所有开通城市
        $firstday = date('Y-m-01'); 
        $currentday=date('Y-m-d');
        // if($firstday != $currentday){
        //     echo "today is not first day,exit".PHP_EOL;
        //     EdjLog::info("today is not first day,exit");
        //     return;
        // }
        //找到上个月最后一个星期五是几号
        $last_friday='';
        $i = 1;
        while(date("w",strtotime("$firstday -$i day")) != 5){
            $i = $i+1;
            $last_friday = date("Y-m-d 07:00:00",strtotime("$firstday -$i day"));
        }
        echo 'last_friday='.$last_friday.PHP_EOL;
        EdjLog::info('last_friday='.$last_friday); 
        $start_time = strtotime($last_friday);
        $end_time = strtotime($last_friday)+24*3600; //加一天

        $num = 10; //皇冠数量,默认最少
        $crown_city_emoney=0;
        $citys = RCityList::model()->getOpenCityList();
        foreach($citys as $key=>$value) {
            $city_id = $key;
            //调整AB类30,C类10
            $cityType=RCityList::model()->getCityByID($city_id,'city_level');
            $cityType=strtoupper(substr($cityType, 0,1));
            if($cityType == 'A' || $cityType == 'B'){
                $num=30;
            }
            //计算皇冠花费的e数量,分配给司管每月60w
            $crown_city_emoney += $num*1000;

            //更新redis各个城市皇冠数量
            $res = DriverStatus::model()->setV2CityMonthCrownLimit($city_id,$num,32*24*3600);
            if($res){
                echo "city_id=".$city_id.',num='.$num.',v2司管理皇冠每月兑换限额设置成功'.PHP_EOL;
                EdjLog::info("city_id=".$city_id.',num='.$num.',v2司管理皇冠每月兑换限额设置成功');
            }else{
                echo "city_id=".$city_id.',num='.$num.',v2司管理皇冠每月兑换限额设置成功'.PHP_EOL;
                EdjLog::info("city_id=".$city_id.',num='.$num.',v2司管理皇冠每月兑换限额设置成功');
            }
        }
        //存入redis中
        $key=date("Y-m").'_CITY_EMONEY_ALLOCATE';
        DriverStatus::model()->single_set($key,$crown_city_emoney,24*3600*32);
        echo date("Y-m").'分配v2后台司机皇冠用掉e币='.$crown_city_emoney.PHP_EOL;
        EdjLog::info(date("Y-m").'分配v2后台司机皇冠用掉e币='.$crown_city_emoney);
        //奖励的e币清0
        $company_emoney_key=date("Y-m").'_REWARD_CITY_EMONEY';
        DriverStatus::model()->single_set($company_emoney_key,0,24*3600*32);
        
    }

    /**
    *   恶劣天气奖励全国
    *
    */
    public function actionRewardAll($date){
        $citys = RCityList::model()->getOpenCityList();
        foreach ($citys as $key => $value) {
            
            $this->actionRewardExtra($date,$key);
        }
    }


    /**
    *   定时任务，每天执行一次
    *
    */
    public function actionRewardTask(){
        $tasks = WeatherRewardTask::model()->getTask();
        foreach ($tasks as $task) {
            $date = $task['weather_day'];
            $city = $task['city_id'];
            //更新状态
            $attr = array('status'=>1);
            WeatherRewardTask::model()->updateByPk($task['id'], $attr);
            $this->actionRewardExtra($date,$city);
        }
    }


    /**
    *   恶劣天气高峰在线15分钟,额外奖励2e
    *   @param $date='2014-12-08' $city=1
    */
    public function actionRewardExtra($date,$city){
        echo '给城市city='.$city.'所有司机额外奖励高峰在线，时间'.$date.PHP_EOL;
        EdjLog::info('给城市city='.$city.'所有司机额外奖励高峰在线，时间'.$date);
        
        $max = 0;
        $pagesize = 2000;
        $criteria = new CDbCriteria();
        $criteria->select = "id,user,phone,ext_phone";
        $criteria->condition = "id>:max and city_id =:city_id and mark!=3";
        $criteria->limit = $pagesize;
        while(true){
            $criteria->params = array(
                                ':max' => $max,
                                ':city_id' => $city,
                            );
            $drivers = Driver::model()->findAll($criteria);
            if($drivers){
                foreach ($drivers as $driver){
                    $max = ( $max > $driver['id']) ? $max : $driver['id'];
                    $create_time=date("Y-m-d H:m:s");
                    $driver_id=$driver['user'];
                    //根据日期获取那天高峰在线数
                    $day_wealth=DriverWealthDayStat::model()->getWealth($driver_id,$date);
                    //有高峰在线的才处理
                    if($day_wealth && $day_wealth['hotline_count'] > 0){
                        $count = $day_wealth['hotline_count'];
                        $wealth=$count*DriverWealthLog::DRIVER_REWARD_WEALTH;
                        $des=$date."恶劣天气上线奖励";
                        //添加奖励日志
                        DriverWealthLog::model()->addLog($driver['user'],DriverWealthLog::DRIVER_REWARD,
                            $wealth,$city,$create_time,$des);
                        DriverExt::model()->addWealth($driver_id,$wealth);
                        //发送短信，以后改push
                        $msg='尊敬的'.$driver['user'].'师傅，你'.$date.'恶劣天气上线，获得额外系统奖励'.$wealth.'e币，感谢您的辛苦付出';
                        $i_phone = ($driver['ext_phone']) ? $driver['ext_phone'] : $driver['phone'];
                        $res = Sms::SendSMS($i_phone, $msg);
                        echo 'driver='.$driver['user'].',day='.$date.'，恶劣天气奖励'.$wealth.'e币'.PHP_EOL;
                        EdjLog::info('driver='.$driver['user'].',day='.$date.'，恶劣天气奖励'.$wealth.'e币');
                    }else{
                        echo 'driver='.$driver['user'].',day='.$date.'，恶劣天气奖励过滤'.PHP_EOL;
                        EdjLog::info('driver='.$driver['user'].',day='.$date.'，恶劣天气奖励过滤');
                    }
                }
               
            }else{
                break;
            }
            
        }
    }


    /**
    *   调查问卷奖励
    *
    */
    public function actionInvestReward(){
        $handle = @fopen("/opt/phone.txt", "r");
        $des='问卷调查奖励';
        if ($handle) {
            while (!feof($handle)) {
                $phone = trim(fgets($handle, 4096));
                if($phone){ //根据工作手机，找到司机信息，发放奖励，并发短信
                    $create_time=date("Y-m-d H:m:s");
                    $sql = "SELECT user,city_id FROM {{driver}} WHERE phone=:phone";
                    $params[':phone']=$phone;
                    $command = Yii::app ()->db_readonly->createCommand ($sql)->bindValues($params);
                    $driver = $command->queryRow();
                    $wealth=39;
                    DriverExt::model()->addWealth($driver['user'],$wealth);
                    DriverWealthLog::model()->addLog($driver['user'],DriverWealthLog::INVEST_TYPE,
                            $wealth,$driver['city_id'],$create_time,$des);
                    $msg='尊敬的'.$driver['user'].'师傅，感谢您在2015年1月7号参与春节回老家相关情况调研活动，特奖励39e币，请查收。';
                    $res = Sms::SendSMS($phone, $msg);
                        
                    echo 'driver='.$driver['user'].'发放调查问卷奖励成功'.PHP_EOL;

                }
            }
            fclose($handle);
        }

    }

}
