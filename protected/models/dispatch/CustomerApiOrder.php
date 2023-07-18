<?php
/**
 * 新版客户端接单、成单、派单
 * @author AndyCong<congming@edaijia-staff.cn
 * @version 2013-10-16
 */
Yii::import('application.models.pay.*');
class CustomerApiOrder{
    private static $_models;

    const SINGLE_PUSH_DRIVER = 1; //单人带工号直接推送给司机
    const SINGLE_CHANGE      = 2; //单人不带工号

    const QUEUE_CHANNEL_SINGLE_DRIVER = '01001'; //选司机下单
    const QUEUE_CHANNEL_SINGLE_CHANGE = '01002'; //换一个
    const QUEUE_CHANNEL_BOOKING       = '01003'; //一键预约
    const QUEUE_CHANNEL_CALLORDER     = '01004'; //电话订单
    const QUEUE_CHANNEL_DRIVER_INPUT  = '01005'; //开启新订单
    const QUEUE_CHANNEL_FIFTEEN_MIN_BOOKING  = '01006'; //15分钟自动下单
    const QUEUE_CHANNEL_REMOTEORDER  = '01007'; //远程叫单
    const QUEUE_CHANNEL_LEISHI        = '01011'; //雷石
    const QUEUE_CHANNEL_400_OR_OTHER  = '0';

    const QUEUE_MAX    = 5; //最大下单数量
    const CANCEL_QUEUE = 1;   //取消orderqueue
    const CANCEL_ORDER = 2;   //销单


    const CANCEL_REASON         = '客户取消'; //取消原因
    const CUSTOMER_BOOKING_CODE = 10;        //一键下单

    const POLLING_STATE_CONTINUE = 0; //继续拉取
    const POLLING_STATE_REJECT   = 1; //司机拒绝
    const POLLING_STATE_FINISH   = 2; //已派出司机

    const POLLING_SECOND_DRIVER   = 60; //选司机
    const POLLING_SECOND_CHANGE   = 90; //换一个
    const POLLING_SECOND_BOOKING  = 90; //一键下单
    const POLLING_SECOND_FIFTEEN_MIN_BOOKING  = 900; // 15分钟自动下单
    const POLLING_SECOND_400_ORDER = 2400; // 400订单polling超时时间40分钟

    const DISPATCH_BACK_TIME      = 600; //离预约多长时间弹回不派单
    const DISPATCH_BACK_TIME_ONE_ORDER        = 300; //一键下单下一单离预约多长时间弹回不派单
    const DISPATCH_BACK_TIME_FIFTEEN_MIN      = 300; //15分钟预约离预约多长时间弹回不派单

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
    
    public function isBlockCustomerByConfig($params) {
        if(empty($params['city_id']) || empty($params['phone'])) {
            return false;
        }
    
        $city_id = $params['city_id'];
        $phone = $params['phone'];
    
        if (in_array($city_id, Yii::app()->params['block_new_customer_cities'])) {
            $customer_order_report = CustomerOrderReport::model()->getCustomerOrder(array('phone' => $phone));
            if(empty($customer_order_report)) {
                EdjLog::info("customer $phone blocked due to new customer in city $city_id",'console');
                return true;
            }
        }
    
        if (in_array($city_id, Yii::app()->params['block_170_customer_cities'])
            && preg_match('/^170\d+/', $phone)) {
            EdjLog::info("customer $phone blocked due to 170 in city $city_id",'console');
            return true;
        }

        return false;
    }

    /**
     * 单人下单（包含有司机、无司机）
     * @param array $params
     * @return boolean $result
     */
    public function single_order($params) {

        $result = false;

        $phone = isset($params['phone']) ? $params['phone'] :'';
        $address = isset($params['address']) ? $params['address'] : '';
        $callid = isset($params['callid']) ? $params['callid'] : '';
        $driver_id = isset($params['driver_id']) ? $params['driver_id'] : '';
        $lng = isset($params['lng']) ? $params['lng'] : '';
        $lat = isset($params['lat']) ? $params['lat'] : '';
        $type = isset($params['type']) ? $params['type'] : '';
        if (empty($phone) || empty($address) || empty($callid) || empty($lng) || empty($lat)) {
            return $result;
        }

        if (self::SINGLE_PUSH_DRIVER == $params['type']) {  //直接推送
            $result = $this->_single_push_driver($params);
        } else {   //加入自动派单队列
            $result = $this->_single_change($params);
        }

        return $result;
    }


    /**
     * 直接推送(1、新建OrderQueue---对应好channel 2、新建Order及QueueMap 3、推送)
     *
     * 其实这里只需要知道order_id,queue_id ,就可以直接推送了。
     * @param unknown_type $params
     */
    private function _single_push_driver($params) {
        //新建OrderQueue
        $params['channel'] = self::QUEUE_CHANNEL_SINGLE_DRIVER;

        $queue = $this->save_order_queue($params);

        if (empty($queue)) {
            return false;
        }

        //生成订单及QueueMap
        $order = Push::model()->getDispatchOrder($queue['id']);
        if (empty($order)) {
            return false;
        }

        //指定司机推送
        $result = $this->_push_order($queue , $order[0]['order_id'] , $params['driver_id']);
        //推送失败 则取消订单  拉取订单直接返回该司机不能为您服务
        if (!$result) {
            $cache_key = 'receive_detail_'.$order[0]['order_id'];
            $is_dispatch = Yii::app()->cache->get($cache_key);
            if (!$is_dispatch) {
                $cache_value = 'dispatched';
                Yii::app()->cache->set($cache_key, $cache_value, 28800);
                $phone = isset($queue['phone']) ? $queue['phone'] : '';
                $queue_id = isset($queue['callid']) ? $queue['callid'] : '';
                $this->cancelQueueRedis($phone , $queue_id);
                $this->cancelOrderQueue($phone , $queue_id);
            }

            //输出log 以便查询订单号 司机工号
            $str = "order_id:".$order[0]['order_id']." AND driver_id:".$params['driver_id']." Push Failed";
            echo Common::jobEnd($str);
        }
        return $result;
    }

    /**
     *  Get order states by app version 
     */
    private function _getOrderAllStates($order_state, $order_states, $app_ver) {
	$ret = array('order_state' => $order_state,
		'order_all_states' => $order_states);

	$old_ver_state = $order_state;
    // 因为5.2以下的app版本不支持这个状态码，所以在这里判断版本过低的话就
    // 过滤掉了这个状态
	if($order_state == OrderProcess::PROCESS_DEST && 
		$app_ver < '5.2.0') {
	    $arr_tmp = array();
	    $old_ver_state = OrderProcess::ORDER_PROCESS_ACCEPT;
	    foreach ($order_states as $state) {
		if($state['order_state_code'] != OrderProcess::PROCESS_DEST &&
			OrderProcess::model()->validOrderState($old_ver_state,
			    $state['order_state_code'])) {
		    $old_ver_state = $state['order_state_code'];
		}

		if($state['order_state_code'] !=
			OrderProcess::PROCESS_DEST) {
		    array_push($arr_tmp , $state);
		} 
	    }
	    $ret = array('order_state' => $old_ver_state,
		    'order_all_states' => $arr_tmp);
	}

	return $ret;
    }

    /**
     * 保存OrderQueue
     * @param array $params
     * @param 类型 $type(app、app)
     * @param 接单调度 $agent_id
     * @return array $queue_arr
     */
    public function save_order_queue($params = array() , $type = Order::SOURCE_CLIENT , $agent_id = OrderQueue::QUEUE_AGENT_CLIENT) {

        if ( empty($params['phone']) || empty($params['city_id'])
            || empty($params['address']) || empty($agent_id) ) {

            return '';
        }

        //参数整理
        $time = time();
        $callid = isset($params['callid']) ? $params['callid'] : Common::genCallId();

        //判定是否为VIP
        $str = '';
        if ($agent_id != OrderQueue::QUEUE_AGENT_CLIENT) {
            $str = ",该单是".$agent_id;
        }
        //取vip的方法修改了，原来太啰嗦。封装的不好，用的也不对。modify by sunhongjing 2013-12-29
        $vipInfo = VipPhone::model()->getVipInfoByPhone($params['phone'],$need_balance=true);

        if ( !empty($vipInfo) ) {
            $comments = "此用户是vip,余额:{$vipInfo['total_balance']}".$str;
        } else {
            $comments = substr($str,1);
        }
        //判定是否为VIP END

        $lng = isset($params['lng']) ? $params['lng'] : '';
        $lat = isset($params['lat']) ? $params['lat'] : '';
        $channel = isset($params['channel']) ? $params['channel'] : self::QUEUE_CHANNEL_SINGLE_DRIVER;

        $name = $this->getCustomerName($params['phone']);
        $queue_arr = array(
            'phone' => $params['phone'],
            'contact_phone' => isset($params['contact_phone']) ? $params['contact_phone'] : $params['phone'],
            'city_id' => $params['city_id'],
            'callid' => $callid,
            'name' => $name,
            'number' => isset($params['number']) ? $params['number'] : 1,
            'dispatch_number' => isset($params['dispatch_number']) ? $params['dispatch_number'] : 0,
            'address' => $params['address'],
            'comments' => $comments,
            'booking_time' => isset($params['booking_time']) ? $params['booking_time'] : date('Y-m-d H:i:s' ,$time+1200),
            'flag' => isset($params['flag']) ? $params['flag'] : OrderQueue::QUEUE_WAIT_COMFIRM,
            'type' => $type ,
            'update_time' => '',
            'agent_id' => $agent_id,
            'dispatch_agent' => isset($params['dispatch_agent']) ? $params['dispatch_agent'] : '',
            'dispatch_time' => isset($params['dispatch_time']) ? $params['dispatch_time'] : '',
            'created' => date('Y-m-d H:i:s' , $time),
            'lng' => $lng,
            'lat' => $lat,
            'channel' => $channel,
        );
        $model = new OrderQueue();
        $model->attributes = $queue_arr;
        $model->lng = $lng;
        $model->lat = $lat;
        $model->google_lng = isset($params['google_lng']) ? $params['google_lng'] : '0.000000';
        $model->google_lat = isset($params['google_lat']) ? $params['google_lat'] : '0.000000';
        $model->channel = $channel;
        $result = $model->save();
        if ($result) {
            $queue_arr['id'] = $model->id;
            $queue_arr['google_lng'] = $model->google_lng;
            $queue_arr['google_lat'] = $model->google_lat;
            if (isset($params['call_time'])) {
                $queue_arr['call_time'] = $params['call_time'];
            } else {
                $queue_arr['call_time'] = $time;
            }
            return $queue_arr;
        } else {
            return '';
        }
    }

    /**
     * 多人下单(加上nearby取出司机司机直接派)
     * @param array $params
     * @return boolean
     */
    private function _single_change($params) {
        $params['channel'] = self::QUEUE_CHANNEL_SINGLE_CHANGE;
	$source = isset($params['source']) ? $params['source'] : Order::SOURCE_CLIENT;
        $queue = $this->save_order_queue($params , $source , OrderQueue::QUEUE_AGENT_KEYBOOKING);
        if (empty($queue)) {
            return false;
        }
        $queue_id = isset($queue['id']) ? $queue['id'] : '';

        //增加优惠券 2014-03-19
        $bonus_sn = isset($params['bonus_sn']) ? $params['bonus_sn'] : '';
        if(!empty($bonus_sn)) {  //有优惠券 将优惠券加到缓存中
            QueueDispatchOrder::model()->queueBonusBind($queue_id , $bonus_sn);
        }

        $result = $this->multi_push($queue_id , $params);
        return $result;
    }

    /**
     * 将order信息加入到缓存
     * @param array $params
     * @return boolean
     */
    public function insert_order_cache($params) {
        $queue_id = isset($params['queue_id']) ? $params['queue_id'] : '';
        $order_ids = isset($params['order_ids']) ? $params['order_ids'] : '';
        if (empty($queue_id) || empty($order_ids)) {
            return false;
        }

        $queue = OrderQueue::model()->findByPk($queue_id);
        $orders = $this->_getOrders($order_ids , $queue->number ,$queue_id);
        if (empty($queue) || empty($orders)) {
            return false;
        }

        $key = $queue->phone."_".$queue->callid;
        $data = array(
            'queue_id' => $queue_id,
            'booking_id' => $queue->callid,
            'booking_type' => isset($queue->channel) ? $queue->channel : '',
            'booking_time' => $queue->booking_time,
            'city_id' => $queue->city_id,
            'number' => $queue->number,
            'address' => $queue->address,
            'lng' => $queue->lng,
            'lat' => $queue->lat,
            'google_lng' => $queue->google_lng,
            'google_lat' => $queue->google_lat,
            'flag' => $queue->flag,
            'ready_time' => 0,
            'orders' => $orders,
        );
        QueueApiOrder::model()->insert($key , $data);
        return true;
    }

    /**
     * 将order信息加入到Redis（取代insert_order_cache）
     * @param array $params
     * @return boolean
     */
    public function insert_orders_redis($params) {
        if (empty($params)) {
            return false;
        }
        $phone = isset($params['phone']) ? $params['phone'] : '';
        $call_id = isset($params['booking_id']) ? $params['booking_id'] : '';
        if (empty($phone) || empty($call_id)) {
            return false;
        }
        $key = $phone."_".$call_id;
        QueueApiOrder::model()->insert($key , $params);
        return true;
    }

    /**
     * 获取订单（redis存储格式）
     * @param array $order_ids
     * @param int $number
     * @return array $orders
     */
    private function _getOrders($order_ids , $number , $queue_id) {
        $orders = array();
        $i = 0;
        foreach ($order_ids as $order_id) {
            $order = Order::model()->findByPk($order_id);
            if (0 == $i && $number > 1) {
                $role = '组长';
            } else {
                $role = '组员';
            }
            $orders[$order_id] = array(
                'order_id' => $order_id,
                'driver_id' =>$order->driver_id,
                'driver_phone' =>$order->driver_phone,
                'driver_name' =>$order->driver,
                'status' =>$order->status,
                'order_state' =>OrderProcess::ORDER_PROCESS_NEW,
                'role' =>$role,
            );
            $i++;
        }
        return $orders;
    }

