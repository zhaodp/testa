<?php

class RemoteDispatchBonusActivity extends CRedis {

    public $host = 'redishaproxy.edaijia-inc.cn';

    public $port = 22121;

    private static $CITY_PREFIX = 'activity_remoteDispatchBonus_city_';

    private static $ORDER_PREFIX = 'activity_remoteDispatchBonus_order_';

    private static $ORDER_KEY_EXPIRE = 604800; // 7 days

    private static $RELATED_STATUS = array(OrderPosition::FLAG_ACCEPT);

    private static $CLASS_NAME = 'RemoteDispatchBonusActivity';

    public static function model($className = __CLASS__) {
        $model = null;
        if (isset (self::$_models[$className] ))
            $model = self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }

    public function onOrderStatusChanged($order_status, $order_id, $driver_id) {
        if (!in_array($order_status, self::$RELATED_STATUS)) {
            return;
        }

        EdjLog::info(self::$CLASS_NAME.' - onOrderStatusChanged |' . $order_id .'|'.$driver_id, 'console');
        $order = $this->queryOrder($order_id);

        switch ($order_status) {
            case OrderPosition::FLAG_ACCEPT:
                $this->actionOnAccept($order,$driver_id);
                break;
            default:
                break;
        }
    }

    public function actionOnAccept($order,$driver_id) {
        if(!$this->meetPrerequisite($order)) {
            return;
        }

        $city_id = $order['city_id'];

        if (!$this->isActive($city_id)) {
            EdjLog::info(self::$CLASS_NAME.' not active for order ' . $order['order_id'] , 'console');
            return;
        }
        EdjLog::info(self::$CLASS_NAME.' order_find , ' . $order['order_id'].'|'.$driver_id, 'console');
        
        $distance_data = QueueDispatchOrder::model()->getOrderDriverDisData($order['order_id'], $driver_id);

        if(empty($distance_data)){
                EdjLog::warning(self::$CLASS_NAME. ' could not get distance_data' , 'console');
                return;
        }
        
        if (! isset ( $distance_data ['dist'] )) {
        	EdjLog::warning ( self::$CLASS_NAME . ' distance is not set', 'console' );
        	return;
        }
        
		if ( isset( $distance_data ['is_remote']) && $distance_data ['is_remote'] != 0 ) {
			EdjLog::warning ( self::$CLASS_NAME . ' is_remote order return', 'console' );
			return;
		}

        $distance = $distance_data['dist'];
        $activity =  $this->getActivity($order['city_id']);
        
        if (empty($activity)){
        	EdjLog::info( self::$CLASS_NAME . ' empty activity stop sending sms', 'console' );
        	return;
        }
        
        $remoteBonus = $this->getRemoteBonus($distance,$activity);

        if ( $remoteBonus > 0){        	 
        	$this->setOrderActivity($order['order_id'], array('city_id' => $city_id,'remote_bonus' => $remoteBonus, 'distance' => $distance, 'driver_id' => $driver_id));
        	$this->sendSMS($order, $driver_id, $distance, $remoteBonus,$activity);
        }
    }

    public function checkBonus($order) {
        $orderActivity = $this->getOrderActivity($order->order_id);
        if(empty($orderActivity)) {
                return;
        }

        $remoteBonus = $orderActivity['remote_bonus'];

        if(empty($orderActivity['bonus_paid'])) {
                EdjLog::info(self::$CLASS_NAME." insert remote bonus record for customer. | " . $order->phone . " | $remoteBonus" . ' | ' . $order->order_id . ' | ' . $order->city_id, 'console');
                SubsidyRecord::model()->remoteDispatchInsert($order->driver_id,$remoteBonus,$order->city_id,$order->order_id,time());
                $orderActivity['bonus_paid'] = true;
                $this->setOrderActivity($order->order_id, $orderActivity);
        }
    }

    private function sendSMS($order,$driver_id,$distance,$remoteBonus,$activity){
        $driver_msg   = sprintf($activity['driver_sms'],$order['name'],$distance,$remoteBonus);
        $customer_msg = sprintf($activity['customer_sms'],$remoteBonus);

        $order['driver_phone'] = DriverStatus::model()->getItem($driver_id, 'phone');

        EdjLog::info(self::$CLASS_NAME.' order_matches, ' . $order['order_id'].'|'.$driver_id.'|'.$order['driver_phone'].'|'.$order['phone'], 'console');
                
        EdjLog::info(self::$CLASS_NAME. ' phone_is:'.$order['driver_phone'].'|'.$order['phone'], 'console');

        Sms::SendSMS($order['driver_phone'], $driver_msg);
        Sms::SendSMS($order['phone'], $customer_msg);
    }

