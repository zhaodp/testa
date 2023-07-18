<?php
class queueCommand extends LoggerExtCommand {

	/**
	 * 测试环境是否正常
	 */
	public function actionTest() {
		//测试全部的数据库连接
		try {
			$ret=Yii::app()->db->createCommand('show tables')->queryAll();
			if ($ret) {
				echo 'success db '."\n";
			}
		} catch(Exception $e) {
			echo 'Database:db test fail';
		}
		
		try {
			$ret=Yii::app()->db_readonly->createCommand('show tables')->queryAll();
			if ($ret) {
				echo 'success db_readonly '."\n";
			}
		} catch(Exception $e) {
			echo 'Database:db_readonly test fail';
		}
		
		
		try {
			$ret=Yii::app()->dbreport->createCommand('show tables')->queryAll();
			if ($ret) {
				echo 'success dbreport '."\n";
			}
		} catch(Exception $e) {
			echo 'Database: dbreport test fail';
		}
		
		try {
			$ret=Yii::app()->dbstat->createCommand('show tables')->queryAll();
			if ($ret) {
				echo 'success dbstat '."\n";
			}
		} catch(Exception $e) {
			echo 'Database:dbstat test fail';
		}
		
		try {
			$ret=Yii::app()->dbstat_readonly->createCommand('show tables')->queryAll();
			if ($ret) {
				echo 'success dbstat_readonly '."\n";
			}
		} catch(Exception $e) {
			echo 'Database:dbstat_readonly test fail';
		}
		
		//测试memcache
		

		//测试mongo
		$mo=new Mongo('mongodb://cache01n.edaijia.cn:27017');
		print_r($mo);
		$mo=new Mongo('mongodb://cache02n.edaijia.cn:27017');
		print_r($mo);
		
		//测试redis
		$redis=new Redis();
		$redis->connect('cache01n.edaijia.cn', 6379);
		print_r($redis);
		$redis->close();
		
		$redis=new Redis();
		$redis->connect('cache02n.edaijia.cn', 6379);
		print_r($redis);
		$redis->close();
		
		$redis=new Redis();
		$redis->connect('redis01n.edaijia.cn', 6379);
		print_r($redis);
		$redis->close();
		
		$redis=new Redis();
		$redis->connect('redis02n.edaijia.cn', 6379);
		print_r($redis);
		$redis->close();
		
	}

	/**
	 * 重新刷新司机数据
	 */
	public function actionReload($index='') {
		
	   	//启动一个进程，随机执行1-5分钟。防止长时间不完。
		$timestamp=time();
		$quit_time=rand(3,8)*59;

		$indexs = array();
		if(strlen($index) > 0) {
		    $index = preg_replace('/[^,\d]/', '', $index);
		    $index = preg_replace('/^,|,$/', '', $index);
		    $indexs = split(',', $index);
		}
		
		while (true) {
			if ( ( time() - $timestamp > $quit_time ) ) { 
				echo "\n-----driver reload worker over define process time: runed {$quit_time}s------\n";
				break;
			} else {
		                //清理超时
                                EdjLog::info("DriverStatus clean begin|".microtime());
		                DriverStatus::model()->clean_redishaproxy($indexs);
                                EdjLog::info("DriverStatus clean end|".microtime());

				//同步状态到mongo
                                EdjLog::info("DriverGPS sync begin|".microtime());
				DriverGPS::model()->sync($indexs);
                                EdjLog::info("DriverGPS sync end|".microtime());

				sleep(10);
			}
	    }

	}

