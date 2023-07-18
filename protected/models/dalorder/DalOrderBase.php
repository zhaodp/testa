<?php
/**
 * 将组装信息和写数据库抽象出来，其他地方可以继承调用
 * @author AndyCong<congming@edaijia-staff.cn>
 * @version 2013-01-04
 */
abstract class DalOrderBase {
	const SINGLE_PUSH_DRIVER = 1; //单人带工号直接推送给司机
	const SINGLE_CHANGE      = 2; //单人不带工号

	const QUEUE_CHANNEL_SINGLE_DRIVER = '01001'; //选司机下单
	const QUEUE_CHANNEL_SINGLE_CHANGE = '01002'; //换一个
	const QUEUE_CHANNEL_BOOKING       = '01003'; //一键预约
	const QUEUE_CHANNEL_CALLORDER     = '01004'; //电话订单
	const QUEUE_CHANNEL_DRIVER_INPUT  = '01005'; //开启新订单
	const QUEUE_CHANNEL_LEISHI        = '01011'; //雷石

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

	const DISPATCH_BACK_TIME      = 600; //离预约多长时间弹回不派单
	
	const SOURCE_CLIENT_MSG = '直呼APP';
    const SOURCE_CALLCENTER_MSG = '呼叫中心';
    const SOURCE_CLIENT_INPUT_MSG = '客户端补单';
    const SOURCE_CALLCENTER_INPUT_MSG = '客户端补单';
    
    const DEFAULT_DRIVER_INFO = 'BJ00000';
    
	public function OrderSingleFactory($params) {}  //选司机工厂方法
	public function OrderMuiltFactory($params) {}   //一键下单工厂方法
	public function CallcenterFactory($params) {}   //呼叫中心派单工厂方法
	public function OrderReceiveFactory($params) {} //司机接单工厂方法
	
	/**
     * 组装Queue数据
     * @param array $params
     * @return array $data
     * @author AndyCong<
     */
    public function orgQueueData($params , $flag = OrderQueue::QUEUE_WAIT_COMFIRM) {
		$comments = '';
    	
		//获取客户姓名
		$name = $this->getCustomerName($params['phone']);
		$time = time();
		$data = array(
		    'phone' => $params['phone'], 
		    'city_id' => $params['city_id'],
		    'address' => $params['address'],
		    'agent_id' => isset($params['agent_id']) ? trim($params['agent_id']) : OrderQueue::QUEUE_AGENT_CLIENT,
		    
		    'name' => $name,
		    'comments' => $comments,
		    'created' => date('Y-m-d H:i:s' , $time),
		    'update_time' => '',
		    
		    'contact_phone' => isset($params['contact_phone']) ? trim($params['contact_phone']) : $params['phone'],
		    'callid' => isset($params['callid']) ? $params['callid'] : Tools::getUniqId('high'),
		    'number' => isset($params['number']) ? $params['number'] : 1,
		    'dispatch_number' => isset($params['dispatch_number']) ? $params['dispatch_number'] : 0,
		    //
		    'booking_time' => isset($params['booking_time']) ? $params['booking_time'] : date('Y-m-d H:i:s' ,$time+1200),
		    'call_time' => isset($params['call_time']) ? $params['call_time'] : date('Y-m-d H:i:s' ,$time),
		    'flag' => $flag,
		    'type' => isset($params['source']) ? $params['source'] : Order::SOURCE_CLIENT, 
		    'dispatch_agent' => isset($params['dispatch_agent']) ? $params['dispatch_agent'] : self::SOURCE_CLIENT_MSG,
		    'dispatch_time' => isset($params['dispatch_time']) ? $params['dispatch_time'] : date('Y-m-d H:i:s' , $time),
		    'lng' => isset($params['lng']) ? $params['lng'] : '0.000000',
		    'lat' => isset($params['lat']) ? $params['lat'] : '0.000000',
		    'google_lng' => isset($params['google_lng']) ? $params['google_lng'] : '0.000000',
		    'google_lat' => isset($params['google_lat']) ? $params['google_lat'] : '0.000000',
		    'channel' => isset($params['channel']) ? $params['channel'] : self::QUEUE_CHANNEL_SINGLE_DRIVER,
		);
		return $data;
    }
    
