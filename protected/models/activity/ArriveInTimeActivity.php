<?php 

class ArriveInTimeActivity extends CActiveRecord {
    
    private static $NO_ACTIVITY_OBJECT = [];
    
    private static $RELATED_STATUS = array(OrderPosition::FLAG_ACCEPT, OrderPosition::FLAG_ARRIVE, OrderPosition::FLAG_START);
    
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }
    
    public function tableName() {
        return '{{activity_arrive_in_time}}';
    }
    
    public function onOrderStatusChanged($order_status, $order_id, $ready_time = null) {
        if (!in_array($order_status, self::$RELATED_STATUS)) {
            return;
        }
        
        EdjLog::info('onOrderStatusChanged |' . $order_id , 'console');
        $order = $this->queryOrder($order_id);
        
        switch ($order_status) {
            case OrderPosition::FLAG_ACCEPT:
                $this->actionOnAccept($order);
                break;
            case OrderPosition::FLAG_ARRIVE:
                $this->actionOnArrive($order, $ready_time);
                break;
            case OrderPosition::FLAG_START:
                $this->actionOnStart($order);
                break;
            default:
                break;
        }
    }
    
    public function isActive($city_id) {
        $activity = $this->getByCityID($city_id);
        if (empty($activity) || !$activity['turn_on']) {
            return false;
        }
        
        $now = time();
        return $activity['start_date'] <= $now && $now <= $activity['end_date'];
    }
    
    public function isActivityApplied($order) {
        $orderActivity = ArriveInTimeActivityRedis::model()->getOrderActivity($order['order_id']);
        return !empty($orderActivity);
    }
    
    public function actionOnAccept($order) {
        EdjLog::info('actionOnAccept |' . $order['order_id'] , 'console');
        // only for orders from App
        if (!Order::model()->checkActivityOrder($order['source'], $order['channel'])) {
            EdjLog::info('order not from App |' . $order['order_id'] , 'console');
            return;
        }
        
        $city_id = $order['city_id'];
        if (!$this->isActive($city_id)) {
            return;
        }
        
        ArriveInTimeActivityRedis::model()->setOrderActivity($order['order_id'], array('city_id' => $city_id));
        
        if (!ArriveInTimeActivityRedis::model()->isContentSent($order['phone'])) {
            EdjLog::info('send content to phone:' . $order['phone'] , 'console');
            $activity = $this->getByCityID($city_id);
            $success = $this->notifyCustomer($order['phone'], $activity['content']);
            if ($success) {
                EdjLog::info('send sms success, end_date is ' . $activity['end_date'] , 'console');
                $expire = null;
                if ($activity['end_date'] > 0) {
                    $expire = $activity['end_date'] - time();
                }
                EdjLog::info('phone expired time:' . $expire , 'console');
                ArriveInTimeActivityRedis::model()->setContentSent($order['phone'], $expire);
            }
        }
    }
    
    public function actionOnArrive($order, $ready_time) {
        $order_id = $order['order_id'];
        EdjLog::info('actionOnArrive |' . $order_id , 'console');
        $orderActivity = ArriveInTimeActivityRedis::model()->getOrderActivity($order_id);
        if (empty($orderActivity)) {
            return;
        }
        
        // if ready time not passed, try to compute it from order states in redis
        if (empty($ready_time)) {
            $map = OrderQueueMap::model()->getQueueIdByOrderId($order_id, true);
            $callid = $map['queue']['callid'];
            $key = $order['phone'] . '_' . $callid;
            
            $orders = QueueApiOrder::model()->get($key, 'orders');
            if (!empty($orders) && !empty($orders[$order_id]) && !empty($orders[$order_id]['order_states'])) {
                $ready_time = $this->computeTimeForArrival($orders[$order_id]['order_states']);
            }
        }
        
        if (empty($ready_time)) {
            EdjLog::warning('could not get ready time from passed or order states' , 'console');
            return;
        }
        
        $orderActivity['arrive_time'] = $ready_time;
        ArriveInTimeActivityRedis::model()->setOrderActivity($order_id, $orderActivity);
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
    
    public function getByCityID($city_id) {
        // first read from redis
        EdjLog::info('get by city ID:' . $city_id, 'console');
        $activity = ArriveInTimeActivityRedis::model()->getByCity($city_id);

        // read from mysql if not found in redis
        if (!$activity) {
            EdjLog::info('no activity from redis, read from mysql', 'console');
            $activityModel = $this->findByPk($city_id);
            EdjLog::info('activity from from mysql:' . serialize($activityModel), 'console');
            if (empty($activityModel)) {
                $activity = self::$NO_ACTIVITY_OBJECT;
            } else {
                $activity = $activityModel->getAttributes();
                $activity['start_date'] = strtotime($activityModel->start_date);
                if (empty($activityModel->end_date)) {
                    $activity['end_date'] = -1;
                } else {
                    $activity['end_date'] = strtotime($activityModel->end_date);
                }
                $activity['time_range'] = (int) ($activityModel->time_range);
            }
            ArriveInTimeActivityRedis::model()->set($city_id, $activity);
        }
        
        return $activity;
    }

    private function computeTimeForArrival($orderStates) {
        foreach ($orderStates as $orderState) {
            if (OrderProcess::PROCESS_ACCEPT === $orderState['order_state_code']) {
                $acceptTime = $orderState['order_state_timestamp'];
            } else if (OrderProcess::PROCESS_READY === $orderState['order_state_code']) {
                $readyTime = $orderState['order_state_timestamp'];
            }
        }
        
        if (empty($acceptTime) || empty($readyTime)) {
            EdjLog::warning('could not get accept or ready time in redis' , 'console');
            return -1;
        }
        
        return $readyTime - $acceptTime;
    }
    
    public function actionOnStart($order) {
        $order_id = $order['order_id'];
        $orderActivity = ArriveInTimeActivityRedis::model()->getOrderActivity($order_id);
        
        if (empty($orderActivity)) {
            return;
        }
        if (empty($orderActivity['arrive_time'])) {
            EdjLog::warning('no arrive time of order:' . $order_id , 'console');
            return;
        }
        
        $arrive_time = $orderActivity['arrive_time'];
        if ($arrive_time < 1) {
            EdjLog::warning('arrive time is invalid, will use data from App on order submit' , 'console');
            return;
        }
        
        $activity = $this->getByCityID($order['city_id']);
        $in_time = $arrive_time <= $activity['time_range'] ? true : false;
        
        if ($in_time) {
            $driver_message = $activity['in_time_driver_msg'];
            $driver_sms = $activity['in_time_driver_sms'];
            $driver_sms = preg_replace('/\{0\}/', $order['driver_id'], $driver_sms, 1);
            
            $arrive_time_minute = (int) ($arrive_time / 60);
            $arrive_time_second = $arrive_time % 60;
            $customer_sms = $activity['in_time_customer_sms'];
            $customer_sms = preg_replace('/\{0\}/', $order['driver_id'], $customer_sms, 1);
            $customer_sms = preg_replace('/\{1\}/', $arrive_time_minute, $customer_sms, 1);
            $customer_sms = preg_replace('/\{2\}/', $arrive_time_second, $customer_sms, 1);
        } else {
            $driver_message = $activity['not_in_time_driver_msg'];
            $driver_sms = $activity['not_in_time_driver_sms'];
            $driver_sms = preg_replace('/\{0\}/', $order['driver_id'], $driver_sms, 1);
            
            $customer_sms = $activity['not_in_time_customer_sms'];
            $customer_sms = preg_replace('/\{0\}/', $order['driver_id'], $customer_sms, 1);
        }
        
        $this->notifyDriver($order['driver_id'], $order['driver_phone'], $driver_message, $driver_sms);
        $customer_sms_success = $this->notifyCustomer($order['phone'], $customer_sms);
        
        $orderActivity['in_time'] = $in_time;
        $orderActivity['customer_sms_success'] = $customer_sms_success;
        $orderActivity['phone'] = $order['phone'];
        ArriveInTimeActivityRedis::model()->setOrderActivity($order_id, $orderActivity);
    }
    
    private function notifyDriver($driver_id, $phone, $message, $sms) {
        // push message is not supported on driver app client side, so use sms currently
        /* 
        $getui_client = GetuiClient::model()->getDriverInfo($driver_id);
        $push_result = null;
        if(!empty($getui_client)){
            $content = PushMsgFactory::model()->orgPushMsg(array('content' => $message), PushMsgFactory::TYPE_MSG);
            $push_result = EPush::model('driver')->send($getui_client['client_id'], $content);
        }
        
        if (empty($push_result) || empty($push_result['result']) || $push_result['result'] != 'ok') {
            Sms::SendSMS($phone, $sms);
        } */
        
        Sms::SendSMS($phone, $sms);
    }
    
    private function notifyCustomer($phone, $sms) {
        return Sms::SendSMS($phone, $sms);
    }
    
    public function notifyCustomerOnOrderSubmit($city_id, $phone, $driver_id) {
        $activity = $this->getByCityID($city_id);
        $sms = $activity['confirm_customer_sms'];
        $sms = preg_replace('/\{0\}/', $driver_id, $sms, 1);
        $this->notifyCustomer($phone, $sms);
    }
    
    public function isInTime($time, $city_id) {
        $activity = $this->getByCityID($city_id);
        return $time <= $activity['time_range'];
    }
    
    public function getCitiesForSettlement() {
        // the order settlement may happen after the acticity end date, while the activity was still applicable for the order
        // so we do not add end_date check here
        // when the time is after the activity end date, we need manually ensure all the activity applicable orders are settled
        // after that, we can set turn_on to 0  
        $condition = 'turn_on = 1 and start_date <= :now';
        $params = array(':now' => date('Y-m-d H:i:s'));
        $activityList = $this->findAll($condition, $params);
        if (empty($activityList)) {
            return false;
        }
        
        $cities = array();
        $index = 0;
        foreach ($activityList as $activity) {
            $cities[$index] = $activity->city_id;
            $index++;
        }
        return $cities;
    }
    
    public function getActivityType() {
        return CityBasedActivity::ACTIVITY_ARRIVE_IN_TIME;
    }
}
?>