    /**
     * 获取对应订单
     * @param string $phone
     * @param string $booking_id
     */
    public function getOrderByBookingID($phone , $booking_id , $timeout = self::POLLING_SECOND_DRIVER) {
        $order = array('driver_id' => '' , 'order_id' => '' , 'polling_state' => self::POLLING_STATE_CONTINUE);
        if (empty($phone) || empty($booking_id) || empty($timeout)) {
            return $order;
        }

        $key = $phone."_".$booking_id;
        $booking_type = QueueApiOrder::model()->get($key , 'booking_type');
        $number = QueueApiOrder::model()->get($key , 'number');
        $flag = QueueApiOrder::model()->get($key , 'flag');
        if ($booking_type == self::QUEUE_CHANNEL_SINGLE_DRIVER && $flag == OrderQueue::QUEUE_CANCEL) {
            $order['polling_state'] = self::POLLING_STATE_REJECT;
        }
        $orders = QueueApiOrder::model()->get($key , 'orders');
        $i = 0;
        if (!empty($orders)) {
            foreach ($orders as $val) {
                if (0 == $i) {
	            //伪造数据
	            if(isset($val['fake']) && $val['fake'] == 'yes') {
                        return $order;
	            }

                    $order['driver_id'] = isset($val['driver_id']) ? $val['driver_id'] : '';
                    $order['order_id'] = isset($val['order_id']) ? $val['order_id'] : '';
                    if ($val['driver_id'] != Push::DEFAULT_DRIVER_INFO) {
                        $order['polling_state'] = self::POLLING_STATE_FINISH;
                    } else {
                        //判定是否已到超时时间，超过超时时间则自动取消
                        $booking_time = QueueApiOrder::model()->get($key , 'booking_time');

                        //添加保障机制 2013-12-07
			if($booking_type == self::QUEUE_CHANNEL_SINGLE_DRIVER) {
			    $polling_time = time() - (strtotime($booking_time)-600);
			}
			elseif($booking_type == self::QUEUE_CHANNEL_SINGLE_CHANGE) {
			    $polling_time = time() - (strtotime($booking_time)-600);
			}
			elseif($booking_type == self::QUEUE_CHANNEL_BOOKING
			    && !empty($number) && $number == 1) {
			    $polling_time = time() - (strtotime($booking_time)-600);
			} else {
			    $polling_time = time() - (strtotime($booking_time)-1200);
			}
                        //判定是最后一次请求则验证是否要取消订单
			//booking type为0是400订单,要避免客户端取消400订单
                        if ($polling_time >= $timeout-5) {
                            //判定订单有没有被接单 接单后不能取消 取消后也不能让司机接单
                            $cache_key = 'receive_detail_'.$order['order_id'];
                            $is_dispatch = Yii::app()->cache->get($cache_key);
                            if (!$is_dispatch) {
                                $cache_value = 'dispatched';
                                Yii::app()->cache->set($cache_key, $cache_value, 28800);
                                $this->cancelQueueRedis($phone , $booking_id);
                                $task = array(
                                    'method' => 'api_queue_cancel',
                                    'params' => array(
                                        'phone' => $phone,
                                        'booking_id' => $booking_id,
                                    ),
                                );
                                Queue::model()->putin($task , 'apporder');

                                $activity_task = array(
                                    'method' => 'customer_cancel_activity',
                                    'params' => array(
                                        'phone' => $phone,
                                    ),
                                );
                                Queue::model()->putin($activity_task , 'activity');
                                
                                $order['polling_state'] = self::POLLING_STATE_REJECT;
                            } else {
                                //如果值不是dispatched 说明派单失败了
                                if ($is_dispatch == 'dispatched') {
                                    $order['polling_state'] = self::POLLING_STATE_REJECT;
                                } else { //司机已经接单了  将接单的司机工号返回
                                    $order['driver_id'] = $is_dispatch;
                                    $order['polling_state'] = self::POLLING_STATE_FINISH;
                                    //做个保障 再次修改订单
                                    $this->_updateOrderRedis($key , $order['order_id'] , $order['driver_id']);
                                    break;
                                }
                            }
                        } elseif ($polling_time >= 30) {  //如果拉取超过30s 则判定有无接单成功
                            $cache_key = 'receive_detail_'.$order['order_id'];
                            $is_dispatch = Yii::app()->cache->get($cache_key);
                            if ($is_dispatch && $is_dispatch != 'dispatched') {
                                $order['driver_id'] = $is_dispatch;
                                $order['polling_state'] = self::POLLING_STATE_FINISH;
                                //做个保障 再次修改订单
                                $this->_updateOrderRedis($key , $order['order_id'] , $order['driver_id']);
                                break;
                            }
                        }
                        //添加保障机制 2013-12-07 END
                    }
                }
                $i++;
            }
        }
        //LOG FOR SPEEDUP by wangjian		
	if( $order['polling_state'] != self::POLLING_STATE_CONTINUE ) {		
	  EdjLog::info("LOG FOR SPEEDUP|".$order['order_id'].'|'		
	      .$order['driver_id'].'|客户端获取接单或拒单状态|end');		
	}
        return $order;
    }

    /**
     * 获取司机司机位置信息
     * @param string $driver_id
     * @param string $gps_type
     * @param string $phone
     * @param string $booking_id
     * @return array
     */
    public function getDriverPosition($driver_id , $gps_type , 
	    $phone , $booking_id, $order_id = '', $app_ver = '0') {
        if (empty($driver_id) || empty($gps_type) || empty($phone) || empty($booking_id)) {
            return array();
        }
        $key = $phone."_".$booking_id;
        $cache = QueueApiOrder::model()->getallfields($key);

        if (empty($cache)) {
            return array();
        }

	$booking_type = isset($cache['booking_type'])?
	    $cache['booking_type']:self::QUEUE_CHANNEL_BOOKING;
	$city_id = isset($cache['city_id'])?$cache['city_id']:0;
        $orders = isset($cache['orders']) ? json_decode($cache['orders'] , true) : '';
        if (empty($orders)) {
            return array();
        }

        //默认角色通过预约人数判定
        if (count($orders) > 1) {
            $role = '组长';
        } else {
            $role = '组员';
        }
        //默认角色通过预约人数判定 END

        $order_state = '已接单';
        $wait_time = '';
        $cancel_type = '';
        $order_id = '';
        $order_state_code = OrderProcess::ORDER_PROCESS_ACCEPT;
        $order_states = array(
            0=> array(
                'order_state_code' => OrderProcess::ORDER_PROCESS_ACCEPT,
                'order_state_timestamp' => time(),
                'order_state_content' => '司机已接单'
            ),
        );

	$remote_order_data = array();
        foreach ($orders as $order) {
            if ($driver_id == $order['driver_id'] && 
		    ( empty($order_id) || 
		      (!empty($order_id) && $order['order_id'] == $order_id))) {
                $role = $order['role'];
                $order_states = isset($order['order_states']) ?
		    $order['order_states'] : $order_states;
		$states_data = $this->_getOrderAllStates(
			$order['order_state'], $order_states, $app_ver);

                $order_state_code = $states_data['order_state'];
		$order_states = $states_data['order_all_states']; 

                $order_state = $this->_getOrderStateInfo($order['order_state']);
                $ready_time = isset($order['ready_time']) ? $order['ready_time'] : '';

                $cancel_type = isset($order['cancel_type']) ? $order['cancel_type'] : '';
                $order_id = $order['order_id'];

		// Set data for remote order
		$accept_time = '';
                $drive_time = '';
                if (!empty($order_states)) {
                    foreach ($order_states as $key=>$state) {
			if ($state['order_state_code'] == OrderProcess::ORDER_PROCESS_ACCEPT) {
			    $accept_time = $state['order_state_timestamp'] ;
			}
                        if ($state['order_state_code'] == OrderProcess::ORDER_PROCESS_DRIVING) {
                            $drive_time = $state['order_state_timestamp'] ;
                        }
                    }
                }

		// For remote order, wait time should be later than expect ready time 
		$expect_ready_time = $cache['booking_time'];
		$expect_ready_time_cost = 0;
		if(isset($order['ready_dist']) && $booking_type == self::QUEUE_CHANNEL_REMOTEORDER) {
		    $expect_value = FinanceConfigUtil::remoteOrderConfig($city_id, $order['ready_dist']);
		    $expect_ready_time_cost = isset($expect_value['readyTime'])?$expect_value['readyTime']:0;
		    $expect_ready_time = date('Y-m-d H:i:s', $accept_time + $expect_ready_time_cost);
		    if(strcmp($cache['booking_time'], $expect_ready_time) > 0) {
			$expect_ready_time = $cache['booking_time'];
		    }
		}
                $wait_time = $this->_getWaitTime($order_state_code , 
			$ready_time, $expect_ready_time, $drive_time);
                if ($order['order_state'] == OrderProcess::ORDER_PROCESS_READY ) {
                    $order_state .= ' 等候'.$wait_time."分钟";
                }

                //如果有已到达的状态 要获取等候时间
                if (!empty($order_states)) {
                    foreach ($order_states as $key=>$state) {
                        if ($state['order_state_code'] == OrderProcess::ORDER_PROCESS_READY) {
                            $order_states[$key]['order_state_content'] = '司机已到达 等候'.intval($wait_time).'分';
                        }
                    }
                }
                //如果有已到达的状态 要获取等候时间 END

		if(isset($order['subsidy'])) {
		    $remote_order_data['subsidy'] = $order['subsidy'];
		}

		if(isset($order['ready_dist'])) {
		    $remote_order_data['ready_dist'] = $order['ready_dist'];
		    $remote_order_data['is_remote'] = 1;
		    $remote_order_data['ready_intime'] = 1;
		    if(!empty($accept_time) && !empty($ready_time)) {
			$ready_time_cost = strtotime($ready_time) - $accept_time;
			if($ready_time_cost > $expect_ready_time_cost) {
			    $remote_order_data['ready_intime'] = 0;
			    $remote_order_data['subsidy'] = 0;
			}
		    }
		}

		if(isset($order['fee'])) {
		    $remote_order_data['fee'] = $order['fee'];
		}

                unset($tmp_arr);
            }
        }

        $driver = Helper::foramt_driver_detail($driver_id , $gps_type, 0, 'default');
        if (empty($driver)) {
            return array();
        }
        $driver['role'] = $role;
        $driver['source'] = isset($cache['source']) ? $cache['source'] : Order::SOURCE_CLIENT;
        $driver['customer_lng'] = $cache['lng'];
        $driver['customer_lat'] = $cache['lat'];
        if ($gps_type == 'google' || $gps_type == 'wgs84') {
            $driver['customer_lng'] = (isset($cache['google_lng']) && $cache['google_lng'] != '0.000000') ? $cache['google_lng'] : $cache['lng'];
            $driver['customer_lat'] = (isset($cache['google_lat']) && $cache['google_lat'] != '0.000000') ? $cache['google_lat'] : $cache['lat'];
        }

		//Add realtime_distance  2014-12-22
		$prefix = 'LAST_POSITION';
		$hashKey = $prefix."_".$driver_id;
		$data = DriverPositionService::getInstance()->getPositionData($hashKey, 'data');
		if(isset($driver['customer_lat']) && isset($driver['customer_lng']) 
				&& ($driver['customer_lng'] + $driver['customer_lat'] > 10))
		{
			if(!empty($data))
			{
				$data_arr = json_decode($data, True);
				$gps_data = $data_arr['gps'];
				$distance = @Helper::Distance($gps_data['baidu_lat'], $gps_data['baidu_lng'], 
					$driver['customer_lat'], $driver['customer_lng']);
				$driver['realtime_distance'] = $distance;
				EdjLog::info("getDriverPosition:realtime_distance=".$distance.".\n");
			}
			else
				EdjLog::info("getDriverPosition:Get driver ".$driver_id." gps position error.\n");
		}
		else
			EdjLog::info("getDriverPosition:customer lat: ".$driver['customer_lat']." or lng: ".$driver['customer_lng']." nonvalid.\n");

        $driver['order_state'] = $order_state;
        $driver['wait_time']   = $wait_time;

        $driver['cancel_type'] = $cancel_type;
        $driver['order_id'] = $order_id;
        $driver['order_state_code'] = $order_state_code;
        $driver['order_all_states'] = $order_states;

        if (isset($order['location_start'])) {
            $driver['location_start'] = $order['location_start'];
        }

        if (isset($order['location_end'])) {
            $driver['location_end'] = $order['location_end'];
        }

        if (isset($order['pay_status'])) {
            $driver['pay_status'] = $order['pay_status'];
        }

        if (isset($order['cash_only'])) {
            $driver['cash_only'] = $order['cash_only'];
        }

	// Set data fro remote order data
	if(isset($remote_order_data['ready_dist'])) {
	    $driver['ready_dist'] = round($remote_order_data['ready_dist']);
	}

	if(isset($remote_order_data['ready_intime'])) {
	    $driver['ready_intime'] = $remote_order_data['ready_intime'];
	}

	if(isset($remote_order_data['is_remote'])) {
	    $driver['is_remote'] = $remote_order_data['is_remote'];
	}

	if(isset($remote_order_data['subsidy'])) {
	    $driver['subsidy'] = $remote_order_data['subsidy'];
	}

	if(isset($remote_order_data['fee'])) {
	    $driver['fee'] = $remote_order_data['fee'];
	}
        return $driver;
    }