    private function meetPrerequisite($order) {
        //APP 或 呼叫中心下单
        if ( $order['source'] != Order::SOURCE_CLIENT && $order['source'] != Order::SOURCE_CALLCENTER){
                EdjLog::info('order soruce is ' . $order['source'] . ', skip. ' . $order['order_id'] , 'console');
                return false;
        }

                if ($order ['source'] == Order::SOURCE_CLIENT) {
                        // 渠道必须为:选司机下单或者一键下单
                        $channelMeet = ($order ['channel'] == CustomerApiOrder::QUEUE_CHANNEL_BOOKING
                                        || $order ['channel'] == CustomerApiOrder::QUEUE_CHANNEL_SINGLE_CHANGE
                                        || $order ['channel'] == CustomerApiOrder::QUEUE_CHANNEL_SINGLE_DRIVER);
                        if (! $channelMeet) {
                                EdjLog::info ( 'order channel not meet, skip. ' . $order ['order_id'], 'console' );
                                return false;
                        }
                }

        if(!$this->isOnlyOneDriverRequested($order['order_id'])) {
            EdjLog::info('more than 1 driver requested, skip. ' . $order['order_id'] , 'console');
            return false;
        }
        return true;
    }

    private function getOrderActivity($order_id) {
        $orderActivity = $this->redis->get(self::$ORDER_PREFIX . $order_id);
        if (!empty($orderActivity)) {
            return json_decode($orderActivity, true);
        }
        return null;
    }

    public function isActive($city_id) {
        $activity = $this->getActivity($city_id);
        if (empty($activity) || !$activity['turn_on']) {
            return false;
        }
        $now = time();
        return strtotime($activity['start_date']) <= $now && $now <= strtotime($activity['end_date']);
    }

    private function queryOrder($order_id) {
        if (strlen($order_id) > 11) {
            return Order::getDbReadonlyConnection()->createCommand()
                ->select('*')
                ->from('t_order')
                ->where('order_number = :order_number', array(':order_number' => $order_id))
                ->queryRow();
        }

        return Order::getDbReadonlyConnection()->createCommand()
            ->select('*')
            ->from('t_order')
            ->where('order_id = :order_id', array(':order_id' => $order_id))
            ->queryRow();
    }

    private function setOrderActivity($order_id, $order_activity) {
        $this->redis->setex(self::$ORDER_PREFIX . $order_id, self::$ORDER_KEY_EXPIRE, json_encode($order_activity));
    }

    private function isOnlyOneDriverRequested($order_id) {
        $map = OrderQueueMap::model()->find(array(
                        'condition' => 'order_id = :order_id',
                        'params'    => array(':order_id' => $order_id),
                        'order'     => ' id ASC'
        ));

        if (empty($map)) {
                EdjLog::warning('could not query order queue map by order id:' . $order_id, 'console' );
                return false;
        }

        // Yii::app()->db_readonly change into OrderQueue::getDbReadonlyConnection()()
        $queue = OrderQueue::getDbMasterConnection()->createCommand()
        ->select('*')
        ->from('t_order_queue')
        ->where('id = :id' , array(':id' => $map->queue_id))
        ->order('id ASC')
        ->queryRow();
        if ($queue['number'] > 1) {
                EdjLog::info('requested driver number more than 1, skip', 'console' );
                return false;
        }
        return true;
    }

    private function getRemoteBonus($distance,$activity){
    	if ( !isset($activity['phase_data']) ){
    		EdjLog::error(self::$CLASS_NAME.'empty phase_data', 'console' );
    		return 0;
    	}
    	
    	$phaseData = $activity['phase_data'];
    	
        for ($i=0; $i<=5; $i++) {
        	$phaseKey =  'phase'.$i.'_bonus';
        	if ( !isset($phaseData[$phaseKey]) ){
        		EdjLog::error(self::$CLASS_NAME.' empty phase '.$phaseKey, 'console' );
        		return 0;
        	}
        }
    	
    	if($distance > 0 && $distance <= 1){
    		return $phaseData['phase0_bonus'];
    	}
    	
    	if($distance > 1 && $distance <= 2){
    		return $phaseData['phase1_bonus'];
    	}
    	 
    	if($distance > 2 && $distance <= 3){
    		return $phaseData['phase2_bonus'];
        }

        if($distance > 3 && $distance <= 4){
        	return $phaseData['phase3_bonus'];
        }

        if($distance > 4 && $distance <= 5){
        	return $phaseData['phase4_bonus'];
        }
        
        if($distance > 5){
        	return $phaseData['phase5_bonus'];
        }

        return 0;
    }

    private function getActivity($city_id) {
        $activity_key = self::$CITY_PREFIX . $city_id;
        $activity = $this->redis->get($activity_key);
        EdjLog::info(self::$CLASS_NAME.' from redis by key $activity_key :' . json_encode($activity), 'console');

        if(!empty($activity)) {
            return json_decode($activity, true);
        }else{
        	return null;
        }
    }
    
}
?>
