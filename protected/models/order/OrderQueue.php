<?php

/**
 * This is the model class for table "{{order_queue}}".
 *
 * The followings are the available columns in table '{{order_queue}}':
 * @property integer $id
 * @property integer $city_id
 * @property string $callid
 * @property string $name
 * @property string $phone
 * @property string $contact_phone
 * @property integer $number
 * @property string $address
 * @property string $booking_time
 * @property string $comments
 * @property string $agent_id
 * @property string $dispatch_agent
 * @property string $dispatch_time
 * @property integer $flag
 * @property string $update_time
 * @property string $created
 */
class OrderQueue extends OrderActiveRecord {
	//等待派单
	const QUEUE_WAIT = 0;
	//已经分派，等待调度接单
	const QUEUE_WAIT_COMFIRM = 1;
	//派单失败，等待客服处理
	const QUEUE_READY = 2;
	//订单取消
	const QUEUE_CANCEL = 3;
	//订单派单成功
	const QUEUE_SUCCESS = 4;
	
	//一键预约添加类型  BY AndyCong
	const QUEUE_TYPE_ACCEPTED = 'accepted';
	const QUEUE_TYPE_FAILURED = 'failured';
	const QUEUE_TYPE_ORDERING = 'ordering';
	const QUEUE_TYPE_CANCELED = 'canceled';
	const QUEUE_TYPE_ORDERED = 'ordered';
	const QUEUE_TYPE_FINISHED = 'finished';
	//一键预约添加类型  BY AndyCong END
	
	//下单类型 BY AndyCong
	const QUEUE_AGENT_CLIENT = '直呼APP';
	const QUEUE_AGENT_CALLCENTER = '400接单';
	const QUEUE_AGENT_KEYBOOKING = '一键预约';
	const QUEUE_AGENT_DRIVERBOOKING = '司机代下单';
	const QUEUE_AGENT_WEIXIN = '微信';
	//下单类型 BY AndyCong END
	
	public $begin_booking_time;
	public $end_booking_time;

    public $booking_time_day;
    public $booking_time_time;
	
