<?php
/**
 * 推送管理（自动二期优化）
 * @author AndyCong<congming@edaijia.cn>
 * @version 2013-07-07
 */
class Push {
	private static $_models;
	
	 // 派单人员
     const QUEUE_DISPATCH_AGENT_AUTO = '自动派单';

     // 推单类型
     const DISPATCH_TYPE_ORDER_DETAIL = 'order_detail';         // 推送订单详情
     const DISPATCH_TYPE_HANDLE = 'handle';                     // 手工推送订单

     //默认司机信息
     const DEFAULT_DRIVER_INFO = 'BJ00000';
     
     //默认时间格式
     const DEFAULT_TIME_FORMAT = '0000-00-00 00:00:00';
     
     const QUEUE_AGENT_AUDIO_BOOKING = '音频预约';
     
     const AUDIO_COMMENTS = '滴滴模式';
     
     const DEFAULT_GPS_TYPE = 'google';
     
     const ORDER_RECEIVE_TIMEOUT = 120;
	/**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Order the static model class
     */
    public static function model($className=__CLASS__) {
        $model=null;
        if (isset(self::$_models[$className]))
            $model=self::$_models[$className];
        else {
            $model=self::$_models[$className]=new $className(null);
        }
        return $model;
    }
	
    /**
     * 获取要派订单
     * @param int $queue_id
     * @return array()
     */
    public function getDispatchOrder($queue_id = 0 , $params = array()) {
	$data = array();
	if ( empty($queue_id) ) {
		return $data;
	}
	//验证queue信息
	$queue = OrderQueue::model()->findByPk($queue_id);
	if ($queue) {
	    //验证订单生成没有

	    $order_count = OrderQueueMap::model()->count('queue_id = :queue_id' , array(':queue_id' => $queue_id));
	    if (0 == $order_count) {
		// Lock the queue for generate order
		$lock = QueueApiOrder::model()->queue_gen_order_lock($queue_id);
		if($lock == false) {
		    // Lock failed or the order is being generated
		    return $data;
		}
		//生成订单 生成OrderQueueMap
		$tmp = array();
		$order_ids = array();

		// Check the remote order fee
                $fees = QueueDispatchOrder::model()->getQueueRemoteOrderFee(
			$queue_id);
		$fees_order_arr = array();
                $fees_arr = empty($fees) ? array() : explode(',' , $fees);

		$is_use_bonus = isset($params['is_use_bonus']) ? intval($params['is_use_bonus']) : 1;  //是否使用优惠券
                
                //查询是否有选定的优惠券
                $bonus_sn = QueueDispatchOrder::model()->getQueueBonus($queue_id);
			
		$bonus_order_arr = array();
                $bonus_arr = empty($bonus_sn) ? array() : explode(',' , $bonus_sn);

        // 判断此订单是否需要执行恶劣天气加价的逻辑——曾坤 2015/3/26
        $bad_weather_surcharge = WeatherRaisePrice::model()->getCityAddPrice(
            $queue->city_id,
            date('Y-m-d H:i:s')
        );

		for ($i = 0 ; $i < $queue->number ; $i++) {
		    $dispatch_number = $i ? 0 : 1;
            // 在创建订单的时候需要保存订单的支付属性，现金支付
            // 的属性是在params的cash_only字段里保存着的，所以
            // 这里除了queue之外单独传了一个参数——曾坤 2015/3/25
            $order_id = $this->_genOrder($queue);
				
		    //系统开始派单
		    EdjLog::info($queue_id.'|'.$order_id.'|102开始派单' , 'console');

                    $map_id = $this->_genMap($queue_id , $order_id , $dispatch_number);
		    $tmp[] = array('queue_id' => $queue_id , 'order_id' => $order_id , 'map_id' => $map_id);
		    $order_ids[] = $order_id;

		    // Set fees for the order
		    $fees_order_arr[$order_id] = '';
		    if(!empty($fees_arr) && isset($fees_arr[$i])) {
			$fees_order_arr[$order_id] = $fees_arr[$i];
		    }

		    $bonus_order_arr[$order_id] = '';

		    if($is_use_bonus) {
			if(!empty($bonus_arr)) { //客户端选择
			    $bonus_code = isset($bonus_arr[$i]) ? $bonus_arr[$i] : '';
			    if(!empty($bonus_code)) {
				$bonus_order_arr[$order_id] = $bonus_code;
				EdjLog::info($queue_id.'|'.$order_id.'|bonus_code:'.$bonus_code.'|102开始派单' , 'console');
				BonusLibrary::model()->BonusOccupancyBySn($queue->phone , $bonus_code , $order_id, 0);
			    }
			} else {
			    EdjLog::info($queue_id.'|'.$order_id.'|没有优惠券|102开始派单' , 'console');
                $bonus_use_limit = isset($params['bonus_use_limit']) ? intval($params['bonus_use_limit']) : 0;
                $app_ver = isset($params['app_ver']) ? intval($params['app_ver']) : 0;
                            //添加优惠券绑定
                            BonusLibrary::model()->BonusOccupancy($queue->phone , $order_id, $queue->type,0,$bonus_use_limit,$app_ver);
                            //添加优惠券绑定
                        }
                    }

		    // For order with fee, generate order ext here
		    // Here must always be the first place where OrderExt is generated
		    // And any other place before here trying to generate OrderExt,
		    // should change the code below
			//FIX 所有记录均创建orderExt 记录	2014-12-16
			$order_ext = new OrderExt();
			$order_ext->order_id = $order_id;
			$order_ext->cash_only = isset($params['cash_only']) ? intval($params['cash_only']) : 0;

		    if(isset($fees_order_arr[$order_id])) {
				$order_ext->fee = $fees_order_arr[$order_id];
				$order_ext->use_fee = 1;
		    }
            if (!empty($bad_weather_surcharge) && isset($bad_weather_surcharge['id'])) {
                $order_ext->bad_weather_surcharge = $bad_weather_surcharge['id'];
            }
		    $order_ext->type = Order::createOrderType($queue->type, $queue->channel, isset($params['from']) ? $params['from'] : '');
		    
		    
			if(!$order_ext->save()) {
			    EdjLog::info('Update order_ext error|Fee:'.$order_ext->fee.
				    '|'.json_encode($order_ext->getErrors()));
			}
		}

			
		//将order信息加入缓存
		$params = $this->_getCacheParamsByQueueObj($queue , $order_ids,
            $bonus_order_arr, $fees_order_arr,
            isset($params['cash_only']) ? intval($params['cash_only']) : 0
        );
		if (!empty($params)) {
            // 把这个异步更新Redis的动作改成同步的，保证Redis里的order_id在此时
            // 和MySQL里的数据一致。如果异步更新Redis的话，很可能Redis的
            // order_id还没有更新，就已经有其它的代码在读取Redis了——曾坤 2015/3/23 
            CustomerApiOrder::model()->insert_orders_redis($params);
            /*
		    $task = array(
			    'method' => 'insert_orders_redis',
			    'params' => $params
			    );
		    Queue::model()->putin($task , 'orderstate');
            */
		}
		//将order信息加入缓存 END
				
		//如果多人 只返回第一个（推送组长 组长接收再推送组员）
		$data[] = $tmp[0];
		unset($tmp);
		QueueApiOrder::model()->queue_gen_order_unlock($queue_id);
	    } else {
		//获取未派订单
		$data = $this->_getNoDispatchOrder($queue_id , $queue->number , $queue->channel);
	    }
	}
	return $data;
    }
    
	/**
         * @modified qiujianping@edaijia-inc.cn 2014-03-26
         * 	Add gen order process to t_order_process
         *
	 * 推单---直接推送详情
	 * @param int $queue_id
	 * @param string $driver_id
	 * @return boolean
	 */
	public function PushOrder($queue_id = 0 , $order_id = 0 , $driver_id = '') {
		
    	if ( empty($queue_id) || empty($order_id) || empty($driver_id) ) {
    		return false;
    	}
    	
    	//验证数据有效性
//    	$queue = OrderQueue::model()->find('id =:id and flag = :flag' , array(':id' => $queue_id , ':flag' => OrderQueue::QUEUE_WAIT_COMFIRM));
    	//走从库 By AndyCong 2013-12-27
    	// Yii::app()->db_readonly change into OrderQueue::getDbReadonlyConnection()
        $queue =OrderQueue::getDbReadonlyConnection()->createCommand()
                     ->select('*')
                     ->from('t_order_queue')
                     ->where('id =:id and flag = :flag' , array(':id' => $queue_id , ':flag' => OrderQueue::QUEUE_WAIT_COMFIRM))
                     ->queryRow();
                     
		if ( !empty($queue) ) {
			//改为直接推送详情
			$msg = $this->setPushOrderMsg($queue_id , $driver_id , $order_id);
			if (0 == $msg['code']) {
				$msg['msg']['order_id'] = $order_id;
				$channel = isset($queue['channel']) ? $queue['channel'] : '';
				$data = array(
		            'type' => GetuiPush::TYPE_ORDER_DETAIL,
		            'content' => $msg['msg'],
			    'level' => GetuiPush::LEVEL_HIGN,  //级别
		            'driver_id' => $driver_id,
		            'queue_id' => $queue_id,
		            'created' => date('Y-m-d H:i:s' , time()),
			    'channel' => $channel,
		        );
		        
		        //发送失败记录请求次数
		        $ret = $this->organizeMessagePush($data);
//				$ret = $this->_pushDetailMsg($queue_id , $driver_id , $msg['msg'] , $channel);

                        // After the push, we generate order process of SYS_DISPATCH
	                $process_params = array();
	                $process_params['queue_id'] = $queue_id;
	                $process_params['order_id'] = $order_id;
	                $process_params['state'] = OrderProcess::PROCESS_SYS_DISPATCH;
	                $process_params['driver_id'] = $driver_id;
	                $process_params['created'] = date("Y-m-d H:i:s", time());

	                // We just ignore if the process is success or failed
	                OrderProcess::model()->genNewOrderProcess($process_params);
				return $ret;
			}
		}
		return false;
    }
    
    /**
	 * 推音频订单---直接推送详情
	 * @param int $queue_id
	 * @param string $driver_id
	 * @return boolean
	 */
	public function PushAudioOrder($queue_id = 0 , $order_id = 0 , $driver_id = '' , $url = '') {
    	if (0 == $queue_id || 0 == $order_id || empty($driver_id) || empty($url)) {
    		return false;
    	}
    	
    	//验证数据有效性
    	$queue = OrderQueue::model()->find('id =:id and flag = :flag' , array(':id' => $queue_id , ':flag' => OrderQueue::QUEUE_WAIT_COMFIRM));
    	$condition = array(':queue_id' => $queue_id , ':order_id' => $order_id , ':flag' => OrderQueueMap::MAP_NOT_CONFIRM);
    	$map = OrderQueueMap::model()->find('queue_id = :queue_id and order_id = :order_id and flag = :flag' , $condition);
		if ($queue && $map) {
			//改为直接推送详情
			$msg = $this->setPushOrderMsg($queue_id , $driver_id , $order_id);
			
			if (0 == $msg['code']) {
				$msg['msg']['order_id'] = $order_id;
				$msg['msg']['url'] = $url;
				$data = array(
		            'type' => GetuiPush::TYPE_ORDER_AUDIO_DETAIL,
		            'content' => $msg['msg'],
		            'level' => GetuiPush::LEVEL_HIGN,  //级别
		            'driver_id' => $driver_id,
		            'queue_id' => $queue_id,
		            'created' => date('Y-m-d H:i:s' , time()),
		        );
		        $ret = $this->organizeMessagePush($data);
				return $ret;
			}
		}
		return false;
    }
    
