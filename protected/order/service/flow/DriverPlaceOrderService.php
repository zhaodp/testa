<?php

/**
 * 司机自助下单
 * 包含电话订单,开启新订单,司机补单
 *
 */
class DriverPlaceOrderService extends BaseFlowService
{
    private static $instance;

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new DriverPlaceOrderService();
        }
        return self::$instance;
    }

    /**
     * 司机接打电话生成订单
     * API driver.order.callorder
     */
    public function placeOrderByPhone($params)
    {
        //接收参数
        $time = time();
        $phone = isset($params['phone']) ? $params['phone'] : '';
        $order_number = isset($params['order_number']) ? $params['order_number'] : '';
        $token = isset($params['token']) ? $params['token'] : '';
        $booking_time = isset($params['booking_time']) ? strtotime($params['booking_time']) : ($time + 1200);
        $call_type = isset($params['call_type']) ? $params['call_type'] : '';
        $lng = isset($params['lng']) ? $params['lng'] : '';
        $lat = isset($params['lat']) ? $params['lat'] : '';
        $gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'baidu';

        //验证预约时间和当前时间不能相隔半小时以上
        if (abs($booking_time - $time) >= 3600) {
            $booking_time = $time;
        }

        //验证参数
        if (empty($phone) || empty($order_number) || empty($token)) {
            $ret = array('code' => 2, 'message' => '参数验证失败');
            return $ret;
        }

        //验证token
        $driver = DriverStatus::model()->getByToken($token);
        if (empty($driver) || $driver->token === null || $driver->token !== $token) {
            $ret = array('code' => 1, 'message' => 'token失效');
            return $ret;
        }

        if (!empty($driver)) {
            //如果司机端没有上传坐标,使用司机上次上传坐标
            if (empty($lng) || empty($lat)) {
                $lng = isset($driver->position['longitude'])
                    ? $driver->position['longitude'] : 0;
                $lat = isset($driver->position['latitude'])
                    ? $driver->position['latitude'] : 0;
            }
            $gps_location = array(
                'longitude' => $lng,
                'latitude' => $lat,
            );
            $gps = GPS::model()->convert($gps_location, $gps_type);
            $city_id = $driver->city_id;
            if (empty($gps['street'])) {
                $gps['street'] = Dict::item('city', $city_id);
            }

            //组织参数
            $data = array(
                'phone' => $phone,
                'driver_id' => $driver->driver_id,
                'order_number' => $order_number,
                'booking_time' => $booking_time,
                'call_type' => $call_type,
                'longitude' => $lng,
                'latitude' => $lat,
                'address' => $gps['street'],
                'city_id' => $city_id,
            );

            $task = array(
                'class' => __CLASS__,
                'method' => 'placeOrderByPhoneJob',
                'params' => $data,
            );

            Queue::model()->putin($task, 'order');
            $ret = array('code' => 0, 'message' => '操作成功');

        } else {
            $ret = array('code' => 1, 'message' => '验证失效,请重新登录');
        }
        return $ret;
    }

    /**
     * 司机接打电话生成订单异步处理
     * QueueProcess call_order
     */
    public function placeOrderByPhoneJob($params)
    {
        //验证订单存在否
        $order = Order::model()->find('order_number = :order_number', array(':order_number' => $params['order_number']));
        if ($order) {
            return false;
        }
        $time = time();
        $data = array(
            'driver_id' => $params['driver_id'],
            'phone' => $params['phone'],
            'city_id' => $params['city_id'],
            'name' => '先生',
            'call_time' => $params['booking_time'],  //呼叫时间改成预约时间前20分钟
            'booking_time' => $params['booking_time'],
            'address' => $params['address'],
            'lng' => $params['longitude'],
            'lat' => $params['latitude'],
            'order_number' => $params['order_number'],
            'call_type' => isset($params['call_type']) ? $params['call_type'] : '',
        );
        $ret = AutoOrder::model()->call_order($data);
        if (!$ret || $ret['code'] == 2) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 司机手动开启新订单
     * API driver.order.booking
     */
    public function placeOrderManually($params)
    {
        //验证参数信息
        if (empty($params['lat']) || empty($params['lng']) || empty($params['phone']) || empty($params['token']) || empty($params['order_number'])) {
            $ret = array(
                'code' => 2,
                'message' => '信息有误，请仔细检查');

            return $ret;
        }

        $gps_type = isset($params['gps_type']) ? $params['gps_type'] : 'wgs84';
        $source = isset($params['source']) ? $params['source'] : Order::SOURCE_CLIENT_INPUT;
        //验证token
        $token = isset($params['token']) ? trim($params['token']) : '';
        //验证token
        $driver = DriverStatus::model()->getByToken($token);
        if (empty($driver) || $driver->token === null || $driver->token !== $token) {
            $ret = array('code' => 1, 'message' => 'token失效');

            return $ret;
        }
        //需要优化，增加缓存。add by sunhongjing
        $driver_id = DriverToken::model()->getDriverIdByToken($token);
        if (!empty($driver_id)) {
            //司机端答题支持
            if($driver->status != DriverStatus::STATUS_DRIVING){
                $ret = DriverQuizLogic::getInstance()->driverNeedQuiz($driver);
                if($ret){
                    return $ret;
                }
            }
            //通过gps反推城市及地址
            $gps_location = array(
                'longitude' => $params['lng'],
                'latitude' => $params['lat'],
            );
            $gps = GPS::model()->convert($gps_location, $gps_type);

            $driver = DriverStatus::model()->get($driver_id);
            $city_id = $driver->city_id;
            $params['city_id'] = $city_id;
            if (isset($gps['street']) && !empty($gps['street'])) {
                $params['address'] = $gps['street'];
            } else {
                $params['address'] = Dict::item('city', $city_id);//这里是什么意思
            }

            $params['driver_id'] = $driver_id;
            //通过gps反推城市及地址 end
            if (empty($params['name'])) {
                $params['name'] = '先生';
            }
            //处理订单(放入队列)
            //	$ret = OrderQueue::model()->booking($params , Order::SOURCE_CLIENT_INPUT , OrderQueue::QUEUE_AGENT_DRIVERBOOKING);

            $task = array(
                'method' => 'driver_supplement_order',
                'params' => $params,
            );
            Queue::model()->putin($task, 'order');

            //处理订单 END
        } else {
            $ret = array(
                'code' => 1,
                'message' => '验证失败'
            );

            return $ret;
        }

        //返回数据信息
        $ret = array('code' => 0, 'message' => '下单成功');
        return $ret;

    }

    /**
     * 司机手动开启新订单异步处理
     * QueueProcess driver_supplement_order
     */
    public function placeOrderManuallyJob($params)
    {
        $result = OrderQueue::model()->booking($params,
            isset($params['source']) ? $params['source'] : Order::SOURCE_CLIENT_INPUT,
            OrderQueue::QUEUE_AGENT_DRIVERBOOKING);
        return $result;
    }

    /**
     * 司机补单
     * API driver.order.create
     */
    public function replenishOrder($params)
    {
        $driver = DriverStatus::model()->getByToken($params['token']);
        if (empty($driver) || $driver->token === null || $driver->token !== $params['token']) {
            $ret = array(
                'code' => 1,
                'message' => '请重新登录'
            );

            return $ret;
        }

        if (empty($params['call_time']) && empty($params['booking_time']) && empty($params['phone']) && !preg_match('%^[0-9]*$%', $params['phone'])) {
            $ret = array(
                'code' => 2,
                'message' => '数据格式不正确');
            return $ret;
        }
        $time = time();
        //验证预约时间和当前时间不能相隔半小时以上
        if (abs(strtotime($params['booking_time']) - $time) >= 3600) {
            $params['booking_time'] = date('Y-m-d H:i:s', $time);
        }

        //增加练习电话 BY AndyCong 2013-12-20
        $contact_phone = isset($params['contact_phone']) ? trim($params['contact_phone']) : $params['phone'];

         //日间模式校验
        if (in_array($params['source'], Order::$daytime_sources)) {
            // 用当前服务器时间做校验
            $check_ret = Order::CheckSpecialOrderSource($params['source'],
                $driver->city_id, strtotime($params['call_time']));

            if (!$check_ret['flag']) {
                if ($check_ret['code'] == ApiErrorCode::ORDER_CITY_ERROR) {
                    $ret = array('code' => $check_ret['code'],
                        'data' => '', 'message' => '当前城市没有开通日间服务,请选择普通订单');
                    EdjLog::info("CheckSpecialOrderSource|" . $driver->driver_id . '|' . $params['source']
                        . '|' . $driver->city_id . '|' . $ret['code']);
                    return $ret;
                } elseif ($check_ret['code'] == ApiErrorCode::ORDER_TIME_ERROR) {
                    $ret = array('code' => $check_ret['code'],
                        'data' => '', 'message' => '订单时段为普通按里程计费方式，请选择普通订单');
                    EdjLog::info("CheckSpecialOrderSource|" . $driver->driver_id . '|' . $params['source']
                        . '|' . $driver->city_id . '|' . $ret['code']);
                    return $ret;
                } elseif ($check_ret['code'] == ApiErrorCode::ORDER_SOURCE_ERROR) {
                    $ret = array('code' => $check_ret['code'],
                        'data' => '',
                        'message' => isset($check_ret['message']) ? $check_ret['message'] : '下单失败');
                    EdjLog::info("CheckSpecialOrderSource|" . $driver->driver_id . '|' . $params['source']
                        . '|' . $driver->city_id . '|' . $ret['code']);
                    return $ret;
                }
            }
        }

        //添加task队列
        $task = array(
            'method' => 'push_order_create',
            'params' => array(
                'city_id' => $driver->city_id,
                'call_time' => strtotime($params['call_time']),
                'order_number' => isset($params['order_number']) ? $params['order_number'] : '',   //BY AndyCong 2013-08-02
                'booking_time' => strtotime($params['booking_time']),
                'phone' => $params['phone'],
                'contact_phone' => $contact_phone,
                'source' => $params['source'],
                'description' => Order::SourceToString($params['source']),
                'driver' => $driver->name,
                'driver_id' => $driver->driver_id,
                'driver_phone' => $driver->driver_phone,
                'imei' => $driver->imei,
                'created' => strtotime($params['booking_time']),
            )
        );

        Queue::model()->putin($task, 'order');

        $ret = array(
            'code' => 0,
            'message' => '成功!'
        );
        return $ret;
    }

    /**
     * 司机补单异步处理
     * QueueProcess push_order_create
     */
    public function replenishOrderJob($params)
    {
        //验证订单是否有补单成功过 BY AndyCong 2013-08-27
        $driver_id = isset($params['driver_id']) ? trim($params['driver_id']) : '';
        $order_number = isset($params['order_number']) ? trim($params['order_number']) : '';

        if (!empty($order_number)) {
            $order = Order::model()->find('order_number = :order_number and driver_id = :driver_id', array(':order_number' => $order_number, 'driver_id' => $driver_id));
            if ($order) {
                return true;
            }
        }

        //验证订单是否有补单成功过 BY AndyCong 2013-08-27 END

        $model = new Order();
        //补录订单忽略检查每小时只能一个客户订单以及司机内部电话
        $builder = $model->getCommandBuilder();
        $table = $model->getMetaData()->tableSchema;
        $command = $builder->createInsertCommand($table, $params);
        if ($command->execute()) {
            $order_id = $builder->getLastInsertID($table);

            //保存order_queue
            echo "\n order_id is " . $order_id . "\n";
            $data = array();
            $data['phone'] = $params['phone'];
            $data['city_id'] = $driver_id ? DriverStatus::model()->getItem($driver_id, 'city_id') : 0;
            $data['address'] = '暂未获取';
            $data['booking_time'] = date('Y-m-d H:i:s', $params['booking_time']);
            $data['dispatch_number'] = 1;
            $data['dispatch_time'] = date('Y-m-d H:i:s', time());
            $data['flag'] = OrderQueue::QUEUE_SUCCESS;
            $queue = CustomerApiOrder::model()->save_order_queue($data, $params['source'], '司机补单');

            if (!empty($queue)) {
                echo "\n queue_id is " . $queue['id'] . "\n";
                $confirm_time = date("Y-m-d H:i:s", time());
                $attributes = array(
                    'order_id' => $order_id,
                    'queue_id' => $queue['id'],
                    'driver_id' => $driver_id,
                    'confirm_time' => $confirm_time
                );
                @OrderQueueMap::getDbMasterConnection()->createCommand()->insert('t_order_queue_map', $attributes);

                // 记录订单状态机
                OrderProcess::model()->genNewOrderProcess(
                    array('queue_id' => $queue['id'],
                        'order_id' => $order_id,
                        'driver_id' => $driver_id,
                        'state' => OrderProcess::PROCESS_DRIVER_CREATE,
                        'created' => date("Y-m-d H:i:s", time()),
                    )
                );
            }

            CustomerApiOrder::model()->orderFavorableCache($order_number);
            //优惠劵的占用  补单不让使用优惠劵  金竹要求  11月13号
//            BonusLibrary::model()->BonusOccupancy($params['phone'], $order_id, $params['source']);
            return true;
        } else {
            return false;
        }
    }
}
