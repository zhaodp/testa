<?php
/**
 * 找司机
 * User: zhanglimin
 * Date: 13-6-21
 * Time: 下午4:06
 */
class DispatchDriver{

    private static $_models;

    private $_nearby_driver_number = 6; //设定获取司机数量 BY AndyCong 2013-07-31
    
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
     * 检查订单是否在锁定池
     * @param $order_id
     * @return bool
     */
    public  function checkDispatchOrderLock($order_id){
        $flag = false;
        $no_in_lock = QueueDispatchOrder::model()->insert($order_id);
        if( ! $no_in_lock ){
            $flag = true;
        }
        return $flag;
    }


    /**
     * 派送司机规则
     * @param array $drivers
     * @return array
     */
    private function _setDriversRule($drivers = array(),$lng=0, $lat=0){

        if(empty($drivers)){
            return array();
        }

        $drivers = $this->_crownRule($drivers);

        $drivers = Tools::driver_dispatch_sort($drivers,'weight','asc',$lng, $lat);

        return $drivers;

    }


    /**
     * 设置皇冠司机
     * @param array $drivers
     * @return array
     */
    private function _crownRule($drivers = array()){

        $crown_weight = 10000; //皇冠权重

        if(empty($drivers)){
            return array();
        }
        $result = array();
        foreach($drivers as $key=>$driver){
            $result[$key] = $driver;
            $driverInfo = DriverStatus::model()->get($driver['driver_id']);

            //皇冠判断逻辑修改   
            $recommand = 0;
			$driver_recommand = $driverInfo->recommand;
			if (!empty($driver_recommand)) {
			 	$begin_time = isset($driver_recommand['begin_time']) ? strtotime($driver_recommand['begin_time']) : 0;
			 	$end_time = isset($driver_recommand['end_time']) ? strtotime($driver_recommand['end_time']) : 0;
			 	$current_time = time();
			 	if ($current_time > $begin_time && $current_time < $end_time) {
			 		$recommand = 1;
			 	}
			} 
			//皇冠判断逻辑修改  2013-08-09
			
            if(0 == $recommand){
                $result[$key]['weight']  = $driver['distance'] ;
                $result[$key]['crown'] = 0;  //加皇冠 BY AndyCong 2013-07-28
            }else{
                $result[$key]['weight']  = $driver['distance']-$crown_weight ;
                $result[$key]['crown'] = 1;  //加皇冠 BY AndyCong 2013-07-28
            }
        }
        return $result;
    }