    /**
     * 接单处理
     * @param array $params
     * @return boolean
     */
    public function OrderReceiveOperate($params) {
    	$validate = $this->_validateParams($params);
    	if ($validate) {
    		//系统开始派单
			EdjLog::info($params['queue_id'].'|'.$params['order_id'].'|301司机接单|'.$params['driver_id'].'|begin' , 'console');
			
    		//修改司机为接单状态 (放入队列)
    		$driver_accept = $this->_updateDriverAcceptStatus($params , AutoDispatchLog::TYPE_DRIVER_ACCEPT);

	    	//读从库 BY AndyCong 2013-12-27
    		// Yii::app()->db_readonly change into OrderQueue::getDbReadonlyConnection()
	    	$queue =OrderQueue::getDbReadonlyConnection()->createCommand()
                     ->select('*')
                     ->from('t_order_queue')
                     ->where('id =:id and flag = :flag' , array(':id' => $params['queue_id'] , ':flag' => OrderQueue::QUEUE_WAIT_COMFIRM))
                     ->queryRow();
	    	
	    	if (!empty($queue)) {
	    		//记录接单  (放入队列)
	    	    $position = $this->_setReceiveOrderPosition($params);
	    	    //验证司机是否可以接此单
	    	    $validate_driver_accept = $this->_validateDriverAccept($params['queue_id'] , $params['driver_id']);
	    	    if (!$validate_driver_accept) {
	    	        return false;
	    	    }
	    	        
		    	//修改订单信息
		    	$order = $this->_updateOrder($params['order_id'] , $params['driver_id']);
		    	if (!$order) {
		    		
		    		//记录log
	    		    EdjLog::warning($params['queue_id'].'|'.$params['order_id'].'|301司机接单|'.$params['driver_id'].'|修改订单失败' , 'console');
	    		    
		    	    return false;
		    	}
		    	    
		    	//修改map信息
		    	$map = $this->_updateMap($params['queue_id'] , $params['order_id'] , $params['driver_id']);
		    	if (!$map) {
		    		
		    		//记录log
	    		    EdjLog::warning($params['queue_id'].'|'.$params['order_id'].'|301司机接单|'.$params['driver_id'].'|修改Map失败' , 'console');
	    		    
		    	    return false;
		    	}

            // 至此，司机已成功接单。判断此订单是否需要提示恶劣天气加价的短信
            // 但是VIP客户是不能发送短信的——曾坤 2015/3/25
            $orderext = OrderExt::model()->findByPk($params['order_id']);
            if (!empty($orderext)
                && isset($orderext['bad_weather_surcharge'])
                && intval($orderext['bad_weather_surcharge']) > 0
            ) {
                static $ENABLED = 0;
                if (empty((VipPhone::model()->getVipByphone($queue['phone'])))) {
                    $bad_weather_surcharge = WeatherRaisePrice::model()->findByPk($orderext['bad_weather_surcharge']);
                    if (!empty($bad_weather_surcharge) && $bad_weather_surcharge['status'] == $ENABLED) {
                        CustomerApiOrder::model()->sendBadWeatherSmsNotify(
                            $queue['phone'],
                            $bad_weather_surcharge['offer_message']
                        );
                    }
                }
            }

		    	
		    	//记录状态机
	                $process_params = array();
	                $process_params['queue_id'] = $params['queue_id'];
	                $process_params['order_id'] = $params['order_id'];
	                $process_params['state'] = OrderProcess::PROCESS_ACCEPT;
	                $process_params['driver_id'] = $params['driver_id'];
	                $process_params['created'] = date("Y-m-d H:i:s", time());

	                // We just ignore if the process is success or failed
	                OrderProcess::model()->genNewOrderProcess($process_params);

		    	//修改司机为接单成功状态 （放入队列）
		    	$driver_success = $this->_updateDriverAcceptStatus($params , AutoDispatchLog::TYPE_DRIVER_ACCEPT_SUCCESS);  
		    	  
		    	//修改queue记录 --- 多人预约 组后一个组员要推送组员给组长
		    	$result = $this->_updateOrderQueueNew($queue , $params['driver_id']);
		    	if (!$result) {
		    		//记录log
	    		    EdjLog::warning($params['queue_id'].'|'.$params['order_id'].'|301司机接单|'.$params['driver_id'].'|修改queue失败' , 'console');
	    		    
		    	}

			EdjLog::info($queue['callid'].'|'.$params['order_id'].'|'.$queue['phone'].'|'.$params['driver_id'].'|Push accept msg|' , 'console');
			// Send accept message
                        $driver = DriverStatus::model()->get($params['driver_id']);
                        if($driver){
			    ClientPush::model()->pushMsgForDriverAcceptOrder($queue['phone'], 
				    $params['driver_id'], $queue['callid'], $params['order_id'], $driver->info['name']);

			    //400订单给用户发送短信
			    if($queue['channel'] == '' && $queue['dispatch_number'] == 0) {
			        $client_message = MessageText::getFormatContent(
                                    MessageText::CUSTOMER_CALLCENTER_DISPATCH,
                                    strtoupper($driver->driver_id),
				    empty($driver->info['name']) ? '' : $driver->info['name'],
				    $driver->phone
				);
				Sms::SendSMS($queue['phone'], $client_message);
			        EdjLog::info('400OrderSendSms'.$queue['phone'].'|'.$driver->driver_id.'|'.$driver->phone);
			    }
			}
		    	
		    	/**
                 * 第三方合作，司机接单后操作
                 * author : zhangtingyi 2013-11-04
                 */
                $order_channel = Order::model()->getOrderChannel($params['order_id']);
                if ($order_channel) {
                    $partner = PartnerFactory::factory($order_channel);
                    if ($partner instanceof AbstractPartner) {
                        if (method_exists($partner,'afterOrderSaveHandler')) {
                            $partner->afterOrderSaveHandler($params['order_id']);
                        }
                    }
                }

                $orderId = $params['order_id'];
                EdjLog::info("400 Invoke create order to queue,order id:$orderId");
                ROrderToKafka::model()->createOrderAddQueue($orderId);

                OrderStatusChangedPublisher::addQueue(array(
                    'bookingId' => $queue['callid'],
                    'orderId'   => $params['order_id'],
                    'status'    => OrderProcess::PROCESS_ACCEPT,
                    'message'   => '司机已接单',
                    'driverId'  => $params['driver_id'],
                    'phone'     => $queue['phone']
                ));
                
                
                //记录log
                EdjLog::info($params['queue_id'].'|'.$params['order_id'].'|301司机接单|'.$params['driver_id'].'|接单成功|end' , 'console');
		    	return $result;
	    	} else {
	    		
	    		//记录log
	    		EdjLog::warning($params['queue_id'].'|'.$params['order_id'].'|301司机接单|'.$params['driver_id'].'|验证queue失败|end' , 'console');
	    		
	    		return false;
	    	}
    	} else {
    		
    		//记录log
    		EdjLog::warning($params['queue_id'].'|'.$params['order_id'].'|301司机接单|'.$params['driver_id'].'|参数验证失败|end' , 'console');
    		
    		return false;
    	}
    }

    /**
     * @author qiujianping@edaijia-staff.cn 2014-04-08
     *   Redo order receive operations when we found 
     * that the driver is not bind with the order.
     * @param $queue_id
     * @param $order_id
     * @param $driver_id
     *
     * @return boolean
     */
    public function redoOrderReceive($queue_id, $order_id, $driver_id) {
      if(!$queue_id || !$order_id || !$driver_id) {
	EdjLog::info($queue_id.'|'.$order_id.'|'.$driver_id.'|Redo order receive failed, invalid params');
	return false;
      }

      //修改订单信息
      $order = $this->_updateOrder($order_id , $driver_id);
      if (!$order) {
	EdjLog::info($queue_id.'|'.$order_id.'|'.'|'.$driver_id.
	    '|Redo order receive failed |Update Order failed');
	return false;
      }

      //修改map信息
      $this->updateOrderQueueMap(OrderQueueMap::MAP_CONFIRM, date('Y-m-d H:i:s' , time()), $driver_id, $queue_id, $order_id);
      
      EdjLog::info($queue_id.'|'.$order_id.'|'.$driver_id.'|Redo order receive finished');

      return true;
    }

    /**
     * @author qiujianping@edaijia-staff.cn 2014-06-19
     *   Redo order receive operations when we dispatch the
     * order but found the driver is in memecached.
     * @param $queue_id
     * @param $order_id
     * @param $driver_id
     *
     * @return boolean
     */
    public function redoOrderReceiveInDispatch($queue_id, 
	    $order_id, $driver_id, $number = 1, $comments = '') {
      if(!$queue_id || !$order_id || !$driver_id) {
	EdjLog::info($queue_id.'|'.$order_id.'|'.$driver_id.
		'|Redo order receive failed, invalid params');
	return false;
      }

      //先检查一下接单时数据库是否更新失败(driver_id == BJ00000)
      $check_order = Order::model()->getOrderById($order_id);
      if($check_order && isset($check_order['driver_id'])
          && $check_order['driver_id'] != Push::DEFAULT_DRIVER_INFO) {
          EdjLog::info($queue_id.'|'.$order_id.'|'.'|'.$driver_id.
            '|Redo order receive failed|Order is updated before');

          return false;
      }

      //修改订单信息
      $order = $this->_updateOrder($order_id , $driver_id);
      if (!$order) {
	EdjLog::info($queue_id.'|'.$order_id.'|'.'|'.$driver_id.
	    '|Redo order receive failed |Update Order failed');
	return false;
      }

      //修改map信息
      $this->updateOrderQueueMap(OrderQueueMap::MAP_CONFIRM, date('Y-m-d H:i:s' , time()), $driver_id, $queue_id, $order_id);

      // Update order queue info
      $queue_data_arr = array('id' => $queue_id,
	      'number' => $number,
	      'comments' => $comments);
      $ret = $this->_updateOrderQueueNew($queue_data_arr , $driver_id);
      if(!$ret){
	  EdjLog::info($queue_id.'|'.$order_id.'|'.$driver_id.
		  '|Redo order receive failed | Update order queue failed');
	  $ret = false;
      } else {
	  EdjLog::info($queue_id.'|'.$order_id.'|'.$driver_id.
		  '|Redo order receive finished');
	  $ret = true;
      }
      return $ret;
    }
    
    /**
     * 音频文件接单处理
     * @param array $params
     * @return boolean
     */
    public function AudioOrderReceiveOperate($params) {
    	$validate = $this->_validateParams($params);
    	if ($validate) {
	    	$up_msg_flag = $this->updateOrderDetailMsgFlag($params['push_msg_id'] , 3 , GetuiPush::TYPE_ORDER_AUDIO_DETAIL );
	    	
	    	//验证Queue有效性
	    	$queue = OrderQueue::model()->find('id = :id and flag = :flag' , array(':id' => $params['queue_id'] , 'flag' => OrderQueue::QUEUE_WAIT_COMFIRM));
	    	
	    	if ($queue) {
	    		//记录接单  (放入队列)
	    	    $position = $this->_setReceiveOrderPosition($params);
	    	    //验证司机是否可以接此单
	    	    $validate_driver_accept = $this->_validateDriverAccept($params['queue_id'] , $params['driver_id']);
	    	    if (!$validate_driver_accept) {
	    	        return false;
	    	    }
	    	        
		    	//修改订单信息
		    	$order = $this->_updateOrder($params['order_id'] , $params['driver_id']);
		    	if (!$order) {
		    	    return false;
		    	}
		    	    
		    	//修改map信息
		    	$map = $this->_updateMap($params['queue_id'] , $params['order_id'] , $params['driver_id']);
		    	if (!$map) {
		    	    return false;
		    	}
		    	  
		    	//修改queue记录 --- 多人预约 组后一个组员要推送组员给组长
		    	$update_flag = $this->_updateOrderQueue($queue , $params['driver_id']);
		    	
		    	$result = $this->_pushCustomerMsg($queue->phone , $params['driver_id'] , $params['queue_id']);
		    	return $result;
	    	} else {
	    		return false;
	    	}
    	} else {
    		return false;
    	}
    }
    
