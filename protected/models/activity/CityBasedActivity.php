<?php 

abstract class CityBasedActivity extends CActiveRecord {

    private static $NO_ACTIVITY_OBJECT = [];
    
    const ACTIVITY_ARRIVE_IN_TIME = 'activity_arrive_in_time';
    
    const ACTIVITY_NEW_CUSTOMER_FREE = 'activity_new_customer_free';
    
    private $name;
    
    public static function getAppliedActivities($order) {
        $activities = array();
        
        if (empty($order)) {
            return $activities;
        }
        
        if (!empty(ArriveInTimeActivityRedis::model()->getOrderActivity($order['order_id']))) {
            $activity = ArriveInTimeActivity::model()->getByCityID($order['city_id']);
            $activityModel = new ArriveInTimeActivity();
            // TODO add set name
//             $activityModel->setName($activity['name']);
            $activities[] = $activityModel;
        }
        
        if (!empty(NewCustomerFreeActivityRedis::model()->getOrderActivity($order['order_id']))) {
            $activity = NewCustomerFreeActivity::model()->getActivity($order['city_id']);
            $activityModel = new NewCustomerFreeActivity();
            $activityModel->setName($activity['name']);
            $activities[] = $activityModel;
        }
        return $activities;
    }
    
    public static function isActivityApplied($order, $activityType) {
        if (self::ACTIVITY_ARRIVE_IN_TIME == $activityType) {
            return !empty(ArriveInTimeActivityRedis::model()->getOrderActivity($order['order_id']));
        }
        
        if (self::ACTIVITY_NEW_CUSTOMER_FREE == $activityType) {
            return !empty(NewCustomerFreeActivityRedis::model()->getOrderActivity($order['order_id']));
        }
        
        EdjLog::warning('unknown activity type:' . $activityType , 'console');
        return false;
    }
    
    public static function getOpenedCitiesForSettlement($activityType) {
        if (self::ACTIVITY_ARRIVE_IN_TIME == $activityType) {
            return ArriveInTimeActivity::model()->getCitiesForSettlement();
        }
        
        if (self::ACTIVITY_NEW_CUSTOMER_FREE == $activityType) {
            return NewCustomerFreeActivity::model()->getCitiesForSettlement();
        }
    }
    
    public function getActivity($city_id) {
        EdjLog::info('get by city ID:' . $city_id, 'console');
        $activity = $this->getActivityFromRedis($city_id);
    
        // read from mysql if not found in redis
        if(is_null($activity)) {
            EdjLog::info('no activity from redis, read from mysql', 'console');
            $activityModel = $this->findByPk($city_id);
            if (empty($activityModel)) {
                EdjLog::info('activityModel is empty', 'console');
                $activity = self::$NO_ACTIVITY_OBJECT;
            } else {
                $activity = $activityModel->getAttributes();
                $activity['start_date'] = strtotime($activityModel->start_date);
                if (!empty($activityModel->end_date)) {
                    $activity['end_date'] = strtotime($activityModel->end_date);
                }
                $this->convertModelData($activity, $activityModel);
                EdjLog::info('activity data:' . json_encode($activity), 'console');
            }
            $this->setActivityInRedis($city_id, $activity);
        }
    
        return $activity;
    }
    
    abstract protected function getActivityFromRedis($city_id);
    
    abstract protected function setActivityInRedis($city_id, $activity);
    
    abstract public function getActivityType();
    
    protected function convertModelData($activity, $model) {}
    
    public function getCitiesForSettlement() {
        // the order settlement may happen after the acticity end date, while the activity was still applicable for some orders which had not been settled 
        // so we need delay end_date check by 7 days (604800 seconds)
        $condition = 'turn_on = 1 and start_date <= :now and end_date >= :end';
        $now = time();
        $params = array(
                ':now' => date('Y-m-d H:i:s', $now),
                ':end' => date('Y-m-d H:i:s', $now - 604800)
        );
        $activityList = $this->findAll($condition, $params);
        if (empty($activityList)) {
            return false;
        }
    
        $cities = array();
        foreach ($activityList as $activity) {
            $cities[] = $activity->city_id;
        }
        return $cities;
    }
    
    protected function isActive($activity) {
        if (empty($activity)) {
            return false;
        }
    
        if (isset($activity['turn_on']) && !$activity['turn_on']) {
            return false;
        }
    
        $now = time();
        if (isset($activity['end_date']) && $activity['end_date'] > 0) {
            return $activity['start_date'] <= $now && $now <= $activity['end_date'];
        }
        return $activity['start_date'] <= $now;
    }
    
    protected function queryOrder($order_id) {
        if (strlen($order_id) > 11) {
            return Yii::app()->dborder_readonly->createCommand()
            ->select('*')
            ->from('t_order')
            ->where('order_number = :order_number', array(':order_number' => $order_id))
            ->queryRow();
        }
    
        return Yii::app()->dborder_readonly->createCommand()
            ->select('*')
            ->from('t_order')
            ->where('order_id = :order_id', array(':order_id' => $order_id))
            ->queryRow();
    }
    
    protected function pushToDriver($driver_id, $message) {
        $client = GetuiClient::model()->getDriverInfo($driver_id);
        if (empty($client)) {
            return;
        }
    
        $params = array ('content' => $message);
        $content = PushMsgFactory::model()->orgPushMsg($params, PushMsgFactory::TYPE_MSG );
        $result = EPush::model ('driver')->send($client ['client_id'], $content);
    
        return !empty($result) && !empty($result['result']) && $result['result'] === 'ok';
    }
    
    protected function sms($phone, $message) {
        return Sms::SendSMS ($phone, $message);
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
}

?>