    /**
     * 锁司机
     * @param $drivers
     * @param $queue_id
     * @param $dispatch_number 锁司机人数
     */
    private function _setDriverLock($drivers,$queue_id = ""  , $order_id , $dispatch_number = 1){
        EdjLog::info('DispatchDriver->_setDriverLock dispatch_number:'. $dispatch_number.'drivers:'.json_encode($drivers));
        $ret = array();
        if(empty($drivers) || empty($queue_id)){
            return $ret;
        }
       

        $i = 1;
        foreach($drivers as $driver){
            if($i>$dispatch_number){
                break;
            }

            //验证订单是否已派过这个司机 如果派过 则找下个司机派单
	    $is_driver_dispatched = QueueDispatchOrder::model()->isDriverDispatched($order_id , $driver['driver_id']);
            if ($is_driver_dispatched) {
                EdjLog::info('DispatchDriver->_setDriverLock driver ' . $driver['driver_id'] . 'is dispatched');
            	continue;
            }
            
            $flag=QueueDispatchDriver::model()->insert($driver['driver_id']);
            if(!$flag){
                EdjLog::info('DispatchDriver->_setDriverLock driver ' . $driver['driver_id'] . ' insert failed');
                continue;
            } else{
            	
                if(count($drivers) > 1) {
                    //如果周围有多个司机,将司机设置为上次派送派过司机
                    QueueDispatchOrder::model()->queueDispatchedDriver($order_id , $driver['driver_id']);
                    EdjLog::info('DispatchDriver->_setDriverLock driver ' . $driver['driver_id'] . ' driver count > 1');
                }
            	
                $params = array(
                    'drivers' => $drivers,
                    'driver' => $driver,
                    'queue_id' => $queue_id,
                    'order_id' => $order_id,
                );
                $task=array(
                    'method'=>'dispatch_drivers_log',
                    'params'=>$params
                );
                //TODO 先放ORDER里,迁移到派单日志队列 ，modify by sunhongjing 2014-01-12
                Queue::model()->putin($task,'dispatchlog');
                                
                EdjLog::info('DispatchDriver->_setDriverLock driver ' . $driver['driver_id'] . ' is put to queue');
                    
                $ret[] = $driver;
                $i++;
            }
        }
        return $ret;
    }
    /**
     * 锁司机
     * @param $drivers
     * @param $queue_id
     * @param $dispatch_number 锁司机人数
     */
    private function _setDriverLockNewRule($drivers,$queue_id = ""  , $order_id , $dispatch_number = 1, $channel = CustomerApiOrder::QUEUE_CHANNEL_BOOKING){
        EdjLog::info('DispatchDriver->_setDriverLockNewRule dispatch_number:'. $dispatch_number.'drivers:'.json_encode($drivers));
        $ret = array();
        if(empty($drivers) || empty($queue_id)){
            return $ret;
        }
        
        $i = 1;
        foreach($drivers as $driver){
            if($i>$dispatch_number){
                break;
            }
            
            //验证订单是否已派过这个司机 如果派过 则找下个司机派单
	    $is_driver_dispatched = QueueDispatchOrder::model()->isDriverDispatched($order_id , $driver['driver_id']);
            if ($is_driver_dispatched) {
                EdjLog::info('DispatchDriver->_setDriverLockNewRule driver ' . $driver['driver_id'] . 'is dispatched');
            	continue;
            }

        // For remote order, only supported by driver version = 2.4.8 or >= 2.5.0
        // Check version
        if($channel ==  CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER
            && !DriverStatus::model()->isSupportRemoteOrder($driver['driver_id'])) {
            EdjLog::info('DispatchDriver->_setDriverLockNewRule driver ' . $driver['driver_id'] . ' is remote order but do not support remote order');
            continue;
        }
            
            $flag=QueueDispatchDriver::model()->insert($driver['driver_id']);
            if(!$flag){
                EdjLog::info('DispatchDriver->_setDriverLockNewRule driver ' . $driver['driver_id'] . ' insert failed');
                continue;
            } else{
            	
                if(count($drivers) > 1) {
                    //如果周围有多个司机,将司机设置为上次派送派过司机
            	    QueueDispatchOrder::model()->queueDispatchedDriver($order_id , $driver['driver_id']);
                    EdjLog::info('DispatchDriver->_setDriverLockNewRule driver ' . $driver['driver_id'] . ' driver count > 1');
		}
            	
                $params = array(
                    'drivers' => $drivers,
                    'driver' => $driver,
                    'queue_id' => $queue_id,
                    'order_id' => $order_id,
                );
                $task=array(
                    'method'=>'dispatch_drivers_log',
                    'params'=>$params
                );
                //TODO 先放ORDER里,迁移到派单日志队列 ，modify by sunhongjing 2014-01-12
                Queue::model()->putin($task,'dispatchlog');
                EdjLog::info('DispatchDriver->_setDriverLockNewRule driver ' . $driver['driver_id'] . ' is put to queue');
                $ret[] = $driver;
                $i++;
            }
        }
        return $ret;
    }

    /**
     * 测试司机
     * @param array $drivers
     * @return array
     */
    public function test(){
        $test_driver = array(
                            'BJ9001',
                            'BJ9002',
                            'BJ9007',
                            'BJ9010',
                            'BJ9011',
                            'BJ9012',
                            'BJ9013',
                            'BJ9005',
                            'BJ9016',
                            'BJ9017',
                            'BJ9023',
                            'BJ9020',
                            'BJ1161',
			    'BJ9036',
			    'BJ9052',
			    'BJ9010',
                            //增加winPhone测试工号
                            'BJ9040',
                            'BJ9041',
                            'BJ9042',
                            'BJ9043',
                    );
        return $test_driver;
    }