    /**
     * 新版设置发送信息
     * @param string $driver_id
     * @param string $queue_id
     * @return array
     */
    public function setPushOrderMsg($queue_id = 0 , $driver_id ="" , $order_id = 0){
    	
        if( empty($driver_id) || empty($queue_id) || empty($order_id)){
            return array('code'=>1);
        }
        
        // Yii::app()->db_readonly change into OrderQueue::getDbReadonlyConnection()
        $queue =OrderQueue::getDbReadonlyConnection()->createCommand()
                     ->select('*')
                     ->from('t_order_queue')
                     ->where('id =:id' , array(':id' => $queue_id))
                     ->queryRow();
        if(empty($queue)){
            return array('code'=>1);
        }

        $is_new = $this->_isNewOrder($queue['channel'] , $queue['type'] , $queue['name']);
        $data = array(
            'address' => $queue['address'],
            'customer_name' => $queue['name'],
            'phone' => '',
            'contact_phone' => '',
            'booking_time' => $queue['booking_time'],
            'number' => $queue['number'],
            'vipcard' => '',
            'role' => '',
            'leader_phone' => '',
            'bonus' => '',
            'card'=>'', //VIP或优惠卷卡号
            'balance'=>0, //VIP余额或优惠卷余额
            'source'=> $queue['type'] ,//订单来源
	        'channel' => $queue['channel'],
            
            'cost_type'=> '' ,//客户类型
            'lng'=> $queue['google_lng'] ,//经度
            'lat'=> $queue['google_lat'] ,//纬度
            'gps_type'=> self::DEFAULT_GPS_TYPE ,//gps类型
            'dist' => '',   //增加时间描述
            'is_new'=> $is_new ,//是否为新订单
        );

        // 添加cash_only字段，通知司机端这是否是一个现金支付订单——曾坤 2015/3/19
        $orders = QueueApiOrder::model()->get($queue['phone']."_".$queue['callid'], 'orders');
        if (!empty($orders) && isset($orders[$order_id], $orders[$order_id]['cash_only'])) {
            $data['cash_only'] = $orders[$order_id]['cash_only'];
        }
        
        if ($queue['contact_phone'] && $queue['contact_phone'] != $queue['phone']) {
            //$data['phone'] = substr($queue['phone'] , 0 , 3)."****".substr($queue['phone'] , -4);
            $data['phone'] = $queue['phone'];
            $data['contact_phone'] = $queue['contact_phone'];
        } else {
            $data['phone'] = $queue['phone'];
        }

        //如果是客户呼叫 或 开启新订单则给司机推送不加坐标 BY AndyCong 2013-12-24
        if ($queue['channel'] == CustomerApiOrder::QUEUE_CHANNEL_DRIVER_INPUT || $queue['channel'] == CustomerApiOrder::QUEUE_CHANNEL_CALLORDER) {
        	$data['gps_type'] = 'baidu';
        	$data['lng'] = '0';
        	$data['lat'] = '0';
        	$data['address'] = '';
        } else {
	        if ($queue['google_lng'] == '0.000000' && $queue['google_lat'] == '0.000000' && $queue['lng'] == '0.000000' && $queue['lat'] == '0.000000') {
	        	unset($data['lng']);
	        	unset($data['lat']);
	        	unset($data['gps_type']);
	        } elseif ($queue['lng'] == $queue['google_lng'] && $queue['lat'] == $queue['google_lat']) {
	        	$data['gps_type'] = 'baidu';
	        } elseif ($queue['google_lng'] == '0.000000' && $queue['google_lat'] == '0.000000' && $queue['lng'] != '0.000000' && $queue['lat'] != '0.000000') {
	        	$data['gps_type'] = 'baidu';
	        	$data['lng'] = $queue['lng'];
	        	$data['lat'] = $queue['lat'];
	        } 
        }

        //获取与客户见距离
        $customer_lng = isset($data['lng']) ? $data['lng'] : 0;
        $customer_lat = isset($data['lat']) ? $data['lat'] : 0;
	$arrive_dist = '';
        if (intval($customer_lng) > 0 && intval($customer_lat) > 0) {
	    $arrive_dist = $this->_getRouteDistanceSpeed($driver_id , $customer_lng , $customer_lat);
	    $data['dist'] = $arrive_dist;
		
		//FIX: 非远程单也记录距离信息到redis	2014-12-16
		$driver_dis_data[] = array();
		$driver_dis_data['dist'] = $arrive_dist;
	    if($queue['channel'] == CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER &&
		    !empty($arrive_dist)) {

		// Remote order
		if($arrive_dist > 5) {
		    $arrive_time = FinanceConfigUtil::remoteOrderConfig($queue['city_id'],$arrive_dist);
		    $data['arrive_time'] = $arrive_time['readyTime'];
		    $data['is_remote'] = '1';
		    $data['subsidy'] = FinanceCastHelper::getSubsidyOfRemoteOrder($queue['city_id'], $arrive_dist, 0);

		    $driver_dis_data['arrive_time'] = $arrive_time['readyTime'];
		    $driver_dis_data['is_remote'] = '1';
		    $driver_dis_data['subsidy'] = $data['subsidy'];
		    $fee = QueueDispatchOrder::model()->getQueueRemoteOrderFee($queue['id']);
		    if(!empty($fee)) {
			$driver_dis_data['fee'] = $fee;
			$data['fee'] = $fee;
		    } else {
			$driver_dis_data['fee'] = 0;
			$data['fee'] = 0;
		    }
		} else{
		    // Normal order
		    $driver_dis_data['is_remote'] = '0';
		}

	    }
		// Save the driver dis_data to redis
		QueueDispatchOrder::model()->setOrderDriverDisData($order_id, $driver_id, $driver_dis_data);
        }

		// 调整费用 add by liutuanwang
		$modifyFee =  @FinanceUtils::getModifyFeeConfig($queue['type'], $queue['city_id'], $queue);
		if(!empty($modifyFee)){
			$data['modify_fee'] = $modifyFee;
		}
	if(in_array($queue['type'], Order::$washcar_sources)) {
	    //一口价(洗车)暂时不支持优惠卷,账户,VIP
	}
	else {
            $order_favorable = Order::model()->getOrderFavorable($queue['phone'] ,strtotime($queue['booking_time']) , $queue['type'] , $order_id);
            if($order_favorable['code'] > 0){
            	$order_favorable['money'] = isset($order_favorable['money']) ? $order_favorable['money'] : 0;
            	$order_favorable['user_money'] = isset($order_favorable['user_money']) ? $order_favorable['user_money'] : 0;
                $data['card'] = isset($order_favorable['card']) ? $order_favorable['card'] : 0;

                $data['balance'] = $order_favorable['money'] + $order_favorable['user_money'];
                $data['user_money'] = $order_favorable['user_money'];
                $data['cost_type'] = (string)$order_favorable['code'];
                switch($order_favorable['code']){
                    case 1:
                        $data['vipcard'] = '余额：'.$order_favorable['money'].'元,不足部分请收取现金';
                        break;
                    case 2:
                        $data['bonus']=' 优惠金额：'.$order_favorable['money'].'元';
                        break;
                    case 4:
                        $data['bonus']=' 优惠金额：'.$order_favorable['money'].'元,个人账户余额'.$order_favorable['user_money'].'元,不足部分请收取现金';
                        break;
                    case 8:
                        $data['bonus']=' 个人账户余额：'.$order_favorable['user_money'].'元,不足部分请收取现金';
                        break;
                }
            }
	}

        if($queue['number'] > 1){
            //预约多人
            $leader = $this->_checkGroupLeader($queue['id']);

            if(empty($leader)){
                $data['role'] = '组长';
                $data['leader_phone'] = '';
            }else{
                $data['role'] = '组员';
                //获取组长的姓名与手机
                $leaderInfo = DriverStatus::model()->get($leader);
                $data['leader_phone'] = $leaderInfo->phone;
                
            }
        }

        return array('code'=>0 , 'msg' =>$data);
    }
    
    /**
     * 判定谁是组长
     * @param int $queue_id
     * @return array
     */
    private function _checkGroupLeader($queue_id){
        $orderQueueMap = OrderQueueMap::model()->find(array(
            'condition' => 'queue_id =:queue_id and confirm_time <> :confirm_time',
            'params'    => array(':queue_id' => $queue_id , ':confirm_time' => self::DEFAULT_TIME_FORMAT),
            'order'     => 'confirm_time ASC'
        ));
        
        if(!empty($orderQueueMap)){
            return $orderQueueMap->driver_id;
        }
        return array();
    }
    
    /**
     * 推送详情
     * @param int $queue_id
     * @param string $driver_id
     * @param string $msg
     * @return boolean
     */
    private function _pushDetailMsg($queue_id , $driver_id , $content , $channel) {
//    	$arr = array(
//    	    CustomerApiOrder::QUEUE_CHANNEL_SINGLE_DRIVER,
//    	    CustomerApiOrder::QUEUE_CHANNEL_SINGLE_CHANGE,
//    	    CustomerApiOrder::QUEUE_CHANNEL_BOOKING,
//    	);
//    	if (in_array($channel , $arr)) {
//    		$type = GetuiPush::TYPE_ORDER_NEW_DETAIL;
//    		$content['source'] = CustomerApiOrder::CUSTOMER_BOOKING_CODE;
//    	} else {
//    		$type = GetuiPush::TYPE_ORDER_DETAIL;
//    	}
    	$data = array(
            'type' => GetuiPush::TYPE_ORDER_DETAIL,
            'content' => $content,
            'level' => GetuiPush::LEVEL_HIGN,  //级别
            'driver_id' => $driver_id,
            'queue_id' => $queue_id,
            'created' => date('Y-m-d H:i:s' , time()),
        );
        
        //发送失败记录请求次数
        $result_push = $this->organizeMessagePush($data);
        if($result_push){
        	return true;
        } else {
        	return false;
        }
    }
    
    /**
     * 验证参数
     * @param array $params
     * @return boolean
     */
    private function _validateParams($params) {
    	$queue_id = isset($params['queue_id']) ? $params['queue_id'] : 0;
    	$order_id = isset($params['order_id']) ? $params['order_id'] : 0;
    	$push_msg_id = isset($params['push_msg_id']) ? $params['push_msg_id'] : 0;
    	$driver_id = isset($params['driver_id']) ? $params['driver_id'] : '';
    	$gps_type = isset($params['gps_type']) ? $params['gps_type'] : '';
    	$lng = isset($params['lng']) ? $params['lng'] : '';
    	$lat = isset($params['lat']) ? $params['lat'] : '';
    	$log_time = isset($params['log_time']) ? $params['log_time'] : '';
    	if (0 == $queue_id || 0 == $order_id || 0 == $push_msg_id || empty($driver_id) || empty($gps_type) || empty($lng) || empty($lat) || empty($log_time)) {
    		return false;
    	} else {
    		return true;
    	}
    }
    
    private function _updateDriverAcceptStatus($params , $flag) {
    	$log_arr = array(
            'queue_id' => $params['queue_id'],
            'driver_id' => $params['driver_id'],
            'flag' => $flag,
            'accept_time' => date("Y-m-d H:i:s" , time()),
            'success_time' => date("Y-m-d H:i:s" , time()),
        );
        $task = array(
            'method' => 'update_driver_accept_log',
            'params' => $log_arr,
        );
        Queue::model()->putin($task , 'orderstate');
        return true;
    }
    