    /**
     * 组装Order信息
     * @param array $params
     * @return array $data
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-02
     */
    public function orgOrderData($params , $unique_order_id , $unique_queue_id = '' , $callid = '') {
    	$driver_id = isset($params['driver_id']) ? $params['driver_id'] : self::DEFAULT_DRIVER_INFO;
    	$source = isset($params['source']) ? intval($params['source']) : Order::SOURCE_CLIENT;
    	$data = array(
    	    'unique_queue_id' => $unique_queue_id,
    	    'callid' => $callid,
    	    'phone' => trim($params['phone']),
	    	'city_id' => intval($params['city_id']),
	    	'location_start' => trim($params['address']),
	    	'source' => $source,
	    	'order_number' => $unique_order_id,
			'name' => isset($params['name']) ? trim($params['name']) : '先生',
			'contact_phone' => isset($params['contact_phone']) ? trim($params['contact_phone']) : trim($params['phone']),
			'driver' => self::DEFAULT_DRIVER_INFO,
			'driver_id' => self::DEFAULT_DRIVER_INFO,
			'driver_phone' => self::DEFAULT_DRIVER_INFO,
			'imei' => self::DEFAULT_DRIVER_INFO,
			'call_time' => time(),
			'order_date' => date('Ymd', time()),
			'booking_time' => isset($params['booking_time']) ? strtotime($params['booking_time']) : time()+1200,
			'description' => Order::SourceToDescription($source),
			'created' => time(),
			'channel' => isset($params['channel']) ? trim($params['channel']) : self::QUEUE_CHANNEL_SINGLE_DRIVER,
    	);
    	return $data;
    }
    
    /**
     * 组装Map信息
     * @param array $params
     * @return array $data
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-02
     */
    public function orgMapData($params , $queue_id , $order_id) {
    	$data = array(
    	    'queue_id' => $queue_id,
		    'order_id' => $order_id,
		    'driver_id' => isset($params['driver_id']) ? trim($params['driver_id']) : self::DEFAULT_DRIVER_INFO,
		    'number' => isset($params['number']) ? intval($params['number']) : 1,
		    'flag' => isset($params['flag']) ? intval($params['flag']) : OrderQueueMap::MAP_CONFIRM,
		    'dispatch_time' => isset($params['dispatch_time']) ? $params['dispatch_time'] : date('Y-m-d H:i:s' , time()),
		    'confirm_time' => isset($params['confirm_time']) ? $params['confirm_time'] : date('Y-m-d H:i:s' , time()),
    	);

    	return $data;
    }
    
    /**
     * 组装Message信息
     * @param array $params
     * @param string $push_msg_id
     * @return array $data
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-02
     */
    public function orgMsgData($params) {
    	if (empty($params['client_id']) || empty($params['type']) || empty($params['content'])) {
    		return array();
    	}
    	$data = array(
    	    'client_id' => $params['client_id'],
			'queue_id' => isset($params['queue_id']) ? $params['queue_id'] : '0',
			'type' => $params['type'],
			'content' => $params['content'],
			'level' => isset($params['level']) ? $params['level'] : EPush::LEVEL_HIGN,
			'version' => isset($params['version']) ? $params['version'] : 'driver',
			'driver_id' => isset($params['driver_id']) ? $params['driver_id'] : '',
			'flag' => isset($params['flag']) ? $params['flag'] : '1',
			'offline_time' => isset($params['offline_time']) ? $params['offline_time'] : '0',
			'created' => isset($params['created']) ? $params['created'] : date('Y-m-d H:i:s'),
    	);
    	return $data;
    }
    
    /**
     * 将queue写入数据库
     * @param array $params
     * @param array $params
     * @param boolean $result
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-02
     */
    public function queueInsert($data , $unique_queue_id) {
    	$model = new OrderQueue();
		$model->attributes = $data;
		$model->lng = $data['lng'];
		$model->lat = $data['lat'];
		$model->google_lng = $data['google_lng'];
		$model->google_lat = $data['google_lat'];
		$model->channel = $data['channel'];
		$result = $model->save();
		if ($result) {
			$queue_id = Yii::app()->dborder->getLastInsertID(); 
			echo "\n queue insert success, queue_id is ".$queue_id." and cache_queue_id is ".$unique_queue_id." \n";
			return $queue_id;
		} else {
			echo "\n queue insert failed, cache_queue_id is ".$unique_queue_id." \n";
			return 0;
		}
    }
    