    /**
     * 通过位置找司机
     * @param string $queue_id
     * @param string $order_id
     * @return array
     */
    public function getOrderDrivers($queue_id = "" , $order_id = ""){
    	EdjLog::info($queue_id.'|'.$order_id.'|180系统派单|找司机|begin' , 'console');
        $result = array('flag'=>false,'msg'=>'', 'order_locked' => false ,'drivers'=>array());

        if(empty($queue_id) || empty($order_id)){
        	//验证参数有误 log 2014-03-03
        	EdjLog::warning($queue_id.'|'.$order_id.'|180系统派单|参数错误' , 'console');
        	
            $result['msg'] = "queue_id 或 order_id 不能为空";
            return $result;
        }

        $ret = OrderQueue::model()->findByPk($queue_id);

        if(empty($ret)){
        	//queue数据没有查询到 log 2014-03-03
        	EdjLog::warning($queue_id.'|'.$order_id.'|180系统派单|queue数据无效' , 'console');
        	
            $result['msg'] = "获取数据不存在";
            return $result;
        }

	$comments =  $ret->comments;
	$order_number = $ret->number;

        //锁订单
        $order_flag = $this->checkDispatchOrderLock($order_id);
        if($order_flag){
        	//订单已被锁定 log 2014-03-03
        	EdjLog::info($queue_id.'|'.$order_id.'|180系统派单|订单已被锁定' , 'console');
        	
            $result['msg'] = "订单己被锁定";
            $result['order_locked'] = true;
            return $result;
        }
        
        //判断订单时间<10分钟 或 创建时间-预约时间 < 20分钟 变为手动派单
        $channel = isset($ret->channel) ? $ret->channel : '';
        if ($channel == '01011') {  //如果是雷石的订单超时取消订单
        	$change_flag = $this->_isChangeQueueFlagSuccess($queue_id ,$ret->booking_time, $order_number);
        } else {
        	$change_flag = $this->_isChangeQueueFlag($queue_id , $order_id , $ret->booking_time , $ret->created);
        }
        
        if($change_flag['flag']){
        	QueueApiOrder::model()->queue_lock($queue_id);
            $result['msg'] = $change_flag['msg'];
            
            //派单超时 log 2014-03-03
        	EdjLog::info($queue_id.'|'.$order_id.'|181|派单超时' , 'console');
            
            return $result;
        }

	// Check if the order is accept or canceled by 
	// some driver but the database is not updated
        $cache_key = 'receive_detail_'.$order_id;
        $is_dispatch = Yii::app()->cache->get($cache_key);
	if(!$is_dispatch) {
	    // Do nothing
	} else {
	    // Do log and return
	    EdjLog::info($queue_id.'|'.$order_id.'|'.$is_dispatch.'|180系统派单|订单已经在Memcached中' , 'console');
            $result['msg'] = '订单已经在Memecached中'.$is_dispatch;
	    // Customer cancel|| Out of time
	    // Maybe i should use queue
	    if($is_dispatch != 'dispatched') {
		EdjLog::info($queue_id.'|'.$order_id.'|'.$is_dispatch.'|180系统派单|Redo receive in dispatch' , 'console');
		Push::model()->redoOrderReceiveInDispatch($queue_id, 
			$order_id, $is_dispatch, $order_number, $comments);
	    }
	    return $result;
	}

        //固定获取司机数量 BY AndyCong 2013-07-31
	$range = isset(Yii::app()->params['Order400DispatchRange']) ? 
            Yii::app()->params['Order400DispatchRange'] : 5000;

        //分城市距离
        if(isset(Yii::app()->params['Order400DispatchRangeByCity'][$ret->city_id])) {
            $range = Yii::app()->params['Order400DispatchRangeByCity'][$ret->city_id];
        }

	$driver_number = isset(Yii::app()->params['Order400DispatchNumber']) ?
	    Yii::app()->params['Order400DispatchNumber'] : 10;

        $drivers=DriverGPS::model()->nearby_printLog($ret->city_id,$order_id,$ret->lng, $ret->lat, 0, $driver_number , $range);
        
        if(empty($drivers)){
	    // Check whether no driver or all driver busy
	    // TODO: When we don't need the log, delete it
	    $drivers=DriverGPS::model()->nearby_printLog($ret->city_id,$order_id,$ret->lng, $ret->lat, 1, 1 , $range);
	    if(!empty($drivers)) {
		EdjLog::info($queue_id.'|'.$order_id.'|180系统派单|司机全在服务中' , 'console');
	    } else {
		EdjLog::info($queue_id.'|'.$order_id.'|180系统派单|附近没司机' , 'console');
	    }
        	
            $msg = '没有获取到周边司机';
            $result['msg'] = $msg;
            return $result;
        }

        //设置排序规则
        // $drivers = $this->_setDriversRule($drivers,$ret->lng, $ret->lat);
        try{
                $filterDriverManager = new FilterDriverManager();
                $filterDriverManager->addStrategy(FilterDriverCrownStrategy::model());
                $filterDriverManager->addStrategy(FilterDriverSpeedStrategy::model());
                $drivers = $filterDriverManager->filter($ret->city_id, $drivers, $ret->lng, $ret->lat, $range,$ret['type'], $order_id);
            } catch (Exception $e) {
                EdjLog::warning('getOrderDrivers:apply FilterDriverStrategy failed, message:' . $e->getMessage() , 'console');
            }

        if(!empty($drivers)){
        foreach($drivers as $driver){
            if(!isset($driver['crown']))
        	EdjLog::info('DispatchDriver:FilterDriverManager| order_id|'.$order_id.'|driverId:'.$driver['driver_id'], 'console');
        }
        }
        //锁司机
        if ($channel == '01011') { //如果是雷石的订单则只派测试司机
        	$drivers = $this->_setTestDriverLock($drivers , 
			$queue_id , $order_id);
        } else {
        	$drivers = $this->_setDriverLock($drivers , $queue_id , $order_id);
        }
        if(empty($drivers)){
        	//司机全部被锁定
        	EdjLog::info($queue_id.'|'.$order_id.'|180系统派单|司机已全部锁定' , 'console');
        	
        	//增加司机全部锁定 弹回提示
//        	$count = QueueDispatchOrder::model()->insertLockDriversCount($queue_id);
//        	if ($count >= 3) {
//        		OrderQueue::model()->setOrder2ManualOpt($queue_id,'--司机已全部被锁定，撤回手动派单--');
//        	}
        	
            $result['msg'] = "司机已全部被锁定";
            return $result;
        }
        
        //司机锁定成功 log 2014-03-03
        if (!empty($drivers[0]['driver_id'])) {
        	EdjLog::info($queue_id.'|'.$order_id.'|180系统派单|'.$drivers[0]['driver_id'].'|司机已锁定|end' , 'console');
        }
        
        $result = array('flag'=>true,'msg'=>'成功','drivers'=>$drivers);
        return $result;
    }
    