    /**
     * 修改message_log状态（放入队列）
     * @param int $push_msg_id
     * @return boolean
     */
    private function _updateOrderDetailMsgFlag($push_msg_id , $flag) {
    	$params = array(
    	    'push_msg_id' => $push_msg_id,
    	    'flag' => $flag,
    	);
    	$task = array(
            'method' => 'update_msg_flag',
            'params' => $params,
        );
        Queue::model()->putin($task , 'dalmessage');
    	return true;
    }
    /**
     * 修改message_log状态（order_detail）
     * @param int $push_msg_id
     * @return boolean
     */
    public function updateOrderDetailMsgFlag($push_msg_id = 0 , $flag = 0 , $type = '') {
    	if (0 == $push_msg_id || 0 == $flag) {
    		return false;
    	}
    	if ($flag == 2) {
    		$content = '订单已被接收';
    	} elseif ($flag == 3) {
    		$content = '司机接单成功';
    	} else {
    		$content = '派单给司机';
    		echo "\n---push_msg_id:".$push_msg_id."---".$content."---".date('Y-m-d H:i:s')." update success---\n";
    		return true;
    	}
    	$type = !empty($type) ? $type : GetuiPush::TYPE_ORDER_DETAIL;
    	$tab = 't_message_log_'.date('Ym');
    	$complate_flag = 3;
    	$sql = 'UPDATE '.$tab.' SET `flag`='.$flag.' WHERE push_msg_id = :push_msg_id AND type = :type AND flag <> :flag';
    	$result = Yii::app()->dbreport->createCommand($sql)->execute(array(':push_msg_id' => $push_msg_id , ':type' => $type , ':flag' => $complate_flag));
    	
    	if ($result) {
    		echo "\n---push_msg_id:".$push_msg_id."---".$content."---".date('Y-m-d H:i:s')." update success---\n";
    		return true;
    	} else {
    		echo "\n---push_msg_id:".$push_msg_id."---".$content."---".date('Y-m-d H:i:s') ." update fail ---\n";
    		return false;
    	}
    }
    
    /**
     * 记录接单位置
     * @param array $params
     * @return boolean
     */
    private function _setReceiveOrderPosition($params) {
    	$position_arr = array(
    	    'order_id' => $params['order_id'],
    	    'gps_type' => $params['gps_type'],
    	    'lng' => $params['lng'],
    	    'lat' => $params['lat'],
    	    'log_time' => $params['log_time'],
    	    'driver_id' => $params['driver_id'],
    	    'flag' => OrderPosition::FLAG_ACCEPT,
    	    'name' => '',
    	    'phone' => '',
    	    'car_number' => '',
    	);
    	$task = array(
    	    'method' => 'push_order_position',
    	    'params' => $position_arr,
    	);
    	Queue::model()->putin($task , 'order');
    	return true;
    }
    
    private function _validateDriverAccept($queue_id , $driver_id) {
    	$count = OrderQueueMap::model()->count('queue_id = :queue_id and driver_id = :driver_id' , array(':queue_id' => $queue_id , ':driver_id' => $driver_id));
    	if ($count) {
    		return false;
    	} else {
    		return true;
    	}
    }
    
    /**
     * 修改订单信息
     * @param int $order_id
     * @param string $driver_id
     * @return boolean
     */
    private function _updateOrder($order_id , $driver_id) {
    	$driver = DriverStatus::model()->get($driver_id);
    	if ($driver) {
    		$attributes = array(
    		    'driver_id' => $driver_id,
    		    'driver_phone' => $driver->phone,
    		    'driver' => $driver->info['name'],
    		    'imei' => $driver->info['imei'],
    		);
    		Order::model()->updateByPk($order_id , $attributes);
    		return true;
    	} else {
    		return false;
    	}
    }
	
    /**
     * 更新映射关系
     * @param int $queue_id
     * @param int $order_id
     * @param string $driver_id
     * @return boolean
     */
    private function _updateMap($queue_id , $order_id , $driver_id) {
        $this->updateOrderQueueMap(OrderQueueMap::MAP_CONFIRM, date('Y-m-d H:i:s' , time()), $driver_id, $queue_id, $order_id);
    	
    	//增加一步骤更新redis
    	$task = array(
    	    'method' => 'api_update_orders',
    	    'params' => array(
    	        'queue_id' => $queue_id,
    	        'order_id' => $order_id,
    	        'driver_id' => $driver_id,
    	        'order_state' => OrderProcess::ORDER_PROCESS_ACCEPT,//
    	    )
    	);
    	Queue::model()->putin($task , 'orderstate');
    	//增加一步骤更新redis END
    	
    	return true;
    }
    
    /**
     * 更新OrderQueue记录
     * @param object $queue
     * @return boolean
     */
    private function _updateOrderQueue($queue , $driver_id) {
    	//获取备注信息
        $condition = array('queue_id' => $queue->id , 'comments' => $queue->comments);
        $comments = $this->getQueueComments($condition , $driver_id);
         
        //获取当前己派送的司机总数
        $count = OrderQueueMap::model()->count('queue_id =:queue_id and flag = :flag',array(':queue_id' => $queue->id , ':flag' => OrderQueueMap::MAP_CONFIRM));
        if($queue->number == $count){
            if($queue->number > 1){
            	echo "------ queue_id:".$queue->id." push leader msg ------\n";
                $lead_msg = $this->_pushLeadMsg($queue->id, $driver_id);
            }
            
            echo "------ queue_id:".$queue->id." update flag ------\n";
            $sql = 'UPDATE t_order_queue SET `flag` = '.OrderQueue::QUEUE_SUCCESS.' , `dispatch_agent` = "'.self::QUEUE_DISPATCH_AGENT_AUTO.'" , `comments` = "'.$comments.'" , `dispatch_time` = "'.date('Y-m-d H:i:s' , time()).'" , `dispatch_number` = `dispatch_number`+1 WHERE id = :id';
            // Yii::app()->db change into OrderQueue::getDbMasterConnection()
            $ret = OrderQueue::getDbMasterConnection()->createCommand($sql)->execute(array(':id' => $queue->id));
            echo "------ queue_id:".$queue->id." update result is ".$ret." affect ------\n";
        } else {
	    if($queue['number'] > 1) {
            	echo "------ queue_id:".$queue->id." push leader msg ------\n";
                $lead_msg = $this->_pushLeadMsg($queue->id, $driver_id);
	    }
        	$sql = 'UPDATE t_order_queue SET `comments` = "'.$comments.'" , `update_time` = "'.date('Y-m-d H:i:s' , time()).'" , `dispatch_number` = `dispatch_number`+1 WHERE id = :id';
        	// Yii::app()->db change into OrderQueue::getDbMasterConnection()
        	$ret = OrderQueue::getDbMasterConnection()->createCommand($sql)->execute(array(':id' => $queue->id));
        }
        
        if ($ret) {
        	return true;
        } else {
        	return false;
        }
    }
    
    /**
     * 更新OrderQueue记录
     * @param object $queue
     * @return boolean
     */
    private function _updateOrderQueueNew($queue , $driver_id) {
    	//获取备注信息
        $condition = array('queue_id' => $queue['id'] , 'comments' => $queue['comments']);
        $comments = $this->getQueueComments($condition , $driver_id);
         
        //获取当前己派送的司机总数
        $count = OrderQueueMap::model()->count('queue_id =:queue_id and flag = :flag',array(':queue_id' => $queue['id'] , ':flag' => OrderQueueMap::MAP_CONFIRM));
        if($queue['number'] == $count){
            if($queue['number'] > 1){
		EdjLog::info($queue['id'].'|'.$driver_id.'| Push leader msg');
                $lead_msg = $this->_pushLeadMsg($queue['id'], $driver_id);
            }
            
	    EdjLog::info($queue['id'].'|'.'Update Order queue flag');
            $sql = 'UPDATE t_order_queue SET `flag` = '.OrderQueue::QUEUE_SUCCESS.' , `dispatch_agent` = "'.self::QUEUE_DISPATCH_AGENT_AUTO.'" , `comments` = "'.$comments.'" , `dispatch_time` = "'.date('Y-m-d H:i:s' , time()).'" , `dispatch_number` = `dispatch_number`+1 WHERE id = :id';
            // Yii::app()->db change into OrderQueue::getDbMasterConnection()
            $ret = OrderQueue::getDbMasterConnection()->createCommand($sql)->execute(array(':id' => $queue['id']));
        } else {
	    if($queue['number'] > 1 && $count > 1) {
		EdjLog::info($queue['id'].'|'.$driver_id.'| Push leader msg');
                $lead_msg = $this->_pushLeadMsg($queue['id'], $driver_id);
	    }
	    $sql = 'UPDATE t_order_queue SET `comments` = "'.$comments.'" , `update_time` = "'.date('Y-m-d H:i:s' , time()).'" , `dispatch_number` = `dispatch_number`+1 WHERE id = :id';
	    // Yii::app()->db change into OrderQueue::getDbMasterConnection()
	    $ret = OrderQueue::getDbMasterConnection()->createCommand($sql)->execute(array(':id' => $queue['id']));
        }
        
        if ($ret) {
	    $ret = true;
        } else {
	    $ret = false;
        }
	return $ret;
    }
    
    /**
     * 组织queue备注信息
     * @param array $data
     * @return string $comments
     */
    public function getQueueComments($data = array() , $driver_id = '') {
    	$queue_id = isset($data['queue_id']) ? $data['queue_id'] : 0;
    	$comments = isset($data['comments']) ? $data['comments'] : '';
    	if (0 == $queue_id || empty($driver_id)) {
    		return '';
    	}
//        $drivers = OrderQueueMap::model()->findAll('queue_id = :queue_id and flag = :flag', array (':queue_id' => $data['queue_id'] , 'flag' => OrderQueueMap::MAP_CONFIRM));
        $driver_info = Driver::getProfile($driver_id);
		$comments_last = sprintf('%s %s', $driver_id, $driver_info->phone)."<br/>";
		$comments = empty($comments) ? $comments_last : $comments.'<br/>'.$comments_last;
		return $comments;
    }
    
    /**
     * 推送组长消息
     * @param int $queue_id
     * @return boolean
     */
    private function _pushLeadMsg($queue_id, $driver_id = 'BJ00000') {
        $leader = $this->orgToLeaderSmsMsg($queue_id, $driver_id);
        if(!empty($leader)){
	    $leader_phone = $leader['leader_phone']; 
	    EdjLog::info($leader_phone.'|Send sms to leader');
	    Sms::SendSMS($leader_phone , $leader['msg']);
        }
        return true;
    }