    /**
     * 获取我的司机
     * @param string $phone
     * @return $drivers
     */
    public function getMyDrivers($phone , $gps_type, $app_ver = '0')
    {
        if (empty($phone) || empty($gps_type)) {
            return array();
        }

        $drivers = array();
        // 2代表什么？不要在代码里写magic number
        $polling_state = 2;
        $cache = QueueApiOrder::model()->getallorders($phone);
        $state_arr = array(
            OrderProcess::ORDER_PROCESS_NEW,
            OrderProcess::ORDER_PROCESS_ACCEPT,
            OrderProcess::ORDER_PROCESS_READY,
            OrderProcess::ORDER_PROCESS_DRIVING,
            OrderProcess::PROCESS_DEST,
        );

        if (!empty($cache)) {
            foreach ($cache as $queue) {
	            //洗车业务不显示
                if(isset($queue['source'])
                   && in_array($queue['source'], Order::$washcar_sources)
                ) {
		            continue;
		        }
		
                if ($queue['flag'] == OrderQueue::QUEUE_CANCEL) {
                    continue;
                }

                $orders = isset($queue['orders']) ? json_decode($queue['orders'] , true) : '';
		        $created_time = isset($queue['created_time'])? $queue['created_time']:'';
		        $address = isset($queue['address'])? $queue['address']:'';
		        $contact_phone = isset($queue['contact_phone'])? $queue['contact_phone']:'';
                $tmp = array(
                    'booking_id' => $queue['booking_id'],
                    'booking_time' => $queue['booking_time'],
                    'created_time' => $created_time,
                    'contact_phone' => $contact_phone,
                    'address' => $address,
                    'booking_type' => $queue['booking_type'],
                    'lat' => $queue['lat'],
                    'lng' => $queue['lng'],
                    'number' => $queue['number'],
                    'orders' => array(),
                );

                $i = 0;
                $dispatch_i = 0;
                $is_fake = 0;
                if (!empty($orders)) {
                    foreach($orders as $order) {
                        if(isset($order['fake']) && $order['fake'] == 'yes') {
                            $is_fake = 1;
	                    }

                        if ($order['order_state'] == OrderProcess::ORDER_PROCESS_DISPATCH 
                            || $order['order_state'] == OrderProcess::ORDER_PROCESS_NEW
                        ) {
                            $dispatch_i += 1;
                        }

                        if ($order['driver_id'] == Push::DEFAULT_DRIVER_INFO
                            && $order['order_state'] != OrderProcess::ORDER_PROCESS_USER_DESTORY 
                            && $order['order_state'] != OrderProcess::ORDER_PROCESS_USER_CANCEL
                        ) {
                            $tmp['orders'][$order['order_id']] = $this->format_driver_info($order);
                            $tmp['orders'][$order['order_id']]['order_all_states'] = 
                                isset($order['order_states']) ? $order['order_states'] : array();
			            }

                        $order_state_code = $order['order_state'];
                        $order_states = isset($order['order_states']) ? $order['order_states'] : array();
                        $states_data = $this->_getOrderAllStates($order_state_code, $order_states, $app_ver);

                        $order_state_code = $states_data['order_state'];
                        $order_states = $states_data['order_all_states'];

                        // In running order
                        if ($order['status'] == 0 
                            && in_array($order['order_state'] , $state_arr) 
                            && $order['driver_id'] != Push::DEFAULT_DRIVER_INFO
                        ) {
                            //需要验证返回值，因为返回有可能为null
                            $driver = Helper::foramt_driver_detail($order['driver_id'] , $gps_type, 0, 'default');
                            if (!empty($driver)) {
                                $dispatch_i += 1;
                                $order_state = $this->_getOrderStateInfo($order_state_code);
                                $driver['order_id']    = $order['order_id'];
                                $driver['role']        = $order['role'];
                                $driver['order_state'] = $order_state;
                                $driver['order_state_code'] = $order_state_code;

                                $driver['location_start'] = isset($order['location_start']) ? $order['location_start'] : '';
                                $driver['location_end'] = isset($order['location_end']) ? $order['location_end'] : '';
                                $driver['pay_status'] = isset($order['pay_status']) ? $order['pay_status'] : '';
                                $driver['cash_only'] = isset($order['cash_only']) ? $order['cash_only'] : '';

                                $driver['source'] = isset($queue['source']) ? $queue['source'] : Order::SOURCE_CLIENT;

                                $ready_time = isset($order['ready_time']) ? $order['ready_time'] : '';

                                //Add realtime_distance  2014-12-22
                                $prefix = 'LAST_POSITION';
                                $hashKey = $prefix."_".$order['driver_id'];
                                $data = DriverPositionService::getInstance()->getPositionData($hashKey, 'data');
                                if(isset($queue['lat'])
                                   && isset($queue['lng']) 
                                   && ($queue['lng'] + $queue['lat'] > 10)
                                ) {
                                    if(!empty($data)) {
							            $data_arr = json_decode($data, True);
							            $gps_data = $data_arr['gps'];
							            $distance = @Helper::Distance($gps_data['baidu_lat'], $gps_data['baidu_lng'], $queue['lat'], $queue['lng']);
                                        $driver['realtime_distance'] = $distance;
                                        EdjLog::info("getMyDrivers:realtime_distance=".$distance.".\n");
                                    } else {
							            EdjLog::info("getMyDrivers:Get driver ".$order['driver_id']." gps position error.\n");
                                    }
					            } else {
							        EdjLog::info("getMyDrivers:queue lat: ".$queue['lat']." or lng: ".$queue['lng']." nonvalid.\n");
                                }

                                // Set data for remote order
                                $accept_time = '';
                                $drive_time = '';
                                foreach ($order_states as $key=>$state) {
                                    if ($state['order_state_code'] == OrderProcess::ORDER_PROCESS_ACCEPT) {
                                        $accept_time = $state['order_state_timestamp'] ;
                                    }
                                    if ($state['order_state_code'] == OrderProcess::ORDER_PROCESS_DRIVING) {
                                        $drive_time = $state['order_state_timestamp'] ;
                                    }
                                }

                                $expect_ready_time = $queue['booking_time'];
                                $expect_ready_time_cost = 0;
                                if(isset($order['ready_dist']) && $queue['booking_type'] == self::QUEUE_CHANNEL_REMOTEORDER) {
                                    $expect_value = FinanceConfigUtil::remoteOrderConfig($queue['city_id'], $order['ready_dist']);
                                    $expect_ready_time_cost = isset($expect_value['readyTime'])?$expect_value['readyTime']:0;
                                    $expect_ready_time = date('Y-m-d H:i:s', $accept_time + $expect_ready_time_cost);
                                    if(strcmp($queue['booking_time'], $expect_ready_time) > 0) {
                                        $expect_ready_time = $queue['booking_time'];
                                    }
                                }

                                $driver['wait_time'] = $this->_getWaitTime($order_state_code , 
                                                                           $ready_time,
                                                                           $expect_ready_time,
                                                                           $drive_time
                                                                          );

				                if(isset($order['subsidy'])) {
					                $driver['subsidy'] = $order['subsidy'];
				                }

				                if(isset($order['ready_dist'])) {
					                $driver['ready_dist'] = round($order['ready_dist']);
                                    $driver['is_remote'] = 1;
                                    $driver['ready_intime'] = 1;
                                    if(!empty($accept_time) && !empty($ready_time)) {
                                        $ready_time_cost = strtotime($ready_time) - $accept_time;
                                        //$expect_value = FinanceConfigUtil::remoteOrderConfig($queue['city_id'], $order['ready_dist']);
                                        //$expect_ready_time = isset($expect_value['readyTime'])?$expect_value['readyTime']:0;
                                        if($ready_time_cost > $expect_ready_time_cost) {
                                            $driver['ready_intime'] = 0;
                                            $driver['subsidy'] = 0;
                                        }
                                    }
                                }

                                if(isset($order['fee'])) {
                                    $driver['fee'] = $order['fee'];
                                }

                                if ($order['order_state'] == OrderProcess::ORDER_PROCESS_READY ) {
                                    $driver['order_state'] .= ' 等候'.$driver['wait_time']."分钟";
                                }
                                
                                $tmp['orders'][$order['order_id']] = $driver;

                                $tmp['orders'][$order['order_id']]['order_all_states'] = $order_states;

                                $i++;
                            }
                        } elseif ($order['order_state'] != OrderProcess::ORDER_PROCESS_USER_DESTORY
                                  && $order['order_state'] != OrderProcess::ORDER_PROCESS_USER_CANCEL
                                  && $order['order_state'] != OrderProcess::ORDER_PROCESS_FINISH
                                  && $order['order_state'] != OrderProcess::ORDER_PROCESS_DRIVER_DESTORY
                        ) {
                            $tmp['orders'][$order['order_id']] = $this->format_driver_info($order);
                            $tmp['orders'][$order['order_id']]['order_all_states'] = $order_states;
                        }
                    }
                }

                if (!empty($tmp['orders']) && $dispatch_i != 0) {
                    ksort($tmp['orders']);
                    $tmp['orders'] = array_values($tmp['orders']);
			        if($is_fake == 1) {
                        $tmp['fake'] = 1;
			        }
                    $drivers[$queue['queue_id']] = $tmp;
                }

                $dispatch_number = $i;
                if ($dispatch_number != $queue['number'] && $dispatch_number != 0 && $queue['flag'] != OrderQueue::QUEUE_SUCCESS) {
                    $polling_state = 0;
                }
            }
        }

        if (!empty($drivers)) {
            krsort($drivers);
            $drivers = array_values($drivers);
        }

        $data = array(
            'drivers' => $drivers,
            'polling_state' => $polling_state,
        );

        return $data;
    }

    /**
     * 取消
     * @param string $phone
     * @param string $booking_id
     * @return boolean
     */
    public function cancelQueueRedis($phone , $booking_id , $up_flag = OrderQueue::QUEUE_CANCEL , $driver_reject = false) {
	$ret =  array('result'=>false);
        if (empty($phone) || empty($booking_id)) {
            return $ret;
        }

        //10分钟未全部派出则默认派单完成
        $key = $phone."_".$booking_id; //缓存key
        if ($up_flag == OrderQueue::QUEUE_SUCCESS) {
            $this->_sysDispatchFinished($key);
            return array('result'=>true, 'order_ids' => array());
        }

        //司机拒单
        if ($driver_reject) {
            $this->_driverRejectNoDispatched($key);
            return array('result'=>true, 'order_ids' => array());
        }

        $result = $this->_customerCancel($key);
        return $result;
    }

    /**
     * 系统派单完成（将OrderQueue置成已派单完成）
     * @modified qiujianping@edaijia-inc.cn 2014-04-03
     *	Add order process to state machine
     * @param string $key
     * @return boolean
     */
    private function _sysDispatchFinished($key) {
        $flag = QueueApiOrder::model()->get($key , 'flag');
        if($flag) {
            QueueApiOrder::model()->update($key , 'flag' , OrderQueue::QUEUE_SUCCESS);
        }
        $queue_id = QueueApiOrder::model()->get($key , 'queue_id');
        $orders = QueueApiOrder::model()->get($key , 'orders');
        if (empty($orders)) {
            return false;
        }
        foreach ($orders as $order) {
            if ($order['driver_id'] == Push::DEFAULT_DRIVER_INFO) {
                $orders[$order['order_id']]['order_state'] = OrderProcess::ORDER_PROCESS_SYS_CANCEL;
	  
		// Save the order state machine
		$process_params = array();
		$process_params['queue_id'] = $queue_id;
		$process_params['order_id'] = $order['order_id'];
		$process_params['state'] = OrderProcess::PROCESS_SYS_CANCEL;
		$process_params['driver_id'] = Push::DEFAULT_DRIVER_INFO;
		$process_params['created'] = date('Y-m-d H:i:s' , time());
		// TODO: Check if the fail type is valid
		$process_params['fail_type'] = OrderProcess::DISPATCH_FAIL_SYS_OUT_OF_TIME;
		// We just ignore if the process is success or failed
		OrderProcess::model()->genNewOrderProcess($process_params);
            }
        }
        QueueApiOrder::model()->update($key , 'orders' , $orders);
        return true;
    }

    /**
     * 系统派单完成（将OrderQueue置成已派单完成）
     * @param string $key
     * @return boolean
     */
    private function _driverRejectNoDispatched($key) {
        $flag = QueueApiOrder::model()->get($key , 'flag');
        if($flag) {
            QueueApiOrder::model()->update($key , 'flag' , OrderQueue::QUEUE_CANCEL);
        }
        $queue_id = QueueApiOrder::model()->get($key , 'queue_id');
        $orders = QueueApiOrder::model()->get($key , 'orders');
        if (empty($orders)) {
            return false;
        }
        foreach ($orders as $order) {
            if ($order['driver_id'] == Push::DEFAULT_DRIVER_INFO) {
                $orders[$order['order_id']]['order_state'] = OrderProcess::ORDER_PROCESS_DRIVER_CANCEL;
		OrderProcess::model()->genNewOrderProcess(
		    array( 'queue_id'  => $queue_id,
		      'order_id'  => $order['order_id'],
		      'driver_id' => Push::DEFAULT_DRIVER_INFO,
		      'state'     => OrderProcess::PROCESS_DRIVER_CANCEL,
		      'created'=>date('Y-m-d H:i:s' , time()),
		      )
		    );
            }
        }
        QueueApiOrder::model()->update($key , 'orders' , $orders);
        return true;
    }

	public function recordMaliciousCancelling($initiator, $driver, $customer, $city)
	{
        EdjLog::info('recordMaliciousCancelling '.$initiator.' '.$driver.' '.$customer.' '.$city, 'console');
		Queue::model()->putin(
			array(
				'method' => 'malicious_cancelling_v1',
				'params' => array(
					'initiator' => $initiator,
					'driver' => $driver,
					'customer' => $customer,
                    'city' => $city,
				),
			),
			'malicious_order_594725'
		);
	}

