<?php

/**
 * This is the model class for table "{{order_position}}".
 *
 * The followings are the available columns in table '{{order_position}}':
 * @property integer $id
 * @property integer $order_id
 * @property integer $type
 * @property double $latitude
 * @property double $longitude
 * @property string $street
 * @property string $created
 */
class OrderPosition extends CActiveRecord {
	const FLAG_ACCEPT = 1; //接单
	const FLAG_ARRIVE = 20; //到达客人处
	const FLAG_START = 2; //开车
	const FLAG_FINISH = 29; //结束代驾
	const FLAG_SUBMIT = 3; //报单
	
	const POSITION_FLAG_TYPE_ACCEPT = 'accept';
	const POSITION_FLAG_TYPE_ARRIVE = 'arrive';
	const POSITION_FLAG_TYPE_START = 'start';
	const POSITION_FLAG_TYPE_FINISH = 'finish';
	const POSITION_FLAG_TYPE_SUBMIT = 'submit';

    const POSITION_TYPE_WGS  = 'wgs84';
    const POSITION_TYPE_BAIDU  = 'baidu';
    const POSITION_TYPE_GOOGLE  = 'google';

	/**
	 * Returns the static model of the specified AR class.
	 * @return OrderPosition the static model class
	 */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * Get db connection
     */
    public function getDbConnection()
    {
	return self::getDbMasterConnection();
    }

    /**
     * Master db connection
     */
    public static function getDbMasterConnection()
    {
	return Yii::app()->dborder;
    }

    /**
     * Slave db connection
     */
    public static function getDbReadonlyConnection()
    {
	return Yii::app()->dborder_readonly;
    }