    /**
     * 把组员的信息以SMS发给组长
     * @param int $queue_id
     * @return array
     */
    public function orgToLeaderSmsMsg($queue_id = 0, $driver_id = 'BJ00000' ){
        if(0 == $queue_id || $driver_id == 'BJ00000'){
            return array();
        }
 
        $orderQueueMapList = $this->findOrderQueueMapList($queue_id);
        if(count($orderQueueMapList) > 1){
            $info_msg = "";
            $head_msg = "";
            $msg = "";
            $leader_phone = "";
            $leader = '';
            foreach($orderQueueMapList as $k=>$val){
            	$order_id=$val->order_id;
                if($k == 0 ){
                    $driver = DriverStatus::model()->get($val->driver_id);
		    $leader_phone =  $driver->phone;
		    $head_msg = sprintf('%s 师傅，您已接多人订单，您为组长，' , $val->driver_id) ;
		    	//组长也需要记录，当组长销单时候，不扣除e币
		    	$leader=$val->driver_id;
                ROrder::model()->setGroup($order_id,'driver_id',$leader);
                ROrder::model()->setGroup($order_id,'leader',$leader);
                EdjLog::info('组长信息加入redis,order='.$order_id.',driver_id='.$driver_id.',leader='.$leader);
            
                }else{
		    if($val->driver_id == $driver_id) {
			$driver = DriverStatus::model()->get($val->driver_id);
			$phone = $driver->phone;
			$info_msg = sprintf('组员:%s 已接单，联系电话:%s' , $val->driver_id , $phone) ;
				//添加组员信息到redis，如果报单，组长加e币
			    $leaderInfo = DriverStatus::model()->get($leader);
                ROrder::model()->setGroup($order_id,'driver_id',$driver_id);
                ROrder::model()->setGroup($order_id,'leader',$leaderInfo->driver_id);
                ROrder::model()->setGroup($order_id,'city_id',$leaderInfo->city_id);
                EdjLog::info('组员信息加入redis,order='.$order_id.',driver_id='.$driver_id.',leader='.$leaderInfo->driver_id);
		    }
                }
            }
	    $msg = $head_msg.$info_msg;
            return array('leader_phone'=>$leader_phone , 'msg' => $msg,);
        }else{
            return array();
        }
    }
    
    /**
     * 把组员的信息发给组长
     * @param int $queue_id
     * @return array
     */
    public function setLeaderMsg($queue_id = 0 ){
        if(0 == $queue_id){
            return array();
        }
 
        $orderQueueMapList = $this->findOrderQueueMapList($queue_id);
        if(count($orderQueueMapList) > 1){
            $message = '工号:%s 手机:%s  ';
            $msg = "组员联系信息:";
            $leader = "";
            foreach($orderQueueMapList as $k=>$val){
                if($k == 0 ){
                    $leader = $val->driver_id;
                }else{
                    $driver = DriverStatus::model()->get($val->driver_id);
                    $phone = $driver->phone;
                    $msg .= sprintf($message , $val->driver_id, $phone) ;
                }
            }
            return array('driver_id'=>$leader , 'msg' => $msg,);
        }else{
            return array();
        }
    }
	
	/**
	 * 生成订单
         * @Modified qiujianping@edaijia-inc.cn 2014-03-26
         * 	Add process data to t_order_process 
         *
	 * @param object $queue
	 * @return int   order_id
	 */
	private function _genOrder($queue) {
		//插入订单
		$order_number = isset($queue->order_number) ? $queue->order_number : '';
		$name = addslashes($queue->name);
		$phone = $queue->phone;
		$contact_phone = $queue->contact_phone;
		$source = $queue->type;
		$city_id = $queue->city_id;
		$driver = self::DEFAULT_DRIVER_INFO;
		$driver_id = self::DEFAULT_DRIVER_INFO;
		$driver_phone = self::DEFAULT_DRIVER_INFO;
		$imei = self::DEFAULT_DRIVER_INFO;
		$call_time = strtotime($queue->created);
		$order_date = date('Ymd', time());
		$booking_time = strtotime($queue->booking_time);
		$location_start = addslashes($queue->address);
		$description = addslashes(Order::SourceToDescription($source));
		$created = time();
		
		$channel = (isset($queue->channel) && !empty($queue->channel)) ? $queue->channel : 0;
        $sql = 'insert into t_order (order_number,name,phone , contact_phone , source,driver,city_id,driver_id,driver_phone,
						imei,call_time,order_date,booking_time,location_start,description,created,channel)
						values ("%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s",%s,"%s")';
        $sql = sprintf($sql, $order_number , $name, $phone, $contact_phone , $source, $driver, $city_id, $driver_id, $driver_phone, $imei, $call_time, $order_date , $booking_time, $location_start, $description, $created,$channel);
        Order::getDbMasterConnection()->createCommand($sql)->execute();
        $order_id = Order::getDbMasterConnection()->getLastInsertID();

        // Now set the order process 
        $process_params = array();
        $process_params['queue_id'] = $queue->id;
        $process_params['order_id'] = $order_id;
        $process_params['state'] = OrderProcess::PROCESS_NEW;
        $process_params['driver_id'] = self::DEFAULT_DRIVER_INFO;
        $process_params['created'] = date("Y-m-d H:i:s", time());

