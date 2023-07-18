<?php
class NewCustomerFreeActivity extends CityBasedActivity {
    
    private static $RELATED_STATUS = array (OrderPosition::FLAG_START);
    
    public static function model($className = __CLASS__) {
        return parent::model ( $className );
    }
    
    public function tableName() {
        return '{{activity_new_customer_free}}';
    }
    
    public function onOrderStatusChanged($order_status, $order_id, $is_multi = false) {
        if (!in_array($order_status, self::$RELATED_STATUS)) {
            return;
        }
        
        $order = $this->queryOrder($order_id);
        if (empty($order)) {
            EdjLog::warning('could not query order with id:' . $order_id, 'console' );
            return;
        }
        
        $activity = $this->getActivity($order['city_id']);
        if (!$this->isActive($activity)) {
            return;
        }
        
        EdjLog::info ( 'NewCustomerFreeActivity.onOrderStatusChanged |' . $order_id, 'console' );
        
        switch ($order_status) {
            case OrderPosition::FLAG_START :
                $this->actionOnStart($order, $activity, $is_multi);
                break;
            default :
                break;
        }
    }
    private function actionOnStart($order, $activity, $is_multi) {
        if (!$this->validateOrder($order, $is_multi)) {
            return;
        }
        
        $driver_id = $order['driver_id'];
        
        $driver = DriverStatus::model()->get($driver_id);
        if (empty($driver)) {
            EdjLog::warning('could not find driver with id:' . $driver_id, 'console' );
            return;
        }
        
        EdjLog::info ('new customer free activity can be applied to order, id:' . $order['order_id'], 'console' );
        NewCustomerFreeActivityRedis::model()->setOrderActivity($order['order_id']);
        // notify customer
        $customer_msg = preg_replace('/\{0\}/', $driver_id, $activity['customer_msg'], 1);
        $this->sms($order['phone'], $customer_msg);
        // notify driver
        $this->pushToDriver($driver_id, $activity['driver_msg']);
        $this->sms($driver->phone, $activity['driver_msg']);
    }
    
    private function validateOrder($order, $is_multi) {
        if (!Order::model()->checkActivityOrder($order['source'], $order['channel'])) {
            EdjLog::info('order not from App, skip', 'console' );
            return false;
        }
        
        // when ordered by multi, the requested driver number should be 1
        if ($is_multi && !$this->isOnlyOneDriverRequested($order['order_id'])) {
            return false;
        }
        
        if (!RCustomerInfo::model()->checkCustomerFirstOrder($order['phone'], $order['order_id'], true)
            && !NewCustomerFreeActivityRedis::model()->isTestPhone($order['phone'])) {
            EdjLog::info('not first order, skip', 'console' );
            return false;
        }
        
        return true;
    }
    
    private function isOnlyOneDriverRequested($order_id) {
        // MKTODO confirm why 'id ASC' needed
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
        $queue = OrderQueue::getDbReadonlyConnection()->createCommand()
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
    
    public function getActivityType() {
        return CityBasedActivity::ACTIVITY_NEW_CUSTOMER_FREE;
    }
    
    protected function getActivityFromRedis($city_id) {
        return NewCustomerFreeActivityRedis::model()->getByCity($city_id);
    }
    
    protected function setActivityInRedis($city_id, $activity) {
        NewCustomerFreeActivityRedis::model()->set($city_id, $activity);
    }
}

?>