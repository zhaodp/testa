<?php
/**
 * 订单流 司机拒单服务
 *
 */
class RefuseOrderService extends BaseFlowService
{

    private static $instance;

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new RefuseOrderService();
        }
        return self::$instance;
    }

    /*
     * API : driver.order.refuse
     * 司机拒单
     */
    public function refuseOrder($params) {
        $token = isset($params['token']) && !empty($params['token']) ? trim($params['token']) : "";
        $queue_id=isset($params['queue_id'])&&!empty($params['queue_id']) ? trim($params['queue_id']) : "";
        $order_id = isset($params['order_id']) ? $params['order_id'] : '';

        //增加类型 BY AndyCong 2013-12-26
        $type = isset($params['type']) ? $params['type'] : OrderRejectLog::REJECT_TYPE_SYSTEM_BACK;
        if (empty($token) || empty($queue_id)) {
            EdjLog::info('Refuse order|Invalid params|'.$order_id);
            $ret=array('code'=>2 , 'message'=>'参数不正确!');
            return $ret;
        }

        $driver = DriverStatus::model()->getByToken($token);
        if ($driver === null || $driver->token === null || $driver->token!==$token ) {
            EdjLog::info('Refuse order|Invalid token|'.$order_id);
            $ret=array( 'code'=>1, 'message'=>'token失效' );
            return $ret;
        }

        $driver_id = $driver->driver_id;
        //添加task队列
        $task=array(
            'class' => __CLASS__,
            'method'=>'refuseOrderJob',
            'params'=>array(
                'queue_id'=>$queue_id,
                'driver_id'=>$driver_id,
                'order_id'=>$order_id,
                'type' => $type,
                'created'=>date("Y-m-d H:i:s"), //确认时间
            )
        );
        $ret = Queue::model()->putin($task,'apporder');
        if(!$ret) {
            EdjLog::info('|Refuse order|Failed|Putin array failed|'.$driver_id.'|'.$order_id);
        }
        //添加队列结束

        //解锁司机，而不直接改司机的状态。add by sunhongjing 2014-01-17
        QueueDispatchDriver::model()->delete($driver_id);

        //解锁订单
        if (!empty($order_id)) {
            QueueDispatchOrder::model()->delete($order_id);
        }

        //如果司机主动拒单,将司机设置为上次派送派过司机
        if($type == OrderRejectLog::REJECT_TYPE_DRIVER_REJECT) {
            QueueDispatchOrder::model()->queueDispatchedDriver($order_id , $driver_id);
        }

        $ret=array(
            'code'=>0,
            'status'=>$driver->status,
            'message'=>'成功!'
        );
        return $ret;
    }

    /*
     * QueueProcess : upload_driver_reject_log,push_order_reject_log
     * 司机拒单
     */
    public function refuseOrderJob($params) {
        if (empty($params['queue_id']) || empty($params['order_id'])
            || empty($params['driver_id']) || empty($params['type'])
            || empty($params['created'])) {
            return false;
        }

        if(OrderDriverMatchType::checkType($params['order_id']) == OrderDriverMatchType::CHOOSE_DRIVER) {
            DalOrder::model()->driverRejectLog($params);
            return true;
        } else {
            CustomerApiOrder::model()->driverReject($params);

            //记录log
            EdjLog::info($params['queue_id'].'|'.$params['order_id'].'|405司机拒单|'.$params['driver_id'].'|begin' , 'console');
            $model = new OrderRejectLog();
            $model->attributes = $params;
            if ($model->save()) {
                //记录log
                EdjLog::info($params['queue_id'].'|'.$params['order_id'].'|405司机拒单|'.$params['driver_id'].'|log记录成功|end' , 'console');
                return true;
            }else {
                //记录log
                EdjLog::info($params['queue_id'].'|'.$params['order_id'].'|405司机拒单|'.$params['driver_id'].'|log记录失败|end' , 'console');
                return false;
            }
        }
    }
}