    /**
     * 状态唯一列表
     * @param string $index  索引名称（目前按华北，华东，华南，其他）
     */
    public function actionStatusUniqueList($index="HUA_BEI") {
        $prefix = 'LAST_STATUS';
        $sKey = $prefix."_".strtoupper($index);

        //启动时间
        $startTime = time();

        while(true) {
            //不是同一天，退出，等待下次重启，重新记录日志
            if (date('d')!=date('d', $startTime)) exit;

            $lists = DriverPositionService::getInstance()->getPositionUniqueIndex($sKey); //与位置共用，只是sKey不同
            if (!is_array($lists) || count($lists) == 0) {
                /*if (time()-$timestamp>$quit_time) {
                    echo "time out: run\n";
                    break;
                }*/
                $rand = rand(20, 100);
                usleep(intval($rand));
                continue;
            }
            echo '['.date("Y-m-d H:i:s")."] begin process\n";
            foreach($lists as $list) {
                $hashKey = $prefix."_".$list;
                $data = DriverPositionService::getInstance()->getPositionData($hashKey, 'data');
                $params = false;
                if ($data) $params = @json_decode($data, true);

                if (isset($params) && isset($params['driver_id']) && isset($params['status'])) {
                    DriverGPS::model()->status($params['driver_id'], $params['status']);

                    echo 'UPDATE DriverStatus '. $params['driver_id'] .' status:'. $params['status'] ."\n";

                    //添加task队列,回写库
                    $task=array(
                        'method'=>'driver_status2db',
                        'params'=>array(
                            'id'=>$params['driver_id'],
                            'status'=> $params['status'],
                        ),
                    );
                    Queue::model()->putin($task,'default');
                }
            }
            echo '['.date("Y-m-d H:i:s")."] end process\n";
        }
    }

    /**
	 * 位置唯一列表，
	 * @param string $index  索引名称（目前按华北，华东，华南，其他）
	 * @author syang on 2013/8/29
	 */
	public function actionPositionUniqueList($index="HUA_BEI") {
            if(Yii::app()->params['order_architecture_refactor_on']) {
                DriverPositionService::getInstance()->processPositionUniqueList($index);
                return;
            }
		$prefix = 'LAST_POSITION';
		$sKey = $prefix."_".strtoupper($index);
		
		//启动时间
		$startTime = time();
	
		while(true) {
			//不是同一天，退出，等待下次重启，重新记录日志
			if (date('d')!=date('d', $startTime)) exit;
			
			$lists = DriverPositionService::getInstance()->getPositionUniqueIndex($sKey);
			if (!is_array($lists) || count($lists) == 0) {
				/*if (time()-$timestamp>$quit_time) {
					echo "time out: run\n";
					break;
				}*/
				$rand = rand(20, 100);
				usleep(intval($rand));
				continue;
			}
			echo '['.date("Y-m-d H:i:s")."] begin process\n";
			foreach($lists as $list) {
				$hashKey = $prefix."_".$list;
				$data = DriverPositionService::getInstance()->getPositionData($hashKey, 'data');
				$params = false;
				if ($data) $params = @json_decode($data, true);

				if (isset($params) && isset($params['gps'])) {
					//更新mongo
					DriverGPS::model()->update($params['id'], $params['driver_id'], array(
						'lng'=>$params['gps']['baidu_lng'],
						'lat'=>$params['gps']['baidu_lat']
					),$params['service_type'],$params['app_ver']);
					echo 'UPDATE DriverGPS '. $params['driver_id'] .' LNG: '. $params['gps']['baidu_lng'] .' LAT: '. $params['gps']['baidu_lat'] . ' SERVICE_TYPE: ' .$params['service_type']. ' APP_VER: ' .$params['app_ver'] ."\n";
					
					//添加task队列,回写库
					$task=array(
							'method'=>'driver_position2db',
							'params'=>array(
										'driver_id'=>$params['driver_id'],
										'gps'=>$params['gps'],
										'log_time'=>date("YmdHis", $params['gps_timestamp']),
									),
					);
					Queue::model()->putin($task,'default');
				}
			}
			echo '['.date("Y-m-d H:i:s")."] end process\n";
		}
	}