    /**
     * 将order写入数据库
     * @param array $params
     * @return boolean $result
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-02
     */
    public function orderInsert($data , $unique_order_id) {
    	$sql = 'insert into t_order (order_number,name,phone , contact_phone , source,driver,city_id,driver_id,driver_phone,
						imei,call_time,order_date,booking_time,location_start,description,created,channel)
						values ("%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s",%s,"%s")';
        $sql = sprintf($sql, $data['order_number'] , $data['name'], $data['phone'], $data['contact_phone'] , $data['source'], $data['driver'] , $data['city_id'] , $data['driver_id'] , $data['driver_phone'] , $data['imei'], $data['call_time'] , $data['order_date'] , $data['booking_time'] , $data['location_start'] , $data['description'] , $data['created'] , $data['channel']);
        $result = Order::getDbMasterConnection()->createCommand($sql)->execute();
        if ($result) {
        	$order_id = Order::getDbMasterConnection()->getLastInsertID(); 
        	echo "\n order insert success, order_id is ".$order_id." and cache_order_id is ".$unique_order_id." \n";
        	return $order_id;
        } else {
        	echo "\n order insert failed, cache_order_id is ".$unique_order_id." \n";
        	return 0;
        }
    }
    
    /**
     * 将map写入数据库
     * @param array $params
     * @return boolean $result
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-02
     */
    public function mapInsert($data , $unique_map_id) {
    	//这里需要将queue_id和order_id转换成数据库中真是存在的id 这个需要处理
		$model = new OrderQueueMap();
		$model->attributes = $data;
		$result = $model->save();
		if ($result) {
			$map_id = OrderQueueMap::getDbMasterConnection()->getLastInsertID(); 
			echo "\n map insert success, map_id is ".$map_id." and cache_map_id is ".$unique_map_id." \n";
			return $map_id;
		} else {
			echo "\n map insert failed, cache_map_id is ".$unique_map_id." \n";
			return 0;
		}
    }
    
    /**
     * 更新Queue表
     * @param array $params
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-05
     */
    public function updateQueue($params) {
    	
    }
    
    /**
     * 更新Order表
     * @param array $params
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-05
     */
    public function updateOrder($params) {
    	
    }
    
    /**
     * 取消订单
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-06
     */
    public function cancelOrder($params) {
    	
    }
    
    /**
     * 组装数据信息（兼容老版本的数据缓存）
     * @param array $params
     * @return array $data
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-03
     */
    public function orgOldCacheData($params , $unique_queue_id , $unique_order_id) {
    	
    }
    
    /**
     * 更新数据缓存
     * @param int $unique_queue_id
     * @param int $unique_order_id
     * @param array $driver
     * @param string $order_state
     */
    public function updateOldCacheData($unique_queue_id , $unique_order_id , $driver , $order_state = OrderProcess::ORDER_PROCESS_ACCEPT) {
    	
    }
    
    /**
     * 将推送消息记录message_log
     * @param array $data
     * @param string $unique_push_msg_id
     * @return boolean
     * @version 2014-01-02
     */
    public function messageInsert($data , $unique_push_msg_id) {
    	$tab = 't_message_log_'.date('Ym');
		$attr = array(
			'client_id'=>$data['client_id'],
			'type'=>$data['type'],
			'content'=>json_encode($data['content']),
			'level'=>isset($data['level']) ? $data['level'] : EPush::LEVEL_HIGN ,
			'driver_id'=>$data['driver_id'],
			'queue_id'=>isset($data['queue_id']) ? $data['queue_id'] : 0,
			'version'=>isset($data['version']) ? $data['version'] : 'driver',
            'created'=>isset($data['created']) ? $data['created'] : date('Y-m-d H:i:s'),
		);
		$result = Yii::app()->dbreport->createCommand()->insert($tab , $attr);
		if ($result) {
			$push_msg_id = Yii::app()->dbreport->getLastInsertID(); 
			echo "\n message_log insert success, push_msg_id is ".$push_msg_id." and cache_push_msg_id is ".$unique_push_msg_id." \n";
			return $push_msg_id;
		} else {
			echo "\n message_log insert failed, cache_push_msg_id is ".$unique_push_msg_id." \n";
			return 0;
		}
    }
    
    /**
     * 获取客户姓名,这个方法也不需要，稍后需要增加客户的cache
     * @param string $phone
     * @return string $name
     */
    public function getCustomerName($phone) {
		return CustomerApiOrder::model()->getCustomerName($phone);
    }
    
    /**
     * 该函数已废弃,请使用 Order::SourceToDescription 2014-12-03
     * 获取描述信息
     * @param int $source
     * @return string $text
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-01-04
     */
    public function getSourceText($source) {
		switch ($source) {
			case Order::SOURCE_CLIENT:
				$text = self::SOURCE_CLIENT_MSG;
				break;
			case ORDER::SOURCE_CALLCENTER:
				$text = self::SOURCE_CALLCENTER_MSG;
				break;
			case Order::SOURCE_CLIENT_INPUT:
				$text = self::SOURCE_CLIENT_INPUT_MSG;
				break;
			case Order::SOURCE_CALLCENTER_INPUT:
				$text = self::SOURCE_CLIENT_INPUT_MSG;
				break;
			default:
				$text = self::SOURCE_CLIENT_MSG;
				break;
		}
        return $text;
	}
	
	/**
	 * 验证为客户服务人数(将之前的CustomerApiOrder中方法迁过来)
	 * @param string $phone
	 * @return int $cnt
	 * @author AndyCong<congming@edaijia-staff.cn>
	 * @version 2014-01-09
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

    	$cache = ROrder::model()->getallorders($phone);
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
}
?>