    /**
     * 用户取消
     * @param string $key
     * @return boolean
     */
    private function _customerCancel($key) {
	$fail_ret=array('result'=>false, 'order_ids'=>array());
	$order_ids =  array();

        $queue_id = QueueApiOrder::model()->get($key , 'queue_id');
        $number = QueueApiOrder::model()->get($key , 'number');
        $orders = QueueApiOrder::model()->get($key , 'orders');
        $city = QueueApiOrder::model()->get($key , 'city_id');
        if (empty($queue_id) || empty($orders) || empty($number) || empty($city)) {
            return $fail_ret;
        }

        list($phone, $booking_id) = split('_', $key);

        //此状态需要消单,其他状态取消
        $state_arr = array(
            OrderProcess::ORDER_PROCESS_ACCEPT,
            OrderProcess::ORDER_PROCESS_READY,
            OrderProcess::ORDER_PROCESS_DRIVING,
            OrderProcess::ORDER_PROCESS_FINISH,
        );

        $i = 0;
        foreach ($orders as $order) {
            $previous_order_state = $order['order_state'];
            //判定要置的状态
            if (in_array($order['order_state'] , $state_arr)) {
                $state = OrderProcess::ORDER_PROCESS_USER_DESTORY;
            } else {
                $state = OrderProcess::ORDER_PROCESS_USER_CANCEL;
            }
            $next_order_state = $state;

            //验证状态机
            $validate = OrderProcess::model()->validOrderState($order['order_state'] , $state , $queue_id , $order['order_id']);
            if ($validate) {


				$order_ids[] = $order['order_id'];
                $orders[$order['order_id']]['order_state'] = $state;

		// 更新订单状态机 by wangjian 2014-03-26
		// 2014-03-26 BEGIN
		$real_order_id = $order['order_id'];
		$tmp_order_id = ROrder::model()->getOrder($order['order_id'], 'order_id');
		if ($tmp_order_id) {
		  $real_order_id = $tmp_order_id;
	
		}

		$save_state = OrderProcess::transFromOldToNew($state);
		OrderProcess::model()->genNewOrderProcess(
		    array( 'queue_id'  => $real_order_id,
		      'order_id'  => $real_order_id,
		      'driver_id' => $order['driver_id'],
		      'state'     => $save_state,
		      'created'=>date('Y-m-d H:i:s' , time()),
		      )    
		    );  

		// 2014-03-26 END
                $i++;

                // 司机已经就位了的订单，如果用户此时取消订单，那么需要做一个记录——曾坤 2015/3/16
                if ($previous_order_state == OrderProcess::ORDER_PROCESS_READY
                    && $next_order_state == OrderProcess::ORDER_PROCESS_USER_DESTORY
                ) {
                    $this->recordMaliciousCancelling(
			    		'customer',
                        $order['driver_id'],
                        $phone,
                        $city
			    	);
                }
            }

        }

        if ($i == 0) { //都处于服务中,不能取消
            return $fail_ret;
        } else {
            if ($i == $number) { //如果全部都被取消了 将OrderQueue状态置成取消
                $flag = OrderQueue::QUEUE_CANCEL;
                QueueApiOrder::model()->update($key , 'flag' , $flag);
            }

            //更新Orders
            QueueApiOrder::model()->update($key , 'orders' , $orders);
        }
	
	$ret = array('result'=>true, 'order_ids'=>$order_ids);
        return $ret;
    }
    /**
     * 取消OrderQueue
     * @param string $booking_id
     * @return boolean
     */
    public function cancelOrderQueue($phone , $booking_id , $flag = OrderQueue::QUEUE_CANCEL , $driver_reject = false) {
        if (empty($phone) || empty($booking_id)) {
            return false;
        }
        $params = array(
            'flag' => $flag,
        );
        
        // Yii::app()->db change into OrderQueue::getDbMasterConnection()()
        $result = OrderQueue::getDbMasterConnection()->createCommand()->update('t_order_queue' , $params ,'callid = :callid AND flag <> :queue_success' , array(
            ':callid' => $booking_id,
            ':queue_success' => OrderQueue::QUEUE_SUCCESS,
        ));

        //同时取消未派出的订单
        $key = $phone."_".$booking_id;
        $orders = QueueApiOrder::model()->get($key , 'orders');
        $queue_id = QueueApiOrder::model()->get($key , 'queue_id');
        if (empty($orders) || empty($queue_id)) {
            return false;
        }

        if ($driver_reject) {
            $cancel_status = Order::ORDER_DRIVER_REJECT_NO_DISPATCH;
        } else {
            if ($flag == OrderQueue::QUEUE_SUCCESS) {
                $cancel_status = Order::ORDER_NO_DISPATCH_CANCEL;
            } else {
                $cancel_status = Order::ORDER_CUSTOMER_CANCEL;
            }
        }

        foreach ($orders as $order_cache) {
            if ($order_cache['driver_id'] == Push::DEFAULT_DRIVER_INFO &&
		    $order_cache['order_state'] != OrderProcess::ORDER_PROCESS_DRIVING &&
		    $order_cache['order_state'] != OrderProcess::PROCESS_DEST &&
		    $order_cache['order_state'] != OrderProcess::ORDER_PROCESS_FINISH) {
                if (strlen($order_cache['order_id']) > 11) {
                    $task = array(
                        'method' => 'customer_cancel_order',
                        'params' => array(
                            'order_id' => $order_cache['order_id'],
                            'queue_id' => $queue_id,
                            'status' => $cancel_status,
                        ),
                    );
                    Queue::model()->putin($task , 'dalorder');
                    //取消优惠券

                } else {
                    $cancel = Order::model()->updateByPk($order_cache['order_id'] , array('status' => $cancel_status) , 'status not in(:order_comfirm , :order_cancel , :order_complate)' , array(
                        ':order_comfirm' => Order::ORDER_COMFIRM,
                        ':order_cancel' => Order::ORDER_CANCEL,
                        ':order_complate' => Order::ORDER_COMPLATE,
                    ));
                    BonusLibrary::model()->BonusUsed($phone, $order_cache['order_id'], 0, 2);
                }


            }
        }
        return true;
    }

    /**
     * 销单order
     * @param string $phone
     * @param string $booking_id
     * @return boolean
     */
    public function cancelOrders($phone , $booking_id,$reason_code=0,$reason_detail='') {
        if (empty($phone) || empty($booking_id)) {
            return false;
        }
        $key = $phone."_".$booking_id;
        $orders = QueueApiOrder::model()->get($key , 'orders');
        $queue_id = QueueApiOrder::model()->get($key , 'queue_id');
        if (empty($orders) || empty($queue_id)) {
            return false;
        }
        foreach ($orders as $order_cache) {
            if ($order_cache['order_state'] != OrderProcess::ORDER_PROCESS_DRIVING &&
                $order_cache['order_state'] != OrderProcess::PROCESS_DEST &&
                $order_cache['order_state'] != OrderProcess::ORDER_PROCESS_FINISH) {
                if (strlen($order_cache['order_id']) > 11) { //order_number取消
                    $task = array(
                        'method' => 'customer_cancel_order',
                        'params' => array(
                            'order_id' => $order_cache['order_id'],
                            'queue_id' => $queue_id,
                            'status' => Order::ORDER_CUSTOMER_CANCEL,
                            'cancel_code' => $reason_code,
                            'cancel_desc' => $reason_detail,
                        ),
                    );
                    Queue::model()->putin($task , 'dalorder');
                    //order_number取消优惠券

                } else { //order_id取消 5.4.1版本改为 客户取消订单后将取消的原因的数字和客户填写的取消详情更新在order对应的cancel_type和cancel_desc中 --20150408
                    $cancel = Order::model()->updateByPk($order_cache['order_id'] , array('status' => Order::ORDER_CUSTOMER_CANCEL,'cancel_code'=>$reason_code,'cancel_desc'=>$reason_detail) , 'status not in(:order_comfirm , :order_cancel , :order_complate)' , array(
                        ':order_comfirm' => Order::ORDER_COMFIRM,
                        ':order_cancel' => Order::ORDER_CANCEL,
                        ':order_complate' => Order::ORDER_COMPLATE,
                    ));
                    BonusLibrary::model()->BonusUsed($phone, $order_cache['order_id'], 0, 2);
                    
                    OrderStatusChangedPublisher::addQueue(array(
                        'bookingId' => $booking_id,
                        'orderId'   => $order_cache['order_id'],
                        'status'    => OrderProcess::ORDER_PROCESS_USER_DESTORY,
                        'message'   => '客户取消订单',
                        'driverId'  => $order_cache['driver_id'],
                        'phone'     => $phone
                    ));
                }
                $this->_pushCancelMsgToDriver($order_cache['order_id'] , $order_cache['driver_id']);
            }
        }
        
        return true;
    }

    /**
     * 更新订单缓存
     * @param array $params
     * @return boolean
     */
    public function updateOrderRedis($params) {
        $queue_id = isset($params['queue_id']) ? $params['queue_id'] : '';
        $order_id = isset($params['order_id']) ? $params['order_id'] : '';
        $driver_id = isset($params['driver_id']) ? $params['driver_id'] : '';
        $order_state = isset($params['order_state']) ? $params['order_state'] : '';
        $cancel_type = isset($params['cancel_type']) ? $params['cancel_type'] : '';
        $location_end = isset($params['location_end']) ? $params['location_end'] : '';
        if (empty($queue_id) || empty($order_id) || empty($driver_id) || empty($order_state)) {
            return false;
        }

        /*验证driver_id 和 state(在接单的时候会调用到这个方法两次 ，会有传driver_id=BJ00000的时候 ，先在这块做个验证 同时定位这个问题出在哪
                                问题原因：接单时候会记录位置 此时会更新redis  状态也为接单状态)*/
        if ($driver_id == Push::DEFAULT_DRIVER_INFO && $order_state == OrderProcess::ORDER_PROCESS_ACCEPT ) {
            return false;
        }
        $driver = DriverStatus::model()->get($driver_id);
        if (!$driver) {
            return false;
        }

        //读库需要走从库
        // Yii::app()->db_readonly change into OrderQueue::getDbReadonlyConnection()()
        $queue = OrderQueue::getDbReadonlyConnection()->createCommand()
            ->select('*')
            ->from('t_order_queue')
            ->where('id = :id' , array(':id' => $queue_id))
            ->order('id ASC')
            ->queryRow();
        if (empty($queue)) {
            return false;
        }

        //获取订单并验证
        $key = $queue['phone']."_".$queue['callid'];
        $orders = QueueApiOrder::model()->get($key , 'orders');
        $city = QueueApiOrder::model()->get($key , 'city_id');
        if (empty($orders)) {
	    // 400 手工派单 redis中没有数据 在此记录订单状态机
            if ($queue['dispatch_agent'] != '自动派单'
                && $queue['dispatch_agent'] != '直呼APP') {
                //记录状态机
                $save_state_400 = OrderProcess::transFromOldToNew($order_state);
                $tmp_process_params = array();
                $tmp_process_params['queue_id'] = $queue_id;
                $tmp_process_params['order_id'] = $order_id;
                $tmp_process_params['state'] = $save_state_400;
                $tmp_process_params['driver_id'] = $driver_id;
                $tmp_process_params['created'] = date("Y-m-d H:i:s", time());
                OrderProcess::model()->genNewOrderProcess($tmp_process_params);
            }
            return false;
        }

        //获取订单渠道，过滤渠道，不发送短信
        $order_channel = QueueApiOrder::model()->get($key , 'booking_type');
        $previous_order_state = -1;
        foreach ($orders as $key_order_id => $order) {
	  // key_order_id是redis中order的key
	  // 参数order_id是unique_order_id时 转成 order_id做比较
	  $cmp_order_id_param = $order_id;
	  if (strlen($order_id) > 11 && is_numeric($order_id)) {
	     $tmp = ROrder::model()->getOrder($order_id, 'order_id');
	     if(!empty($tmp)) {
               $cmp_order_id_param = $tmp;
	     }
	  }
	  $cmp_order_id_redis = $order['order_id'];
          // 如果redis中$order['order_id']是unique_order_id 转成 order_id做比较
	  if (strlen($order['order_id']) > 11 && is_numeric($order['order_id'])){
	    $tmp = ROrder::model()->getOrder($order['order_id'], 'order_id');
	    if(!empty($tmp)) {
              $cmp_order_id_redis = $tmp;
	    }
	  }
          if ($cmp_order_id_param == $cmp_order_id_redis) {

	    if(OrderProcess::ORDER_PROCESS_ACCEPT != $order_state) {
	      //记录状态机
              $order_process_flag = isset($params['order_process_flag']) ? $params['order_process_flag'] : 0;
              $save_state = OrderProcess::transFromOldToNew($order_state, $order_process_flag);
	      $process_params = array();
	      $process_params['queue_id'] = $queue_id;
	      $process_params['order_id'] = $cmp_order_id_redis;
	      $process_params['state'] = $save_state;
	      $process_params['driver_id'] = $driver_id;
	      $process_params['created'] = date("Y-m-d H:i:s", time());
	      // 2014-03-29 end

	      // We just ignore if the process is success or failed
	      OrderProcess::model()->genNewOrderProcess($process_params);
	    }


                //司机接单 或 订单中司机工号为BJ00000 更新司机信息就可以
                if ($order_state == OrderProcess::ORDER_PROCESS_ACCEPT || $order['driver_id'] == Push::DEFAULT_DRIVER_INFO) {
                    $orders[$key_order_id]['driver_id'] = $driver_id;
                    $orders[$key_order_id]['driver_name'] = $driver->info['name'];
                    $orders[$key_order_id]['driver_phone'] = $driver->phone;

		    // For remote order, save the subsidy and distance, fee to redis
			//FIX: 所有订单记录距离信息到orderExt		2014-12-16
		    //if($queue['channel'] == CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER) {
			$driver_dis_data = 
			    QueueDispatchOrder::model()->getOrderDriverDisData($order_id, $driver_id);
			$order_ext = OrderExt::model()->find(
				'order_id = :order_id',
				array(':order_id' => $order_id));
			//FIX: 所有订单记录距离信息到orderExt		2014-12-16
		    if($queue['channel'] == CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER) {
				if(!empty($driver_dis_data) && 
					isset($driver_dis_data['is_remote']) &&
					$driver_dis_data['is_remote'] == 1) {
					// Remote order
					$orders[$key_order_id]['ready_dist'] = 
					isset($driver_dis_data['dist'])? $driver_dis_data['dist']:0; 
					$orders[$key_order_id]['fee'] = 
					isset($driver_dis_data['fee'])? $driver_dis_data['fee']:0;
					$orders[$key_order_id]['subsidy'] = 
					isset($driver_dis_data['subsidy'])? $driver_dis_data['subsidy']:0; 
				}
			}

			if(!empty($order_ext)) {
			    if(isset($driver_dis_data['is_remote']) &&
				$driver_dis_data['is_remote'] == 1) {
				$order_ext->use_fee = 1;
				$order_ext->linear_ready_distance = 
				    isset($driver_dis_data['dist'])? $driver_dis_data['dist']:0; 
			    } else {
				$order_ext->use_fee = 0;
				//FIX:非远程单记录距离信息到数据库	2014-12-16
				$order_ext->linear_ready_distance = 
				    isset($driver_dis_data['dist'])? $driver_dis_data['dist']:0; 
			    }
			    if(!$order_ext->update()) {
				EdjLog::info('order::Accept update order_ext error|'.json_encode($modelExt->getErrors()));
			    }
			} // Valid order ext 
		    //} // Remote order
                }

                $orders[$key_order_id]['cancel_type'] = $cancel_type;
                if (!empty($location_end)) {
                    $orders[$key_order_id]['location_end'] = $location_end;
                }

                $order_states = isset($order['order_states']) ? $order['order_states'] : array();
                $ready_time = isset($orders[$key_order_id]['ready_time']) ? $orders[$key_order_id]['ready_time'] : 0;

                $previous_order_state = $orders[$key_order_id]['order_state'];

                //将状态加入到状态集合
                $states_data = $this->_recordOrderAllStates($order_states ,
			$order_state , $ready_time, $orders[$key_order_id]['order_state']);
                //如果是已就位 则把就位时间加上
                if ($order_state == OrderProcess::ORDER_PROCESS_READY) {
                    $orders[$key_order_id]['ready_time'] = $states_data['ready_time'];

		    // For remote order, we have to set the driver ready time to db
		    if($queue['channel'] == CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER) {
			$accept_time = '';
			foreach($order_states as $each_order_state) {
			    if($each_order_state['order_state_code'] == 
				    OrderProcess::ORDER_PROCESS_ACCEPT) {
				$accept_time = $each_order_state['order_state_timestamp'];
			    }
			}

			if(!empty($accept_time)) {
			    $ready_time_cost = strtotime($states_data['ready_time']) - $accept_time;
			    $order_ext = OrderExt::model()->find(
				    'order_id = :order_id',
				    array(':order_id' => $order_id));
			    if(!empty($order_ext)) {
				$order_ext->driver_ready_time = $ready_time_cost;
			    
				EdjLog::info('Update order_ext,order_id:'.$order_id.'|driver_ready_time:'.$ready_time_cost);
				if(!$order_ext->update()) {
				    EdjLog::info('Update order_ext error,order_id:'.$order_id.'|driver_ready_time:'.$ready_time_cost.
				    '|'.json_encode($order_ext->getErrors()));
				}
			    }
			}
		    }
                }
                $orders[$key_order_id]['order_states'] = 
		    $states_data['order_states'];


		//符合订单状态机才更新
		if(isset($states_data['invalid']) && $states_data['invalid'] == true) {
		}
		else {
		    $orders[$key_order_id]['order_state'] = $states_data['curr_state'];
		}
            }
        }
        QueueApiOrder::model()->update($key , 'orders' , $orders);
	
	// If driver ready, send a push msg to customer
	//if($order_state == OrderProcess::ORDER_PROCESS_READY) {
	  //  EdjLog::Info($queue['callid'].'|'.$order_id.'|'.$queue['phone'].'|'.$driver_id.'|Push driver ready msg|' , 'console');
	    //ClientPush::model()->orgPushMsgForDriverReachOrder($queue['phone'], 
	//		$driver_id, $queue['callid'], $order_id, $driver->info['name']);
	//}

        //如果是司机取消订单 需要给客户发一条短信
        if ($order_state == OrderProcess::ORDER_PROCESS_DRIVER_DESTORY) {
            
            // 如果司机已就位之后取消订单，记录一下——曾坤 2015/3/17
            if ($previous_order_state == OrderProcess::ORDER_PROCESS_READY) {
                CustomerApiOrder::model()->recordMaliciousCancelling(
                    'driver',
                    $driver_id,
                    $queue['phone'],
                    $city
                );
            }

	    // Send Push msg when the driver cancel the order
	    EdjLog::Info($queue['callid'].'|'.$order_id.'|'.$queue['phone'].'|'.$driver_id.'|Push driver cancel msg|' , 'console');
	    ClientPush::model()->pushMsgForDriverCancelOrder($queue['phone'], 
			$driver_id, $queue['callid'], $order_id, $driver->info['name'], $cancel_type);

            $temp = SmsTemplate::model()->getContentBySubject('dianping_cancel_order' , array('$driver_id$'=>$driver_id.'师傅' , '昨天的e代驾司机' => ','));
            if (!empty($temp['content'])) {
                $message = $temp['content'];
                //判断订单是否是合作方订单，并且是否可以发短信给客户 author zhangtingyi
                $partner_common = new PartnerCommon();
                $is_forbid = $partner_common->checkForbidSmsByChannel($order_channel);
                if (!$is_forbid) {
                    Sms::SendSMS($queue['phone'] , $message);
                }
            }
        }
        return true;
    }