    /**
     * 自动派单(改用worker,用之前要将dispatchNew停掉)
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-08-29
     * @editor AndyCong<congming@edaijia-staff.cn>
     *  -------2014-01-11
     *  -------$p = 1,2,3,4,5       
     */
    public function actionDispatchWorker($p = ''){
    	//启动一个进程，随机执行1-5分钟。防止长时间不完。
		$timestamp=time();
		$quit_time=rand(1,5)*59;
    	
		$i = 1;
		
		while (true) {
			if ( $i>100 || (time() - $timestamp > $quit_time ) ) {
				echo "-----Auto Dispatch Order over max times {$i} or over define process time: runed {$quit_time}s------\n";
				break;
			} else {
		        $message="%s|%s|%s|%s|%s\n";
		        $error_message="%s|%s|%s|%s|%s|%s\n";
		        $dispatch_time=date(Yii::app()->params['formatDateTime'], time());
		        $time=date("Y-m-d H:i:s", time()+Yii::app()->params['dispatch']['time_interval']);
		        //echo "booking_time:".$time."\n";
		        $orders = DispatchOrderQueue::model()->getWaitFormQueue($time , 200 , $p);
		        if(!empty($orders)){
		            foreach($orders as $order){
		                $drivers = DispatchDriver::model()->getOrderDrivers($order['queue_id'],$order['order_id']);
		                if(empty($drivers) || (isset($drivers['flag'])&& $drivers['flag']== false)|| empty($drivers['drivers']) ){
		                    //输出错误信息。。。
		                    $order_locked = isset($drivers['order_locked']) ? $drivers['order_locked'] : false;
		                    if (!$order_locked) {
		                    	echo sprintf($error_message, $dispatch_time, "queue_id:".$order['queue_id'], "order_id:".$order['order_id'], $drivers['msg'], $order['booking_time'], $order['address']);
		                    }
		                    continue;
		                }
		                foreach($drivers['drivers'] as $driver){
		                    echo sprintf($message, $dispatch_time, $driver['driver_id'], "order_id:".$order['order_id'], $order['booking_time'], $order['address']);
		                    $task=array(
		                        'method'=>'dispatch_driver_new',
		                        'params'=>array(
		                            'queue_id'=>$order['queue_id'],
		                            'order_id'=>$order['order_id'],
		                            'driver_id'=>$driver['driver_id'],
		                        )
		                    );
		                    Queue::model()->putin($task,'dispatch');  //队列需要改一下
		                }
		            }
		        }
		        sleep(2);
		        $i++;
			}
	    }
    }
    
    /**
     * 自动派单(客户端一键下单)
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-08-29
     */
    public function actionDispatchCustomerBooking(){
    	//启动一个进程，随机执行1-5分钟。防止长时间不完。
		$timestamp=time();
		$quit_time=rand(1,5)*59;
    	
		$i=1;
		
		while (true) {
			if ( (time() - $timestamp > $quit_time ) ) { //$i>20 || 
				echo "-----Auto Dispatch Order over max times {$i} or over define process time: runed {$quit_time}s------\n";
				break;
			} else {
		        $message="%s|%s|%s|%s|%s\n";
		        $error_message="%s|%s|%s|%s|%s|%s\n";
		        $dispatch_time=date(Yii::app()->params['formatDateTime'], time());
		        $time=date("Y-m-d H:i:s", time()+Yii::app()->params['dispatch']['time_interval']);
		        $orders = DispatchOrderQueue::model()->getWaitFormQueueByChannel($time);
		        if(!empty($orders)){
		            foreach($orders as $order){	

		                if(isset($order['phone'])
		                   && isset($order['city_id'])
		                   && CustomerApiOrder::model()->isBlockCustomerByConfig($order)) {
		                   EdjLog:info('queueCommand.actionDispatchCustomerBooking do not dispatch order due to customer blocked, order info:' . json_encode($order), 'console');
		                   continue;
		                }
		                
		                $drivers = DispatchDriver::model()->getOrderDriversNewRule($order['queue_id'],$order['order_id']);
		                if(empty($drivers) || (isset($drivers['flag'])&& $drivers['flag']== false)|| empty($drivers['drivers']) ){
		                    //输出错误信息。。。
		                    $order_locked = isset($drivers['order_locked']) ? $drivers['order_locked'] : false;
		                    if (!$order_locked) {
		                    	echo sprintf($error_message, $dispatch_time, "queue_id:".$order['queue_id'], "order_id:".$order['order_id'], $drivers['msg'], $order['booking_time'], $order['address']);
                                
		                    	EdjLog::info('Can not find any driver to dispath error info:'. $error_message);
		                    }
		                    continue;
		                }
		                foreach($drivers['drivers'] as $driver){
		                    echo sprintf($message, $dispatch_time, $driver['driver_id'], "order_id:".$order['order_id'], $order['booking_time'], $order['address']);
                            
		                    EdjLog::info('The selected driver info as:' . $message);

		                    $task=array(
		                        'method'=>'dispatch_driver_new',
		                        'params'=>array(
		                            'queue_id'=>$order['queue_id'],
		                            'order_id'=>$order['order_id'],
		                            'driver_id'=>$driver['driver_id'],
		                        )
		                    );
		                    Queue::model()->putin($task,'dispatch');  //队列需要改一下
		                }
		            }
		        }
		        sleep(2);
		        $i++;
			}
	    }
    }