	/**
	 * @return string the associated database table name
	 */
	public function tableName() {
		return '{{order_position}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() {
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
        return array(
            array(
                'order_id, flag, gps_type, lat, lng, street, log_time, created',
                'required'),
            array(
                'order_id, flag',
                'numerical',
                'integerOnly'=>true),
            array(
                'gps_type',
                'length',
                'max'=>20),
            array(
                'lat, lng',
                'length',
                'max'=>10),
            array(
                'street',
                'length',
                'max'=>100),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array(
                'id, order_id, flag, gps_type, lat, lng, street, log_time, created',
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
        return array(
            'id' => 'ID',
            'order_id' => 'Order',
            'flag' => 'Flag',
            'gps_type' => 'Gps Type',
            'lat' => 'lat',
            'lng' => 'lng',
            'street' => 'Street',
            'log_time' => 'log_time',
            'created' => 'Created',
        );
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search() {
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.


        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('order_id',$this->order_id);
        $criteria->compare('flag',$this->flag);
        $criteria->compare('gps_type',$this->gps_type);
        $criteria->compare('lat',$this->lat);
        $criteria->compare('lng',$this->lng);
        $criteria->compare('street',$this->street);
        $criteria->compare('log_time',$this->log_time);
        $criteria->compare('created',$this->created);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
	}


    /**
     * 插入位置数据
     * @param array $params
     * @return bool
     */
    public function insertInfo($params = array()){
        if(empty($params)){
            return false;
        }
        
        //只记录order_position记录 不验证查询 不更新order BY AndyCong 2013-11-18
        $gps_position=array(
            'latitude'=>sprintf('%.6f', $params['lat']),
            'longitude'=>sprintf('%.6f', $params['lng']),
        );
        $gps=GPS::model()->convert($gps_position, $params['gps_type']);
        $params['street'] = $gps['street'];
        $params['created'] = date("Y-m-d H:i:s");
        $rst = OrderPosition::getDbMasterConnection()->createCommand()->insert('t_order_position',$params);

	$ret = array('ret' => $rst,
		'position' => array(
		    'lat' => $gps['baidu_lat'],
		    'lng' => $gps['baidu_lng']));
	return $ret;
        //只记录order_position记录 不验证查询 不更新order BY AndyCong 2013-11-18
    }

    /**
     * Get order positions from database
     * Order positions are gotten from Driver position
     *
     * @param string $order_id
     * @return array
     */
    public function getOrderPositionsFromDB($order_id){
	$accept_time = 0;
	$arrive_time = 0;
	$drive_time = 0;
	$end_time = 0;
	$last_time = time();
	$accept_time_str = '';
	$arrive_time_str = '';
	$drive_time_str = '';
	$end_time_str = '';

	$order_states_info = array('ret' => 'success');

	$arrive_data = array();
	$drive_data = array();

	// Get driver id for the order
        $driver_info = Order::getDbReadonlyConnection()->createCommand()
            ->select('driver_id')
            ->from('t_order')
            ->where('order_id=:order_id', array(':order_id' => $order_id))
            ->queryRow();

	if(empty($driver_info)) {
	    $ret = array('accept_time'=>$accept_time,
		    'drive_time' => $drive_time,
		    'last_time' => $last_time,
		    'finish_time' => $end_time,
		    'arrive_time' => $arrive_time,
		    'order_states_info' => $order_states_info,
		    'arrive' => $arrive_data,
		    'drive' => $drive_data);
		EdjLog::Info("getOrderPositionsFromDB:driver_info is empty.\n"); 
	    return $ret;
	}

	$driver_id = $driver_info['driver_id'];

	// Get basic order info
	// Accept time, arrive time, driver time , and finish time
	$order_positions = OrderPosition::getDbReadonlyConnection()
	    ->createCommand()
	    ->select("flag,created,log_time,lat,lng")
	    ->from("t_order_position")
	    ->where("order_id = :order_id",
		    array(':order_id' => $order_id))
	    ->queryAll();

	foreach($order_positions as $order_position) {
	    switch($order_position['flag']) {
		case 1:
		    $accept_time = strtotime($order_position['log_time']);
		    $accept_time_str = $order_position['log_time'];
		    $order_states_info['accept_desc'] = '司机已接单';
		    $order_states_info['accept_pos'] = 
			array('lat' => $order_position['lat'],
				'lng' => $order_position['lng']); 
		    $order_state = OrderProcess::PROCESS_ACCEPT;
		    RDriverPosition::model()->setOrderInfo($order_id, 
			    $driver_id, $order_state, 
			    $order_states_info['accept_pos'],
			    $accept_time);
		    break;
		case 20:
		    $arrive_time = strtotime($order_position['created']);
		    $arrive_time_str = $order_position['created'];
		    $order_states_info['arrive_pos'] = 
			array('lat' => $order_position['lat'],
				'lng' => $order_position['lng']); 
		    $order_state = OrderProcess::PROCESS_READY;
		    RDriverPosition::model()->setOrderInfo($order_id, 
			    $driver_id, $order_state, 
			    $order_states_info['arrive_pos'],
			    $arrive_time);
		    break;
		case 2:
		    $drive_time = strtotime($order_position['created']);
		    $drive_time_str = $order_position['created'];
		    $order_states_info['drive_desc'] = '开车行驶';
		    $order_states_info['drive_pos'] = 
			array('lat' => $order_position['lat'],
				'lng' => $order_position['lng']); 
		    $order_state = OrderProcess::PROCESS_DRIVING;
		    RDriverPosition::model()->setOrderInfo($order_id, 
			    $driver_id, $order_state, 
			    $order_states_info['drive_pos'],
			    $drive_time);
		    break;
		case 29:
		    $end_time = strtotime($order_position['created']);
            EdjLog::info('whytest: end_time:'.$end_time,'console');
		    $end_time_str = $order_position['created'];
		    $order_states_info['finish_desc'] = '到达目的地';
		    $order_states_info['finish_pos'] = 
			array('lat' => $order_position['lat'],
				'lng' => $order_position['lng']); 
		    $order_state = OrderProcess::ORDER_PROCESS_FINISH;
		    RDriverPosition::model()->setOrderInfo($order_id, 
			    $driver_id, $order_state, 
			    $order_states_info['finish_pos'],
			    $end_time);
		    break;
	    }
	}

		//FIXED:剔除（1.0,1.0）的轨迹点
		//TODO:判断是否设置了start时间，以及点是否符合条件
        // Set arrive head pos
        if(isset($order_states_info['accept_pos'])) {
			if($order_states_info['accept_pos']['lat'] != 1.0 || 
					$order_states_info['accept_pos']['lng'] != 1.0)
				$arrive_data[] = $order_states_info['accept_pos'];
			else
				EdjLog::Info("Filter accept pos.\n"); 
        }

        // set drive head pos
        if(isset($order_states_info['drive_pos'])) {
			if($order_states_info['drive_pos']['lat'] != 1.0 || 
					$order_states_info['drive_pos']['lng'] != 1.0)
				$drive_data[] = $order_states_info['drive_pos'];
			else
				EdjLog::Info("Filter drive pos.\n"); 
        }

	if($arrive_time != 0 && $drive_time != 0) {
	    $wait_time = ($drive_time - $arrive_time)/60;
	    $wait_time = floor($wait_time);
	    if($wait_time < 0) {
		$wait_time = 0;
	    }
	    $order_states_info['arrive_desc'] = '已就位';
	}
 
	$driver=DriverStatus::model()->get($driver_info['driver_id']);
	if(empty($driver)) {
	    // Save arrive position data
	    $arrive_pos_datas = array('driver_id' => $driver_id,
		    'order_id' => $order_id,
		    'order_state' => OrderProcess::PROCESS_ACCEPT,
		    'positions' => $arrive_data);
	    RDriverPosition::model()->insertBatchPosition($arrive_pos_datas, $arrive_time);
	    // Save drive position data
	    $drive_pos_datas = array('driver_id' => $driver_id,
		    'order_id' => $order_id,
		    'order_state' => OrderProcess::PROCESS_DRIVING,
		    'positions' => $drive_data);
	    RDriverPosition::model()->insertBatchPosition($drive_pos_datas, $drive_time);

	    $ret = array('accept_time'=>$accept_time,
		    'drive_time' => $drive_time,
		    'last_time' => $last_time,
		    'finish_time' => $end_time,
		    'arrive_time' => $arrive_time,
		    'order_states_info' => $order_states_info,
		    'arrive' => $arrive_data,
		    'drive' => $drive_data);
		EdjLog::Info("getOrderPositionsFromDB:driverstatus is empty.\n"); 
	    return $ret;
	}

	if($accept_time != 0 && $arrive_time != 0){
	    // Get data from driver position
	    $table_name='t_driver_position_'.date('Ym', $accept_time);
	    $driver_positions = Yii::app()->dbstat->createCommand()
		->select("latitude,longitude")
		->from($table_name)
		->where(array('AND', 'created>=:start', 
			    'created<=:end', 'user_id=:user_id'),
			    array(':start'=>$accept_time_str, 
			    ':end'=>$arrive_time_str, ':user_id'=>$driver->id))
		->order('created asc')
		->queryAll();
	    foreach($driver_positions as $driver_position){
		$arrive_data[] = array('lat'=>$driver_position['latitude'],
			'lng'=>$driver_position['longitude']);
	    }
	}

	if($drive_time !=0 && $end_time != 0) {
	    $table_name='t_driver_position_'.date('Ym', $drive_time);
	    $driver_positions = Yii::app()->dbstat->createCommand()
		->select("latitude,longitude")
		->from($table_name)
		->where(array('AND', 'created>=:start', 
			    'created<=:end', 'user_id=:user_id'),
			    array(':start'=>$drive_time_str, 
			    ':end'=>$end_time_str, ':user_id'=>$driver->id))
		->order('created asc')
		->queryAll();
	    foreach($driver_positions as $driver_position){
		$drive_data[] = array('lat'=>$driver_position['latitude'],
			'lng'=>$driver_position['longitude']);
	    }
	}

	//FIXED:剔除（1.0,1.0）的轨迹点
	// Set end pos
	if(isset($order_states_info['arrive_pos'])) {
		if($order_states_info['arrive_pos']['lat'] != 1.0 || 
			$order_states_info['arrive_pos']['lng'] != 1.0)
			$arrive_data[] = $order_states_info['arrive_pos'];
		else
			EdjLog::Info("Filter arrive pos.\n"); 
	}

	// set driver end pos
	if(isset($order_states_info['finish_pos'])) {
		if($order_states_info['finish_pos']['lat'] != 1.0 || 
			$order_states_info['finish_pos']['lng'] != 1.0)
			$drive_data[] = $order_states_info['finish_pos'];
		else
			EdjLog::Info("Filter finish pos.\n"); 
	}

	// Save arrive position data
	$arrive_pos_datas = array('driver_id' => $driver_id,
		'order_id' => $order_id,
		'order_state' => OrderProcess::PROCESS_ACCEPT,
		'positions' => $arrive_data);
	RDriverPosition::model()->insertBatchPosition($arrive_pos_datas, $arrive_time);
	// Save drive position data
	$drive_pos_datas = array('driver_id' => $driver_id,
		'order_id' => $order_id,
		'order_state' => OrderProcess::PROCESS_DRIVING,
		'positions' => $drive_data);
	RDriverPosition::model()->insertBatchPosition($drive_pos_datas, $drive_time);

	$ret = array('accept_time'=>$accept_time,
		'drive_time' => $drive_time,
		'last_time' => $last_time,
		'arrive_time' => $arrive_time,
		'finish_time' => $end_time,
		'order_states_info' => $order_states_info,
		'arrive' => $arrive_data,
		'drive' => $drive_data);
	return $ret;
    }

    /**
     * 重新load数据从数据库中，到redis中
     * @param $order_id
     */
    private function reloadOrderPositionFromDB($order_id){
        $stime=time();
        $this->getOrderPositionsFromDB($order_id);
        $order_info =  RDriverPosition::model()->getOrderInfo($order_id);
        $etime=time();
        EdjLog::info('from DB costTime:'.($etime-$stime));
        return $order_info;
    }

    /**
     * Get order positions
     * Order positions are gotten from Driver position
     *
     * @param string $order_id
     * @return array
     */
    public function getOrderPositions($order_id, $start = ''){
    	$ret = array();
    
    	// Get driver id first
    	$order_info =  RDriverPosition::model()->getOrderInfo($order_id);
    	if(empty($order_info) || !isset($order_info['order_driver']) ) {
            EdjLog::info('redis empy:'.$order_id,'console');
            $order_info = $this->reloadOrderPositionFromDB($order_id);
            if(empty($order_info) || !isset($order_info['order_driver'])) {
                EdjLog::Info("order_id:".$order_id."\torder_info is empty or order_driver is not set, return null.\n");
                return $ret;
            }
    	}elseif(isset($order_info['order_state'] )
                 && $order_info['order_state'] == OrderProcess::ORDER_PROCESS_FINISH
                 && !isset($order_info['order_finish_time'])) {
            $order_info = $this->reloadOrderPositionFromDB($order_id);
        }
        if(empty($order_info) || !isset($order_info['order_driver'])) {
            EdjLog::Info("order_id:".$order_id."\torder_info is empty or order_driver is not set, return null.\n");
            return $ret;
        }
        EdjLog::info('order_id:'.$order_id.',info:'.json_encode($order_info),'console');
    	$driver_id =  $order_info['order_driver'];
    
    	// Get all order data
    	$params = array('order_id' => $order_id,
    			'driver_id' => $driver_id);
    
    	EdjLog::Info("start value is:" . $start . ".\n");
    
    	if (!empty($start)) {
    		$params['start'] = $start;
    	}
    
    	$order_states_info = array('ret' => 'success');
    
    	// For different states get the datas
    	$order_state = '301';
    	if(isset($order_info['order_state'])) {
    		$order_state = $order_info['order_state'];
    		$order_states_info['order_state'] = $order_state;
    	}
    
    	// Get arrive positions
    	$now_state = '301';
    	$arrive_pos = array();
    	$await_pos = array();
    	$drive_pos = array();
    	$accept_time = 0;
    	$arrive_time = 0;
    	$drive_time = 0;
    	$finish_time = 0;
    	$last_time = time();
    
    	// set the time values
    	if(isset($order_info['order_accept_time'])) {
    		$accept_time = $order_info['order_accept_time'];
    		$order_states_info['accept_desc'] = '司机已接单';
    		$order_states_info['accept_pos'] =
    		json_decode($order_info['order_accept_pos'], TRUE);;
    	}
    
    	if(isset($order_info['order_arrive_time'])) {
    		$arrive_time = $order_info['order_arrive_time'];
    		$order_states_info['arrive_desc'] = '已就位';
    		$order_states_info['arrive_pos'] =
    		json_decode($order_info['order_arrive_pos'], TRUE);;
    	}
    
    	if(isset($order_info['order_drive_time'])) {
    		$drive_time = $order_info['order_drive_time'];
    		$order_states_info['drive_desc'] = '开车行驶';
    		$order_states_info['drive_pos'] =
    		json_decode($order_info['order_drive_pos'], TRUE);;
    	}
    
    	if(isset($order_info['order_finish_time'])) {
    		$finish_time = $order_info['order_finish_time'];
    		$order_states_info['finish_desc'] = '到达目的地';
    		$order_states_info['finish_pos'] =
    		json_decode($order_info['order_finish_pos'], TRUE);
    
    		// Check if there are driving status
    		// If not set it to be the arriving time
    		if(isset($order_info['order_arrive_time']) &&
    				!isset($order_info['order_drive_time'])) {
    					$drive_time = $order_info['order_arrive_time'];
    					$order_states_info['drive_desc'] = '开车行驶';
    					$order_states_info['drive_pos'] =
    					json_decode($order_info['order_arrive_pos'], TRUE);;
    				}
    
    				// Check if there are arriving status
    				// If not set it to be the driving time
    				if(!isset($order_info['order_arrive_time']) &&
    						isset($order_info['order_drive_time'])) {
    							$arrive_time = $order_info['order_drive_time'];
    							$order_states_info['arrive_desc'] = '已就位';
    							$order_states_info['arrive_pos'] =
    							json_decode($order_info['order_drive_pos'], TRUE);;
    						}
    	}
    
    	/** FIXED:	验证经纬度的合法性, 剔除(1.0,1.0)的点
    	 *	FIXED:	检查时间戳，判断start时间是否小于开车时间
    	 */
    
    	// Set arrive head pos
    	if(empty($start) || (!empty($start) && $accept_time >= $start)) {
    		if(isset($order_states_info['accept_pos'])) {
    			if($order_states_info['accept_pos']['lat'] != 1.0 ||
    					$order_states_info['accept_pos']['lng'] != 1.0)
    						$arrive_pos[] = $order_states_info['accept_pos'];
    					else
    						EdjLog::Info("Filter accept pos.\n");
    		}
    	}
    
    	// set driver head pos
    	if(empty($start) || (!empty($start) && $drive_time >= $start)) {
    		if(isset($order_states_info['drive_pos'])) {
    			if($order_states_info['drive_pos']['lat'] != 1.0 ||
    					$order_states_info['drive_pos']['lng'] != 1.0)
    						$drive_pos[] = $order_states_info['drive_pos'];
    					else
    						EdjLog::Info("Filter drive pos.\n");
    		}
    	}
        if (!empty($accept_time) && empty($start)){
              $start_time = $accept_time;
        }else{
              $start_time = $start;
        }
        EdjLog::Info("start_time value is:" . $start_time . ".\n");
    	if (empty($start_time) || (time() - $start_time) <= RDriverPosition::EXPIRE_TIME_MONTH) {
            $stime= microtime();
    		$pos_datas = RDriverPosition::model()->getPositions($params);
            $etime= microtime();
            EdjLog::info($order_id.' from redis trace total costTime:'.($etime-$stime));
    	}
        if (empty($finish_time)){
            $order_db = Order::model()->findByPk($order_id);
            if (!empty($order_db)){
                $finish_time= $order_db->end_time;
            }
        }

    	if (empty($pos_datas)) {
    		//             $driver_id='SZ51673';
    		//             $accept_time=1428076740;
    		//             $finish_time=1428079320;
            $stime= microtime();
    		$pos_datas = OrderPositionSSDB::getOrderPositionsFromSSDB($driver_id,$accept_time,$finish_time);
            $etime=microtime();
            EdjLog::info($order_id.' from SSDB total costTime:'.($etime-$stime));
    	}
    	EdjLog::Info('pos_datas is :'.json_encode($pos_datas), 'console');
//    	if (empty($pos_datas)) {
//    		EdjLog::Info('pos_datas is empty:', 'console');
//    		return;
//    	}
    	foreach($pos_datas as $position) {
    		$time = $position['created'];

    		if(!empty($finish_time) && $time > $finish_time) {
    			continue;
    		}
            $last_time = $time;
            if($position['order_state'] == '1') {
                if ($time>= $accept_time && $time<= $arrive_time ){
                    $arrive_pos[] = array('lat' => $position['lat'],
                        'lng' => $position['lng']);
                }elseif($time<= $drive_time && $time>= $arrive_time && $arrive_time!=0){
                    $await_pos[] = array('lat' => $position['lat'],
                        'lng' => $position['lng']);
                }else{
                    $drive_pos[] = array('lat' => $position['lat'],
                        'lng' => $position['lng']);
                }
            }
    		if($position['order_state'] == '301') {//接单
    			$arrive_pos[] = array('lat' => $position['lat'],
    					'lng' => $position['lng']);
    			$last_time = $time;
    		} else if($position['order_state'] == '303') {//开车
    			$drive_pos[] = array('lat' => $position['lat'],
    					'lng' => $position['lng']);
    			$last_time = $time;
    		} else if($position['order_state'] == '302'){//就位
    			// 302
    			// Maybe the points is really for driving
    			if($arrive_time != 0 && $time <= $arrive_time) {
    				$arrive_pos[] = array('lat' => $position['lat'],
    						'lng' => $position['lng']);
    				$last_time = $time;
    			}
    
    			if($drive_time != 0 && $time >= $drive_time) {
    				$drive_pos[] = array('lat' => $position['lat'],
    						'lng' => $position['lng']);
    				$last_time = $time;
    			}
    
    			//Add await_pos
    			if($arrive_time != 0 && $time > $arrive_time) {
    				if(($drive_time == 0) || ($drive_time != 0 && $time < $drive_time)) {
    					$await_pos[] = array('lat' => $position['lat'],
    							'lng' => $position['lng']);
    					$last_time = $time;
    				}
    			}
    
    		} else {
    			// 501
                $last_time = $time;
    		}
    	}
    
    	// Set arrive end pos
    	if(empty($start) || (!empty($start) && $arrive_time >= $start)) {
    		if(isset($order_states_info['arrive_pos'])) {
    			if($order_states_info['arrive_pos']['lat'] != 1.0 ||
    					$order_states_info['arrive_pos']['lng'] != 1.0)
    						$arrive_pos[] = $order_states_info['arrive_pos'];
    					else
    						EdjLog::Info("Filter arrive pos.\n");
    		}
    	}
    
    	// set driver end pos
    	if(empty($start) || (!empty($start) && $finish_time >= $start)) {
    		if(isset($order_states_info['finish_pos'])) {
    			if($order_states_info['finish_pos']['lat'] != 1.0 ||
    					$order_states_info['finish_pos']['lng'] != 1.0)
    						$drive_pos[] = $order_states_info['finish_pos'];
    					else
    						EdjLog::Info("Filter finish pos.\n");
    		}
    	}
    
    	//FIX: fix lng=1.0 and lat=1.0 position in order key position
    	if(isset($order_states_info['accept_pos'])) {
    		if($order_states_info['accept_pos']['lat'] == 1.0 &&
    				$order_states_info['accept_pos']['lng'] == 1.0) {
    					if(!empty($arrive_pos))
    						$order_states_info['accept_pos'] = $arrive_pos[0];
    					else
    						unset($order_states_info['accept_pos']);
    				}
    	}
    
    	if(isset($order_states_info['arrive_pos'])) {
    		if($order_states_info['arrive_pos']['lat'] == 1.0 &&
    				$order_states_info['arrive_pos']['lng'] == 1.0) {
    					if(!empty($arrive_pos))
    						$order_states_info['arrive_pos'] = end($arrive_pos);
    					else
    						unset($order_states_info['arrive_pos']);
    				}
    	}
    
    	if(isset($order_states_info['drive_pos'])) {
    		if($order_states_info['drive_pos']['lat'] == 1.0 &&
    				$order_states_info['drive_pos']['lng'] == 1.0) {
    					if(!empty($drive_pos))
    						$order_states_info['drive_pos'] = $drive_pos[0];
    					else
    						unset($order_states_info['drive_pos']);
    				}
    	}
    
    	if(isset($order_states_info['finish_pos'])) {
    		if($order_states_info['finish_pos']['lat'] == 1.0 &&
    				$order_states_info['finish_pos']['lng'] == 1.0) {
    					if(!empty($drive_pos))
    						$order_states_info['finish_pos'] = end($drive_pos);
    					else
    						unset($order_states_info['finish_pos']);
    				}
    	}
    
    
    	//TODO: 需验证有没有逻辑漏洞
    	if(!empty($drive_pos)) {
    		$order_states_info['current_pos'] = end($drive_pos);
    	} else if(!empty($arrive_pos)) {
    		$order_states_info['current_pos'] = end($arrive_pos);
    	} else {
    		if(isset($order_info['order_current_pos'])) {
    			$order_states_info['current_pos'] =
    			json_decode($order_info['order_current_pos'], TRUE);
    		}
    	}
        if(!empty($finish_time) && $last_time > $finish_time) {
            $last_time = $finish_time;
        }
    
    	$ret = array('accept_time'=>$accept_time,
    			'drive_time' => $drive_time,
    			'last_time' => $last_time,
    			'arrive_time' => $arrive_time,
    			'finish_time' => $finish_time,
    			'order_states_info' => $order_states_info,
    			'arrive' => $arrive_pos,
    			'await' => $await_pos,
    			'drive' => $drive_pos,
    			'polling_next' => 10,
    			'display_type' => 1);
    	return $ret;
    }
    

    /**
     * Get order current position and status
     * Order positions are gotten from Driver position
     *
     * @param string $order_id
     * @return array
     */
    public function getOrderCurrentPosition($order_id){
	$ret = array();

	// Get driver id first
	$order_info =  RDriverPosition::model()->getOrderInfo($order_id);
	if(empty($order_info) || !isset($order_info['order_driver'])) {
	    // TODO: return null?
	    return $ret;
	}
	$driver_id =  $order_info['order_driver'];

	$order_states_info = array('ret' => 'success');

	// For different states get the datas
	$order_state = '301';
	if(isset($order_info['order_state'])) {
	    $order_state = $order_info['order_state'];
	    $order_states_info['order_state'] = $order_state;
	}

	// Get arrive positions
	$now_state = '301';
	$arrive_pos = array();
	$drive_pos = array();
	$accept_time = 0;
	$arrive_time = 0;
	$drive_time = 0;
	$finish_time = 0;
	$last_time = time();

	// set the time values
	if(isset($order_info['order_accept_time'])) {
	    $accept_time = $order_info['order_accept_time'];
	    $order_states_info['accept_desc'] = '司机已接单';
	    $order_states_info['accept_pos'] = 
		json_decode($order_info['order_accept_pos'], TRUE);;
	}

	if(isset($order_info['order_arrive_time'])) {
	    $arrive_time = $order_info['order_arrive_time'];
	    $order_states_info['arrive_desc'] = '已就位';
	    $order_states_info['arrive_pos'] = 
		json_decode($order_info['order_arrive_pos'], TRUE);;
	}

	if(isset($order_info['order_drive_time'])) {
	    $drive_time = $order_info['order_drive_time'];
	    $order_states_info['drive_desc'] = '开车行驶';
	    $order_states_info['drive_pos'] = 
		json_decode($order_info['order_drive_pos'], TRUE);;
	}

	if(isset($order_info['order_finish_time'])) {
	    $finish_time = $order_info['order_finish_time'];
	    $order_states_info['finish_desc'] = '到达目的地';
	    $order_states_info['finish_pos'] = 
		json_decode($order_info['order_finish_pos'], TRUE);
	}

	$order_states_info['current_pos'] = array('lat' => 0, 'lng' => 0);
	if(isset($order_info['order_current_pos'])) {
	    $order_states_info['current_pos'] = 
		json_decode($order_info['order_current_pos'], TRUE);
	}

	$ret = array('accept_time'=>$accept_time,
		'drive_time' => $drive_time,
		'last_time' => $last_time,
		'arrive_time' => $arrive_time,
		'finish_time' => $finish_time,
		'order_states_info' => $order_states_info);
        return $ret;
    }
    
    
    /**
     * 获取当前位置
     * @param string $order_id
     * @return array
     */
    public function getOrderExt($order_id){
        $order_position = OrderPosition::getDbReadonlyConnection()->createCommand()
                                        ->select("*")
                                        ->from("t_order_position")
                                        ->where("order_id = :order_id and flag = :flag",
                                                array(':order_id' => $order_id, ':flag' => self::FLAG_START))
                                        ->queryRow();
        return $order_position;
    }
    
    
    
    
    

    
}