	public function init() {
		$this->begin_booking_time = date('Y-m-d 07:00:00', time()-7*60*60);
		$this->end_booking_time = date('Y-m-d H:i:s', time()+2*86400);
		
		parent::init();
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return OrderQueue the static model class
	 */
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{order_queue}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array (
			array (
				'city_id, callid, name, phone, number, address, booking_time, agent_id', 
				'required'), 
			array (
				'city_id, number,dispatch_number, flag , type,is_vip', 
				'numerical', 
				'integerOnly'=>true), 
			array (
				'callid', 
				'length', 
				'max'=>40), 
			array (
				'name, phone, contact_phone, dispatch_agent', 
				'length', 
				'max'=>20), 
			array (
				'address', 
				'length', 
				'max'=>100), 
			array (
				'comments', 
				'length', 
				'max'=>500), 
			array (
				'agent_id', 
				'length', 
				'max'=>10), 
			array (
				'dispatch_time, update_time,created,lng,lat,google_lng,google_lat,channel', 
				'safe'), 
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array (
				'id, city_id, callid, name, phone, contact_phone, number, dispatch_number , address, booking_time, comments, agent_id, dispatch_agent, dispatch_time, flag, update_time, created , type,is_vip', 
				'safe', 
				'on'=>'search'));
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array ();
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() {
		return array (
			'id'=>'ID', 
			'city_id'=>'城市', 
			'callid'=>'Callid', 
			'name'=>'客户姓名', 
			'phone'=>'客户电话', 
			'contact_phone'=>'联系人电话', 
			'number'=>'人数 ',
			'dispatch_number'=>'己派人数 ',
			'address'=>'地址', 
			'booking_time'=>'预约时间', 
			'comments'=>'备注', 
			'agent_id'=>'接单调度', 
			'dispatch_agent'=>'派单调度', 
			'dispatch_time'=>'派单时间', 
			'flag'=>'派单状态', 
			'update_time'=>'Update Time', 
			'created'=>'接单时间',
			'type'=>'类型');
	}
	
	public static function getCallTimeByQueueId($queueId = 0) {
		
		$datetime = date('Y-m-d H:i:s');
		$callLogSheetId = 1;
		
		$orderQueue = self::model()->find('id=:id', array (
			':id'=>$queueId));
		if ($orderQueue) {
			if (strlen($orderQueue->callid)>30) {
				$callLogSheetId = $orderQueue->callid;
			} else {
				$criteria = new CDbCriteria();
				$criteria->condition = 'phone=:phone and id<:id and flag=:flag and length(callid) > 30';
				$criteria->params = array (
					':phone'=>$orderQueue->phone, 
					':id'=>$queueId, 
					':flag'=>OrderQueue::QUEUE_CANCEL);
				$criteria->limit = 1;
				$criteria->order = 'id DESC';
				$lastOrderQueue = self::model()->find($criteria);
				if ($lastOrderQueue) {
					$callLogSheetId = $lastOrderQueue->callid;
				} else {
					return array (
						'Ring'=>$datetime, 
						'Begin'=>$datetime, 
						'End'=>$datetime);
				}
			}
			
			$sql = "SELECT Ring, Begin, End FROM t_callcenter_log WHERE CallsheetId='".$callLogSheetId."'";
			
			$callCenterLog = Yii::app()->db_readonly->createCommand($sql)->queryRow();
			
			if ($callCenterLog) {
				if (trim($callCenterLog['Begin'])=='0000-00-00 00:00:00') {
					$callCenterLog['Begin'] = $callCenterLog['Ring'];
				}
				return $callCenterLog;
			} else {
				return array (
					'Ring'=>$datetime, 
					'Begin'=>$datetime, 
					'End'=>$datetime);
			}
		
		} else {
			return array (
				'Ring'=>$datetime, 
				'Begin'=>$datetime, 
				'End'=>$datetime);
		}
	}
	
	public function beforeSave() {
		if ($this->isNewRecord) {
			//检查callid是否已经存在
			$queue = self::model()->find('callid=:callid', array (
				':callid'=>$this->callid));
			if ($queue) {
				return false;
			} else {
				if (!isset($this->name)) {
					$this->name = '先生';
				}
				//预约时间不能小于当前时间
				$this->model()->addError('booking_time', '预约时间不能小于当前时间');
				$this->created = date(Yii::app()->params['formatDateTime'], time());
			}
		} else {
			$comments = '';
			//把派单司机的名单写到队列的备注中
//			$drivers = OrderQueueDispatch::model()->findAll('queue_id=:queue_id', array (
//				':queue_id'=>$this->id));

            //获取司机工号从t_order_queue_map BY AndyCong 2013-05-23
			$drivers = OrderQueueMap::model()->findAll('queue_id=:queue_id and driver_id <> :driver_id', array (
				':queue_id'=>$this->id , ':driver_id' => Push::DEFAULT_DRIVER_INFO));
		    //获取司机工号从t_order_queue_map BY AndyCong 2013-05-23 END
		    
			if ($drivers) {
				foreach($drivers as $driver) {
					$driver_info = Driver::getProfile($driver->driver_id);
					$comments .= sprintf('%s %s', $driver->driver_id, $driver_info->phone)."<br/>";
				}
				
				if ($this->comments) {
					$this->comments .= '<br/>'.$comments;
				} else {
					$this->comments .= $comments;
				}
				$this->dispatch_agent = Yii::app()->user->id;
				$this->dispatch_time = date(Yii::app()->params['formatDateTime'], time());
			}
		
		}
		return true;
	}

	public function afterSave() {
	    if($this->isNewRecord) {
	        // 记录agent_id,400派单队列页面显示调度人员列表
	        if(!empty($this->agent_id)) {
		    AdminCache::model()->cache_agent_id($this->agent_id);
		}
	    }
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		//指定model的数据库类型为从库 BY AndyCong 2013-11-29
		$criteria = new CDbCriteria();
		
		$criteria->compare('id', $this->id);
		$criteria->compare('city_id', $this->city_id);
		$criteria->compare('callid', $this->callid );
		$criteria->compare('name', $this->name );
		$criteria->compare('phone', $this->phone );
		
		if( !empty($this->is_vip) ){
			$criteria->compare('is_vip', $this->is_vip );
		}
		
		//添加联系人查询 或的关系 BY AndyCong 2013-05-07
		if ($this->phone) {
			$criteria->addCondition("contact_phone='".$this->phone."'" , 'OR');
		}
		
		$criteria->compare('contact_phone',$this->contact_phone);
		$criteria->compare('number', $this->number);
		//$criteria->compare('dispatch_number', $this->dispatch_number);
		$criteria->compare('address', $this->address,true);
		$criteria->compare('comments', $this->comments);
		$criteria->compare('agent_id', $this->agent_id);
		$criteria->compare('dispatch_agent', $this->dispatch_agent);
		$criteria->compare('dispatch_time', $this->dispatch_time);

        $criteria->compare('flag', $this->flag);
        $user_id = isset(Yii::app()->user->user_id) ? Yii::app()->user->user_id : 1;
        if ($user_id != 130) {
			$criteria->addCondition("channel not in('01001' , '01002' , '01003' , '01004' , '01005' , '01006', '01007')" , 'AND');
		}

		//$criteria->compare('update_time', $this->update_time, true);
		$criteria->compare('booking_time', '>='.$this->begin_booking_time);
		

		//TODO:队列状态不等于等待派单或者查询全部状态时，只显示当天的队列
		if ($this->begin_booking_time&&$this->end_booking_time) {
			//$criteria->addBetweenCondition('booking_time', $this->begin_booking_time, $this->end_booking_time);
		}
		
		if ($this->flag==self::QUEUE_SUCCESS) {
			$criteria->order = 'dispatch_time desc';
		} else {
			$criteria->order = 'flag,booking_time,created,number desc';
		}
		
		//print_r($criteria);
		$rowData = self::model()->findAll($criteria);
		$data = new CArrayDataProvider($rowData, array(
			'id'	=> 'id',
			'pagination'=>array(
				'pageSize'=>10,
			),
		));
	
		//查询后指定model的数据库类型为主库 BY AndyCong 2013-11-29
		return $data;
	}
	

	/**
	 * 设置成手动派单，把自动派单设置成手动派单
	 * 
	 * @param unknown_type $queue_id
	 * @param unknown_type $comments
	 */
	public function setOrder2ManualOpt($queue_id,$comments=''){
		$ret = false;
		if( empty($queue_id) || empty($comments) ){
			return $ret;
		}	

		$sql = "UPDATE t_order_queue SET flag = 0 , comments = concat(comments,:comments) WHERE id=:id and flag=1";
		
		// Yii::app()->db change into getDbMasterConnection()
		$command = OrderQueue::getDbMasterConnection()->createCommand($sql);
		$command->bindParam(":comments" , $comments);
		$command->bindParam(":id" , $queue_id);
		$ret = $command->execute();
		return $ret;
	}
	
	/**
	 * 获取队列（一键预约订单）列表
	 * @param string $phone
	 * @param int $offset
	 * @param int $pageSize
	 * @return array $data
	 * @author AndyCong<congming@edaijia.cn>
	 */
	public function getOrderQueueByPhone($phone = null , $offset = 0 , $pageSize = 20 , $token) {
		if ($phone == null) {
			return false;
		}
		//加缓存 一分钟
		$cache_key = 'queuelist_'.$token.'_'.$offset;
		$data = Yii::app()->cache->get($cache_key);
		//加缓存 一分钟 END
		
		if (!$data) {
			//取个数
			// Yii::app()->db_readonly change into getDbReadonlyConnection()
			$count = OrderQueue::getDbReadonlyConnection()->createCommand()
	    	              ->select('COUNT(id) AS cnt')
	    	              ->from('t_order_queue')
	    	              ->where('phone=:phone',array(':phone'=>$phone))
	    	              ->queryRow();
			//取个数 END
			
			//取订单
			// Yii::app()->db_readonly change into getDbReadonlyConnection()
			$result = OrderQueue::getDbReadonlyConnection()->createCommand()
	    	              ->select('id AS order_id , flag , address  AS order_street_name, unix_timestamp(created) AS order_time , number AS order_drivers_count , (unix_timestamp(booking_time)-unix_timestamp(created)) AS order_timedelta_from_now_on , phone AS order_customer_phone , contact_phone AS order_contact_phone , booking_time')
	    	              ->from('t_order_queue')
	    	              ->where('phone=:phone',array(':phone'=>$phone))
	    	              ->order('id desc')
	    	              ->limit($pageSize)
						  ->offset($offset)
	    	              ->queryAll();
	    	//取订单 END
			
			$time = time();
			$current_time = date('Y-m-d H:i:s' , $time);
			foreach ($result as $key=>$val) {
				//获取司机工号
				if ($val['flag'] == self::QUEUE_WAIT) {
					//判定是否过期
					$booking_time = strtotime($val['booking_time']);
					$h = date('H' , $booking_time);
					if (intval($h) < 7) {
						$expired_time = date("Y-m-d" , $booking_time)." 07:00:00";
					} else {
						$expired_time = date("Y-m-d" , ($booking_time+86400))." 07:00:00";
					}
					if ($current_time > $expired_time) {
						$result[$key]['order_status'] = self::QUEUE_TYPE_FAILURED;
					} else {
					    $result[$key]['order_status'] = self::QUEUE_TYPE_ACCEPTED;
					}
					//判定是否过期 end
	//				$result[$key]['order_status'] = self::QUEUE_TYPE_ACCEPTED;
				} elseif ($val['flag'] == self::QUEUE_WAIT_COMFIRM) {
					$result[$key]['order_status'] = self::QUEUE_TYPE_ORDERING;
				} elseif ($val['flag'] == self::QUEUE_READY) {
					$result[$key]['order_status'] = self::QUEUE_TYPE_ORDERING;
				} elseif ($val['flag'] == self::QUEUE_CANCEL) {
					$result[$key]['order_status'] = self::QUEUE_TYPE_CANCELED;
				} elseif ($val['flag'] == self::QUEUE_SUCCESS) {
					$result[$key]['order_status'] = self::QUEUE_TYPE_ORDERED;
				}
				if (empty($result[$key]['order_contact_phone'])) {
					$result[$key]['order_contact_phone'] = $result[$key]['order_customer_phone'];
				}
				
				$result[$key]['order_contact_phone'] = substr_replace($result[$key]['order_contact_phone'], '****', 3, 4);
				$result[$key]['order_customer_phone'] = substr_replace($result[$key]['order_customer_phone'], '****', 3, 4);
				
				$result[$key]['order_drivers'] = $this->getDriverIDByQueueID($val['order_id']);
				
				//派单中判定是否已完成
				if ($result[$key]['order_status'] == self::QUEUE_TYPE_ORDERED) {
					$status = $this->getQueueFlag($val['order_id']);
					if ($status) {
						$result[$key]['order_status'] = self::QUEUE_TYPE_FINISHED; 
					}
				}
				//派单中判定是否已完成 END
				
				unset($result[$key]['flag']);
				unset($result[$key]['booking_time']);
			}
			$data = array(
			    'orderList' => $result,
			    'orderCount' => $count['cnt'],
			);
			Yii::app()->cache->set($cache_key, $data, 60);
		}
		return $data;
	}
	
	public function getQueueFlag($queue_id) {
		if (empty($queue_id)) {
			return false;
		}
		$status = true;
        $orderQueueMapList = OrderQueueMap::model()->findAllByAttributes(
            array('queue_id' => $queue_id),
            array('select'   => 'order_id')
        );
		foreach ($orderQueueMapList as $map) {
			$order = Order::model()->findByPk($map->order_id);
			if ($order->status == Order::ORDER_READY) {
				$status = false;
			}
		}
		return $status;
	}
	
	/**
	 * 获取订单详情
	 * @param int $queue_id
	 * @param string $phone
	 * @return array $result
	 * @author AndyCong<congming@edaijia.cn>
	 */
	public function getOrderQueueByID($queue_id , $phone , $gps_type = null , $token) {
		if (empty($queue_id) || empty($phone)) {
			return false;
		}
		if (strlen($queue_id) > 20) {
			return Yii::app()->cache->get($token.'_'.$phone);
		}
		//加缓存 一分钟
		$cache_key = $token."_".$queue_id;
		$result = Yii::app()->cache->get($cache_key);
		//加缓存 一分钟 END
		
		if (!$result) {
			$arr = explode('_' , $queue_id);
			if ($arr[0]."_" == Order::ORDER_ID_PRE) {
				$result = Order::model()->getOrderByIdPhone($arr[1] , $phone , $gps_type);
				$result['order_contact_phone'] = substr_replace($result['order_contact_phone'], '****', 3, 4);
				$result['order_customer_phone'] = substr_replace($result['order_customer_phone'], '****', 3, 4);
				Yii::app()->cache->set($cache_key, $result, 60);
				return $result;
			}
			$result = array();
			//取订单
			// Yii::app()->db_readonly change into getDbReadonlyConnection()
			$result = OrderQueue::getDbReadonlyConnection()->createCommand()
			               ->select('id AS order_id , flag , address  AS order_street_name, unix_timestamp(created) AS order_time , number AS order_drivers_count , (unix_timestamp(booking_time)-unix_timestamp(created)) AS order_timedelta_from_now_on , phone AS order_customer_phone , contact_phone AS order_contact_phone , booking_time')
			               ->from("t_order_queue")
			               ->where('id = :id AND phone = :phone' , array(':id'=>$queue_id , ':phone' => $phone))
			               ->queryRow();
			//取订单 END
			if (!empty($result)) {
				if ($result['flag'] == self::QUEUE_WAIT) {
					//判定是否过期
					$time = time();
					$current_time = date('Y-m-d H:i:s' , $time);
					$booking_time = strtotime($result['booking_time']);
					$h = date('H' , $booking_time);
					if (intval($h) < 7) {
						$expired_time = date("Y-m-d" , $booking_time)." 07:00:00";
					} else {
						$expired_time = date("Y-m-d" , ($booking_time+86400))." 07:00:00";
					}
					if ($current_time > $expired_time) {
						$result['order_status'] = self::QUEUE_TYPE_FAILURED;
					} else {
					    $result['order_status'] = self::QUEUE_TYPE_ACCEPTED;
					}
					//判定是否过期 end
				} elseif ($result['flag'] == self::QUEUE_WAIT_COMFIRM) {
					$result['order_status'] = self::QUEUE_TYPE_ORDERING;
				} elseif ($result['flag'] == self::QUEUE_READY) {
					$result['order_status'] = self::QUEUE_TYPE_ORDERING;
				} elseif ($result['flag'] == self::QUEUE_CANCEL) {
					$result['order_status'] = self::QUEUE_TYPE_CANCELED;
				} elseif ($result['flag'] == self::QUEUE_SUCCESS) {
					$result['order_status'] = self::QUEUE_TYPE_ORDERED;
				}
				if (empty($result['order_contact_phone'])) {
					$result['order_contact_phone'] = $result['order_customer_phone'];
				}
				
				$result['order_contact_phone'] = substr_replace($result['order_contact_phone'], '****', 3, 4);
				$result['order_customer_phone'] = substr_replace($result['order_customer_phone'], '****', 3, 4);
				
				$result['order_drivers'] = $this->getDriverIDByQueueID($queue_id , true , $gps_type);
				
				//派单中判定是否已完成
				if ($result['order_status'] == self::QUEUE_TYPE_ORDERED) {
					$status = $this->getQueueFlag($queue_id);
					if ($status) {
						$result['order_status'] = self::QUEUE_TYPE_FINISHED; 
					}
				}
				//派单中判定是否已完成 END
				
//				$result['money'] = $this->_getOrderSubmitMoney($queue_id);
				
				unset($result['booking_time']);
				Yii::app()->cache->set($cache_key, $result, 60);
			}
		}
		return $result;
	}
	
	/**
	 * 通过queue_id获取driver_id
	 * @param int $queue_id
	 * @return array $result
	 * @author AndyCong<congming@edaijia.cn>
	 */
	public function getDriverIDByQueueID($queue_id = 0 , $driver_info = false , $gps_type = null) {
		if ($queue_id == 0) {
			return false;
		}
		$driverInfo = array();
		$result = array();
		
        $orderQueueMapList = OrderQueueMap::model()->findAllByAttributes(
                array('queue_id' => $queue_id),
                array('select'   => 'order_id, driver_id')
        );
        foreach ($orderQueueMapList as $orderQueueMap) {
            $result[] = $orderQueueMap->getAttributes();
        }
        
		if ($driver_info) {
			foreach ($result as $key=>$val) {
				$driver = $this->getOrderDriverInfo($val['order_id'] , $val['driver_id'] , $gps_type);
				if (!empty($driver)) {
					$driverInfo[] = $driver;
				}
			}
			return $driverInfo;
		} else {
			return $result;
		}
	}
	
	/**
	 * 获取司机详细信息
	 * @param intval $driver_id
	 * @return array $driverInfo
	 */
	public function getDriverInfo($driver_id = null , $gps_type = null) {
		if ($driver_id == null) {
			return false;
		}
		$driver=DriverStatus::model()->get($driver_id);
		if ($driver) {
			if ($driver->info['level']==''||empty($driver->info['level'])) {
				$new_level=0;
			} else {
				$new_level=$driver->info['level'];
			}
			$id_card=isset($driver->info['id_card']) ? substr_replace($driver->info['id_card'], '******', 10, 6) : '';
			$car_card=isset($driver->info['car_card']) ? substr_replace($driver->info['car_card'], '******', 10, 6) : '';
			
			switch ($gps_type) {
				case 'google' :
					$longitude=$driver->position['google_lng'];
					$latitude=$driver->position['google_lat'];
					break;
				default :
					$longitude=$driver->position['baidu_lng'];
					$latitude=$driver->position['baidu_lat'];
					break;
			}
			$distance = '1.0公里';
			$driverInfo=array(
					'driver_id'=>$driver_id,
					'id'=>$driver->info['imei'],
					'name'=>$driver->info['name'],
					'picture'=>'',
					'phone'=>$driver->phone,
					'idCard'=>$id_card,
					'domicile'=>$driver->info['domicile'],
					'card'=>$car_card,
					'year'=>$driver->info['year'],
					'level'=>round($driver->info['level']),
					'new_level'=>$new_level,
					'goback'=>$driver->goback,
					'state'=>$driver->status,
					'price'=>'',
					'order_count'=>$driver->service['service_times'],
					'comment_count'=>$driver->service['high_opinion_times'],
					'longitude'=>$longitude,
					'latitude'=>$latitude,
					'distance'=>$distance,
					'picture_small'=>$driver->info['picture_small'],
					'picture_middle'=>$driver->info['picture_middle'],
					'picture_large'=>$driver->info['picture_large']
			);
		}else {
			$driverInfo = null;
		}
		return $driverInfo;
	}
	
	/**
	 * 获取司机详细信息
	 * @param intval $driver_id
	 * @return array $driverInfo
	 */
	public function getOrderDriverInfo($order_id = 0 , $driver_id = null , $gps_type = null) {
		if (0 == $order_id || $driver_id == null) {
			return false;
		}
		$driverInfo = array();
		$driver=DriverStatus::model()->get($driver_id);
		if ($driver && $driver_id != Push::DEFAULT_DRIVER_INFO && $driver_id !='未派') {
			if ($driver->info['level']==''||empty($driver->info['level'])) {
				$new_level=0;
			} else {
				$new_level=$driver->info['level'];
			}
			$id_card=isset($driver->info['id_card']) ? substr_replace($driver->info['id_card'], '******', 10, 6) : '';
			$car_card=isset($driver->info['car_card']) ? substr_replace($driver->info['car_card'], '******', 10, 6) : '';
			
			switch ($gps_type) {
				case 'google' :
					$longitude=$driver->position['google_lng'];
					$latitude=$driver->position['google_lat'];
					break;
				default :
					$longitude=$driver->position['baidu_lng'];
					$latitude=$driver->position['baidu_lat'];
					break;
			}
			$distance = '1.0公里';
			$driverInfo=array(
					'driver_id'=>$driver_id,
					'id'=>$driver->info['imei'],
					'name'=>$driver->info['name'],
					'money' => 0,
					'picture'=>'',
					'phone'=>$driver->phone,
					'idCard'=>$id_card,
					'domicile'=>$driver->info['domicile'],
					'card'=>$car_card,
					'year'=>$driver->info['year'],
					'level'=>round($driver->info['level']),
					'new_level'=>$new_level,
					'goback'=>$driver->goback,
					'state'=>$driver->status,
					'price'=>'',
					'order_count'=>$driver->service['service_times'],
					'comment_count'=>$driver->service['high_opinion_times'],
					'longitude'=>$longitude,
					'latitude'=>$latitude,
					'distance'=>$distance,
					'picture_small'=>$driver->info['picture_small'],
					'picture_middle'=>$driver->info['picture_middle'],
					'picture_large'=>$driver->info['picture_large']
			);
			$order = Order::model()->findByPk($order_id);
			if ($order) {
				if (in_array($order->status , array(Order::ORDER_COMPLATE , Order::ORDER_NOT_COMFIRM))) {
					$driverInfo['money'] = $order->income;
				}
				$driverInfo['order_status'] = $order->status;
			}
		}
		return $driverInfo;
	}
	
	private function _getOrderSubmitMoney($queue_id) {
        $orderQueueMapList = OrderQueueMap::model()->findAllByAttributes(
            array('queue_id' => $queue_id),
            array('select'   => 'order_id')
        );

        $money = 0;
	    foreach ($orderQueueMapList as $map) {
	    	$order = Order::model()->findByPk($map->order_id);
	    	if (in_array($order->status , array(1 , 4))) {
	    		$money += $order->income;
	    	}
	    }
	    return $money;
	}
	
	/**
	 * 取消队列（一键预约订单）
	 * @param unknown_type $queue_id
	 * @return unknown
	 * @author AndyCong<congming@edaijia.cn>
	 */
	public function cancelOrderQueueByID($queue_id = 0) {
		if ($queue_id == 0) {
			return false;
		}
		$sql = "UPDATE t_order_queue SET flag = 3 WHERE id=:id";
		
		// Yii::app()->db change into getDbMasterConnection()
		$command = OrderQueue::getDbMasterConnection()->createCommand($sql);
		$command->bindParam(":id" , $queue_id);
		$result = $command->execute();
		return $result;
	}

    /**
     * 取队列已派单人数
     * 
     * 
     * @param unknown_type $queue_ids
     */
    public function getDriverNumber($end_time = '' , $limit = 10 )
    {
    	//queue_id,dispatch_num,lng,lat
    	$ret = array();
    	
     	//获取状态为1的queue list
     	
        $sql = " select id,phone,city_id,number,dispatch_number,address,booking_time,lng,lat,created from t_order_queue  where booking_time <= :end_time and flag = 1 and comments not like '".Push::AUDIO_COMMENTS."%' order by id asc limit  ".$limit ;

        // Yii::app()->db change into getDbMasterConnection()
        $result  = OrderQueue::getDbMasterConnection()->createCommand($sql)->queryAll(true , array('end_time'=>trim($end_time)));
        if(empty($result)){
            return array();
        }

        foreach($result as $val){

        	 $dispatch_num = $this->getQueueMapNum($val['id']);
        	 $tmp['number'] = $val['number'];// - $dispatch_num ;

             /**
             * 当lng或lat 为0时，去地址池里的查位置
             * modify zhanglimin 2013-06-17
             */
             $lng = trim($val['lng']);
             $lat = trim($val['lat']);
             if( $lng == 0  || $lat == 0 ){
                 $address_pool = AddressPool::model()->getAddressFromPool($val['address'],$val['city_id']);
                 $tmp['lng'] = isset($address_pool['lng']) ? $address_pool['lng'] : 0;
                 $tmp['lat'] = isset($address_pool['lat']) ? $address_pool['lat'] : 0;
             }else{
                 $tmp['lng'] = $lng;
                 $tmp['lat'] = $lat;
             }

        	 $tmp['queue_id'] = $val['id'];
        	 $tmp['phone'] = $val['phone'];
        	 $tmp['dispatch_number'] = $val['dispatch_number'];
        	 $tmp['address'] = $val['address'];
        	 $tmp['booking_time'] = $val['booking_time'];
        	 $tmp['created'] = $val['created'];
        	 $ret[$val['id']] = $tmp;
        }
        return $ret;
        
    }
    
    
    /**
     * 取得已派单的人数
     * 
     * @param unknown_type $queue_id
     */
    public function getQueueMapNum($queue_id)
    {
    	$result = 0;
    	if($queue_id){
    		$sql2 = "select count(id) from t_order_queue_map  where  queue_id=:queue_id ";
        	$result = OrderQueueMap::getDbMasterConnection()->createCommand($sql2)->queryScalar(array(':queue_id'=>$queue_id));
    	}
     	
        return $result;
    }
    
    /**
     * 设置queue_id 中分配司机信息缓存(暂时废弃掉)
     * @param int $queue_id
     * @param string $driver_id
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-21
     */
    public function setDispatchDriverCache($queue_id , $driver_id) {
    	if (empty($queue_id) || empty($driver_id)) {
    		return false;
    	}
    	$driver = DriverStatus::model()->get($driver_id);
    	if ($driver) {
	    	$cache_key = Yii::app()->params['CACHE_KEY_DISPATCH_DRIVER'].$queue_id;
	    	$json_cache = Yii::app()->cache->get($cache_key);
	    	if (!$json_cache) {
	    		$json_arr = array(
	    		    $driver_id => array(
	    		        'driver_id' => $driver_id,
	    		        'phone' => $driver->phone,
	    		    ),
	    		);
	    	} else {
	    		$json_arr = json_decode($json_cache , true);
	    		$json_arr[$driver_id] = array(
	    		    'driver_id' => $driver_id,
			        'phone' => $driver->phone,
	    		);
	    	}
	    	$json = json_encode($json_arr);
	    	Yii::app()->cache->set($cache_key, $json, 3600);
    	}
    	return true;
    }
    
    /**
     * 通过queue_id获取缓存派单司机数据
     * @param int $queue_id
     * @return array $result
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-21
     */
    public function getDispatchDriverCache($queue_id) {
    	if (empty($queue_id)) {
    		return '';
    	}
    	$driver_ids = OrderQueueMap::getDbReadonlyConnection()->createCommand()
    	                 ->select('driver_id')
    	                 ->from('t_order_queue_map')
    	                 ->where('queue_id = :queue_id' , array(':queue_id' => $queue_id))
    	                 ->queryAll();
    	$data = array();
    	if (!empty($driver_ids)) {
    		foreach ($driver_ids as $driver_arr) {
    			$driver = DriverStatus::model()->get($driver_arr['driver_id']);
    		    $data[$driver_arr['driver_id']] = array(
    		        'driver_id' => $driver_arr['driver_id'],
    		        'phone' => $driver->phone,
    		    );
	    	}
    	}
    	return $data;
    }


    /**
     * 查询当前订单是否己派
     * @param string $queue_id
     * @return bool
     */
    public function checkComfirm($queue_id = ""){
        $sql=" select id from t_order_queue where id = :id and flag =:flag ";
        
        // Yii::app()->db change into getDbMasterConnection()
        $result=OrderQueue::getDbMasterConnection()->createCommand($sql)->queryRow(true, array(
            ':id'=>$queue_id,
            ':flag'=>self::QUEUE_WAIT_COMFIRM
        ));
        if(empty($result)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 司机客户端一键预约
     * @param array $params
     * @param string $agent
     * @return boolean
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-27
     */
    public function booking($params , $type = Order::SOURCE_CLIENT , $agent = self::QUEUE_AGENT_CLIENT) {
    	if (empty($params)) {
    		return false;
    	}

        //验证order_number是否已经存在
        if (!empty($params['order_number']) && !empty($params['driver_id'])) {
            $order_number_check = Order::model()->find(
                'order_number = :order_number and driver_id = :driver_id',
                array(
                    ':order_number' => $params['order_number'],
		    ':driver_id' => $params['driver_id']
                )
            );
            if($order_number_check) {
    		return array('code' => 0,'message' => '下单成功');
            }    
        }    
    	
        //验证订单
        $is_repeat = Push::model()->validateIsRepeat($params);
    	if ($is_repeat) {
    		return $ret = array('code' => 0,'message' => '下单成功');
    	}
        
        //验证订单 END
        
		//一：生成t_order_queue 
		$order_queue = $this->OrderQueueSave($params , $type , $agent);
		if (empty($order_queue)) {
			return $ret = array(
			    'code' => 2,
			    'message' => '队列生成失败',
			);
		}
		
		//一：生成t_order_queue  END

	    //二：生成t_order	
	    $channel = CustomerApiOrder::QUEUE_CHANNEL_DRIVER_INPUT;
	    $order_arr = array(
            'queue_id' =>$order_queue['id'],
            'driver_id' =>$params['driver_id'],
            'order_number' =>isset($params['order_number']) ? $params['order_number'] : '',
            'name'=>$order_queue['name'],
            'phone'=>$order_queue['phone'],
            'address'=>$order_queue['address'],
            'booking_time'=>$order_queue['booking_time'],
            'city_id'=>$order_queue['city_id'],
            'type'=>$order_queue['type'],
            'created'=>$order_queue['created'],
            'channel' => isset($order_queue['channel']) ? $order_queue['channel'] : $channel,
        );
	    $order = AutoOrder::model()->setGenOrder($order_arr);
	    if ($order['code'] == 1) {
	    	return $ret = array(
			    'code' => 2,
			    'message' => '订单生成失败',
			);
	    } else {
	    	$order_id = $order['order_id'];
	    }
	    
	    //将订单加入缓存 2013-11-27
	    $order_arr['order_id'] = $order_id;
	    $cache_params = Push::model()->getCacheParamsByQueueArr($order_queue , $order_arr);
	    if (!empty($cache_params)) {
	    	$task = array(
			    'method' => 'insert_orders_redis',
			    'params' => $cache_params
			);
			Queue::model()->putin($task , 'orderstate');
	    }
	    //将订单加入缓存 2013-11-27 END

		
	    if (!empty($order_arr['order_number'])) {
	    	CustomerApiOrder::model()->orderFavorableCache($order_arr['order_number']);
	    }
	    
	    //三：生成t_order_queue_map
	    $confirm_time = date('Y-m-d H:i:s');	
	    $order_queue_map = AutoOrder::model()->setOrderQueueRelations($order_id , $order_queue['id'] , $params['driver_id'] , $confirm_time);
	    if (!$order_queue_map || 1 == $order_queue_map['code']) {
	    	return $ret = array(
			    'code' => 2,
			    'message' => '映射关系建立失败',
			);
	    }
	    
        //记录t_order_position
	    $arr = array(
             'order_id' => $order_id,
             'flag' => OrderPosition::FLAG_ACCEPT,
             'gps_type'=>isset($params['gps_type']) ? $params['gps_type'] : 'wgs84',
             'lat'=>isset($params['lat']) ? $params['lat'] : 1,
             'lng'=>isset($params['lng']) ?$params['lng'] : 1,
             'log_time'=>date("Y-m-d H:i:s"),
         );
         $ret = OrderPosition::model()->insertInfo($arr);
         unset($arr);
	
	 // Save the driver id and order id into redis
	 // For order trace
	 $convert_pos = isset($ret['position'])? $ret['position']:array();
	 RDriverPosition::model()->setCurrentOrder(
		 $params['driver_id'], 
		 $order_id, OrderProcess::PROCESS_ACCEPT,
		 $convert_pos);
	    
	    //四：推送消息
        $msg = Push::model()->setPushOrderMsg($order_queue['id'] , $params['driver_id'] , $order_id);
        if (!$msg || 1 == $msg['code']) {
        	return $ret = array(
			    'code' => 2,
			    'message' => '组织消息失败',
			);
        }
        
        //推送消息
        $msg['msg']['order_id'] = $order_id;
        if (isset($params['order_number'])) {
            $msg['msg']['order_number'] = $params['order_number'];    	
        }
        
        $message_arr=array(
            'type'=>IGtPush::TYPE_ORDER_DETAIL,
            'content'=>$msg['msg'],
            'level'=>3,  //级别
            'driver_id'=>$params['driver_id'],
            'queue_id'=>$order_queue['id'],
            'created'=>date('Y-m-d H:i:s' , time()),
        );
        $result = Push::model()->organizeMessagePush($message_arr);
        if (!$result) {
        	return $ret = array(
			    'code' => 2,
			    'message' => '推送消息失败',
			);
        } else {
        	return $ret = array(
			    'code' => 0,
			    'message' => '下单成功',
			);
        }
    }
    
    /**
     * 客户端补单插入t_order_queue
     * @param array $params
     * @param string $source
     * @return array $queue_arr
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-27
     */
    public function OrderQueueSave($params , $type , $agent_id) {
    	if (empty($params) || empty($agent_id)) {
    		return '';
    	}
    	//参数整理
    	$time = time();
    	if (isset($params['booking_time'])) {
    		$booking_time = date("Y-m-d H:i:s" , $params['booking_time']);
    	} else {
			$booking_time = date('Y-m-d H:i:s' ,time());
    	}
//		$call_id = md5(time().floor(microtime()*1000).rand(1000,9999));
        $call_id = md5(uniqid(rand(), true));
		//判定是否为VIP
		$str = '';
		if ($agent_id != self::QUEUE_AGENT_CLIENT) {
			$str = ",该单是".$agent_id;
		}
        $vipInfo = VipService::service()->getVipInfo($params['phone'], false);//这里不取信用额度
        $comments = substr($str,1);
        if ($vipInfo) {
            $status = $vipInfo['status'];
            if (Vip::STATUS_NORMAL == $status) {  // TODO ... 整理仍然有耦合
                $comments = '此用户是vip'. $comments;
            }
            if (Vip::STATUS_ARREARS == $status) {
                $comments = '此用户是vip,余额是' . $vipInfo['amount'].$str;
            }
        }
		$channel = in_array($type, Order::$client_input_source) ? CustomerApiOrder::QUEUE_CHANNEL_DRIVER_INPUT : CustomerApiOrder::QUEUE_CHANNEL_CALLORDER;
		//判定是否为VIP END
		$queue_arr = array(
		    'phone' => $params['phone'],              //客户电话
		    'contact_phone' => $params['phone'],      //客户电话
		    'city_id' => $params['city_id'],          //需要gps反推
		    'callid' => $call_id,                     //callid 时间戳加密
		    'channel' => $channel,                    //渠道号
		    'name' => $params['name'],                //需要传进来
		    'number' => 1,                            //司机数量
//		    'dispatch_number' => 1,                   //司机数量
		    'address' => (isset($params['address']) && !empty($params['address'])) ? $params['address'] : '暂未获取',          //地址                
		    'comments' => $comments,                  //说明
		    'booking_time' => $booking_time,          //传进来的时间+20分钟
		    'flag' => self::QUEUE_SUCCESS,            //派单状态
		    'type' => $type ,     //派单状态
		    'update_time' => '0000-00-00 00:00:00',   //更新时间
		    'agent_id' => $agent_id,                    //操作员 --- 
		    'dispatch_agent' => $agent_id,                   //下单的时间
		    'dispatch_time' => $booking_time, //下单的时间
		    'created' => date('Y-m-d H:i:s' , $time), //下单的时间
		);
		$model = new OrderQueue();
		$model->attributes = $queue_arr;
		$lng = isset($params['lng']) ? $params['lng'] : '0.000000';
		$lat = isset($params['lat']) ? $params['lat'] : '0.000000';
		$model->lng = $lng;
		$model->lat = $lat;
		
		$model->google_lng = isset($params['google_lng']) ? $params['google_lng'] : $lng;
		$model->google_lat = isset($params['google_lat']) ? $params['google_lat'] : $lat;
		$model->channel = $channel;
		$result = $model->save();
		if ($result) {
			$queue_arr['id'] = $model->id;
			
			//开启新订单增加坐标 BY AndyCong 2013-12-16
			$queue_arr['lng'] = $lng;
			$queue_arr['lat'] = $lat;
			$queue_arr['google_lng'] = isset($params['google_lng']) ? $params['google_lng'] : $lng;
			$queue_arr['google_lat'] = isset($params['google_lat']) ? $params['google_lat'] : $lat;
			//开启新订单增加坐标 BY AndyCong 2013-12-16 END
			
			if (isset($params['call_time'])) {
				$queue_arr['call_time'] = $params['call_time'];
			} else {
				$queue_arr['call_time'] = time();
			}
			return $queue_arr;
		} else {
			return '';
		}
    }
    
    /**
     * 司机补单保存订单(废弃掉)
     * @param array $queue_arr
     * @param string $driver_id
     * @return int $order_id
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-05-27
     */
    public function OrderSave($queue_arr , $driver_id) {
    	$order_id  = 0;
    	if (empty($queue_arr) || empty($driver_id)) {
    		return false;
    	}
    	$time = time();
    	$driver = Driver::getProfile($driver_id);
    	
    	$sql = 'insert into t_order (name,phone,contact_phone,source,driver,city_id,driver_id,driver_phone,
				imei,call_time,order_date,booking_time,location_start,description,created) 
				values ("%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s",%s)';
		$sql = sprintf($sql, $queue_arr['name'], $queue_arr['phone'], $queue_arr['contact_phone'] , $queue_arr['type'], $driver->name, $queue_arr['city_id'], $driver_id, $driver->phone, $driver->imei, $queue_arr['call_time'], date('Ymd', time()), strtotime($queue_arr['booking_time']), $queue_arr['address'], $queue_arr['agent_id'] , $time);
		$result = Order::getDbMasterConnection()->createCommand($sql)->execute();
		if ($result) {
			$order_id = Order::getDbMasterConnection()->getLastInsertID();
		}
		return $order_id;
    }

    /**
     * 查询时间段内的 客服接了多少单
     * @author mengtianxue 2013-05-25
     * @param string $date     日期
     * @param string $user     用户名
     * @return int
     */
    public function getCallCenterTotle($date, $user){
        $number = 0;
        $start_time = $date;
        $end_time = date('Y-m-d H:i:s', strtotime($date) + 3600);

        // Yii::app()->db_readonly change into getDbReadonlyConnection()
        $number = OrderQueue::getDbReadonlyConnection()->createCommand()
            ->select("count(1) as number")
            ->from("t_order_queue")
            ->where("agent_id = :user and created >= :start_time and created < :end_time",
                array(':user' => $user, ':start_time' => $start_time, 'end_time' => $end_time))
            ->queryScalar();
        return $number;
    }

    /**
     * 查询时间段内的 客服派了多少单
     * @author mengtianxue 2013-05-25
     * @param string $date     日志
     * @param string $user     用户名
     * @return int
     */
    public function getdispatchTotle($date, $user){
        $number = 0;
        $start_time = $date;
        $end_time = date('Y-m-d H:i:s', strtotime($date) + 3600);

        // Yii::app()->db_readonly change into getDbReadonlyConnection()
        $number = OrderQueue::getDbReadonlyConnection()->createCommand()
            ->select("count(1) as number")
            ->from("t_order_queue")
            ->where("dispatch_agent = :user and dispatch_time >= :start_time and dispatch_time < :end_time",
                array(':user' => $user, ':start_time' => $start_time, 'end_time' => $end_time))
            ->queryScalar();

        return $number;
    }

    /**
     * 查询时间段内的 客服接了多少电话
     * @author mengtianxue 2013-05-25
     * @param string $date     日志
     * @param string $user_id     用户id
     * @return int
     */
    public function getCallTotal($date, $user_id){
        $number = 0;
        $start_time = $date;
        $end_time = date('Y-m-d H:i:s', strtotime($date) + 3600);
        $tabel_name = 't_callcenter_log_' . date('Ym', strtotime($date));
        $number = Yii::app()->db_readonly->createCommand()
            ->select("count(1) as number")
            ->from($tabel_name)
            ->where("user_id = :user_id and created >= :start_time and created < :end_time",
                array(':user_id' => $user_id, ':start_time' => $start_time, ':end_time' => $end_time))
            ->queryScalar();
        return $number;
    }
    
    /**
     * 获取预约信息 BY id (订单列表用)
     * @param int $queue_id
     * @return object $result
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-06-23
     */
    public function getByID($queue_id = 0) {
    	if (0 == $queue_id) {
    		return false;
    	}
    	$result = self::model()->findByPk($queue_id);
    	return $result;
    }

    /**
     *  move from themes/api/views/client/order.php
     **/
    public function getOrderQueueByWaitConfirm($queue_id) {
        $sql=" select id from t_order_queue where id = :id and flag =:flag ";
        // Yii::app()->db change into OrderQueue::getDbMasterConnection()
        $order_queue=OrderQueue::getDbMasterConnection()->createCommand($sql)
            ->queryRow(true, array(
                                   ':id'=>$queue_id,
                                   ':flag'=>OrderQueue::QUEUE_WAIT_COMFIRM
                                   ));


        return $order_queue;

    }



    /**
     * 根据queue_id查询该订单的渠道号
     * author zhangtingyi
     * @param $order_queue_id
     * @return string | null
     */
    public function getOrderQueueChannel($order_queue_id) {
    	// Yii::app()->db_readonly change into getDbReadonlyConnection()
        $channel = OrderQueue::getDbReadonlyConnection()->createCommand()
            ->select('channel')
            ->from('t_order_queue')
            ->where('id = :id' , array(
                ':id' => $order_queue_id,
            ))
            ->queryScalar();

        return $channel;
    }
}