    /**
     * 通过位置找司机
     * @param string $queue_id
     * @param string $order_id
     * @return array
     */
    public function getOrderDriversNewRule($queue_id = "" , $order_id = ""){
    	//订单找司机 log 2014-03-03
        EdjLog::info($queue_id.'|'.$order_id.'|180系统派单|找司机|begin' , 'console');
        
        $result = array('flag'=>false,'msg'=>'', 'order_locked' => false ,'drivers'=>array());
        if(empty($queue_id) || empty($order_id)){
        	//验证参数有误 log 2014-03-03
        	EdjLog::warning($queue_id.'|'.$order_id.'|180系统派单|参数错误' , 'console');
        	
            $result['msg'] = "queue_id 或 order_id 不能为空";
            return $result;
        }

        $ret = OrderQueue::model()->findByPk($queue_id);
        if(empty($ret)){
        	//queue数据没有查询到 log 2014-03-03
        	EdjLog::warning($queue_id.'|'.$order_id.'|180系统派单|queue数据无效' , 'console');
        	
            $result['msg'] = "获取数据不存在";
            return $result;
        }
	$comments =  $ret->comments;
	$order_number = $ret->number;
        
        //锁订单
        $order_flag = $this->checkDispatchOrderLock($order_id);
        if($order_flag){
        	//订单已被锁定 log 2014-03-03
        	EdjLog::info($queue_id.'|'.$order_id.'|180系统派单|订单已被锁定' , 'console');
        	
            $result['msg'] = "订单己被锁定";
            $result['order_locked'] = true;
            return $result;
        }
        
        //距离预约时间小于10分钟  将OrderQueue置为派单完成 未派出的Order取消
        $channel = isset($ret->channel) ? $ret->channel : '';
        $change_flag = $this->_isChangeQueueFlagSuccess($queue_id , $ret->booking_time, $channel, $order_number);
        if($change_flag['flag']){
        	//订单已被锁定 log 2014-03-03
        	EdjLog::info($queue_id.'|'.$order_id.'|181|派单超时' , 'console');
        	
        	QueueApiOrder::model()->queue_lock($queue_id);
            $result['msg'] = $change_flag['msg'];
            return $result;
        }

	// Check if the order is accept or canceled by 
	// some driver but the database is not updated
        $cache_key = 'receive_detail_'.$order_id;
        $is_dispatch = Yii::app()->cache->get($cache_key);
	if(!$is_dispatch) {
	    // Do nothing
	} else {
	    // Do log and return
	    EdjLog::info($queue_id.'|'.$order_id.'|'.$is_dispatch.'|180系统派单|订单已经在Memcached中' , 'console');
            $result['msg'] = '订单已经在Memecached中'.$is_dispatch;
	    if($is_dispatch != 'dispatched') {
		EdjLog::info($queue_id.'|'.$order_id.'|'.$is_dispatch.'|180系统派单|Redo receive in dispatch' , 'console');
		Push::model()->redoOrderReceiveInDispatch($queue_id, 
			$order_id, $is_dispatch, $order_number, $comments);
	    }
	    return $result;
	}

        //固定获取司机数量 BY AndyCong 2013-07-31
	$range = isset(Yii::app()->params['OrderOneKeyDispatchRange']) ? 
            Yii::app()->params['OrderOneKeyDispatchRange'] : 5000;

        //分城市距离
        if(isset(Yii::app()->params['OrderOneKeyDispatchRangeByCity'][$ret->city_id])) {
            $range = Yii::app()->params['OrderOneKeyDispatchRangeByCity'][$ret->city_id];
        }

	$driver_number = isset(Yii::app()->params['OrderOneKeyDispatchNumber']) ?
	    Yii::app()->params['OrderOneKeyDispatchNumber'] : 10;

	// Set range for remote order 15KM
	if($channel == CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER) {
	    $range = 15000;
	}

        //日间订单要派2.5.0以上版本司机端
        $driver_app_ver = null;
        if(isset($ret->type) && in_array($ret->type, Order::$daytime_sources)) {
            $driver_app_ver = '2.5.0';
            EdjLog::info($queue_id.'|'.$order_id.'|Daytime nearby' , 'console');
        }    
        //获取支持洗车司机
        if(isset($ret->type) && in_array($ret->type, Order::$washcar_sources)) {
            $driver_app_ver = '2.5.0';
            $drivers=DriverGPS::model()->nearbyService($ret->lng, $ret->lat, 0, $driver_number , $range, Driver::SERVICE_TYPE_FOR_XICHE,$driver_app_ver);
        }else{
            $drivers=DriverGPS::model()->nearby_printLog($ret->city_id,$order_id,$ret->lng, $ret->lat, 0, $driver_number , $range, $driver_app_ver);
        }
        
        if(!empty($drivers) 
            && ($channel == CustomerApiOrder::QUEUE_CHANNEL_BOOKING || $channel == CustomerApiOrder::QUEUE_CHANNEL_SINGLE_CHANGE)) {
            try {
                $filterDriverManager = new FilterDriverManager();
                $filterDriverManager->addStrategy(FilterDriverCrossRiverStrategy::model());
                $filterDriverManager->addStrategy(FilterDriverCrownStrategy::model());
                $filterDriverManager->addStrategy(FilterDriverSpeedStrategy::model());
                $drivers = $filterDriverManager->filter($ret->city_id, $drivers, $ret->lng, $ret->lat, $range,$ret['type'],$order_id,$driver_app_ver);
            } catch (Exception $e) {
                EdjLog::warning('getOrderDriversNewRule:apply FilterDriverStrategy failed, message:' . $e->getMessage() , 'console');
            }
        }
        
        if(empty($drivers)){
	    // Check whether no driver or all driver busy
	    // TODO: When we don't need the log, delete it
	    $drivers=DriverGPS::model()->nearby_printLog($ret->city_id,$order_id,$ret->lng, $ret->lat, 1, 1 , $range, $driver_app_ver);
	    if(!empty($drivers)) {
		EdjLog::info($queue_id.'|'.$order_id.'|180系统派单|司机全在服务中' , 'console');
	    } else {
		EdjLog::info($queue_id.'|'.$order_id.'|180系统派单|附近没司机' , 'console');
	    }
        	
            $msg = '没有获取到周边司机';
            $result['msg'] = $msg;
            return $result;
        }
        
        if(!empty($drivers)) {
        foreach($drivers as $driver){
           if(!isset($driver['crown'])) 
        	EdjLog::info('DispatchDriver:FilterDriverManager2| order_id|'.$order_id.'|driverId:'.$driver['driver_id'], 'console');
        }
     }
        //设置排序规则
        // $drivers = $this->_setDriversRule($drivers);

        //锁司机
        //测试电话号只派测试司机
        $phone = isset($ret->phone) ? $ret->phone : '';
        $test_phone = $this->test_phone();
        if (in_array($phone , $test_phone)) {
        	$drivers = $this->_setTestDriverLock($drivers , 
			$queue_id , $order_id, 1, $channel);
        } else {
        	$drivers = $this->_setDriverLockNewRule($drivers ,
			$queue_id , $order_id, 1, $channel);
        }
        
        if(empty($drivers)){
        	EdjLog::info($queue_id.'|'.$order_id.'|180系统派单|司机已全部锁定' , 'console');
        	
            $result['msg'] = "司机已全部被锁定";
            return $result;
        }
        //订单已被锁定 log 2014-03-03
        if (!empty($drivers[0]['driver_id'])) {
        	EdjLog::info($queue_id.'|'.$order_id.'|180系统派单|'.$drivers[0]['driver_id'].'|司机已锁定|end' , 'console');
        }
    	
        $result = array('flag'=>true,'msg'=>'成功','drivers'=>$drivers);
        return $result;
    }