    /**
     * 选司机下单推送重试
     * @author wangjian<wangjian@edaijia.cn>
     * @version 2014-06-19
     * @params limit 数据库中读取最大行数 
     * @params sleep 循环间隔时间
     * @params city 用于灰度发布的城市司机id前缀,为空时正式上线
     */
    public function actionSinglePushRetry($limit=200,$sleep=2,$city='',$start=0,$range=0) {
        if((empty(Yii::app()->params['SingleRetryStart']) && $start == 0)
            || (empty(Yii::app()->params['SingleRetryRange']) && $range == 0)) {
            return false;
        }

	//优先使用命令行参数
	$retry_start = $start == 0 ? Yii::app()->params['SingleRetryStart'] : $start;
        $retry_range = $range == 0 ? Yii::app()->params['SingleRetryRange'] : $range;

        //启动一个进程，随机执行1-5分钟。防止长时间不完。
        $start_time = time();
        $quit_time = rand(5,10)*59;

        $gpid = Common::get_gpid();
        EdjLog::monitor("WORKER BEGIN gpid:$gpid qname:SinglePushRetry");
    	
	$i = 0;
        while(true) {
            $now = time();
            if(($now - $start_time > $quit_time)) { 
                EdjLog::monitor("WORKER END gpid:$gpid qname:SinglePushRetry loop:$i");
                break;
            }

            $where = array(
                ':channel' => CustomerApiOrder::QUEUE_CHANNEL_SINGLE_DRIVER,
                ':status'  => Order::ORDER_READY,
		':driver_id'  => Push::DEFAULT_DRIVER_INFO,
                ':begin_time' => $now - $retry_start - $retry_range,
                ':end_time'   => $now - $retry_start,
            );

            $orders = Order::getDbReadonlyConnection()->createCommand()
                ->select("order_id, order_number, call_time")
                ->from('t_order')
                ->where('driver_id =:driver_id and channel=:channel and status=:status and call_time between :begin_time and :end_time',
                    $where)
                ->limit($limit)
                ->queryAll();

            if(!empty($orders)){
	        $j = 0;
                foreach($orders as $order){
                    DalOrder::model()->single_smspush_retry($order, $city);
		    if($j % 20 == 0) {
                        EdjLog::monitor("WORKER HEART gpid:$gpid qname:SinglePushRetry loop:$i-$j");
		    }
		    $j ++;
                }
            }

	    if($i % 10 == 0) {
                EdjLog::monitor("WORKER HEART gpid:$gpid qname:SinglePushRetry loop:$i start:$retry_start range:$retry_range");
	    }
	    $i++;
	    sleep($sleep);
        }
    }

    /**
	 * 
	 * 统一封装入口，执行队列任务，需要传入要执行的队列类型。任务对应的处理过程在QueueProcess定义
	 * 队列随机执行10-15分钟，超时退出，等corn重启
	 * @author sunhongjing 2013-07-09
	 * 
	 * @param string $qname 队列名称
	 * @return null
	 */
	public function actionWorker($qname='default') {
        $gpid = Common::get_gpid();

		$qname = strtolower($qname);

        EdjLog::monitor("WORKER BEGIN gpid:$gpid qname:$qname");

		// check qname
	        $base_qname = QNameManagerService::model()->get_base_qname($qname);
		if(empty($base_qname)){
            EdjLog::monitor("WORKER EXECPTION gpid:$gpid qname:$qname");
			return;
		}

		//启动一个进程，随机执行3-10分钟。防止长时间不完。
		$timestamp=time();
		$quit_time=rand(5, 20)*59;

		$i=1;
        try {

		while(true) {
			if (  time() - $timestamp > $quit_time  ) {
				//echo "\n"."the worker over max times {$i} or over define process time: runed {$quit_time}s\n";
                //EdjLog::info("over max times {$i} worker: $qname");
                EdjLog::monitor("WORKER END gpid:$gpid qname:$qname loop:$i");
				break;
			}else{
                $start_time = Common::get_current_time();

				$task=Queue::model()->getit($qname);
                
                $end_get_time = Common::get_current_time();
                $redis_time = sprintf("%0.4f", ($end_get_time - $start_time)*1000);



				if ($task) {

					$this->queue_run($task);

                    $end_run_time = Common::get_current_time();
                    $run_time = sprintf("%0.4f", ($end_run_time - $end_get_time)*1000);
                    
                    if(isset($task['method'])) {
                        $method = $task['method'];
                    } else {
                        $method = '';
                    }
                    EdjLog::monitor("WORKER HEART DO $method gpid:$gpid qname:$qname loop:$i redis_tm:$redis_time run_tm:$run_time") ;
				}else{		
					sleep(1);
                    //EdjLog::monitor("WORKER HEART EMPTY gpid:$gpid qname:$qname loop:$i redis_tm:$redis_time");
					$i++;
				}
			}
		}

        } catch (Exception $e) {
            $errmsg = $e->getMessage();
            EdjLog::warning("execption worker: $qname err: $errmsg");
            EdjLog::monitor("WORKER EXECPTION gpid:$gpid qname:$qname err:$errmsg");
        }


	}
	

