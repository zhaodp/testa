<?php
/**
 * 找司机 --- 只查询附近司机 其他什么都不干
 */
class DalDispatchDriver{
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
     * 通过位置找司机
     * @param string $queue_id
     * @param string $order_id
     * @return array
     */
    public function getDrives($queue_id = "" , $order_id = ""){
        $result = array('flag'=>false,'msg'=>'','drivers'=>array());

        if(empty($queue_id) || empty($order_id)){
            $result['msg'] = "queue_id 或 order_id 不能为空";
            return $result;
        }

        $ret = OrderQueue::model()->findByPk($queue_id);

        if(empty($ret)){
            $result['msg'] = "获取数据不存在";
            return $result;
        }

        //判断订单时间<10分钟 或 创建时间-预约时间 < 20分钟 变为手动派单
        $channel = isset($ret->channel) ? $ret->channel : '';
        if ($channel == '01011') {  //如果是雷石的订单超时取消订单
        	$change_flag = $this->_isChangeQueueFlagSuccess($queue_id ,$ret->booking_time);
        } else {
        	$change_flag = $this->_isChangeOrderFlag($ret->booking_time,$ret->created,$queue_id);
        }
        
        if($change_flag['flag']){
        	QueueApiOrder::model()->queue_lock($queue_id);
            $result['msg'] = $change_flag['msg'];
            return $result;
        }

        /*$dispath_count  = $this->_dispatchCount($queue_id);
        if($dispath_count){
            $result['msg'] = "己超最大派单次数,现己变了手动派单";
            return $result;
        }*/

        $dispath_number = $ret->number - $ret->dispatch_number;

        if($dispath_number == 0 ){
            $result['msg'] = "司机己派完";
            return $result;
        }

//        $dipatch_nearby_drivers = $this->_getMinNumber($dispath_number);   //获取司机数量可以固定

        //固定获取司机数量 BY AndyCong 2013-07-31
        $drivers=DriverGPS::model()->nearby_printLog($ret->city_id,$order_id,$ret->lng, $ret->lat, 0, $this->_nearby_driver_number , 3000);

        if(empty($drivers)){
            $flag = OrderQueue::model()->setOrder2ManualOpt($queue_id,'--附近没有司机，请手动派单--');
            if($flag){
                $msg = "没有获取到周边司机,己变为手动派单";
            }else{
                $msg = '没有获取到周边司机';
            }
            $result['msg'] = $msg;
            return $result;
        }
        
        //锁订单
        $order_flag = $this->checkDispatchOrderLock($order_id);
        if($order_flag){
            $result['msg'] = "订单己被锁定";
            return $result;
        }

        //设置排序规则
        $drivers = $this->_setDriversRule($drivers);

        //锁司机
        if ($channel == '01011') { //如果是雷石的订单则只派测试司机
        	$drivers = $this->_setTestDriverLock($drivers , $queue_id , $order_id);
        } else {
        	$drivers = $this->_setDriverLock($drivers , $queue_id , $order_id);
        }

        if(empty($drivers)){
            $result['msg'] = "没有获取到周边司机";
            return $result;
        }
        $result = array('flag'=>true,'msg'=>'成功','drivers'=>$drivers);

        return $result;

    }
    
