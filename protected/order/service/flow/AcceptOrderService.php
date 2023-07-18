<?php
/**
 * 订单流 接单服务
 *
 */
class AcceptOrderService extends BaseFlowService
{

    private static $instance;

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new AcceptOrderService();
        }
        return self::$instance;
    }

    /*
     * API driver.order.receive
     * 司机接单API service
     */
    public function acceptOrder($params) {
        //接收并验证参数
        $queue_id = isset($params['queue_id']) ? $params['queue_id'] : '';
        $order_id = isset($params['order_id']) ? $params['order_id'] : '';
        //$driver_id = isset($params['driver_id']) ? $params['driver_id'] : '';
        $push_msg_id = isset($params['push_msg_id']) ? $params['push_msg_id'] : 0;
        $gps_type = isset($params['gps_type']) ? $params['gps_type'] : "wgs84";
        $lng = isset($params['lng']) ? $params['lng'] : '';
        $lat = isset($params['lat']) ? $params['lat'] : '';
        $log_time = isset($params['log_time']) ? $params['log_time'] : '';
        $token = isset($params['token']) ? $params['token'] : '';


        if (0 == $queue_id || 0 == $order_id || 0 == $push_msg_id ||
                empty($token) || empty($lng) || empty($lat) || empty($log_time)) {
            EdjLog::info('Recevie order|failed|Invalid params|'.$order_id);
            $ret = array('code' => 2 , 'message' =>'参数有误');
            return $ret;
        }

        //验证token
        $driver = DriverStatus::model()->getByToken($token);
        if ($driver) {
            //两种类型订单 DAL层司机接单 和 老版本接单方式
            if (OrderDriverMatchType::checkType($order_id) == OrderDriverMatchType::CHOOSE_DRIVER) {
                //验证订单是否已被接单 BY AndyCong 2013-08-28
                $cache_key = 'receive_detail_'.$order_id;
                $is_dispatch = Yii::app()->cache->get($cache_key);
                // TODO: Use cahe here as lock is not Bug free
                // Use redis in the future
                if (!$is_dispatch) {
                    $cache_value = $driver->driver_id;
                    $set_ret = Yii::app()->cache->set($cache_key, $cache_value, 28800);
                    if(!$set_ret) {
                        // Update memechace failed
                        EdjLog::info('|Recevie order|Failed| Write memcached failed|'.$driver->driver_id.'|'.$order_id);
                        $ret = array('code' => 2 , 'message' => '网络问题，接单失败');
                        return $ret;
                    }
                } else {
                        // Do log now and don't handle the case that the dispatch value does not equal the driver
                    // TODO: Check if the driver need to receive the order
                    if ($is_dispatch != $driver->driver_id) {
                        EdjLog::info('|Recevie order|Failed|Order has been received by'.$is_dispatch.
                            '|'.$driver->driver_id.'|'.$order_id);
                        $ret = array('code' => 2 , 'message' => '订单已被派出');
                        return $ret;
                    }

                    EdjLog::info('|Recevie order|Success|Order has been received by'.$is_dispatch.
                        '|'.$driver->driver_id.'|'.$order_id);
                    $ret = array('code' => 0 , 'message' => '接单成功');
                    if($ret['code'] == 0){//接单成功
                        ClientPush::model()->pushShareForCustomer($order_id, PageConfig::TRIGGER_RECEIVE);
                    }
                    return $ret;
                }
                $driver_info = DriverStatus::model()->get($driver->driver_id);
                $driver_info->status = 1;
                $params = array(
                    'queue_id' => $queue_id,
                    'order_id' => $order_id,
                    'driver_id' => $driver->driver_id,
                    'push_msg_id' => $push_msg_id,
                    'gps_type' => $gps_type,
                    'lng' => $lng,
                    'lat' => $lat,
                    'log_time' => $log_time,
                );
                $task = array(
                    'class'  => __CLASS__,
                    'method' => 'acceptOrderJob',
                    'params' => $params,
                );
                $ret = Queue::model()->putin($task,'order');
                if(!$ret) {
                    // TODO: handle putin failed in the future
                    EdjLog::info('|Recevie order|Failed|Putin array failed|'.$driver->driver_id.'|'.$order_id);
                }
                $ret = array('code' => 0 , 'message' => '接单成功');
                if($ret['code'] == 0){//接单成功
                    ClientPush::model()->pushShareForCustomer($order_id, PageConfig::TRIGGER_RECEIVE);
                }
                EdjLog::info('|Recevie order|Success|Order received|'.$driver->driver_id.'|'.$order_id);
                return $ret;
            }

            //验证是否派单弹回 BY AndyCong 2013-12-28
            $queue_lock = QueueApiOrder::model()->validate_queue_lock($queue_id);
            if ($queue_lock) {
                    EdjLog::info('|Recevie order|Failed| The queue has been locked|'.$driver->driver_id.'|'.$order_id);
                $ret = array('code' => 2 , 'message' => '因网络延迟，订单已失效。');
                return $ret;
            }

            //验证订单是否已被接单 BY AndyCong 2013-08-28
            $cache_key = 'receive_detail_'.$order_id;
            $is_dispatch = Yii::app()->cache->get($cache_key);
            if (!$is_dispatch) {
                $cache_value = $driver->driver_id;
                $set_ret = Yii::app()->cache->set($cache_key, $cache_value, 28800);
                if(!$set_ret) {
                    // Update memechace failed
                    EdjLog::info('|Recevie order|Failed| Write memcached failed|'.$driver->driver_id.'|'.$order_id);
                    $ret = array('code' => 2 , 'message' => '网络问题，接单失败');
                    return $ret;
                }
            } else {
                if ($is_dispatch != $driver->driver_id) {
                    EdjLog::info('|Recevie order|Failed|Order has been received by'.$is_dispatch.
                        '|'.$driver->driver_id.'|'.$order_id);
                    $ret = array('code' => 2 , 'message' => '订单已被派出');
                    return $ret;
                } else {
                    //如果司机接过此单 直接返回成功 2013-11-12
                    EdjLog::info('|Recevie order|Success|Order already received|'.$driver->driver_id.'|'.$order_id);
                    $driver_info = DriverStatus::model()->get($driver->driver_id);
                    $driver_info->status = 1;
                    $ret = array('code' => 0 , 'message' => '接单成功');
                    if($ret['code'] == 0){//接单成功
                        ClientPush::model()->pushShareForCustomer($order_id, PageConfig::TRIGGER_RECEIVE);
                    }

                    return $ret;
                    //如果司机接过此单 直接返回成功 2013-11-12 END
                }
            }
            //验证订单是否已被接单 BY AndyCong 2013-08-28

            //司机置成服务中
            $driver_info = DriverStatus::model()->get($driver->driver_id);
            $driver_info->status = 1;

            $params = array(
                'queue_id' => $queue_id,
                'order_id' => $order_id,
                'driver_id' => $driver->driver_id,
                'push_msg_id' => $push_msg_id,
                'gps_type' => $gps_type,
                'lng' => $lng,
                'lat' => $lat,
                'log_time' => $log_time,
            );
            $task = array(
                'class'  => __CLASS__,
                'method' => 'acceptOrderJob',
                'params' => $params,
            );

            $ret = Queue::model()->putin($task,'order');
            if(!$ret) {
                EdjLog::info('|Recevie order|Failed|Putin array failed|'.$driver->driver_id.'|'.$order_id);
            }

            EdjLog::info('|Recevie order|Success|Order received|'.$driver->driver_id.'|'.$order_id);
            $ret = array('code' => 0 , 'message' => '接单成功');
            if($ret['code'] == 0){//接单成功
                ClientPush::model()->pushShareForCustomer($order_id, PageConfig::TRIGGER_RECEIVE);
            }
        } else {
            EdjLog::info('Recevie order|Failed| Invalid token|'.$order_id);
            $ret = array('code' => 1 , 'message' => '请重新登录');
        }

        return $ret;
    }

    /*
     * QueueProcess : dal_order_received,order_receive_operate
     *
     */
    public function acceptOrderJob($params) {
        if (empty($params['queue_id']) || empty($params['order_id'])
            || empty($params['push_msg_id']) ||empty($params['gps_type'])
            || empty($params['lng'])|| empty($params['lat']) || empty($params['log_time'])) {
            return false;
        }

        $result = true;
        if(OrderDriverMatchType::checkType($params['order_id']) == OrderDriverMatchType::CHOOSE_DRIVER) {
            //TODO $this->chooseDriverAcceptOrder
            $result = DalOrder::model()->OrderReceiveFactory($params);
        } else {
            //TODO $this->automaticAcceptOrder
            $result = Push::model()->OrderReceiveOperate($params);
        }

        return $result;
    }

    private function chooseDriverAcceptOrder($params) {
    }

    private function automaticAcceptOrder($params) {
    }
}