    /**
     * 判断订单 是否从自动派单换成成手动派单
     * (预约时间 - 当前时间 ) < 10分钟 或 (预约时间 - 接单时间 )大于 40 分钟 订单设置flag = 0 ，走手动派单。否则走自动派单
     * @param $booking_time
     * @param $created 接单时间
     * @param $queue_id
     * @return bool
     */
    private function _isChangeQueueFlag($queue_id , $order_id , $booking_time , $created){
        $ret= array(
          'flag' => false,
          'msg'=>"",
        );
        //预约时间 - 接单时间大于 40 分钟的
        if( strtotime($booking_time)  - strtotime($created)  > 2400 ){
            //预约时间 - 当前时间 小于 20 分钟的
            if(( strtotime($booking_time) - time() ) < 1200 ){
            	//到时间可以强推一次 BY AndyCong 2013-09-02
//            	$is_push_more = QueueDispatchOrder::model()->isDispatchMoreOnce($order_id);
//            	if ($is_push_more) {
//            		return $ret;
//            	}
            	//到时间可以强推一次 BY AndyCong 2013-09-02 END
            	
                $flag = OrderQueue::model()->setOrder2ManualOpt($queue_id,'--长时间未派出，撤回手动派单--');
                $ret['flag'] = $flag;
                $ret['msg'] = "订单时间小于20分钟,己变了手动派单";
            }
        }else{
            if(( strtotime($booking_time) - time() ) < 600 ){
            	//到时间可以强推一次 BY AndyCong 2013-09-02
//            	$is_push_more = QueueDispatchOrder::model()->isDispatchMoreOnce($order_id);
//            	if ($is_push_more) {
//            		return $ret;
//            	}
            	//到时间可以强推一次 BY AndyCong 2013-09-02 END
            	
                $flag = OrderQueue::model()->setOrder2ManualOpt($queue_id,'--长时间未派出，撤回手动派单--');
                $ret['flag'] = $flag;
                $ret['msg'] = "订单时间小于10分钟,己变了手动派单";
            }
        }
        return $ret;
    }
    