    /**
     * 通过位置找司机(适用于老版本)
     * @param string $queue_id
     * @param string $order_id
     * @return array
     */
    public function getDriversAdaptOldVersion($queue_id = ""){
        if(empty($queue_id)){
            return array();
        }

        //验证OrderQueue
        $ret = OrderQueue::model()->findByPk($queue_id);
        if(empty($ret)){
            return array();
        }

        //判断订单时间<10分钟 或 创建时间-预约时间 < 20分钟 变为手动派单
        $change_flag = $this->_isChangeOrderFlag($ret->booking_time,$ret->created,$queue_id);
        if($change_flag['flag']){
            return array();
        }
        
        //获取要派司机人数及周围3公里司机
        $dispath_number = $ret->number - $ret->dispatch_number;
        if($dispath_number == 0 ){
            return array();
        }
        $dipatch_nearby_drivers = $this->_getMinNumber($dispath_number); 
        $drivers=DriverGPS::model()->nearby_printLog($ret->city_id,$ret->order_id,$ret->lng, $ret->lat, 0, $dipatch_nearby_drivers , 3000);
        if(empty($drivers)){
            return array();
        }

        //设置排序规则
        $drivers = $this->_setDriversRule($drivers);
        return $drivers;
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
     * 最少取多少司机
     * @param $number
     * @return int
     */
    private function _getMinNumber($number){
        $dipatch_drivers = 10;
        if( $number<4 ){
            $dipatch_drivers = 10;
        }else{
            $dipatch_drivers = $number*2;
        }
        return $dipatch_drivers;
    }

    /**
     * 判断订单 是否从自动派单换成成手动派单
     * (预约时间 - 当前时间 ) < 10分钟 或 (预约时间 - 接单时间 )大于 40 分钟 订单设置flag = 0 ，走手动派单。否则走自动派单
     * @param $booking_time
     * @param $created 接单时间
     * @param $queue_id
     * @return bool
     */
    private function _isChangeOrderFlag($booking_time,$created , $queue_id){
        $ret= array(
          'flag' => false,
          'msg'=>"",
        );
        //预约时间 - 接单时间大于 40 分钟的
        if( strtotime($booking_time)  - strtotime($created)  > 2400 ){
            //预约时间 - 当前时间 小于 20 分钟的
            if(( strtotime($booking_time) - time() ) < 1200 ){
                $flag = OrderQueue::model()->setOrder2ManualOpt($queue_id,'--长时间未派出，撤回手动派单--');
                $ret['flag'] = $flag;
                $ret['msg'] = "订单时间小于20分钟,己变了手动派单";
            }
        }else{
            if(( strtotime($booking_time) - time() ) < 600 ){
                $flag = OrderQueue::model()->setOrder2ManualOpt($queue_id,'--长时间未派出，撤回手动派单--');
                $ret['flag'] = $flag;
                $ret['msg'] = "订单时间小于10分钟,己变了手动派单";
            }
        }
        return $ret;
    }


    /**
     * 单人不锁订单池
     * @param $number
     * @return bool
     */
    private function _checkSingleNumberOrderLock($number){
        $flag = true;
        if( 1 < $number){
           $flag = false;
        }
        return $flag;
    }


    /**
     * 派送司机规则
     * @param array $drivers
     * @return array
     */
    private function _setDriversRule($drivers = array()){

        if(empty($drivers)){
            return array();
        }

        $drivers = $this->_crownRule($drivers);

        $drivers = Common::array_sort($drivers,'weight','asc');

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
     * 根据派单次数将订单设置为手动派单
     * @param $queue_id
     * @return bool
     */
    private  function _dispatchCount($queue_id){
       $count = 5 ;
       $dispath_count = QueueDispatchOrder::model()->dispatchGetQueueCount($queue_id);
       if($dispath_count < $count) {
           QueueDispatchOrder::model()->dispatchSetQueueCount($queue_id);
           return false;
       }else{
           OrderQueue::model()->setOrder2ManualOpt($queue_id,'--超过5次未派出，撤回手动派单--');
           QueueDispatchOrder::model()->dispatchDeleteQueueCount($queue_id);
           return true;
       }
    }

    /**
     * 锁司机
     * @param $drivers
     * @param $queue_id
     * @param $dispatch_number 锁司机人数
     */
    private function _setDriverLock($drivers,$queue_id = ""  , $order_id , $dispatch_number = 1){
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

            //测试工号
//            $test_drivers = $this->test();
//
//            if(!in_array($driver['driver_id'],$test_drivers)){
//                continue;
//            }else{

                $flag=QueueDispatchDriver::model()->insert($driver['driver_id']);
                if(!$flag){
                    continue;
                }
                else{

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
                    //TODO 先放ORDER里
                    Queue::model()->putin($task,'order');

                    $ret[] = $driver;
                    $i++;
                }

//            }

        }
        return $ret;
    }
    /**
     * 锁司机测试
     * @param $drivers
     * @param $queue_id
     * @param $dispatch_number 锁司机人数
     */
    private function _setDriverLockNewRule($drivers,$queue_id = ""  , $order_id , $dispatch_number = 1){
        $ret = array();
        if(empty($drivers) || empty($queue_id)){
            return $ret;
        }
        //解锁已超时的司机
        //QueueDispatchDriver::model()->unlock();

        $i = 1;
        foreach($drivers as $driver){
        	echo "\n".$driver['driver_id']."\n";
            if($i>$dispatch_number){
                break;
            }

            //测试工号
//            $test_drivers = $this->test();

//            if(!in_array($driver['driver_id'],$test_drivers)){
//                continue;
//            }else{

            $flag=QueueDispatchDriver::model()->insert($driver['driver_id']);
            if(!$flag){
                continue;
            } else{
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
                //TODO 先放ORDER里
                Queue::model()->putin($task,'order');

                $ret[] = $driver;
                $i++;
            }

//            }

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
        $result = array('flag'=>false,'msg'=>'', 'order_locked' => false ,'drivers'=>array());

        if(empty($queue_id) || empty($order_id)){
            $result['msg'] = "queue_id 或 order_id 不能为空";
            return $result;
        }

        $ret = OrderQueue::model()->findByPk($queue_id);

        if(empty($ret)){
            $result['msg'] = "获取数据不存在";
            return $result;
        }

        //锁订单
        $order_flag = $this->checkDispatchOrderLock($order_id);
        if($order_flag){
            $result['msg'] = "订单己被锁定";
            $result['order_locked'] = true;
            return $result;
        }
        
        //判断订单时间<10分钟 或 创建时间-预约时间 < 20分钟 变为手动派单
        $channel = isset($ret->channel) ? $ret->channel : '';
        if ($channel == '01011') {  //如果是雷石的订单超时取消订单
        	$change_flag = $this->_isChangeQueueFlagSuccess($queue_id ,$ret->booking_time);
        } else {
        	$change_flag = $this->_isChangeQueueFlag($queue_id , $order_id , $ret->booking_time , $ret->created);
        }
        
        if($change_flag['flag']){
        	QueueApiOrder::model()->queue_lock($queue_id);
            $result['msg'] = $change_flag['msg'];
            echo "\nD|长时间未派出弹回|".date('Y-m-d H:i:s')."|".$queue_id."|".$order_id."\n";
            return $result;
        }

        $range = 0;
        //固定获取司机数量 BY AndyCong 2013-07-31
        if( '23'==$ret->city_id ){
            $range = 5000;
        }else{
            $range = 3000;
        }
        
        $drivers=DriverGPS::model()->nearby_printLog($ret->city_id,$order_id,$ret->lng, $ret->lat, 0, $this->_nearby_driver_number , $range);
        
        if(empty($drivers)){
            $msg = '没有获取到周边司机';
            $result['msg'] = $msg;
            return $result;
        }

        //设置排序规则
        //$drivers = $this->_setDriversRule($drivers);
        $filterDriverManager = new FilterDriverManager();
        $filterDriverManager->addStrategy(FilterDriverCrownStrategy::model());
        $filterDriverManager->addStrategy(FilterDriverSpeedStrategy::model());
        $drivers = $filterDriverManager->filter($ret->city_id, $drivers, $ret->lng, $ret->lat, $range,$ret['type'], $order_id);

        
        if(!empty($drivers)){
        foreach($drivers as $driver){
           if(!isset($driver['crown'])) 
        	EdjLog::info('DalDispatchDriver:FilterDriverManager| order_id|'.$order_id.'|driverId:'.$driver['driver_id'], 'console');
        }
        }
      
        //锁司机
        if ($channel == '01011') { //如果是雷石的订单则只派测试司机
        	$drivers = $this->_setTestDriverLock($drivers , $queue_id , $order_id);
        } else {
        	$drivers = $this->_setDriverLock($drivers , $queue_id , $order_id);
        }
        if(empty($drivers)){
            $result['msg'] = "司机已全部被锁定";
            return $result;
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
    	echo "\n queue_id is ".$queue_id." AND order_id is".$order_id."\n";
        $result = array('flag'=>false,'msg'=>'', 'order_locked' => false ,'drivers'=>array());

        if(empty($queue_id) || empty($order_id)){
            $result['msg'] = "queue_id 或 order_id 不能为空";
            return $result;
        }

        $ret = OrderQueue::model()->findByPk($queue_id);

        if(empty($ret)){
            $result['msg'] = "获取数据不存在";
            return $result;
        }

        //锁订单
        $order_flag = $this->checkDispatchOrderLock($order_id);
        if($order_flag){
            $result['msg'] = "订单己被锁定";
            $result['order_locked'] = true;
            return $result;
        }
        
        //判断订单时间<10分钟 或 创建时间-预约时间 < 20分钟 变为手动派单
        $change_flag = $this->_isChangeQueueFlagSuccess($queue_id , $ret->booking_time);
        if($change_flag['flag']){
        	QueueApiOrder::model()->queue_lock($queue_id);
            $result['msg'] = $change_flag['msg'];
            return $result;
        }
        
        $range = 3000;
        //固定获取司机数量 BY AndyCong 2013-07-31
        $drivers=DriverGPS::model()->nearby_printLog($ret->city_id,$order_id,$ret->lng, $ret->lat, 0, $this->_nearby_driver_number , $range);
        if(empty($drivers)){
            $msg = '没有获取到周边司机';
            $result['msg'] = $msg;
            return $result;
        }

        //设置排序规则
        //$drivers = $this->_setDriversRule($drivers);
        $filterDriverManager = new FilterDriverManager();
        $filterDriverManager->addStrategy(FilterDriverCrownStrategy::model());
        $filterDriverManager->addStrategy(FilterDriverSpeedStrategy::model());
        $drivers = $filterDriverManager->filter($ret->city_id, $drivers, $ret->lng, $ret->lat, $range,$ret['type'], $order_id);
       
         
        if(!empty($drivers)){
        foreach($drivers as $driver){
            if(!isset($driver['crown']))
        	EdjLog::info('DalDispatchDriver:FilterDriverManager2| order_id|'.$order_id.'|driverId:'.$driver['driver_id'], 'console');
        }
        }
        //锁司机
        //测试电话号只派测试司机
        $phone = isset($ret->phone) ? $ret->phone : '';
        $test_phone = $this->test_phone();
        if (in_array($phone , $test_phone)) {
        	$drivers = $this->_setTestDriverLock($drivers , $queue_id , $order_id);
        } else {
        	$drivers = $this->_setDriverLockNewRule($drivers , $queue_id , $order_id);
        }
        
        if(empty($drivers)){
            $result['msg'] = "司机已全部被锁定";
            return $result;
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
            	$is_push_more = QueueDispatchOrder::model()->isDispatchMoreOnce($order_id);
            	if ($is_push_more) {
            		return $ret;
            	}
            	//到时间可以强推一次 BY AndyCong 2013-09-02 END
            	
                $flag = OrderQueue::model()->setOrder2ManualOpt($queue_id,'--长时间未派出，撤回手动派单--');
                $ret['flag'] = $flag;
                $ret['msg'] = "订单时间小于20分钟,己变了手动派单";
            }
        }else{
            if(( strtotime($booking_time) - time() ) < 600 ){
            	//到时间可以强推一次 BY AndyCong 2013-09-02
            	$is_push_more = QueueDispatchOrder::model()->isDispatchMoreOnce($order_id);
            	if ($is_push_more) {
            		return $ret;
            	}
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
    private function _isChangeQueueFlagSuccess($queue_id , $booking_time){ 
    	$ret= array(
          'flag' => false,
          'msg'=>"",
        );
        $time = time();
        $end_time = strtotime($booking_time) - CustomerApiOrder::DISPATCH_BACK_TIME;
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
    private function _setTestDriverLock($drivers,$queue_id = ""  , $order_id , $dispatch_number = 1){
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

            //测试工号
            $test_drivers = $this->test();
            if(!in_array($driver['driver_id'],$test_drivers)){
                continue;
            }else{
                $flag=QueueDispatchDriver::model()->insert($driver['driver_id']);
                if(!$flag){
                    continue;
                } else{
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
                    //TODO 先放ORDER里
                    Queue::model()->putin($task,'order');
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
                    );
        return $test_phone;
    }
}