        // We just ignore if the process is success or failed
        OrderProcess::model()->genNewOrderProcess($process_params);
        return $order_id;
	}
	
	/**
	 * 生成OrderQeueuMap记录并返回map_id
	 * @param int $queue_id
	 * @param int $order_id
	 * @return int map_id
	 */
	private function _genMap($queue_id , $order_id , $number) {
		$attributes = array(
		    'queue_id' => $queue_id,
		    'order_id' => $order_id,
		    'driver_id' => self::DEFAULT_DRIVER_INFO,
		    'number' => $number,
		    'dispatch_time' => date('Y-m-d H:i:s' , time()),
		    'confirm_time' => self::DEFAULT_TIME_FORMAT,
		);
		$model = new OrderQueueMap();
		$model->attributes = $attributes;
		$model->save();
		return $model->id;
	}
	
	/**
	 * 获取未派出司机的订单
	 * @param int $queue_id
	 * @param int $number
	 * @return array $ret
	 */
	public function _getNoDispatchOrder($queue_id , $number, $channel='0') {
		$ret = array();
		
        $count = OrderQueueMap::model()->count('queue_id = :queue_id and flag = :flag' , array(':queue_id' => $queue_id , ':flag' => OrderQueueMap::MAP_CONFIRM));
        if (0 == $count && 1 != $number) {
            //获取组长订单
            $leaderMap = OrderQueueMap::model()->find(array(
                'condition' => 'queue_id = :queue_id',
                'params'    => array(':queue_id' => $queue_id),
                'order'     => 'id ASC'
            ));

			// Check the type of the order, if it's single change or keybooking
			// The dispatch times is set to be smaller than 10 times
			if(!empty($leaderMap) && ($channel == '01003' || $channel == '01002') &&
			    $leaderMap->number > 10) {
			  // TODO: Do nohting now, check the respone of client
			  // Set to be dispatch finished
			  EdjLog::info($leaderMap->queue_id . '|' . $leaderMap->order_id . '|' . $leaderMap->number . '|Dispatch more than 10 times');
			}
			
			$orderQueueMapList = array($leaderMap);
		} else {
			//获取全部信息
            $orderQueueMapList = OrderQueueMap::model()->findAll('queue_id = ? AND flag = ?', array($queue_id, OrderQueueMap::MAP_NOT_CONFIRM));
		}
		
		//MKTODO which method from AR class should be used for update sql that includes both counter and normal column? 
		$sql = "UPDATE `t_order_queue_map` SET `number` = `number`+1 , dispatch_time = :dispatch_time WHERE id = :id";
		foreach ($orderQueueMapList as $map) {
		  // Ok, This is the first try for dispatch, Set the process to be start dispatch
		  if($map->number == 0) {
		    $process_params = array();
		    $process_params['queue_id'] = $map->queue_id;
		    $process_params['order_id'] = $map->order_id;
		    $process_params['state'] = OrderProcess::PROCESS_START_DISPATCH;
		    $process_params['driver_id'] = self::DEFAULT_DRIVER_INFO;
		    $process_params['created'] = date("Y-m-d H:i:s", time());

		    // We just ignore if the process is success or failed
		    OrderProcess::model()->genNewOrderProcess($process_params);
		  }

		  // Check if an order is locked, if true,ignore it
		  // The order is keybooking or single change add 400
		  if($this->_isOrderLocked($map->order_id)) {
		    continue;
		  }
		  $ret[] = array('queue_id' => $queue_id , 'order_id' => $map->order_id, 'map_id' => $map->id);
		  Yii::app()->dborder->createCommand($sql)->execute(array(':id' => $map->id, ':dispatch_time' => date('Y-m-d H:i:s' , time())));
		}
		return $ret;
	}
	
	/**
	 * 组织短信内容推送
	 * @param array $params
	 * @return boolean
	 */
	public function organizeMessagePush($params) {
            //验证参数
            if (empty($params)) {
                return false;
            }

            //根据类型获取消息内容格式
            $push_msg_id = Tools::getUniqId('nomal');
            $message = $this->_getMessageByType($params);
            $message['push_msg_id'] = $push_msg_id;

            //增加输出log
            $p = array(
                'push_msg_id' => $push_msg_id,
                'queue_id' => isset($params['queue_id']) ? $params['queue_id'] : '',
                'order_id' => isset($params['content']['order_id']) ? $params['content']['order_id'] : '',
                'driver_id' => isset($params['driver_id']) ? $params['driver_id'] : '',
                'log_time' => time(),
            );
            //增加输出log

            //其他包装推送参数
            $data=array('client_id' => $params['client_id'] , 'level' => $params['level']);
            if (isset($params['offline_time']))
                $data['offline_time'] = $params['offline_time'];
            $data['message']=json_encode($message);
            $data['queue_id'] = isset($params['queue_id']) ? $params['queue_id'] : 0;
            $data['driver_id'] = $params['driver_id'];

            EdjLog::info($data['queue_id'].'|'.$p['order_id'].'|'.$message['push_msg_id'].'|'.$data['driver_id'].'|个推推送|begin');

            //如果是一键下单推送订单,Push存储在redis,用于短信下发Push
            if(isset($params['type'])
                && $params['type'] == GetuiPush::TYPE_ORDER_DETAIL
                && isset($params['channel'])
                && $params['channel'] == CustomerApiOrder::QUEUE_CHANNEL_BOOKING) {
                $data['push_distinct_id'] = Tools::getUniqId('nomal');
                ROrder::model()->insertMessage($data['push_distinct_id'],
                    array_merge($params, $data, $message));
            }

            $result = GetuiPush::model($params['version'])->PushToSingle($data, $params['version']);

            if ($result['result']=='ok' && $result['status'] == "successed_online") {
                EdjLog::info($data['queue_id'].'|'.$p['order_id'].'|'.$data['driver_id'].'|'.$result['taskId'].'|'.$result['status'].'|'.$result['result'].'|个推推送成功|end');
                $result = true;
            } else {
                if ($result['result']=='ok' && $result['status'] == "successed_offline") {
                    EdjLog::info($data['queue_id'].'|'.$p['order_id'].'|'.$data['driver_id'].'|'.$result['taskId'].'|'.$result['status'].'|'.$result['result'].'|个推推送成功|end');
                } else {
                    EdjLog::info($data['queue_id'].'|'.$p['order_id'].'|'.$data['driver_id'].'|'.$result['result'].'|个推推送失败|end');
                }
                $result = false;

                //如果是一键下单推送订单并且版本支持 短信下发push
                if(isset($params['channel'])
                    && $params['channel'] == CustomerApiOrder::QUEUE_CHANNEL_BOOKING
                    && isset($params['type'])
                    && $params['type'] == GetuiPush::TYPE_ORDER_DETAIL) {

                    $app_ver = DriverStatus::model()->app_client_ver($data['client_id']);
                    if( !empty($app_ver)
                        && !empty($data['push_distinct_id'])
                        && !empty(Yii::app()->params['SmsPushLimitedVersion'])
                        //司机端版本大于短信Push限制版本
                        && Helper::compareVersion($app_ver,
                            Yii::app()->params['SmsPushLimitedVersion'])) {

                        $driver_phone = DriverStatus::model()->getItem($data['driver_id'], 'phone');
                        if(!empty($driver_phone)) {
                            $result = EPush::sms_push($data['push_distinct_id'], $driver_phone);
                            if($result) {
                                EdjLog::info('SmsPushLog_multi'.'|'.$data['queue_id'].'|'.$p['order_id']
                                .'|'.$data['driver_id'].'|'.$data['push_distinct_id']);
                            }
                        }
                    }
                }
            }

	    //message入库
            $push_msg_id = $this->_genMessageLog($params);

            return $result;
	}
	
	/**
	 * 分表去记录message_log
	 * @param array $params
	 * @return int 
	 */
	private function _genMessageLog($params) {
		$tab = 't_message_log_'.date('Ym');
		$attr = array(
			'client_id'=>$params['client_id'],
			'type'=>$params['type'],
			'content'=>json_encode($params['content']),
			'level'=>$params['level'],
			'driver_id'=>$params['driver_id'],
			'queue_id'=>$params['queue_id'],
			'version'=>$params['version'],
            'created'=>$params['created'],
		);
		$result = Yii::app()->dbreport->createCommand()->insert($tab , $attr);
		if ($result) {
			$push_msg_id = Yii::app()->dbreport->getLastInsertID(); 
			return $push_msg_id;
		} else {
			return 0;
		}
	}
	
	/**
	 * 通过类型获取推送消息体
	 * @param array $params
	 * @return array $message
	 */
	private function _getMessageByType(&$params) {
		switch ($params['type']) {
			case GetuiPush::TYPE_ORDER : //订单 司机端
				$message=array(
						'type'=>GetuiPush::TYPE_ORDER,
						'queue_id'=>$params['queue_id'],
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case GetuiPush::TYPE_ORDER_DETAIL : //订单详情 司机端
				$message=array(
						'type'=>GetuiPush::TYPE_ORDER_DETAIL,
						'queue_id'=>$params['queue_id'],
						'content'=>$params['content'],
						'timestamp'=>time()
				);
				$is_new = isset($params['content']['is_new']) ? $params['content']['is_new'] : 0;
				if ($is_new == 1) {
					$message['timeout'] = self::ORDER_RECEIVE_TIMEOUT;
				}
				
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case GetuiPush::TYPE_MSG_LEADER : //消息  司机端
				$message=array(
						'type'=>'msg',
						'content'=>array(
								'message'=>$params['content'],
								'feedback' => 1,
						),
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case GetuiPush::TYPE_ORDER_AUDIO_DETAIL : //消息  司机端
				$message=array(
						'type'=>GetuiPush::TYPE_ORDER_AUDIO_DETAIL,
						'queue_id'=>$params['queue_id'],
						'content'=>$params['content'],
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case GetuiPush::TYPE_MSG_CUSTOMER : //消息  司机端
				$message=array(
						'type'=>'msg',
						'content'=>$params['content'],
						'timestamp'=>time()
				);
				$params['version'] = 'customer';
				$phone = isset($params['phone']) ? $params['phone'] : '';
				$client = CustomerClient::model()->getByPhone($phone);
				break;
			case GetuiPush::TYPE_ORDER_CANCEL : //消息  司机端
				$message=array(
						'type'=>GetuiPush::TYPE_ORDER_CANCEL,
						'content'=>isset($params['content']) ? $params['content'] : '',
						'timestamp'=>time()
				);
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			case GetuiPush::TYPE_ORDER_NEW_DETAIL : //订单详情 司机端
				$message=array(
						'type'=>GetuiPush::TYPE_ORDER_NEW_DETAIL,
						'queue_id'=>$params['queue_id'],
						'content'=>$params['content'],
						'timestamp'=>time()
				);
				
				$is_new = isset($params['content']['is_new']) ? $params['content']['is_new'] : 0;
				if ($is_new == 1) {
					$message['timeout'] = self::ORDER_RECEIVE_TIMEOUT;
				}
				
				$params['version']='driver';
				$client=GetuiLog::model()->getDriverInfoByDriverID($params['driver_id']);
				break;
			default :
				break;
		}
		$params['client_id'] = isset($client['client_id']) ? $client['client_id'] : '';
		return $message;
	}
	
	/**
	 * 获取未派出的订单
	 * @param int $queue_id
	 * @return array $order
	 */
	public function getNodispatchOrder($queue_id = 0) {
		if (0 == $queue_id) {
			return '';
		}
		$order = OrderQueueMap::getDbReadonlyConnection()->createCommand()
                    ->select('*')
                    ->from('t_order_queue_map')
                    ->where('queue_id = :queue_id and flag = :flag and driver_id = :driver_id' , array(
                        ':queue_id' => $queue_id,
                        ':flag' => OrderQueueMap::MAP_NOT_CONFIRM,
                        ':driver_id' => self::DEFAULT_DRIVER_INFO,
                    ))
                    ->order('order_id asc')
                    ->queryRow();
                return $order;
	}
	
	/**
	 * 订单绑定司机（单人预约）
	 * @param int $queue_id
	 * @param int $order_id
	 * @param string $driver_id
	 */
	public function orderBindingDriver($queue_id = 0 , $order_id = 0 , $driver_id = '') {
		if (0 == $queue_id || 0 == $order_id || empty($driver_id)) {
			return false;
		}
		
		//更新订单
		$this->_updateOrder($order_id , $driver_id);
		
		//更新map
		$this->_updateMap($queue_id , $order_id , $driver_id);
		return true;
	}
	
	/**
	 * 取消queue同时取消未派出的订单
	 * @param int $queue_id
	 * @return boolean
	 */
	public function cancelNoDispatchOrder($queue_id = 0) {
		if (0 == $queue_id) {
			return false;
		}
		
		//取出所有未派订单
        $notDispatchedMap = OrderQueueMap::model()->findAllByAttributes(
            array(
                'queue_id'  => $queue_id,
                'flag'      => OrderQueueMap::MAP_NOT_CONFIRM,
                'driver_id' => self::DEFAULT_DRIVER_INFO
            ),
            array('select'    => 'order_id')
        );
		                         
		
		//取消未派订单
		foreach ($notDispatchedMap as $map) {
    	    Order::model()->updateByPk($map->order_id, array('status' => Order::ORDER_NO_DISPATCH_CANCEL));
    	    $this->_cancelBonus($map->order_id);
        }
		return true;
	}
	
	/**
	 * 针对测试工号
	 * @param unknown_type $queue_id
	 * @param unknown_type $driver_id
	 * @return unknown
	 */
	public function PushOrderTest($queue_id = 0 , $driver_id = '') {
		if (0 == $queue_id || empty($driver_id)) {
			return false;
		}
		
		//非测试工号返回
		$new_test_drivers = Common::getCallOrderAutoTestDriverIds();
		if (!in_array($driver_id , $new_test_drivers)) {
			return false;
		}
		
		//获取未派订单
		$orders = $this->getDispatchOrder($queue_id);
		
		//只派第一个订单
		$i = 0;
		foreach ($orders as $order) {
			if (0 == $i) {
				$this->PushOrder($queue_id , $order['order_id'] , $driver_id);
			}
			$i++;
		}
		return true;
	}
	
	/**
	 * 获取组长信息
	 * @param int $queue_id
	 * @return array $data
	 */
	public function  getGroupLeader($queue_id = 0) {
		if (0 == $queue_id) {
			return array();
		}
		$data = array();
        $orderQueueMap = OrderQueueMap::model()->find(array(
            'select'    => 'driver_id',
            'condition' => 'queue_id = :queue_id and confirm_time <> :confirm_time',
            'params'    => array(':queue_id' => $queue_id, ':confirm_time' => self::DEFAULT_TIME_FORMAT)
        ));
        if (!empty($orderQueueMap)) {
        	$driver = DriverStatus::model()->get($orderQueueMap->driver_id);
        	if ($driver) {
        		$data['driver_id'] = $driver->driver_id;
        		$data['phone'] = $driver->phone;
        		$data['name'] = $driver->info['name'];
        	}
        }
        return $data;

	}
	
	/**
	 * 音频预约订单
	 * @param array $params
	 * @return boolean
	 */
	public function audioBooking($params = array()) {
		//验证参数
		$validate = $this->_validateAudioParams($params);
		if ($validate) {
			$queue_id = $this->_saveOrderQueue($params);
			if (!$queue_id) {
				return false;
			}
			
			//获取订单
			$order = $this->getDispatchOrder($queue_id);
			if (empty($order)) {
				return false;
			}
			
			//保存t_order_ext
			$ext = $this->_saveOrderExt($order[0]['order_id'] , $params['url']);
			
			//获取司机派单
			$arr = array('queue_id' => $queue_id , 'order_id' => $order[0]['order_id'] , 'lng' => $params['lng'] , 'lat' => $params['lat'] , 'url' => $params['url']);
			$result = $this->_dispatchAudioOrderDriver($arr);
			return $result;
		}
		//生成预约信息OrderQueue

	}
	
	/**
	 * 验证参数
	 * @param array $params
	 * @return boolean
	 */
	private function _validateAudioParams(&$params) {
		$booking_time = isset($params['booking_time']) ? $params['booking_time'] : date('Y-m-d H:i:s' , time());
		$phone = isset($params['phone']) ? $params['phone'] : '';
		$name = isset($params['name']) ? $params['name'] : '先生';
		$lng = isset($params['lng']) ? $params['lng'] : '';
		$lat = isset($params['lat']) ? $params['lat'] : '';
		$gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'wgs84';
		$url = isset($params['url']) ? $params['url'] : '';
		$gps_location = array(
		    'longitude' => isset($params['lng']) ? $params['lng'] : '0.000000',
		    'latitude' => isset($params['lat']) ? $params['lat'] : '0.000000',
		);
		$gps = GPS::model()->convert($gps_location , $gps_type);
		$city = GPS::model()->getCityByBaiduGPS($gps['baidu_lng'] , $gps['baidu_lat']);
		$city_id = Dict::code('city', $city);
		$address = isset($params['address']) ? $params['address'] : $gps['street']; 
		if (empty($phone) || empty($city_id) || empty($lng) || empty($lat) || empty($address) || empty($url)) {
			return false;
		}
		
		$params['name'] = $name;
		$params['address'] = $address;
		$params['city_id'] = $city_id;
		$params['booking_time'] = $booking_time;
		$params['type'] = Order::SOURCE_CLIENT;
		$params['agent_id'] = self::QUEUE_AGENT_AUDIO_BOOKING;
		return true;
	}
	
	/**
	 * 保存OrderQueue
	 * @param array $params
	 * @return int
	 */
	private function _saveOrderQueue($params) {
    	//参数整理
    	$time = time();
        $call_id = md5(uniqid(rand(), true));
        $vipInfo = VipService::service()->getVipInfo($params['phone'], false);//这里不取信用额度
        $comments = self::AUDIO_COMMENTS;
        if($vipInfo){
            $status = $vipInfo['status'];
            if(Vip::STATUS_NORMAL == $status){  // TODO ... 整理仍然有耦合
                $comments = self::AUDIO_COMMENTS.',此用户是vip';
            }
            if(Vip::STATUS_ARREARS == $status){
                $comments = self::AUDIO_COMMENTS.',此用户是vip,余额是'.$vipInfo['amount'];
            }
        }
		$model = new OrderQueue();
		$queue_arr = array(
		    'phone' => $params['phone'],              //客户电话
		    'contact_phone' => $params['phone'],      //客户电话
		    'city_id' => $params['city_id'],          //需要gps反推
		    'callid' => $call_id,                     //callid 时间戳加密
		    'name' => $params['name'],                //需要传进来
		    'number' => 1,                            //司机数量
		    'address' => $params['address'],          //地址                
		    'comments' => $comments,                  //说明
		    'booking_time' => $params['booking_time'],//预约时间
		    'flag' => OrderQueue::QUEUE_WAIT_COMFIRM,        //派单状态
		    'type' => $params['type'] ,                      //订单来源
		    'update_time' => self::DEFAULT_TIME_FORMAT ,     //更新时间
		    'dispatch_time' => self::DEFAULT_TIME_FORMAT ,   //更新时间
		    'agent_id' => $params['agent_id'],               //操作员
		    'dispatch_agent' => '',                   //操作员
		    'created' => date('Y-m-d H:i:s' , $time), //下单的时间
		);
		
		$model->attributes = $queue_arr;
		$model->lng = $params['lng'];
		$model->lat = $params['lat'];
		$result = $model->save();
		if ($result) {
			return $model->id;
		} else {
			return 0;
		}
    }
    
    /**
     * 保存音频文件到order_ext
     * @param int $order_id
     * @param string $url
     * @return boolean
     */
    private function _saveOrderExt($order_id , $url) {
    	$model = new OrderExt();
    	$attributes = array(
    	    'order_id' => $order_id,
    	    'created' => date('Y-m-d H:i:s'),
    	);
    	$model->attributes = $attributes;
    	$model->mark = $url;
    	$model->save();
    	return true;
    }
    
    /**
     * 针对音频订单派司机
     * @param array $params
     * @return unknown
     */
    private function _dispatchAudioOrderDriver($params) {
    	$order_flag = DispatchDriver::model()->checkDispatchOrderLock($params['order_id']);
    	$drivers=array(0=>array('driver_id'=>'BJ9005') , 1=>array('driver_id' => 'BJ9013') , 2=>array('driver_id'=>'BJ1161') , 3=>array('driver_id' => 'BJ9016'));
    	if (!empty($drivers)) {
    		$i = 1;
    		//测试工号
            $test_drivers = DispatchDriver::model()->test();
    		foreach ($drivers as $driver) {
    			if ($i > 1) {
    				continue;
    			}
    			if(!in_array($driver['driver_id'],$test_drivers)){
	                continue;
	            }else{
	
	                $flag=QueueDispatchDriver::model()->insert($driver['driver_id']);
	                if(!$flag){
	                    continue;
	                }
                    
	                $this->PushAudioOrder($params['queue_id'] , $params['order_id'] , $driver['driver_id'] , $params['url']);
	                
                    $i++;
                    break;
	            }
    		}
    	} else {
    		$flag = OrderQueue::model()->setOrder2ManualOpt($params['queue_id'],'--附近没有司机，请手动派单--');
    		return false;
    	}
    	return true;
    }
    
    private function _pushCustomerMsg($phone , $driver_id , $queue_id) {
    	$driver = DriverStatus::model()->get($driver_id);
    	if (!$driver) {
    		return false;
    	}
    	
    	$client_message='欢迎预约e代驾!我们安排了%s代驾员%s(%s)司机为您服务,代驾员已出发.祝您一路平安!监督电话:4006913939';
    	$client_message=sprintf($client_message, $driver_id , $driver->info['name'], $driver->phone);
    	$data = array(
            'type' => GetuiPush::TYPE_MSG_CUSTOMER,
            'content' => $client_message,
            'level' => GetuiPush::LEVEL_HIGN,  //级别
            'driver_id' => $driver_id,
            'queue_id' => $queue_id,
            'phone' => $phone,
            'created' => date('Y-m-d H:i:s' , time()),
        );
        Sms::SendSMS($phone , $client_message);
        $result = $this->organizeMessagePush($data);
        return $result;
    }
    
    /**
	 * 推单---再次推送详情
	 * @param int $queue_id
	 * @param string $driver_id
	 * @return boolean
	 */
	public function PushOrderAgain($queue_id = 0 , $order_id = 0 , $driver_id = '') {
    	if (0 == $queue_id || 0 == $order_id || empty($driver_id)) {
    		return false;
    	}
    	
    	//验证数据有效性
    	$queue = OrderQueue::model()->findByPk($queue_id);
    	$condition = array(':queue_id' => $queue_id , ':order_id' => $order_id);
    	$map = OrderQueueMap::model()->find('queue_id = :queue_id and order_id = :order_id' , $condition);
		if ($queue && $map) {
			//改为直接推送详情
			$msg = $this->setPushOrderMsg($queue_id , $driver_id , $order_id);
			if (0 == $msg['code']) {
				$msg['msg']['order_id'] = $order_id;
				$channel = isset($queue->channel) ? $queue->channel : '';
				$ret = $this->_pushDetailMsg($queue_id , $driver_id , $msg['msg'] , $channel);
				return $ret;
			}
		}
		return false;
    }
    
    /**
     * 取消优惠券的绑定
     * @param int $order_id
     * @return boolean
     */
    private function _cancelBonus($order_id) {
    	$order = Order::model()->findByPk($order_id);
    	$result = BonusLibrary::model()->BonusUsed($order->phone, $order_id, 0, 2);
    	return $result;
    }
    
    private function _getSource($type , $channel) {
    	$source = $type;
    	if (CustomerApiOrder::QUEUE_CHANNEL_BOOKING == $channel) {
    		$source = CustomerApiOrder::CUSTOMER_BOOKING_CODE;
    	}
    	return $source;
    }
    
    /**
     * 判定是否为新订单(400、客户端下单为新订单)
     * @param string $channel
     * @param int $type
     * @return int $is_new
     */
    private function _isNewOrder($channel , $type , $name) {
    	$is_new = 0;
    	$channel_arr = array(
            CustomerApiOrder::QUEUE_CHANNEL_SINGLE_DRIVER,
            CustomerApiOrder::QUEUE_CHANNEL_SINGLE_CHANGE,
            CustomerApiOrder::QUEUE_CHANNEL_BOOKING,
	    CustomerApiOrder::QUEUE_CHANNEL_FIFTEEN_MIN_BOOKING,
	    CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER,
        );
        
        if (in_array($channel , $channel_arr)
	    || in_array($type , Order::$callcenter_sources)
	    || in_array($type , Order::$callcenter_input_sources)
	    || $name == OrderQueue::QUEUE_AGENT_KEYBOOKING ) {
        	$is_new = 1;
        }
        return $is_new;
    }

    /**
     * @author qiujianping@edaijia-staff.cn 2014-04-14
     *  Check it an order is locked
     *  This function has been fullfilled in DispatchDriver,
     *  But maybe it's better to rewrite it here.
     */
    private function _isOrderLocked($order_id) {
      $ret = false;
      $locked = QueueDispatchOrder::model()->exist($order_id);
      if( $locked == 1){
	$ret = true;
      }
      return $ret;
    }
    
    /**
     * 400呼叫中心派单(扔到队列)
     * @param int $queue_id
     * @param int $order_id
     * @param string $driver_id
     * @return boolean
     */
    public function callcenterDispatchOrder($queue_id , $order_id , $driver_id) {
    	if (empty($queue_id) || empty($order_id) || empty($driver_id)) {
    		return false;
    	}
    	$params = array(
    	    'queue_id'  => $queue_id,
    	    'order_id'  => $order_id,
    	    'driver_id' => $driver_id,
    	);
    	$task = array(
    	    'method' => 'callcenter_handle_dispatch',
    	    'params' => $params,
    	);
    	
    	Queue::model()->putin($task , 'task');
    	return true;
    }
    
    /**
     * 400手动派单走推送
     * @param int $queue_id
     * @param int $order_id
     * @param int $driver_id
     * @param array $team 多人订单指定组长 组员
     * @return boolean $result
     */
    public function PushNewOrder($queue_id , $order_id , $driver_id,
        $team=null) {
    	$result = false;
    	if (empty($queue_id) || empty($order_id) || empty($driver_id)) {
    		return $result;
    	}
    	
    	//获取推送消息
		$msg = $this->setPushOrderMsg($queue_id , $driver_id , $order_id);
		
		if (0 == $msg['code']) {
                    $msg['msg']['order_id'] = $order_id;

		    // 400手工派多人单时, 主从库同步需要时间
		    // setPushOrderMsg中获取的组长信息可能不正正确
		    // 所以调用时指定组员和组长
                    if($team !== null) {
		        if(isset($team['role']) && isset($team['leader_phone'])) {
			    $msg['msg']['role'] = $team['role'];
                            $msg['msg']['leader_phone'] = $team['leader_phone'];
			}
		    }
			
			//推送新订单详情格式
			$data = array(
	            'type' => GetuiPush::TYPE_ORDER_NEW_DETAIL,
	            'content' => $msg['msg'],
	            'level' => GetuiPush::LEVEL_MIDDLE,  //级别  修改为中级  BY AndyCong 2014-01-22
	            'offline_time' => 300,  //增加离线时间 BY AndyCong 2014-01-22
	            'driver_id' => $driver_id,
	            'queue_id' => $queue_id,
	            'created' => date('Y-m-d H:i:s' , time()),
	        );
	        
	        //发送失败记录请求次数
	        $result_push = $this->organizeMessagePush($data);
	        if($result_push){
	        	$result = true;
	        } else {
	        	$result = false;
	        }
		}
		return $result;
    }
    
    /**
     * 获取要取消的订单信息
     * @param int $order_id
     * @param string $order_number
     * @param string $driver_id
     * @return boolean
     */
    public function getCancelOrder($order_id , $order_number) {
    	$order = array();
    	if(!empty($order_id)){
		   $order = Order::model()->find('order_id = :order_id' , array(':order_id' => $order_id));
		}elseif (!empty($order_number)){
		   $order = Order::model()->find('order_number = :order_number' , array(':order_number' => $order_number));
		}
		return $order;
    }
    
    /**
     * 生成取消订单
     * @param array $params
     * @return $order_id
     */
    public function genCancelOrder($params) {
    	//验证是否为司机电话号
    	$is_driver = DriverStatus::model()->getByPhone($params['phone']);
    	if ($is_driver) {
    		return 1;
    	}
    	
    	//验证是否为白名单
        if(CustomerWhiteList::model()->in_whitelist($params['phone'])) {
    		return 1;
    	}
    	
    	$order_number = isset($params['order_number']) ? $params['order_number'] : '';
    	if (!empty($order_number)) {
    		$order = Order::model()->find('order_number = :order_number' , array(':order_number' => $order_number));
    		if ($order) {
    			return 1;
    		}
    	}
    	$driver = DriverStatus::model()->get($params['driver_id']);
    	if (!$driver) {
    		return 0;
    	}
    	$driver_id = $params['driver_id'];
    	$driver_name = $driver->info['name'];
    	$driver_imei = $driver->info['imei'];
    	$driver_phone = $driver->phone;
    	
		$name = '先生';
		$phone = $params['phone'];
		$contact_phone = isset($params['contact_phone']) ? $params['contact_phone'] : $params['phone'];
		$source = ORDER::SOURCE_CLIENT;
		$city_id = $driver->city_id;
		
		$time = time();
		$order_date = date('Ymd', $time);
		$call_time = $time;
		$booking_time = $time+1200;
		$location_start = '';
		$description = AutoOrder::SOURCE_CLIENT_MSG;
		$created = $time;
		
		$status = Order::ORDER_DRIVER_REJECT_CANCEL;
		$channel = CustomerApiOrder::QUEUE_CHANNEL_CALLORDER;
		
		$cancel_desc = isset($params['log']) ? $params['log'] : '';
		$cancel_type = isset($params['cancel_type']) ? intval($params['cancel_type']) : 0 ;
		
        $sql = 'insert into t_order (order_number,name,phone , contact_phone , source,driver,city_id,driver_id,driver_phone,
						imei,call_time,order_date,booking_time,location_start,description,created,channel , status , cancel_desc , cancel_type)
						values ("%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s",%s,"%s",%s,"%s" , %s)';
        $sql = sprintf($sql, $order_number , $name, $phone, $contact_phone , $source, $driver_name, $city_id, $driver_id, $driver_phone, $driver_imei, $call_time, $order_date , $booking_time, $location_start, $description, $created,$channel , $status , $cancel_desc , $cancel_type);
        Order::getDbMasterConnection()->createCommand($sql)->execute();
        
        $order_id = Order::getDbMasterConnection()->getLastInsertID();
        return $order_id;
    }
    
    /**
     * 获取要缓存的数据
     * @param object $queue
     * @param array $order_ids
     * @return array
     */
    public function _getCacheParamsByQueueObj($queue , $order_ids, 
        $bonus_arr = array(), $fees_arr = array(), $cash_only = 0)
    {
    	if (empty($queue) || empty($order_ids)) {
    		return array();
    	}
    	$number = isset($queue->number) ? $queue->number : 1;
    	$orders = array();
    	$i = 0;
    	foreach ($order_ids as $order_id) {
    		if (0 == $i && $number > 1) {
    			$role = '组长';
    		} else {
    			$role = '组员';
    		}

		$bonus_sn = '';
		if(!empty($bonus_arr) && !empty($bonus_arr[$order_id])) {
		    $bonus_sn =  $bonus_arr[$order_id];
		}

    		$orders[$order_id] = array(
    		    'order_id' => $order_id,
    		    'driver_id' => self::DEFAULT_DRIVER_INFO,
    		    'driver_phone' =>self::DEFAULT_DRIVER_INFO,
    		    'driver_name' =>self::DEFAULT_DRIVER_INFO,
                'location_start' => isset($queue['address']) ? $queue['address'] : '',
                'location_end' => '',
                'cash_only' => $cash_only ? 1 : 0, //1 是，0 否
                'pay_status' => 0, //0 未支付, 1 已支付
    		    'status' => Order::ORDER_READY ,
    		    'order_state' =>OrderProcess::ORDER_PROCESS_NEW,
                'order_states' => array(
                    array(
                        'order_state_code' => OrderProcess::ORDER_PROCESS_NEW,
                        'order_state_timestamp' => time(),
                        'order_state_content' => '正在联络司机',
                    ),  
                ),
    		    'role' =>$role,
		    'bonus_sn' => $bonus_sn,
		    'select_driver_id' => '',
		    'select_driver_name' => '',
    		);

		// Set remote order fee
		if(!empty($fees_arr) && isset($fees_arr[$order_id])) {
		    $orders[$order_id]['fee'] = $fees_arr[$order_id];
		}
    		$i++;
    	}
    	$params = array(
    	    'queue_id' => isset($queue->id) ? $queue->id : 0,
    	    'booking_id' => isset($queue->callid) ? $queue->callid : '',
    	    'booking_type' => (isset($queue->channel) && !empty($queue->channel)) ? $queue->channel : 0,
    	    'booking_time' => isset($queue->booking_time) ? $queue->booking_time : '',
    	    'created_time' => isset($queue->created) ? $queue->created : '',
    	    'phone' => isset($queue->phone) ? $queue->phone : '',
    	    'contact_phone' => isset($queue->contact_phone) ? $queue->contact_phone : '',
    	    'city_id' => isset($queue->city_id) ? $queue->city_id : 1,
    	    'number' => $number,
    	    'address' => isset($queue->address) ? $queue->address : '',
    	    'lng' => isset($queue->lng) ? $queue->lng : '0.000000',
    	    'lat' => isset($queue->lat) ? $queue->lat : '0.000000',
    	    'google_lng' => isset($queue->google_lng) ? $queue->google_lng : '0.000000',
    	    'google_lat' => isset($queue->google_lat) ? $queue->google_lat : '0.000000',
    	    'flag' => isset($queue->flag) ? $queue->flag : 1,
            'source' => isset($queue->type) ?$queue->type : 0,
    	    'ready_time' => 0,
    	    'orders' => $orders,
    	);
    	return $params;
    }
    
    public function getCacheParamsByQueueArr($queue_arr , $order_arr) {
    	if (empty($queue_arr) || empty($order_arr['order_id']) || empty($order_arr['driver_id'])) {
    		return array();
    	}
    	$driver=DriverStatus::model()->get($order_arr['driver_id']);
    	if (!$driver) {
    		return array();
    	}
    	$orders[$order_arr['order_id']] = array(
		    'order_id' => $order_arr['order_id'],
		    'driver_id' => $order_arr['driver_id'],
		    'driver_phone' => $driver->phone,
		    'driver_name' =>$driver->info['name'],
		    'status' => Order::ORDER_READY ,
		    'order_state' =>OrderProcess::ORDER_PROCESS_ACCEPT,
		    'role' =>'组员',
		    'bonus_sn' => '',
		    'select_driver_id' => '',
		    'select_driver_name' => '',
		);
		$orders[$order_arr['order_id']]['order_states'] = array();
		$orders[$order_arr['order_id']]['order_states'][] = array(
	        'order_state_code' => OrderProcess::ORDER_PROCESS_ACCEPT,
	        'order_state_content' => '司机已接单',
	        'order_state_timestamp' => time(),
	    );
    	$params = array(
    	    'queue_id' => isset($queue_arr['id']) ? $queue_arr['id'] : '',
    	    'booking_id' => isset($queue_arr['callid']) ? $queue_arr['callid'] : '',
    	    'booking_type' => isset($queue_arr['channel']) ? $queue_arr['channel'] : '',
    	    'booking_time' => isset($queue_arr['booking_time']) ? $queue_arr['booking_time'] : '',
    	    'phone' => isset($queue_arr['phone']) ? $queue_arr['phone'] : '',
    	    'contact_phone' => isset($queue_arr['contact_phone']) ? $queue_arr['contact_phone'] : '',
    	    'city_id' => isset($queue_arr['city_id']) ? $queue_arr['city_id'] : 1,
    	    'number' => isset($queue_arr['number']) ? $queue_arr['number'] : 1,
    	    'address' => isset($queue_arr['address']) ? $queue_arr['address'] : '暂未获取',
    	    'lng' => isset($queue_arr['lng']) ? $queue_arr['lng'] : '0.000000',
    	    'lat' => isset($queue_arr['lat']) ? $queue_arr['lat'] : '0.000000',
    	    'google_lng' => isset($queue_arr['google_lng']) ? $queue_arr['google_lng'] : '0.000000',
    	    'google_lat' => isset($queue_arr['google_lat']) ? $queue_arr['google_lat'] : '0.000000',
    	    'flag' => isset($queue_arr['flag']) ? $queue_arr['flag'] : 1,
    	    'ready_time' => 0,
    	    'orders' => $orders,
    	);
    	return $params;
    }
    
    /**
     * 推送消息给司机
     * @param array $params
     * @return obj 
     */
    public function PushMsgToDriver($params) {
    	$driver_id = isset($params['driver_id']) ? $params['driver_id'] : '';
    	$content = isset($params['content']) ? $params['content'] : '';
    	$category = isset($params['category']) ? $params['category'] : '';
    	if (empty($driver_id) || empty($content)) {
    		return false;
    	}
    	$params = array(
            'type' => GetuiPush::TYPE_MSG_DRIVER,
            'content' => $content,
            'level' => 1,
            'driver_id' => $driver_id,
            'created' => date('Y-m-d H:i:s'),
            'category' => $category,
        );
        $result = PushMessage::model()->organize_message_push($params);
        return $result;
    }
    
    /**
     * 验证有无相同电话号和工号的呼叫中心的单子 如果有则去重（将order_number写入400手动派单的订单）
     * @param array $params
     */
    public function validateIsRepeat($params) {
    	$driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';
    	$customer_phone = isset($params['phone']) ? trim($params['phone']) : '';
    	$order_number = isset($params['order_number']) ? trim($params['order_number']) : '';
    	$booking_time = isset($params['booking_time']) ? trim($params['booking_time']) : time();
    	if (empty($driver_id) || empty($customer_phone) || empty($order_number)) {
    		return false;
    	}
    	$start_time = $booking_time-7200;
    	$end_time = $booking_time+7200;
    	$order = Yii::app()->dborder_readonly->createCommand()
    	              ->select('order_id , order_number')
    	              ->from('t_order')
    	              ->where('driver_id=:driver_id and source IN (:source) and status = :status and (phone=:phone OR contact_phone=:contact_phone) and booking_time between :start_time and :end_time' , array(
    	              ':driver_id'=>$driver_id,
    	              ':source'=> join(',', Order::$callcenter_sources),
    	              ':status'=>Order::ORDER_READY,
    	              ':phone'=>$customer_phone,
    	              ':contact_phone'=>$customer_phone,
    	              ':start_time'=>$start_time,
    	              ':end_time'=>$end_time,
    	              ))
    	              ->queryRow();
    	if (!empty($order)) {
    		//更新order_number
    		$up = Order::model()->updateByPk($order['order_id'] , array('order_number' => $order_number));
    		
    		//查询queue_id
            $map = OrderQueueMap::model()->findByAttributes(
    		      array('order_id' => $order['order_id']),
                  array('select' => 'queue_id')
            );
    		
    		$queue_id = isset($map->queue_id) ? $map->queue_id : '';
    		if (empty($queue_id)) {
    			return true;
    		}
    		
    		//设置推送消息
    		$msg = Push::model()->setPushOrderMsg($queue_id , $driver_id , $order['order_id']);
    		if (!$msg || 1 == $msg['code']) {
	        	return true;
	        }
	        $msg['msg']['order_id'] = $order['order_id'];
            $msg['msg']['order_number'] = $order_number;
            $data = array(
	            'type' => GetuiPush::TYPE_ORDER_NEW_DETAIL,
	            'content' => $msg['msg'],
	            'level' => GetuiPush::LEVEL_HIGN,  //级别
	            'driver_id' => $driver_id,
	            'queue_id' => $queue_id,
	            'created' => date('Y-m-d H:i:s' , time()),
	        );
	        $this->organizeMessagePush($data);	
	        return true;
    	}
    	
    	return false;
    }
    
    /**
     * 获取路网距离
     * @param string $driver_id
     * @param float $customer_lng
     * @param float $customer_lat
     * @return string $dist
     * @version 2013-12-27
     */
    public function _getRouteDistanceSpeed($driver_id , $customer_lng , $customer_lat) {
    	$distance = '';
    	$driver = DriverStatus::model()->get($driver_id);
    	if ($driver) {
    		//获取司机坐标
    		$driver_lng = isset($driver->position['baidu_lng']) ? $driver->position['baidu_lng'] : '';
    		$driver_lat = isset($driver->position['baidu_lat']) ? $driver->position['baidu_lat'] : '';
    		
    		//验证坐标
    		if (intval($driver_lng) > 0 && intval($driver_lat) > 0) {
		    $distance = @Helper::Distance($driver_lat, $driver_lng, $customer_lat, $customer_lng);
		    $distance = $distance/1000;
    		}
    	}
    	return $distance;
    }
    
    private function updateOrderQueueMap($flag, $confirm_time, $driver_id, $queue_id, $order_id) {
        OrderQueueMap::model()->updateAll(
            array(
                'flag'          => $flag,
                'confirm_time'  => $confirm_time,
                'driver_id'     => $driver_id
            ),
            'queue_id = :queue_id AND order_id = :order_id',
            array(':queue_id' => $queue_id, ':order_id' => $order_id)
        );
    }
    
    private function findOrderQueueMapList($queue_id) {
        return OrderQueueMap::model()->findAll(array(
            'condition' => 'queue_id =:queue_id and confirm_time <> :confirm_time',
            'params'    => array(':queue_id' => $queue_id , ':confirm_time' => self::DEFAULT_TIME_FORMAT),
            'order'     => 'confirm_time ASC'
        ));
    }
}