	/**
	 * 
	 * 执行队列任务，任务对应的处理过程在QueueProcess定义
	 * 队列随机执行10-15分钟，超时退出，等corn重启
	 */
	public function actionRun() {
		$timestamp=time();
		$quit_time=rand(10, 15)*60;
		while(true) {
			$task=Queue::model()->task();
			if ($task) {
				$this->queue_run($task);
			} else {
				if (time()-$timestamp>$quit_time) {
					echo "\n"."time out: run\n";
					break;
				}
				sleep(2);
			}
		}
	}

	/**
	 * 
	 * 日志队列中的日志dump到数据库
	 */
	public function actionDumpLog() {
		$timestamp=time();
		$quit_time=rand(10, 15)*60;
		
		while(true) {
			$task=Queue::model()->dumplog();
			if ($task) {
				$this->queue_run($task);
			} else {
				if (time()-$timestamp>$quit_time) {
					echo "\n"."time out:DumpLog\n";
					break;
				}
				sleep(2);
			}
		}
	}

	/**
	 * 司机定位数据队列处理
	 */
	public function actionPosition() {
		$timestamp=time();
		$quit_time=rand(10, 15)*60;
		while(true) {
			$task=Queue::model()->position();
			if ($task) {
				$this->queue_run($task);
			} else {
				if (time()-$timestamp>$quit_time) {
					echo "time out: position\n";
					break;
				}
				sleep(2);
			}
		}
	}

	/**
	 * 心跳处理队列
	 */
	public function actionHeartbeat() {
		$timestamp=time();
		$quit_time=rand(10, 15)*60;
		
		while(true) {
			$task=Queue::model()->heartbeat();
			if ($task) {
				$this->queue_run($task);
			} else {
				if (time()-$timestamp>$quit_time) {
					echo "time out:heartbeat\n";
					break;
				}
				sleep(2);
			}
		}
	}

	private function queue_run($task) {
            if(Yii::app()->params['order_architecture_refactor_on']) {
                Queue::model()->processTask($task);
                return;
            }

	    if(!isset($task['method'], $task['params'])) {
	        $task_content = json_encode($task);
	        EdjLog::error("can not run task due to no 'method' or 'params' specified, task is $task_content");
	        return;
	    }
	    
		$method=$task['method'];
		$params=$task['params'];
		$class = isset($task['class']) ? $task['class'] : "QueueProcess";
		//echo "\n".$method.':'.json_encode($params);
        EdjLog::info("REDIS_QUEUE_OUT MODEL:$class METHOD:$method PARAMS:".json_encode($params));
		try {
            //throw new Exception("Value must be 1 or below");
			$queue_process=new $class();
            // check this method is exist, if not throw ReflectionException
            new ReflectionMethod($queue_process, $method);

			call_user_func_array(array(
					$queue_process,
					$method
			), array(
					$params
			));
		} catch(Exception $e) {
			//echo "error:$method\n";
			//print_r($e);
            $errmsg = $e->getMessage();
            EdjLog::warning("execption queue_run method:$method err: $errmsg");
		}
	}
	
	/**
	 * 定时取消派单失败的订单
	 * @author AndyCong<congming@edaijia-staff.cn>
	 * @version 2013-11-04
	 */
	public function actionCancelQueue() {
		echo Common::jobBegin('Cancel No Dispatch Order');
		DispatchOrderQueue::model()->dispatchFailedProcess();
		echo Common::jobEnd('Cancel No Dispatch Order');
	}

}