    /**
     * 客户端一键下单到时间将状态置成完成
     * @param int $queue_id
     * @param string $booking_time
     * @return boolean
     */
    private function _isChangeQueueFlagSuccess($queue_id , $booking_time, $channel='', $number=0){ 
    	$ret= array(
          'flag' => false,
          'msg'=>"",
        );
        $time = time();
	if(!empty($channel) && $channel == CustomerApiOrder::QUEUE_CHANNEL_FIFTEEN_MIN_BOOKING) {
	    //一键下单 15分钟订单 距离预约时间5分钟弹回
	    $end_time = strtotime($booking_time) - CustomerApiOrder::DISPATCH_BACK_TIME_FIFTEEN_MIN;
	} else {
	    if($number == 1) {
	        //一键下单 下一单 距离预约时间5分钟弹回
	        $end_time = strtotime($booking_time) - CustomerApiOrder::DISPATCH_BACK_TIME_ONE_ORDER;
	    }
	    else {
	        //一键下单 下多单 距离预约时间10分钟弹回
	        $end_time = strtotime($booking_time) - CustomerApiOrder::DISPATCH_BACK_TIME;
	    }
	}
        if ($time > $end_time) {
        	CustomerApiOrder::model()->dispatchFinished($queue_id);
        	$ret['flag'] = true;
            $ret['msg'] = "订单已被收回";
        }
        return $ret;
    }