    /**
     * 就位及到达更新redis中order数据
     * @param int $order_id
     * @param int $flag
     * @return boolean
     */
    public function updateOrderRedisByOrderFlag($order_id , $order_state , 
	$driver_id = Push::DEFAULT_DRIVER_INFO, $cancel_type = '', $order_process_flag = 0, $location_end = '') {
        //验证参数 只处理已接单之后的操作（已就位 已开车 已报单 司机销单）
        if (empty($order_id) || empty($order_state) || $order_state == OrderProcess::ORDER_PROCESS_ACCEPT ) {
            return false;
        }
        
        $map = OrderQueueMap::model()->findByAttributes(
                array('order_id' => $order_id),
                array('order'    => 'id ASC')
        );

        if (!empty($map)) {
	  // Check the driver id here. If we are sure that the driver has accept the
	  // order, then the driver_id must not be default. Check it and update it
	  // if it's still default
	  if($map->driver_id ==  Push::DEFAULT_DRIVER_INFO &&
	      $driver_id != Push::DEFAULT_DRIVER_INFO) {
	    // Do the work an receive must have done
	    // update the order and
	    // update the order queue map
	    EdjLog::info('|'.$order_id.'|'.$driver_id.'|'.$order_state.
		'|Rebind order receveive actions' , 'console');
	    Push::model()->redoOrderReceive($map->queue_id, $order_id, $driver_id);
	  } else if($map->driver_id != Push::DEFAULT_DRIVER_INFO){
	    $driver_id = $map->driver_id;
	  } else {
	    // Both of the driver id are default
	    EdjLog::info('|'.$order_id.'|'.$driver_id.'|'.$order_state.  '|Cannot rebind because both driver ids are default' , 'console');
	  }

            $params = array(
                'queue_id' => $map->queue_id,
                'order_id' => $order_id,
                'driver_id' => $driver_id,
                'order_state' => $order_state,
                'cancel_type' => $cancel_type,
                'order_process_flag' => $order_process_flag,
                'location_end' => $location_end,
            );
            $task = array(
                'method' => 'api_update_orders',
                'params' => $params,
            );
            Queue::model()->putin($task , 'orderstate');
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取客户的历史订单
     * @param $phone
     * @param int $pageNo
     * @param int $pageSize
     * @return array|bool
     * author mengtianxue
     */
    public function getOrderByPhone($phone, $pageNo = 0, $pageSize = 20)
    {
        //开始条数
        $offset = $pageNo * $pageSize;

        if (empty($phone)) {
            return false;
        }

        //加缓存 一天
        $cache_key = 'ORDER_COMPLATE_' . $phone . '_' . $pageNo;
        $data = Yii::app()->cache->get($cache_key);
//        $data = array();
        if (!$data) {
            $data = array(
                'orderList' => array(),
                'orderCount' => 0,
            );

            $where = 'phone = :phone and status = :status';
            $params = array(':phone' => $phone, ':status' => Order::ORDER_COMPLATE);

            //获取订单总数
            $count = Order::getDbReadonlyConnection()->createCommand()
                ->select('COUNT(order_id) AS cnt')
                ->from('t_order')
                ->where($where, $params)
                ->queryScalar();

            if($count > 0){
                //获取已报单订单列表
                $result = Order::getDbReadonlyConnection()->createCommand()
                    ->select('city_id, order_id , income, start_time, location_start, location_end')
                    ->from('t_order')
                    ->where($where, $params)
                    ->order('order_id DESC')
                    ->limit($pageSize)
                    ->offset($offset)
                    ->queryAll();

                $num = $count - $offset;
                foreach ($result as $k => $v) {
                    $v['id'] = $num;
                    $v['location_start'] = Helper::getShortAddress($v['location_start'], $v['city_id']);
                    $v['location_end'] = Helper::getShortAddress($v['location_end'], $v['city_id']);
                    $result[$k] = $v;
                    $num--;
                }
                $data['orderList'] = $result;
                $data['orderCount'] = $count;
            }
            Yii::app()->cache->set($cache_key, $data, 600);
        }
        return $data;
    }

    /**
     * 删除历史订单缓存
     * 默认会把前十页的缓存删掉
     * @param $phone
     * @param int $pages
     * @return bool
     * author mengtianxue
     */
    public function delOldOrderListCache($phone, $pages = 10){
        for($i = 0; $i <= $pages; $i++){
            $cache_key = 'ORDER_COMPLATE_' . $phone . '_' . $i;
            if(Yii::app()->cache->set($cache_key, '', 0)){
                echo "delete". $cache_key."\n";
            }else{
                echo "error". $cache_key."\n";
            }
        }
        return true;
    }

    /**
     * 检查订单是否评价
     * @param $order_id
     * @return string
     * author mengtianxue
     */
    public function checkedCommentByOrderID($order_id){

//        $cache_key = 'ORDER_COMMENT_' . $order_id;
//        $result = Yii::app()->cache->get($cache_key);
        $result = ROrderComment::model()->getComment($order_id);

        if(empty($result)){
            $comment = Yii::app()->db_readonly->createCommand()
                ->select('*')
                ->from('t_comment_sms')
                ->where('order_id = :order_id', array(':order_id' => $order_id))
                ->queryRow();
            $result = array();
            if ($comment) {
                $result['is_comment'] = 'Y';
                $result['level'] = $comment['level'];
            } else {
                $result['is_comment'] = 'N';
                $result['level'] = 0;
            }
            ROrderComment::model()->setComment($order_id, $result);

//            Yii::app()->cache->set($cache_key, $result, 86400);
        }
        return (object)$result;
    }

    /**
     * 清除客户端历史订单缓存
     */
    public function deleteOrderInfoByOrderID($order_id){
        $cache_key = 'CUSTOMER_ORDER_' . $order_id;
        Yii::app()->cache->delete($cache_key);
    }

    /**
     * 优惠劵详情
     * @param $order_id
     * @return array
     * author mengtianxue
     */
    public function getOrderInfoByOrderID($order_id){
        $cache_key = 'CUSTOMER_ORDER_' . $order_id;
        $result = Yii::app()->cache->get($cache_key);
        if(!$result){
            $order = Order::getDbReadonlyConnection()->createCommand()
                ->select('*')
                ->from('t_order')
                ->where('order_id=:order_id', array(':order_id' => $order_id))
                ->queryRow();
            $result = array();
            if($order){
                $result['order_id'] = $order['order_id'];
                $result['start_time'] = $order['start_time'];
                $result['end_time'] = $order['end_time'];
	            $result['source']     = $order['source'];
	            $result['channel']    = $order['channel'];
                $result['income'] = $order['income'];
                $result['price'] = $order['price'];
                $result['distance'] = $order['distance'];
                $result['driver_id'] = $order['driver_id'];
                $result['location_start'] = $order['location_start'];
                $result['location_end'] = $order['location_end'];
                $result['vip'] = 0;
                $result['coupon'] = 0;
                $result['user_money'] = 0;
                $cost_type = $order['cost_type'];
                $result['cast_type'] = $cost_type;

                $orderExt = OrderExt::model()->getPrimary($order_id);
                $result['cash_card_balance'] = 0;
                if ($orderExt) {
                    $result['cash_card_balance'] = !empty($orderExt['coupon_money']) ? $orderExt['coupon_money'] : 0;
				}
	            $orderDetail = FinanceCastHelper::getOrderFeeDetail($order_id, true);
	            $total = FinanceCastHelper::getOrderTotalMoney($order, $orderExt, $orderDetail);
				$result['income'] = $total;//为了,考虑不同版本客户端,修改income表示的值
				switch($cost_type){
                    case 1:
                        $result['vip'] = $total - $order['price'];

                        $result['vip'] -= $result['cash_card_balance'];
                        if($result['vip'] < 0) {
                            $result['vip'] = 0;
                        }

                        break;
                    case 2:
                        //是否用了优惠劵
                        $bonusUsed = CustomerBonus::model()->checkedBonusUseByOrderID($result['order_id']);
                        if($bonusUsed){
                            $result['coupon'] = $bonusUsed['balance'];
                        }
                        break;
                    case 4:
                        //是否用了优惠劵
                        $bonusUsed = CustomerBonus::model()->checkedBonusUseByOrderID($result['order_id']);
                        if($bonusUsed){
                            $result['coupon'] = $bonusUsed['balance'];
                        }
                        $result['user_money'] = $total - $order['price'] - $result['coupon'];

                        $result['user_money'] -= $result['cash_card_balance'];
                        if($result['user_money'] < 0) {
                            $result['user_money'] = 0;
                        }

                        break;
                    case 8:
                        $result['user_money'] = $total - $order['price'];

                        $result['user_money'] -= $result['cash_card_balance'];
                        if($result['user_money'] < 0) {
                            $result['user_money'] = 0;
                        }

                        break;
                }
	            //
	            $waitTime = isset($orderExt['wait_time']) ? $orderExt['wait_time'] : 0;
	            if($waitTime > 0 ){
		            $result['waiting_time'] = $waitTime.'分钟';
	            }
	            $result['booking_time'] = date('Y-m-d H:i:s', $order['booking_time']);
	            $result = array_merge($result, $orderDetail);
            }
            Yii::app()->cache->set($cache_key, $result, 86400);
        }
        return $result;
    }
    
    /**
     * 格式化司机信息
     * @param string $driver_id
     * @return array
     */
    public function format_driver_info($datas = array()) {
	if(!isset($datas['driver_id']) || !isset($datas['order_state']) 
		|| !isset($datas['order_id']) || !isset($datas['select_driver_id'])
		|| !isset($datas['select_driver_name']) || !isset($datas['bonus_sn'])) {
	    return array();
	}

	$driver_id = $datas['driver_id'];
	$order_state = $datas['order_state'];
	$order_id = $datas['order_id'];
	$select_driver_id = $datas['select_driver_id'];
	$select_driver_name = $datas['select_driver_name'];
	$bonus_sn = $datas['bonus_sn'];

        $state = '派单中';
        if ($driver_id == Push::DEFAULT_DRIVER_INFO && $order_state == OrderProcess::ORDER_PROCESS_NEW) {
            $state = '派单中';
            $order_state_code = $order_state;
        } elseif ($order_state == OrderProcess::ORDER_PROCESS_DRIVER_DESTORY) {
            $state = '司机销单';
            $order_state_code = $order_state;
        } else {
            $state = '派单失败';
            $order_state_code = OrderProcess::ORDER_PROCESS_SYS_CANCEL;
        }
        $driver_info = array(
            'driver_id'	=> '',
            'name'		=> '',
            'year'		=> '',
            'state'		=> '',
            'domicile'	=> '',
            'new_level'	=> '',
            'recommand'	=> '',
            'goback'	=> '',
            'service_times'	=> '',
            'distance'  => '',
            'longitude'	=> '',
            'latitude'	=> '',
            'picture_small'	=> '',
            'phone'	=> '',
            'idCard' => '',
                                                    
            'location_start' => isset($datas['location_start']) ? $datas['location_start'] : '',
            'location_end' => isset($datas['location_end']) ? $datas['location_end'] : '',          
            'pay_status' => isset($datas['pay_status']) ? $datas['pay_status'] : '',
            'cash_only' => isset($datas['cash_only']) ? intval($datas['cash_only']) : 0,

            'order_id' => $order_id,
            'role' => '',
            'order_state' => $state,
            'order_state_code' => $order_state_code,
	        'bonus_sn' => $bonus_sn,
	        'select_driver_id' => $select_driver_id,
	        'select_driver_name' => $select_driver_name,
        );
        return $driver_info;
    }

    /**
     * 返回服务中的人数
     * @param string $phone
     * @return boolean
     */
    public function validateOrderNumber($phone) {
        if (empty($phone)) {
            return self::QUEUE_MAX;
        }

        $state_arr = array(
            OrderProcess::ORDER_PROCESS_SYS_CANCEL,
            OrderProcess::ORDER_PROCESS_DRIVER_DESTORY,
            OrderProcess::ORDER_PROCESS_FINISH,
            OrderProcess::ORDER_PROCESS_USER_CANCEL,
            OrderProcess::ORDER_PROCESS_USER_DESTORY,
        );

        $cache = QueueApiOrder::model()->getallorders($phone);
        $cnt = 0;
        if (empty($cache)) {
            return $cnt;
        }
        foreach ($cache as $queue) {
            $orders = isset($queue['orders']) ? json_decode($queue['orders'] , true) : array();
            if ($queue['flag'] == OrderQueue::QUEUE_CANCEL) {
                continue;
            }
            foreach ($orders as $order) {
                if (!in_array($order['order_state'] , $state_arr)) {
                    $cnt += 1;
                }
            }
        }

        return $cnt;
    }

    /**
     * 客户取消订单给司机推送消息
     * @param int $order_id
     * @param string $driver_id
     * @return boolean
     */
    private function _pushCancelMsgToDriver($order_id , $driver_id) {
        if (empty($order_id) || empty($driver_id)) {
            return false;
        }
        $content = array(
            'order_id' => $order_id,
            'cancel_reason' => self::CANCEL_REASON,
        );
        $data = array(
            'type' => GetuiPush::TYPE_ORDER_CANCEL,
            'content' => $content,
            'level' => GetuiPush::LEVEL_HIGN,  //级别
            'driver_id' => $driver_id,
            'queue_id' => 0,
            'created' => date('Y-m-d H:i:s' , time()),
        );
        $result = Push::model()->organizeMessagePush($data);
        if ($result) {
            $driver = DriverStatus::model()->get($driver_id);
            $driver->status = 0;
        }
        return $result;
    }

    /**
     * @modified qiujianping@edaijia-inc.cn 2014-04-03
     *	Add process to t_order_process	
     *	The process must provide fail type
     *
     * 司机拒绝
     * @param array $params
     * @return boolean
     */
    public function driverReject($params) {
        $task = array(
            'method' => 'api_driver_reject_process',
            'params' => $params,
        );
        Queue::model()->putin($task , 'apporder');

	// We save the process here for we want the time to be 
	// correct as much as possible
    	if (empty($params['queue_id']) || empty($params['order_id']) || empty($params['driver_id'])
    	    || empty($params['type']) || empty($params['created'])) {
	  // TODO: add log here
	  return true;
	} else {
	  // Save the process
	  $process_params = array();
	  $process_params['queue_id'] = $params['queue_id'];
	  $process_params['order_id'] = $params['order_id'];
	  $process_params['driver_id'] = $params['driver_id'];
	  $process_params['created'] = date("Y-m-d H:i:s", time());
	  if($params['type'] == 2 || $params['type'] == 3 || $params['type'] == 4 
	      || $params['type'] == 5 || $params['type'] == 7) {
	    $process_params['state'] = OrderProcess::PROCESS_DISPATCH_FAIL_DRIVER_RELATED;
	  } else {
	    $process_params['state'] = OrderProcess::PROCESS_DISPATCH_FAIL_SYS_RELATED;
	  }
	  $process_params['fail_type'] = $params['type'];

	  // We just ignore if the process is success or failed
	  OrderProcess::model()->genNewOrderProcess($process_params);
	}
	
        return true;
    }

    /**
     * 司机拒单处理
     * @param array $params
     * @return boolean
     */
    public function driverRejectProcess($params) {
        $queue_id = isset($params['queue_id']) ? $params['queue_id'] : '';
        if (empty($queue_id)) {
            return false;
        }
        $queue = OrderQueue::model()->findByPk($queue_id);
        if (!$queue) {
            return false;
        }
        $key = $queue->phone."_".$queue->callid;
        $booking_type = QueueApiOrder::model()->get($key , 'booking_type');
        if ($queue->channel == self::QUEUE_CHANNEL_SINGLE_DRIVER) {
            //设置OrderQueue为取消状态
            $this->cancelQueueRedis($queue->phone , $queue->callid , OrderQueue::QUEUE_CANCEL , true);
            $this->cancelOrderQueue($queue->phone , $queue->callid , OrderQueue::QUEUE_CANCEL , true);
        }
        return true;
    }

    /**
     * 获取司机状态信息
     * @param string $state
     * @return string $order_state
     */
    private function _getOrderStateInfo($state) {
        switch ($state) {
            case OrderProcess::ORDER_PROCESS_NEW :
                $order_state = '派单中';
                break;
            case OrderProcess::ORDER_PROCESS_ACCEPT :
                $order_state = '已接单';
                break;
            case OrderProcess::ORDER_PROCESS_READY :
                $order_state = '已就位';
                break;
            case OrderProcess::ORDER_PROCESS_DRIVING :
                $order_state = '已开车';
                break;
            case OrderProcess::PROCESS_DEST:
                $order_state = '到达目的地';
                break;
            case OrderProcess::ORDER_PROCESS_DRIVER_DESTORY :
                $order_state = '已取消';
                break;
            case OrderProcess::ORDER_PROCESS_FINISH :
                $order_state = '已完成';
                break;
            default:
                $order_state = '已接单';
                break;
        }
        return $order_state;
    }

    public function sendBadWeatherSmsNotify($phone, $content)
    {
        Queue::model()->putin(
            array(
                'method' => 'bad_weather_sms_notify',
                'params' => array(
                    'phone' => $phone,
                    'content' => $content,
                ),
            ),
            'bad_weather_sms_notify'
        );
    }

    /**
     * 预约上来先推送
     * @param int $queue_id
     * @return boolean $result
     */
    public function multi_push($queue_id , $params = array()) {
    	//jjj log
        EdjLog::info($queue_id.'|开始派单|begin' , 'console');
        $result = false;
        if (empty($queue_id)) {
            return $result;
        }
        $queue = OrderQueue::model()->findByPk($queue_id);
        if (!$queue) {
            return $result;
        }

        //生成订单及QueueMap 
        $order = Push::model()->getDispatchOrder($queue_id , $params);
        if (empty($order)) {
            return $result;
        }

	// For the leader order, it begins to be dispatched
	$process_params = array();
	$process_params['queue_id'] = $queue_id;
	$process_params['order_id'] = $order[0]['order_id'];
	$process_params['state'] = OrderProcess::PROCESS_START_DISPATCH;
	$process_params['driver_id'] = Push::DEFAULT_DRIVER_INFO;
	$process_params['created'] = date('Y-m-d H:i:s' , time());

	// We just ignore if the process is success or failed
	OrderProcess::model()->genNewOrderProcess($process_params);

        //记录log
        EdjLog::info($queue_id.'|'.$order[0]['order_id'].'|180系统派单|开始找司机' , 'console');

	$range = isset(Yii::app()->params['OrderOneKeyDispatchRange']) ? 
            Yii::app()->params['OrderOneKeyDispatchRange'] : 5000;

        //分城市距离
        if(isset(Yii::app()->params['OrderOneKeyDispatchRangeByCity'][$queue->city_id])) {
            $range = Yii::app()->params['OrderOneKeyDispatchRangeByCity'][$queue->city_id];
        }

	$driver_number = isset(Yii::app()->params['OrderOneKeyDispatchNumber']) ?
	    Yii::app()->params['OrderOneKeyDispatchNumber'] : 10;

	// Check if the order is remote order
	if($queue['channel'] == CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER) {
	    $range = 15000;
	}

	//日间订单要派2.5.0以上版本司机端
	$driver_app_ver = null;
	if(isset($queue['type']) && in_array($queue['type'], Order::$daytime_sources)) {
	    $driver_app_ver = '2.5.0';
            EdjLog::info($queue_id.'|'.$order[0]['order_id'].'|Daytime nearby' , 'console');
	}

        //获取支持洗车司机
    if(isset($queue['type']) && in_array($queue['type'], Order::$washcar_sources)) {
            $driver_app_ver = '2.5.0';
            $drivers=DriverGPS::model()->nearbyService($queue->lng, $queue->lat, 0, $driver_number , $range, Driver::SERVICE_TYPE_FOR_XICHE,$driver_app_ver);
        }else{
            $drivers=DriverGPS::model()->nearby_printLog($queue->city_id,$order[0]['order_id'],$queue->lng, $queue->lat, 0 , $driver_number , $range, $driver_app_ver,$queue->city_id);
        }
        
        if(!empty($drivers) 
            && ($queue['channel'] == CustomerApiOrder::QUEUE_CHANNEL_BOOKING || $queue['channel'] == CustomerApiOrder::QUEUE_CHANNEL_SINGLE_CHANGE)) {
            try {
                $filterDriverManager = new FilterDriverManager();
                $filterDriverManager->addStrategy(FilterDriverCrossRiverStrategy::model());
                $filterDriverManager->addStrategy(FilterDriverCrownStrategy::model());
                $filterDriverManager->addStrategy(FilterDriverSpeedStrategy::model());
                $drivers = $filterDriverManager->filter($queue->city_id, $drivers, $queue->lng, $queue->lat, $range,$queue['type'], $order[0]['order_id'],$driver_app_ver);
                
            } catch (Exception $e) {
                EdjLog::warning('multi_push:apply FilterDriverAcrossRiver failed, message:' . $e->getMessage() , 'console');
            }
            EdjLog::info($queue_id.'|'.json_encode($drivers), 'console');
        }
        
        if (empty($drivers)) {
        	//记录log
            EdjLog::info($queue_id.'|'.$order[0]['order_id'].'|180系统派单|附近无司机' , 'console');
            return false;
        }

        //测试电话号只推送测试工号 否则推送线上司机
        $phone = isset($queue->phone) ? $queue->phone : '';
        $test_phone = DispatchDriver::model()->test_phone();
        if (in_array($phone , $test_phone)) {
            $driver_id = $this->_getMultiLeaderTestDriverID($drivers, $queue['channel']);
        } else {
            $driver_id = $this->_getMultiLeaderDriverID($drivers, $queue['channel']);
        }
        if (empty($driver_id)) {
        	//记录log
            EdjLog::info($queue_id.'|'.$order[0]['order_id'].'|180系统派单|司机全部被锁定' , 'console');
            return false;
        }
    

        if(!empty($drivers)){
        foreach($drivers as $driver){
            if(!isset($driver['crown']))
        	EdjLog::info('CustomerApiOrder:FilterDriverManager| order_id|'.$order[0]['order_id'].'|driverId:'.$driver['driver_id'], 'console');
        }
        }
        	
	//将司机设置为上次派送派过司机
	if(count($drivers) > 1) {
	    QueueDispatchOrder::model()->queueDispatchedDriver($order[0]['order_id'] , $driver_id);
	}

        //锁定订单
        $order_flag = QueueDispatchOrder::model()->insert($order[0]['order_id']);
        if (!$order_flag) {
        	//记录log
            EdjLog::info($queue_id.'|'.$order[0]['order_id'].'|180系统派单|订单已被锁定' , 'console');
            return false;
        }
        $result = Push::model()->PushOrder($queue_id , $order[0]['order_id'] , $driver_id);
        if (!$result) {
            echo "\n queue_id:".$queue_id."|order_id:".$order[0]['order_id']."|driver_id:".$driver_id."|推送失败 \n";

            //推送失败将订单解锁
            QueueDispatchOrder::model()->delete($order[0]['order_id']);
        } else {
            echo "\n queue_id:".$queue_id."|order_id:".$order[0]['order_id']."|driver_id:".$driver_id."|推送成功 \n";
        }

	//如果是代叫,向客户发送短信
	if(!empty($queue->contact_phone)
	    && $queue->phone != $queue->contact_phone) {
	    $message = MessageText::getFormatContent(
	        MessageText::CUSTOMER_NOTICE_CONNECT_PHONE,
		substr_replace($queue->phone, "****", 3, 4),
                !empty($queue->number) ? $queue->number : 1
            );
            Sms::SendSMS($queue->contact_phone, $message);
	    EdjLog::info('SendSms to connect_phone|'.$queue->contact_phone);
        }

        //增加结束log
        EdjLog::info($queue_id.'|'.$order[0]['order_id'].'|201|系统已派单|'.$result.'|end' , 'console');
        return $result;
    }
    
    /**
     * 获取组长工号
     * @param array $drivers
     * @return string $driver_id
     */
    private function _getMultiLeaderDriverID($drivers, $channel = CustomerApiOrder::QUEUE_CHANNEL_BOOKING) {
//    	$test_drivers = DispatchDriver::model()->test();
        $driver_id = '';
        foreach ($drivers as $driver) {
//			if(!in_array($driver['driver_id'],$test_drivers)){
//                continue;
//            }

        // For remote order, only supported by driver version = 2.4.8 or >= 2.5.0
        // Check version
        if($channel == CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER 
            && !DriverStatus::model()->isSupportRemoteOrder($driver['driver_id'])) {
            continue;
        }

            $flag = QueueDispatchDriver::model()->insert($driver['driver_id']);
            if(!$flag){
                continue;
            }
            $driver_id = $driver['driver_id'];
            break;
        }
        return $driver_id;
    }
    
    /**
     * 获取组长工号（测试工号）
     * @param array $drivers
     * @return string $driver_id
     */
    private function _getMultiLeaderTestDriverID($drivers, $channel = CustomerApiOrder::QUEUE_CHANNEL_BOOKING) {
        $test_drivers = DispatchDriver::model()->test();
        $driver_id = '';
        foreach ($drivers as $driver) {
            if(!in_array($driver['driver_id'],$test_drivers)){
                continue;
            }

        // For remote order, only supported by driver version = 2.4.8 or >= 2.5.0
        // Check version
        if($channel ==  CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER
            && !DriverStatus::model()->isSupportRemoteOrder($driver['driver_id'])) {
            continue;
        }

            $flag = QueueDispatchDriver::model()->insert($driver['driver_id']);
            if(!$flag){
                continue;
            }
            $driver_id = $driver['driver_id'];
            break;
        }
        return $driver_id;
    }

    /**
     * 脚本运行超过预约时间将OrderQueue置成完成状态
     * @param int $queue_id
     * @return boolean
     */
    public function dispatchFinished($queue_id) {
        if (empty($queue_id)) {
            return false;
        }
        $queue = OrderQueue::model()->findByPk($queue_id);
        if (!$queue) {
            return false;
        }
        //设置redis状态为完成

        $this->cancelQueueRedis($queue->phone , $queue->callid , OrderQueue::QUEUE_SUCCESS);
        //设置orderQueue为完成状态
        $result = $this->cancelOrderQueue($queue->phone , $queue->callid , OrderQueue::QUEUE_SUCCESS);
        return $result;
    }

    /**
     * 获取为我服务的司机
     * @param string $phone
     * @return array
     */
    public function getServiceDrivers($phone) {
        $drivers = array();
        if (empty($phone)) {
            return $drivers;
        }

//    	$start_time = microtime(true);
        $cache = QueueApiOrder::model()->getallorders($phone);
//    	$end_time = microtime(true);
        //打印读缓存的时间
//    	$task = array(
//    	    'method' => 'print_get_cache_time',
//    	    'params' => array(
//    	        'phone' => $phone,
//    	        'start_time' => $start_time,
//    	        'end_time' => $end_time,
//    	    ),
//    	);
//    	Queue::model()->putin($task , 'test');

        $state_arr = array(
            OrderProcess::ORDER_PROCESS_ACCEPT,
            OrderProcess::ORDER_PROCESS_READY,
            OrderProcess::ORDER_PROCESS_DRIVING,
            OrderProcess::PROCESS_DEST,
        );
        if (empty($cache)) {
            return $drivers;
        }
        foreach ($cache as $queue) {
            if ($queue['flag'] == OrderQueue::QUEUE_CANCEL) {
                continue;
            }

            $orders = isset($queue['orders']) ? json_decode($queue['orders'] , true) : '';
            if (empty($orders)) {
                continue;
            }

            foreach($orders as $order) {
                if ($order['status'] == 0 && in_array($order['order_state'] , $state_arr) ) {
                    $drivers[] = array(
                        'booking_id' => isset($queue['booking_id']) ? $queue['booking_id'] : '',
                        'driver_id' => isset($order['driver_id']) ? $order['driver_id'] : '',
			'order_id' => isset($order['order_id'])? $order['order_id'] : '',
                    );
                }
            }

        }
        return $drivers;
    }

    /**
     * 获取客户姓名,这个方法也不需要，稍后需要增加客户的cache
     * @param string $phone
     * @return string $name
     */
	public function getCustomerName($phone)
	{
		$name = '先生';
		if (empty($phone)) {
			return $name;
		}

		$customer = Yii::app()->db_readonly->createCommand()
			->select("name , gender")
			->from('{{customer_main}}')
			->where('phone = :phone', array(':phone' => $phone))
			->queryRow();
		if(!empty($customer)){
			$name = $customer['name'];
		}
		$data = VipPhone::model()->getPrimary($phone);
		if(!empty($data) && isset($data['name'])){
//在这里不把名字改成vip名字
//			$name = $data['name'];
		}
		if (!empty($customer)) {
//			$name = $customer['name'];
			switch ($customer['gender']) {
				case 2:
					if (empty($name)) {
						$name = '女士';
						break;
					}
					if (strpos($name, '先生') === false && strpos($name, '女士') === false) {
						$name .= "女士";
						break;
					}

				default:
					if (empty($name)) {
						$name = '先生';
						break;
					}
					if (strpos($name, '先生') === false && strpos($name, '女士') === false) {
						$name .= "先生";
						break;
					}
			}
		}
		return $name;
	}

    /**
     * 获取等候时间
     * @param string $order_state
     * @param string $ready_time
     * @return string $wait_time
     */
    private function _getWaitTime($order_state , $ready_time, $booking_time = '', $drive_time = '') {
        $wait_time = '';
        $state_arr = array(
            OrderProcess::ORDER_PROCESS_READY,
            OrderProcess::ORDER_PROCESS_DRIVING,
            OrderProcess::PROCESS_DEST,
        );

	$start_time = $booking_time;
	if(strcmp($ready_time,$booking_time) >0) {
	    $start_time = $ready_time;
	}

        if (in_array($order_state , $state_arr) && !empty($start_time)) {
	    if($order_state != OrderProcess::ORDER_PROCESS_READY
		&& !empty($drive_time)) {
                $wait_time = intval(($drive_time - strtotime($start_time))/60);
	    }
	    else {
                $wait_time = intval((time() - strtotime($start_time))/60);
	    }
        }
	if(!empty($wait_time) && $wait_time < 0) {
	    EdjLog::info("Wrong wait time|Current time:".
		    time()."| Read time:".strtotime($start_time));		
	    $wait_time = 0;
	}
        return $wait_time;
    }

    /**
     * 是否及时就位
     * @param string $order_states
     * @param string $ready_time
     */
    private function _isReadyInTime($order_states , $ready_time) {
        $wait_time = '';
        $state_arr = array(
            OrderProcess::ORDER_PROCESS_READY,
            OrderProcess::ORDER_PROCESS_DRIVING,
            OrderProcess::PROCESS_DEST,
        );

	$start_time = $booking_time;
	if(strcmp($ready_time,$booking_time) >0) {
	    $start_time = $ready_time;
	}

        if (in_array($order_state , $state_arr) && !empty($start_time)) {
            $wait_time = intval((time() - strtotime($start_time))/60);
        }
	if(!empty($wait_time) && $wait_time < 0) {
	    EdjLog::info("Wrong wait time|Current time:".
		    time()."| Read time:".strtotime($start_time));		
	    $wait_time = 0;
	}
        return $wait_time;
    }

    /**
     * 将优惠信息加入缓存
     * @param string $order_number
     * @return boolean
     */
    public function orderFavorableCache($order_number) {
        if (empty($order_number)) {
            return false;
        }

        $order = Order::model()->find('order_number = :order_number' , array(':order_number' => $order_number));
        if (!$order) {
            return false;
        }
        $cache_key = 'GET_ORDER_FAVORABLE_'.$order_number;
        $cache = Yii::app()->cache->get($cache_key);
        if (!$cache) {
            $data = array(
                'cost_type' => 0 ,
                'vipcard' => '' ,
                'bonus' => '' ,
                'card' => '' ,
                'balance' => 0,
                'user_money' => 0,
                'order_id' => $order->order_id,
                'order_number' => $order->order_number,
            );

            if ($order->source != 1 && $order->source != 3) {
                $favorable = Order::model()->getOrderFavorable($order->phone, $order->booking_time , $order->source, $order->order_id);
                if($favorable){
                    $favorable['user_money'] = isset($favorable['user_money']) ? $favorable['user_money'] : 0;
                    $favorable['money'] = isset($favorable['money']) ? $favorable['money'] : 0;
                    $data['card'] = isset($favorable['card']) ? $favorable['card'] : '';

                    $data['balance'] = $favorable['money'] + $favorable['user_money'];
                    $data['user_money'] = $favorable['user_money'];
                    $data['cost_type'] = (string)$favorable['code'];
                    switch($favorable['code']){
                        case 1:
                            $data['vipcard'] = '余额：'.$favorable['money'].'元,不足部分请收取现金';
                            break;
                        case 2:
                            $data['bonus']=' 优惠金额：'.$favorable['money'].'元';
                            break;
                        case 4:
                            $data['bonus']=' 优惠金额：'.$favorable['money'].'元,个人帐户余额'.$favorable['user_money'].'元,不足部分请收取现金';
                            break;
                        case 8:
                            $data['bonus']=' 个人帐户余额：'.$favorable['user_money'].'元,不足部分请收取现金';
                            break;
                    }
                }
            }
            Yii::app()->cache->set($cache_key, $data, 3600);
        }
        return true;;
    }

    /**
     * 记录所有订单状态
     * @param array $order_states---所有订单状态
     * @param string $order_state---当前订单状态
     * @param string $ready_time---就位时间
     * @return array
     */
    private function _recordOrderAllStates($order_states , 
	    $order_state = OrderProcess::ORDER_PROCESS_ACCEPT, 
	    $ready_time = 0, 
	    $last_order_state = OrderProcess::ORDER_PROCESS_ACCEPT) {
	$ret = array('ready_time' => $ready_time, 
		'curr_state' => $order_state,
		'order_states' => $order_states);
        $arr_state = array(
            OrderProcess::ORDER_PROCESS_ACCEPT,
            OrderProcess::ORDER_PROCESS_READY,
            OrderProcess::ORDER_PROCESS_DRIVING,
            OrderProcess::PROCESS_DEST,
        );

	// Check the status of current should be valid to continue
	$validate = OrderProcess::model()->validOrderState(
		$last_order_state, $order_state);
	if(!$validate) {
	    EdjLog::info("Invalid order state|last state:".
		    $last_order_state.'|Current state:'.$order_state);		
	    $ret['invalid'] = true;
	    return $ret;
	}

	if ($order_state == OrderProcess::ORDER_PROCESS_READY) {
	    $ready_time = date('Y-m-d H:i:s' , time());
	}

        if (!in_array($order_state , $arr_state)) {
            return $ret;
        }
	$rst_order_state = $order_state;

        switch ($order_state) {
            case OrderProcess::ORDER_PROCESS_ACCEPT:
                /*
				 * 因为我们在order_stats里面加上了101这个状态
				 * 所以count($order_states) > 0，进而导致订单
				 * 无法进入已接单的状态，所以我注释了这一部分
				 * 代码——曾坤 2015/3/16
		if(!empty($order_states) && count($order_states) > 0) {
		    // Maybe already be accept, or arrive or driver
		    EdjLog::info("Already been accept|Current status".
			    $last_order_state);		
		    $rst_order_state = $last_order_state;
		    break;
		}
                 */
                $order_states = array();
                $order_states[] = array(
                    'order_state_code' => $order_state,
                    'order_state_timestamp' => time(),
                    'order_state_content' => '司机已接单',
                );
                break;
            case OrderProcess::ORDER_PROCESS_READY:
                $arr_tmp = array();
                $arr_tmp[] = array(
                    'order_state_code' => $order_state,
                    'order_state_timestamp' => time(),
                    'order_state_content' => '司机已到达 等候0分',
                );

		if($last_order_state == OrderProcess::ORDER_PROCESS_DRIVING ||
			$last_order_state == OrderProcess::ORDER_PROCESS_READY) {
		    // Already been driving 
		    EdjLog::info("Already been driving|Current status".
			    $last_order_state);		
		    $rst_order_state = $last_order_state;
		    break;
		}

                if (!empty($order_states)  && count($order_states) == 1) {
                    foreach ($order_states as $state) {
                        array_push($arr_tmp , $state);
                    }
                    $order_states = $arr_tmp;
                }
                unset($arr_tmp);
                break;
            case OrderProcess::ORDER_PROCESS_DRIVING:
                $time = time();
                $arr_tmp = array();
                $arr_tmp[] = array(
                    'order_state_code' => $order_state,
                    'order_state_timestamp' => $time,
                    'order_state_content' => '开车行驶',
                );

		if($last_order_state == OrderProcess::ORDER_PROCESS_DRIVING) {
		    // Already been driving 
		    EdjLog::info("Already been driving|Current status:".
			    $last_order_state."|Update status:".$order_status);		
		    break;
		}

                if (!empty($order_states) && 
			(count($order_states) == 2 || 
			 count($order_states) == 1)) {
                    foreach ($order_states as $state) {
                        array_push($arr_tmp , $state);
                    }
                    $order_states = $arr_tmp;
                }
                unset($arr_tmp);
                break;
            case OrderProcess::PROCESS_DEST:
                $time = time();
                $arr_tmp = array();
                $arr_tmp[] = array(
                    'order_state_code' => $order_state,
                    'order_state_timestamp' => $time,
                    'order_state_content' => '到达目的地',
                );

		if($last_order_state == OrderProcess::PROCESS_DEST) {
		    // Already been driving 
		    EdjLog::info("Already been dest|Current status:".
			    $last_order_state."|Update status:".$order_status);		
		    break;
		}

                if (!empty($order_states) && 
			(count($order_states) == 3 || 
			 count($order_states) == 2 || 
			 count($order_states) == 1)) {
                    foreach ($order_states as $state) {
                        array_push($arr_tmp , $state);
                    }
                    $order_states = $arr_tmp;
                }
                unset($arr_tmp);
                break;
            default:
                break;
        }
	$ret['ready_time'] = $ready_time;
	$ret['curr_state'] = $rst_order_state;
	$ret['order_states'] = $order_states;
        return $ret;
    }

    /**
     * 订单状态保障机制（有司机接单后再次修改状态）
     * @param string $key
     * @param int $order_id
     * @param string $driver_id
     * @version 2013-12-06
     */
    private function _updateOrderRedis($key , $order_id , $driver_id) {
        $orders = QueueApiOrder::model()->get($key , 'orders');
        $queue_id = QueueApiOrder::model()->get($key , 'queue_id');
        $channel = QueueApiOrder::model()->get($key , 'booking_type');
        $driver = DriverStatus::model()->get($driver_id);
        if (!$driver) {
            return false;
        }

        $order_state = OrderProcess::ORDER_PROCESS_ACCEPT;
        $state_arr = array(
            OrderProcess::ORDER_PROCESS_NEW,
            OrderProcess::ORDER_PROCESS_DISPATCH,
            OrderProcess::ORDER_PROCESS_ACCEPT,
        );

        foreach ($orders as $order) {
            if ($order_id == $order['order_id'] && in_array($order['order_state'] , $state_arr) ) {
                $orders[$order_id]['driver_id'] = $driver_id;
                $orders[$order_id]['driver_name'] = $driver->info['name'];
                $orders[$order_id]['driver_phone'] = $driver->phone;

                //记录状态机
                $order_states = isset($order['order_states']) ? $order['order_states'] : '';
                $ready_time = isset($orders[$order_id]['ready_time']) ? $orders[$order_id]['ready_time'] : 0;

                //将状态加入到状态集合
                $states_data = $this->_recordOrderAllStates($order_states , 
			$order_state , $ready_time, $orders[$order_id]['order_state']);
                $orders[$order_id]['order_states'] = $states_data['order_states'];
                $orders[$order_id]['order_state'] = $states_data['curr_state'];;

		if($channel == CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER) {
		    $driver_dis_data = 
			QueueDispatchOrder::model()->getOrderDriverDisData($order_id, $driver_id);
		    $order_ext = OrderExt::model()->find(
			    'order_id = :order_id',
			    array(':order_id' => $order_id));
		    if(!empty($driver_dis_data) && 
			    isset($driver_dis_data['is_remote']) &&
			    $driver_dis_data['is_remote'] == 1) {
			// Remote order
			$orders[$order_id]['ready_dist'] = 
			    isset($driver_dis_data['dist'])? $driver_dis_data['dist']:0; 
			$orders[$order_id]['fee'] = 
			    isset($driver_dis_data['fee'])? $driver_dis_data['fee']:0;
			$orders[$order_id]['subsidy'] = 
			    isset($driver_dis_data['subsidy'])? $driver_dis_data['subsidy']:0; 
		    }

		    if(!empty($order_ext)) {
			if(isset($driver_dis_data['is_remote']) &&
			    $driver_dis_data['is_remote'] == 1) {
			    $order_ext->use_fee = 1;
			    $order_ext->linear_ready_distance = 
				isset($driver_dis_data['dist'])? $driver_dis_data['dist']:0; 
			} else {
			    $order_ext->use_fee = 0;
			}
			if(!$order_ext->update()) {
			    EdjLog::info('order::Accept update order_ext error|'.json_encode($modelExt->getErrors()));
			}
		    } // Valid order ext 
		} // Remote order
            }
        }
        QueueApiOrder::model()->update($key , 'orders' , $orders);
        return true;
    }

    /**
     * 选司机派单独立
     * @param array $queue
     * @param int $order_id
     * @param string $driver_id
     * @version 2013-12-27
     */
    private function _push_order($queue , $order_id , $driver_id) {
        $data = array(
            'order_id' => $order_id,
            'address' => $queue['address'],
            'customer_name' => $queue['name'],
            'phone' => $queue['phone'],
            'contact_phone' => '',
            'booking_time' => $queue['booking_time'],
            'number' => $queue['number'],
            'vipcard' => '',
            'role' => '',
            'leader_phone' => '',
            'bonus' => '',
            'card'=>'', //VIP或优惠卷卡号
            'balance'=>0, //VIP余额或优惠卷余额
            'user_money'=>0, //帐户金额
            'source'=> $queue['type'] ,//订单来源
            'cost_type'=> '' ,//客户类型
            'lng'=> $queue['google_lng'] ,//经度
            'lat'=> $queue['google_lat'] ,//纬度
            'gps_type'=> Push::DEFAULT_GPS_TYPE ,//gps类型
            'dist' => '',   //增加时间描述
            'is_new'=> 1 ,//是否为新订单
        );

        if ($queue['lng'] == $queue['google_lng'] && $queue['lat'] == $queue['google_lat']) {
            $data['gps_type'] = 'baidu';
        }

        $favorable = Order::model()->getOrderFavorable($queue['phone'] ,strtotime($queue['booking_time']) , $queue['type'] , $order_id);
        if($favorable){
        	$favorable['money'] = isset($favorable['money']) ? $favorable['money'] : 0;
        	$favorable['user_money'] = isset($favorable['user_money']) ? $favorable['user_money'] : 0;
        	
            $data['card'] = $favorable['card'];
            $data['balance'] = $favorable['money'] + $favorable['user_money'];
            $data['user_money'] = $favorable['user_money'];
            $data['cost_type'] = (string)$favorable['code'];
            switch($favorable['code']){
                case 1:
                    $data['vipcard'] = '余额：'.$favorable['money'].'元,不足部分请收取现金';
                    break;
                case 2:
                    $data['bonus']=' 优惠金额：'.$favorable['money'].'元';
                    break;
                case 4:
                    $data['bonus']=' 优惠金额：'.$favorable['money'].'元,个人账户余额'.$favorable['user_money'].'元,不足部分请收取现金';
                    break;
                case 8:
                    $data['bonus']=' 个人账户余额：'.$favorable['user_money'].'元,不足部分请收取现金';
                    break;
            }
        }

//        $order_favorable = Order::model()->getOrderFavorable($queue['phone'] ,strtotime($queue['booking_time']) , $queue['type'] , $order_id);
//        if($order_favorable['code'] == 1){     //VIP`
//            $data['vipcard'] = '余额：'.$order_favorable['money'].'元,不足部分请收取现金';
//            $data['card'] = $order_favorable['card'];
//            $data['balance'] = $order_favorable['money'];
//            $data['cost_type'] = '1';
//        }elseif($order_favorable['code'] == 2){ //优惠劵
//            $data['bonus']=' 优惠金额：'.$order_favorable['money'].'元';
//            $data['card'] = $order_favorable['card'];
//            $data['balance'] = $order_favorable['money'];
//            $data['cost_type'] = '2';
//        }

        $message = array(
            'type' => GetuiPush::TYPE_ORDER_DETAIL,
            'content' => $data,
            'level' => GetuiPush::LEVEL_HIGN,  //级别
            'driver_id' => $driver_id,
            'queue_id' => $queue['id'],
            'created' => date('Y-m-d H:i:s' , time()),
        );
        $result = Push::model()->organizeMessagePush($message);
        return $result;
    }

    /**
     * 获取订单、司机信息
     * @param string $phone
     * @return $drivers
     */
    public function getThirdSignOrders($phone , $contact_phone , $booking_id , $channel) {
        $data = array(
            'drivers' => array(),
        );
        if (empty($channel) || empty($phone)) {
            return $data;
        }

        //获取缓存订单信息
        if (!empty($booking_id)) {
            $cache = array();
            $key = $phone.'_'.$booking_id;
            $orders = ROrder::model()->getallfields($key);
            $cache[] = $orders;
        } elseif (!empty($contact_phone)) {
            $cache = ROrder::model()->get_all_orders($contact_phone);
        } else {
            $cache = ROrder::model()->get_all_orders($phone);
        }
        if (empty($cache)) {
            return $data;
        }

        $drivers = array();
        $state_arr = array(
            OrderProcess::ORDER_PROCESS_ACCEPT,
            OrderProcess::ORDER_PROCESS_READY,
            OrderProcess::ORDER_PROCESS_DRIVING,
            OrderProcess::ORDER_PROCESS_FINISH,
            OrderProcess::ORDER_PROCESS_DRIVER_DESTORY,
            OrderProcess::ORDER_PROCESS_USER_DESTORY,
            OrderProcess::ORDER_PROCESS_USER_CANCEL,
        );

        //遍历去获取queue和order信息
        foreach ($cache as $queue) {
            if (empty($queue)) {
                continue;
            }

            //取消后不返回
            if ($queue['flag'] == OrderQueue::QUEUE_CANCEL) {
                continue;
            }

            //获取orders信息和对应的司机信息
            $orders = isset($queue['orders']) ? json_decode($queue['orders'] , true) : '';
            if (empty($orders)) {
                continue;
            }

            $tmp = array(
                'booking_id' => $queue['booking_id'],
                'number' => $queue['number'],
                'orders' => array(),
            );
            foreach($orders as $order) {
                if ($order['driver_id'] == Push::DEFAULT_DRIVER_INFO
                    && $order['order_state'] != OrderProcess::ORDER_PROCESS_USER_DESTORY
                    && $order['order_state'] != OrderProcess::ORDER_PROCESS_USER_CANCEL
                ){  //未派出司机
//					$driver = $this->format_driver_info($order['driver_id'] , $order['order_state']);
                    $tmp['orders'][$order['order_id']] = array(
                        'order_id' => $order['order_id'],
                        'driver_id' => $order['driver_id'],
                        'order_state_code' => $order['order_state'],
                        'role' => $order['role'],
                        'name' => $order['driver_name'],
                        'phone' => $order['driver_phone'],
                    );
                } if (in_array($order['order_state'] , $state_arr) && $order['driver_id'] != Push::DEFAULT_DRIVER_INFO) {
                    //已派出司机
//					$driver = Helper::foramt_driver_detail($order['driver_id'] , 'baidu', 0, 'default');
//					if (!empty($driver)) {
                    $tmp['orders'][$order['order_id']] = array(
                        'order_id' => $order['order_id'],
                        'driver_id' => $order['driver_id'],
                        'order_state_code' => $order['order_state'],
                        'role' => $order['role'],
                        'name' => $order['driver_name'],
                        'phone' => $order['driver_phone'],
                    );
//					}
                } elseif ($order['order_state'] != OrderProcess::ORDER_PROCESS_USER_DESTORY
                    && $order['order_state'] != OrderProcess::ORDER_PROCESS_USER_CANCEL
                    && $order['order_state'] != OrderProcess::ORDER_PROCESS_FINISH
                    && $order['order_state'] != OrderProcess::ORDER_PROCESS_DRIVER_DESTORY)
                {
                    //异常状态处理兼容
//					$driver = $this->format_driver_info($order['driver_id'] , $order['order_state']);
                    $tmp['orders'][$order['order_id']] = array(
                        'order_id' => $order['order_id'],
                        'driver_id' => $order['driver_id'],
                        'order_state_code' => $order['order_state'],
                        'role' => $order['role'],
                        'name' => $order['driver_name'],
                        'phone' => $order['driver_phone'],
                    );
                }
            }

            //对订单正序排序处理（组长在前显示）
            if (!empty($tmp['orders'])) {
                ksort($tmp['orders']);
                $tmp['orders'] = array_values($tmp['orders']);
                $drivers[$queue['queue_id']] = $tmp;
            }
        }

        //对司机排序 后预约的靠前显示		
        if (!empty($drivers)) {
            krsort($drivers);
            $drivers = array_values($drivers);
        }
        $data['drivers'] = $drivers;
        return $data;
    }

    public function payOrder($order_id)
    {
        $map = OrderQueueMap::model()->findByAttributes(
                    array('order_id' => $order_id),
                    array('order'    => 'id ASC')
               );   

        $queue = OrderQueue::getDbReadonlyConnection()->createCommand()
                    ->select('*')
                    ->from('t_order_queue')
                    ->where('id = :id' , array(':id' => $map->queue_id))
                    ->order('id ASC')
                    ->queryRow();

        if (empty($queue)) {
            return false;
        }    

        $orders = QueueApiOrder::model()->get($queue['phone']."_".$queue['callid'], 'orders');
        if (empty($order)) {
            return false;
        }

        foreach ($orders as $index => $order) {
            if ($order['order_id'] == $order_id) {
                $orders[$index]['pay_status'] = 1;
                break;
            }
        }

        QueueApiOrder::model()->update($queue['phone']."_".$queue['callid'], 'orders' , $orders);

        return true;
    }

    /**
     * 支持客户端取消读秒,生成订单前,预先在redis中伪造写入部分信息
     * @param params array
     * @return array
     */
    public function init_phonebooking($params) {
        $orders = array();
        $number = isset($params['number']) ? intval($params['number']) : 1;
	for($i=0; $i<$number; $i++) {
            if (0 == $i && $number > 1) { 
                $role = '组长';
            } else {
                $role = '组员';
            }    
	    $order_id = Tools::getUniqId('nomal');
            $orders[$order_id] = array(
                'order_id' => $order_id,
                'driver_id' => Push::DEFAULT_DRIVER_INFO,
                'driver_phone' => Push::DEFAULT_DRIVER_INFO,
                'driver_name' => Push::DEFAULT_DRIVER_INFO,
                'location_start' => isset($params['address']) ? $params['address'] : '',
                'location_end' => '',
                'cash_only' => isset($params['cash_only']) ? intval($params['cash_only']) : 0, //0表示非现金支付
                'pay_status' => 0,  //0代表未支付，1表示已支付
                'status' => Order::ORDER_READY ,
                'order_state' =>OrderProcess::ORDER_PROCESS_NEW,
                'order_states' => array(
                    array(
                        'order_state_code' => OrderProcess::ORDER_PROCESS_NEW,
                        'order_state_timestamp' => time(),
                        'order_state_content' => '正在联络司机',
                    ),  
                ),
                'role' => $role,
                'bonus_sn' => '',
                'select_driver_id' => '',
                'select_driver_name' => '',
	            'fake'   => 'yes', //伪造数据标记
            );
        }

        $data = array(
            'queue_id' => Tools::getUniqId('nomal'),
            'booking_id' => isset($params['callid']) ? $params['callid'] : '',
            'booking_type' => '',
	    'source' => isset($params['source']) ? $params['source'] : Order::SOURCE_CLIENT,
            'booking_time' => isset($params['booking_time']) ? $params['booking_time'] : '',
            'phone' => isset($params['phone']) ? $params['phone'] : '',
            'contact_phone' => isset($params['contact_phone']) ? $params['contact_phone'] : '',
            'city_id' => isset($params['city_id']) ? $params['city_id'] : '',
            'number' => $number,
            'address' => isset($params['address']) ? $params['address'] : '',
            'lng' => isset($params['lng']) ? $params['lng'] : '0.000000',
            'lat' => isset($params['lat']) ? $params['lat'] : '0.000000',
            'google_lng' => isset($params['google_lng']) ? $params['google_lng'] : '0.000000',
            'google_lat' => isset($params['google_lat']) ? $params['google_lat'] : '0.000000',
            'flag' => OrderQueue::QUEUE_WAIT_COMFIRM,
            'ready_time' => 0,
            'orders' => $orders,
        );

        return $data;
    }
}
?>