    /**
     * 锁司机（只派测试工号）
     * @param $drivers
     * @param $queue_id
     * @param $dispatch_number 锁司机人数
     */
    private function _setTestDriverLock($drivers,$queue_id = ""  , $order_id , $dispatch_number = 1, $channel = CustomerApiOrder::QUEUE_CHANNEL_BOOKING){
        $ret = array();
        if(empty($drivers) || empty($queue_id)){
            return $ret;
        }
        
        //解锁已超时的司机
        //QueueDispatchDriver::model()->unlock();
        
        $i = 1;
        foreach($drivers as $driver){
            if($i>$dispatch_number){
                break;
            }

            //验证订单是否已派过这个司机 如果派过 则找下个司机派单
//            $is_driver_dispatched = QueueDispatchOrder::model()->isDriverDispatched($queue_id , $driver['driver_id']);
//            if ($is_driver_dispatched) {
//            	continue;
//            }

            // For remote order, only supported by driver version = 2.4.8 or >= 2.5.0
            // Check version
            if($channel ==  CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER
               && !DriverStatus::model()->isSupportRemoteOrder($driver['driver_id'])) {
                continue;
            }
            
            //测试工号
            $test_drivers = $this->test();
            if(!in_array($driver['driver_id'],$test_drivers)){
                continue;
            }else{
                $flag=QueueDispatchDriver::model()->insert($driver['driver_id']);
                if(!$flag){
                    continue;
                } else{
                	
                	//将司机加入到该订单已派过司机中 BY AndyCong 2014-02-26
//            	    QueueDispatchOrder::model()->queueDispatchedDriver($queue_id , $driver['driver_id']);
                	
                    $params = array(
                        'drivers' => $drivers,
                        'driver' => $driver,
                        'queue_id' => $queue_id,
                        'order_id' => $order_id,
                    );
                    $task=array(
                        'method'=>'dispatch_drivers_log',
                        'params'=>$params
                    );
                    //TODO 先放ORDER里,迁移到派单日志队列 ，modify by sunhongjing 2014-01-12
                    Queue::model()->putin($task,'dispatchlog');
                    $ret[] = $driver;
                    $i++;
                }
            }
        }
        return $ret;
    }
    
    /**
     * 添加测试电话号（只派测试工号）
     * @return array
     * @author AndyCong<congming@edaijia.cn>
     * @version 2013-12-12
     */
    public function test_phone() {
    	$test_phone = array(
                            '18614059527',
                            '15901530932',
                            '13611126764',
                            '13120179213',
                            '13835744808',
                            '18701552183',
                            //下边是winPhone测试电话号
                            '15801988026',
                            '18621583135',
                            '15801988026',
                            '18601713339',
                            '18739945982',
                            //增加测试电话号---郭平
                            '18610629663',
                            //增加测试电话号---焦龙
                            '18210674280',
                            //增加测试电话号---马学云
                            '18511760287',
                            //人保账号 上线需要屏蔽掉 
                            '95518955183',
                            '95518955180',
                            //增加产品电话号
                            '18801319030',
                    );
        return $test_phone;
    }
    
}
