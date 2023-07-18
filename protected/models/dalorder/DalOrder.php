<?php

/**
 * 新版客户端接单、成单、派单
 * @author AndyCong<congming@edaijia-staff.cn
 * @version 2013-10-16
 */
class DalOrder extends DalOrderBase
{
    private static $_models;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Order the static model class
     */
    public static function model($className = __CLASS__)
    {
        $model = null;
        if (isset(self::$_models[$className]))
            $model = self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }


    /**
     * 选司机下单工厂
     * @param array $params
     * @return boolean $result
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2013-01-04
     */
    public function OrderSingleFactory($params)
    {
        //验证
        if (empty($params['phone']) || empty($params['city_id']) || empty($params['address'])
            || empty($params['lng']) || empty($params['lat']) || empty($params['driver_id'])
        ) {
            return false;
        }

        //生成unique_id
        $unique_queue_id = Tools::getUniqId('nomal');
        $unique_order_id = Tools::getUniqId('nomal');
        $unique_map_id = Tools::getUniqId('nomal');

        //获取司机信息
        $driver = DriverStatus::model()->get($params['driver_id']);
//    	$driver->client_id = '8230ef06a1db6723cde4b71d29829b29';
        $client_id = $driver->client_id;
        $driver_phone = $driver->phone;
        $driver_name = $driver->info['name'];

        //组装订单信息写入cache
        $queue = $this->orgQueueData($params, OrderQueue::QUEUE_SUCCESS);
        $order = $this->orgOrderData($params, $unique_order_id, $unique_queue_id, $queue['callid']);

        //增加选定优惠券功能
        $is_use_bonus = isset($params['is_use_bonus']) ? intval($params['is_use_bonus']) : 1;
        $bonus_sn = isset($params['bonus_sn']) ? $params['bonus_sn'] : '';
        $order['bonus_sn'] = $bonus_sn;
        $order['is_use_bonus'] = $is_use_bonus;
        //增加选定优惠券功能 END

        $map = $this->orgMapData($params, $unique_queue_id, $unique_order_id);
        $order_data = array(
            'queue' => array('key' => $unique_queue_id, 'data' => $queue),
            'order' => array('key' => $unique_order_id, 'data' => $order),
            'map' => array('key' => $unique_map_id, 'data' => $map),
        );
        ROrder::model()->OrderAddCache($order_data);

        //兼容线上版本的数据缓存
        $bonus_value = '';
        if (!empty($bonus_sn) && $is_use_bonus) { //判定是否有选定优惠券
            $bonus_value = $bonus_sn;
        }
        $old_cache_data = $this->orgOldCacheData($params,
            $unique_queue_id, $unique_order_id, $bonus_value,
            $driver_name, $queue['created']);
        $key = $params['phone'] . '_' . $queue['callid'];
        ROrder::model()->insert($key, $old_cache_data);

        // 为了更新订单状态机 记录所选择的司机 driver_id by wangjian 2014-03-28
        // 2014-03-28 BEGIN
        $order_data['real_driver_id'] = $params['driver_id'];
        // 2014-03-28 END
        //FIXED: 为支持计算并存储客户与司机距离，加入客户(lat,lng)信息 2014-12-16
        $order_data['lat'] = $params['lat'];
        $order_data['lng'] = $params['lng'];

        //将数据异步插入数据库
        $task = array(
            'method' => 'dump_insert_order',
            'params' => $order_data,
        );
        Queue::model()->putin($task, 'dalorder');
//        QueueProcess::model()->dump_insert_order($order_data); 

        //组装推送消息体
        $push_msg_id = Tools::getUniqId('nomal');
        $queue['queue_id'] = $unique_queue_id;
        $queue['order_id'] = $unique_order_id;

        $queue['cost_type'] = 0;
        $queue['card'] = '';
        $queue['balance'] = 0;
        $queue['user_money'] = 0;
        $queue['bonus'] = '';

        if (in_array($order['source'], Order::$washcar_sources)) {
            //一口价(洗车)暂时不支持
        } else {
            if (!empty($bonus_sn) && $is_use_bonus) { //判定是否有选定优惠券
                $bonus = BonusLibrary::model()->getBonusInfo($bonus_sn,$params['phone']);
                if (!empty($bonus)) {
                    print_r($bonus);
                    $money = isset($bonus['balance']) ? $bonus['balance'] : '';
                    $card = isset($bonus['bonus_sn']) ? $bonus['bonus_sn'] : '';

                    echo "\n single|order_number:" . $unique_order_id . "|phone:" . $params['phone'] . "|booking_time:" . $params['booking_time'] . "|bonus_sn:" . $bonus_sn . "|money:" . $money . "|card:" . $card . " \n";

                    $queue['bonus'] = ' 优惠金额：' . $money . '元';
                    $queue['card'] = $card;
                    $queue['balance'] = $money;
                    $queue['cost_type'] = '2';
                }

                //个人帐户金额
                $user_info = NormalAccountService::getUserAmount($params['phone']);
                if ($user_info['amount'] > 0) {
                    $user_money = $user_info['amount'];
                    $queue['user_money'] = $user_money;
                    if ($queue['cost_type'] == 2) {
                        $queue['bonus'] = ' 优惠金额：' . $money . '元,个人帐户余额' . $user_money . '元,不足部分请收取现金';
                        $queue['balance'] = $money + $user_money;
                        $queue['cost_type'] = 4;     //既有优惠劵，个人帐户里面又有钱
                    } else {
                        $queue['bonus'] = ' 个人帐户余额：' . $user_money . '元,不足部分请收取现金';
                        $queue['balance'] = $user_money;
                        $queue['cost_type'] = 8;     //个人帐户里面有钱
                    }
                }

            } else {

                $vipInfo = VipPhone::model()->getVipInfoByPhone($params['phone'], true);
                if (!empty($vipInfo)) {
                    $queue['vipcard'] = '余额：' . $vipInfo['total_balance'] . '元,不足部分请收取现金';
                    $queue['card'] = $vipInfo['vipcard'];
                    $queue['balance'] = ($vipInfo['total_balance'] > 0) ? $vipInfo['total_balance'] : 0;
                    $queue['cost_type'] = '1';
                } else {

                    if ($is_use_bonus) {  //可以使用优惠券
                        $bonus_use_limit = isset($params['bonus_use_limit']) ? intval($params['bonus_use_limit']) : 0;
                        $app_ver = isset($params['app_ver']) ? intval($params['app_ver']) : 0;
                        $bonus = BonusLibrary::model()->getBonus_sn($params['phone'], $order['source'], $bonus_use_limit, $app_ver);
                        if (!empty($bonus)) {  //优惠劵
                            $money = isset($bonus['money']) ? $bonus['money'] : '';
                            $card = isset($bonus['card']) ? $bonus['card'] : '';
                            $queue['bonus'] = ' 优惠金额：' . $money . '元';
                            $queue['card'] = $card;
                            $queue['balance'] = $money;
                            $queue['cost_type'] = '2';
                        }

                    }

                    //个人帐户金额
                    $user_info = NormalAccountService::getUserAmount($params['phone']);
                    BCustomers::$db = Yii::app()->db;
                    if ($user_info['amount'] > 0) {
                        $user_money = $user_info['amount'];
                        $queue['user_money'] = $user_money;
                        if ($queue['cost_type'] == 2) {
                            $queue['bonus'] = ' 优惠金额：' . $money . '元,个人帐户余额' . $user_money . '元,不足部分请收取现金';
                            $queue['balance'] = $money + $user_money;
                            $queue['cost_type'] = 4;     //既有优惠劵，个人帐户里面又有钱
                        } else {
                            $queue['bonus'] = ' 个人帐户余额：' . $user_money . '元,不足部分请收取现金';
                            $queue['balance'] = $user_money;
                            $queue['cost_type'] = 8;     //个人帐户里面有钱
                        }
                    }
                }
            }
        }

        $message = PushMsgFactory::model()->orgPushMsg($queue, PushMsgFactory::TYPE_ORDER_DETAIL, $push_msg_id);

        //记录messagelog
        $msg_arr = array(
            'client_id' => $client_id,
            'queue_id' => $unique_queue_id,
            'type' => PushMsgFactory::TYPE_ORDER_DETAIL,
            'content' => $message['content'],
            'driver_id' => isset($params['driver_id']) ? $params['driver_id'] : '',
        );

        if (!empty($message['push_distinct_id'])) {
            ROrder::model()->insertMessage($message['push_distinct_id'],
                array_merge($msg_arr, $message));  //加入缓存
            //为了重试记录信息
            $push_retry_info = array(
                'distinct_id' => $message['push_distinct_id'],
                'driver_id' => $params['driver_id'],
                'driver_phone' => $driver_phone,
                'client_id' => $client_id,
            );
            ROrder::model()->single_push_retry_record($unique_order_id,
                $push_retry_info);
        }

        //推送(需要放入队列,有问题能直接定位)
        $task = array(
            'method' => 'push_order',
            'params' => array(
                'version' => 'driver',
                'driver_id' => isset($params['driver_id']) ? $params['driver_id'] : '',
                'client_id' => $client_id,
                'message' => $message,
                'driver_phone' => $driver_phone,
            ),
        );
        Queue::model()->putin($task, 'pushmsg');
//    	QueueProcess::model()->push_order(array('version' => 'driver' , 'client_id' => $client_id , 'message' => $message));

        $tmp = array(
            'key' => $push_msg_id,
            'data' => $msg_arr,
        );
        $task = array(
            'method' => 'dump_insert_messagelog',
            'params' => $tmp,
        );
        Queue::model()->putin($task, 'dalmessage');           //写入DB
//        QueueProcess::model()->dump_insert_messagelog($tmp);

        //返回并输出结果
        return true;
    }

    /**
     * 选司机短信方式Push重试
     * @return boolean
     * @author wangjian<wangjian@edaijia-staff.cn>
     * @version 2014-06-18
     */
    public function single_smspush_retry($params, $city = '')
    {
        if (empty($params['call_time'])
            || empty($params['order_number'])
            || empty($params['order_id'])
        ) {
            return false;
        }

        $retry_info = ROrder::model()->single_push_can_retry($params['order_number']);
        if (!empty($retry_info)) {
            $retry_info = @json_decode($retry_info, true);

            if (!empty($retry_info['distinct_id'])
                && !empty($retry_info['driver_id'])
                && !empty($retry_info['driver_phone'])
                && !empty($retry_info['client_id'])
            ) {

                //如果通过driver.log.report上传过distinct_id,
                //证明已经收到push,redis中message被清除,不进行二次推送
                if (!ROrder::model()->existsMessage($retry_info['distinct_id'])) {
                    return true;
                }

                //城市灰度发布
                $city_id = DriverStatus::model()->getItem($retry_info['driver_id'], 'city_id');
                $city_prefix = Dict::item("city_prefix", $city_id);
                if (!empty($city) && $city_prefix != $city) {
                    return true;
                }

                $app_ver = DriverStatus::model()->app_client_ver($retry_info['client_id']);
                if (!empty($app_ver)
                    && !empty(Yii::app()->params['SmsPushLimitedVersion'])
                    && Helper::compareVersion($app_ver, Yii::app()->params['SmsPushLimitedVersion'])
                    && !in_array($app_ver, array('2.3.0', '2.3.1'))
                ) {

                    if (EPush::sms_push($retry_info['distinct_id'], $retry_info['driver_phone'])) {
                        EdjLog::info('SmsPushLog_single_retry|'
                            . $retry_info['distinct_id'] . '|' . $params['order_id']
                            . '|' . $retry_info['driver_phone']);
                    }
                }
            }
        }
    }

    /**
     * 一键下单工厂方法
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-04
     */
    public function OrderMuiltFactory($params)
    {
        return true;
    }

    /**
     * 呼叫中心派单工厂方法
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-04
     */
    public function CallcenterFactory($params)
    {
        return true;
    }

    /**
     * 司机接单处理
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-05
     */
    public function OrderReceiveFactory($params)
    {
        if (empty($params['queue_id']) || empty($params['order_id']) || empty($params['push_msg_id']) || empty($params['driver_id'])
            || empty($params['gps_type']) || empty($params['lng']) || empty($params['lat']) || empty($params['log_time'])
        ) {
            return false;
        }

        //获取司机信息
        $driver = DriverStatus::model()->get($params['driver_id']);

        //更新Order缓存
        $driver_info = array(
            'driver_id' => $params['driver_id'],
            'driver' => $driver->info['name'],
            'driver_phone' => $driver->phone,
            'imei' => $driver->info['imei'],
        );
        ROrder::model()->updateOrder($params['order_id'], $driver_info);

        //更新老版本缓存数据
        $this->updateOldCacheData($params['queue_id'], $params['order_id'], $driver_info);

        //更新订单信息到数据库
        $order_data = array(
            'order_info' => $params,
            'driver_info' => $driver_info,
        );
        $task = array(
            'method' => 'dump_update_order',
            'params' => $order_data,
        );
        Queue::model()->putin($task, 'dalorder');
//        QueueProcess::model()->dump_update_order($order_data);
        $orderId = $params['order_id'];
        EdjLog::info("Driver invoke create order to queue,order id:$orderId");
        ROrderToKafka::model()->createOrderAddQueue($orderId);
        return true;
    }

    /**
     * 组装订单缓存数据,客户端用到
     * @param array $params
     * @param int $unique_queue_id
     * @param int $unique_order_id
     * @return array $data
     */
    public function orgOldCacheData($params, $unique_queue_id, $unique_order_id,
                                    $bonus_sn = '', $driver_name = '', $created_time = '')
    {
        $orders = array();
        $orders[$unique_order_id] = array(
            'order_id' => $unique_order_id,
            'driver_id' => self::DEFAULT_DRIVER_INFO,
            'driver_phone' => self::DEFAULT_DRIVER_INFO,
            'driver_name' => self::DEFAULT_DRIVER_INFO,
            'location_start' => isset($params['address']) ? $params['address'] : '',
            'location_end' => '',
            'pay_status' => 0, //0 未支付, 1 已支付
            'cash_only' => 0, //1 是，0 否
            'status' => Order::ORDER_READY,
            'order_state' => OrderProcess::ORDER_PROCESS_NEW,
            'order_states' => array(
                array(
                    'order_state_code' => OrderProcess::ORDER_PROCESS_NEW,
                    'order_state_timestamp' => time(),
                    'order_state_content' => '正在联络司机',
                ),
            ),
            'role' => isset($params['role']) ? trim($params['role']) : '组员',
            'bonus_sn' => $bonus_sn,
            'select_driver_id' => $params['driver_id'],
            'select_driver_name' => $driver_name,
        );

        $data = array(
            'queue_id' => $unique_queue_id,
            'booking_id' => isset($params['callid']) ? $params['callid'] : '',
            'booking_type' => isset($params['channel']) ? $params['channel'] : self::QUEUE_CHANNEL_SINGLE_DRIVER,
            'booking_time' => isset($params['booking_time']) ? $params['booking_time'] : '',
            'created_time' => $created_time,
            'phone' => isset($params['phone']) ? $params['phone'] : '',
            'contact_phone' => isset($params['contact_phone']) ? $params['contact_phone'] : '',
            'city_id' => $params['city_id'],
            'number' => isset($params['number']) ? intval($params['number']) : 1,
            'address' => isset($params['address']) ? $params['address'] : '',
            'lng' => isset($params['lng']) ? $params['lng'] : '0.000000',
            'lat' => isset($params['lat']) ? $params['lat'] : '0.000000',
            'google_lng' => isset($params['google_lng']) ? $params['google_lng'] : '0.000000',
            'google_lat' => isset($params['google_lat']) ? $params['google_lat'] : '0.000000',
            'flag' => OrderQueue::QUEUE_WAIT_COMFIRM,
            'ready_time' => 0,
            'orders' => $orders,
        );
        return $data;
    }

    /**
     * 更新老版本数据缓存
     * @param int $queue_id
     * @param array $driver
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-05
     */
    public function updateOldCacheData($unique_queue_id, $unique_order_id, $driver, $order_state = OrderProcess::ORDER_PROCESS_ACCEPT, $cancel_type = '', $location_end = '')
    {
        if (empty($unique_queue_id)) {
            $unique_queue_id = ROrder::model()->getOrder($unique_order_id, 'unique_queue_id');
        }
        $callid = ROrder::model()->getQueue($unique_queue_id, 'callid');
        $phone = ROrder::model()->getQueue($unique_queue_id, 'phone');
        $city = ROrder::model()->getQueue($unique_queue_id, 'city_id');
        $key = $phone . '_' . $callid;
        if ($order_state == OrderProcess::ORDER_PROCESS_DRIVER_CANCEL) {
            ROrder::model()->update($key, 'flag', OrderQueue::QUEUE_CANCEL);
        }
        $orders = ROrder::model()->get($key, 'orders');
        $order_states = isset($orders[$unique_order_id]['order_states']) ? $orders[$unique_order_id]['order_states'] : array();
        if (empty($orders)) {
            return false;
        }

        $last_order_state = $orders[$unique_order_id]['order_state'];
        $validate = OrderProcess::model()->validOrderState(
            $last_order_state, $order_state);
        if (!$validate) {
            EdjLog::info("Invalid order state|last state:" .
                $last_order_state . '|Current state:' . $order_state);
            return false;
        }

        // 如果司机已就位之后取消订单，记录一下——曾坤 2015/3/17
        if ($order_state == OrderProcess::ORDER_PROCESS_DRIVER_DESTORY
            && $last_order_state == OrderProcess::ORDER_PROCESS_READY
        ) {
            CustomerApiOrder::model()->recordMaliciousCancelling(
                'driver',
                $orders[$unique_order_id]['driver_id'],
                $phone,
                $city
            );
        }

        // 当司机接单之后，判断此订单是否需要提示恶劣天气加价的短信
        // 但是VIP客户是不能发送短信的——曾坤 2015/3/25
        if ($order_state == OrderProcess::ORDER_PROCESS_ACCEPT) {
            $order_id = ROrder::model()->getOrder($unique_order_id, 'order_id');
            $orderext = OrderExt::model()->findByPk($order_id);
            if (!empty($orderext)
                && isset($orderext['bad_weather_surcharge'])
                && intval($orderext['bad_weather_surcharge']) > 0
            ) {
                static $ENABLED = 0;
                if (empty((VipPhone::model()->getVipByphone($phone)))) {
                    $bad_weather_surcharge = WeatherRaisePrice::model()->findByPk($orderext['bad_weather_surcharge']);
                    if (!empty($bad_weather_surcharge) && $bad_weather_surcharge['status'] == $ENABLED) {
                        CustomerApiOrder::model()->sendBadWeatherSmsNotify(
                            $phone,
                            $bad_weather_surcharge['offer_message']
                        );
                    }
                }
            }
        }

        switch ($order_state) {
            case OrderProcess::ORDER_PROCESS_ACCEPT:
                $order_states = array();
                $order_states[] = array(
                    'order_state_code' => $order_state,
                    'order_state_timestamp' => time(),
                    'order_state_content' => '司机已接单',
                );
                break;
            case OrderProcess::ORDER_PROCESS_READY:
                $arr_tmp = array();
                $arr_tmp[] = array(
                    'order_state_code' => $order_state,
                    'order_state_timestamp' => time(),
                    'order_state_content' => '司机已到达 等候0分',
                );
                if (!empty($order_states) && count($order_states) == 1) {
                    foreach ($order_states as $state) {
                        array_push($arr_tmp, $state);
                    }
                    $order_states = $arr_tmp;
                }
                unset($arr_tmp);
                $orders[$unique_order_id]['ready_time'] = date('Y-m-d H:i:s', time());
                break;
            case OrderProcess::ORDER_PROCESS_DRIVING:
                $time = time();
                $arr_tmp = array();
                $arr_tmp[] = array(
                    'order_state_code' => $order_state,
                    'order_state_timestamp' => $time,
                    'order_state_content' => '开车行驶',
                );
                if (!empty($order_states) && (count($order_states) == 2 || count($order_states) == 1)) {
                    foreach ($order_states as $state) {
                        array_push($arr_tmp, $state);
                    }
                    $order_states = $arr_tmp;
                }
                unset($arr_tmp);
                break;
            case OrderProcess::PROCESS_DEST:
                $time = time();
                $arr_tmp = array();
                $arr_tmp[] = array(
                    'order_state_code' => $order_state,
                    'order_state_timestamp' => $time,
                    'order_state_content' => '到达目的地',
                );
                if (!empty($order_states) &&
                    (count($order_states) == 3 ||
                        count($order_states) == 2 ||
                        count($order_states) == 1)
                ) {
                    foreach ($order_states as $state) {
                        array_push($arr_tmp, $state);
                    }
                    $order_states = $arr_tmp;
                }
                unset($arr_tmp);
                break;
            default:
                break;
        }
        $orders[$unique_order_id]['order_state'] = $order_state;
        $orders[$unique_order_id]['order_states'] = $order_states;
        if (!empty($driver) && isset($driver['driver_phone'])) {
            $orders[$unique_order_id]['driver_id'] = $driver['driver_id'];
            $orders[$unique_order_id]['driver_phone'] = $driver['driver_phone'];
            $orders[$unique_order_id]['driver_name'] = $driver['driver'];
        }

        if (!empty($cancel_type)) {
            $orders[$unique_order_id]['cancel_type'] = $cancel_type;
        }

        if (!empty($location_end)) {
            $orders[$unique_order_id]['location_end'] = $location_end;
        }

        $result = ROrder::model()->update($key, 'orders', $orders);

        // Send message to customer when the order is accepted
        if (!empty($driver) && !empty($driver['driver_id'])) {
            switch ($order_state) {
                case OrderProcess::ORDER_PROCESS_ACCEPT:
                    EdjLog::Info($callid . '|' . $unique_order_id . '|' . $phone . '|' . $driver['driver_id'] . '|Push accept msg|', 'console');
                    ClientPush::model()->pushMsgForDriverAcceptOrder($phone,
                        $driver['driver_id'], $callid, $unique_order_id, $driver['driver']);
                    break;
                case OrderProcess::ORDER_PROCESS_DRIVER_CANCEL:
                    EdjLog::Info($callid . '|' . $unique_order_id . '|' . $phone . '|' . $driver['driver_id'] . '|Push reject msg|', 'console');
                    ClientPush::model()->pushMsgForDriverRejectOrder($phone,
                        $driver['driver_id'], $callid, $unique_order_id, $driver['driver']);
                    break;
                case OrderProcess::ORDER_PROCESS_DRIVER_DESTORY:
                    EdjLog::Info($callid . '|' . $unique_order_id . '|' . $phone . '|' . $driver['driver_id'] . '|Push driver cancel msg|', 'console');
                    ClientPush::model()->pushMsgForDriverCancelOrder($phone,
                        $driver['driver_id'], $callid, $unique_order_id, $driver['driver'], $cancel_type);
                    break;
                //case OrderProcess::ORDER_PROCESS_READY:
                //   EdjLog::Info($callid.'|'.$unique_order_id.'|'.$phone.'|'.$driver['driver_id'].'|Push driver ready msg|' , 'console');
                //  ClientPush::model()->orgPushMsgForDriverReachOrder($phone,
                //	    $driver['driver_id'], $callid, $unique_order_id, $driver['driver']);
                //   break;
                default:
                    break;
            }
        }

        if ($result) {
            echo "\n update orders success \n";
        } else {
            echo "\n update orders failed \n";
        }
        return $result;
    }

    /**
     * 司机上报订单状态及位置
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-06
     */
    public function uploadOrderPosition($params)
    {
        //return true;
        //更新cache(需要有queue_id , 看王栋那可不可以传过来)
        $flag_arr = array(
            OrderPosition::FLAG_ARRIVE,
            OrderPosition::FLAG_START,
            OrderPosition::FLAG_FINISH,
            OrderPosition::FLAG_SUBMIT,
        );
        if (in_array($params['flag'], $flag_arr)) {
            switch ($params['flag']) {
                case OrderPosition::FLAG_ARRIVE:
                    $order_state = OrderProcess::ORDER_PROCESS_READY;
                    break;
                case OrderPosition::FLAG_START:
                    $order_state = OrderProcess::ORDER_PROCESS_DRIVING;
                    break;
                case OrderPosition::FLAG_FINISH:
                    $order_state = OrderProcess::PROCESS_DEST;
                    break;
                case OrderPosition::FLAG_SUBMIT:
                    $order_state = OrderProcess::ORDER_PROCESS_FINISH;
                    break;
            }
            //这个可以让王栋传过来 ，先读出来
            $driver_info = '';
            if (isset($params['driver_id'])) {
                $driver = DriverStatus::model()->get($params['driver_id']);
                $driver_info = array(
                    'driver_id' => $params['driver_id'],
                    'driver' => $driver->info['name'],
                );
            }

            $unique_queue_id = ROrder::model()->getOrder($params['order_id'], 'unique_queue_id');
            if (!empty($unique_queue_id)) {
                $this->updateOldCacheData($unique_queue_id, $params['order_id'], $driver_info, $order_state);
            }
        }

        //将位置信息记录到数据库(扔到dalorder队列执行)
        $task = array(
            'method' => 'insert_order_position',
            'params' => $params,
        );
        Queue::model()->putin($task, 'dalorder');
//    	QueueProcess::model()->insert_order_position($params);
        return true;
    }

    /**
     * 取消订单
     * @param array $params
     * @return boolean
     * @author AndyCong<congming@edaijia-staff.cn>
     * @version 2014-01-06
     */
    public function cancelOrder($params)
    {
        //更新缓存
        $unique_queue_id = ROrder::model()->getOrder($params['order_id'], 'unique_queue_id');
        if (!empty($unique_queue_id)) {
            //增加消单原因 BY AndyCong 2014-01-23
            $params['cancel_type'] = isset($params['cancel_type']) ? $params['cancel_type'] : '';
            $params['cancel_type'] = Common::convertCancelType($params['cancel_type']);
            $cancel_type = $params['cancel_type'] == 0 ? '' : Dict::item('qx_o_type', $params['cancel_type']);
            // cancel_type 可能是投诉类型
            if (!$cancel_type) {
                $cancel_type = $params['cancel_type'] == 0 ? '' : Dict::item('ts_o_type', $params['cancel_type']);
            }

            // Get driver info
            $driver = DriverStatus::model()->get($params['driver_id']);
            $driver_info = array(
                'driver_id' => $params['driver_id'],
                'driver' => $driver->info['name'],
            );

            $this->updateOldCacheData($unique_queue_id, $params['order_id'], $driver_info, OrderProcess::ORDER_PROCESS_DRIVER_DESTORY, $cancel_type);
        }

        //dump到数据库
        $task = array(
            'method' => 'dump_cancel_order',
            'params' => $params,
        );
        Queue::model()->putin($task, 'dalorder');
//        QueueProcess::model()->dump_cancel_order($params);
        return true;
    }

    public function driverRejectLog($params)
    {
        if (empty($params['queue_id']) || empty($params['order_id']) || empty($params['driver_id'])
            || empty($params['type']) || empty($params['created'])
        ) {
            return false;
        }


        // Get driver info
        $driver = DriverStatus::model()->get($params['driver_id']);
        $driver_info = array(
            'driver_id' => $params['driver_id'],
            'driver' => $driver->info['name'],
        );

        //更新缓存
        $this->updateOldCacheData($params['queue_id'], $params['order_id'], $driver_info, OrderProcess::ORDER_PROCESS_DRIVER_CANCEL);

        // 更新订单状态机 by wangjian 2014-03-29 TODO
        // 司机拒单 状态405
        // 2014-03-29 BEGIN
        // 获取order_id 和 queue_id
        $real_queue_id = ROrder::model()->getQueue($params['queue_id'], 'queue_id');
        if (empty($real_queue_id)) {
            EdjLog::warning($params['order_id'] . '|405司机拒单|' . $params['driver_id'] . '|状态机更新获取real_queue_id失败|end', 'console');
            $real_queue_id = $params['queue_id'];
        }
        $real_order_id = ROrder::model()->getOrder($params['order_id'], 'order_id');
        if (empty($real_order_id)) {
            EdjLog::warning($params['order_id'] . '|405司机拒单|' . $params['driver_id'] . '|状态机更新获取real_order_id失败|end', 'console');
            $real_order_id = $params['order_id'];
        }

        $order_process_state = 0;
        if ($params['type'] == 2 || $params['type'] == 3
            || $params['type'] == 4 || $params['type'] == 5
            || $params['type'] == 7
        ) {
            $order_process_state = OrderProcess::PROCESS_DISPATCH_FAIL_DRIVER_RELATED;
        } else {
            $order_process_state = OrderProcess::PROCESS_DISPATCH_FAIL_SYS_RELATED;
        }
        OrderProcess::model()->genNewOrderProcess(
            array('queue_id' => $real_queue_id,
                'order_id' => $real_order_id,
                'driver_id' => $params['driver_id'],
                'state' => $order_process_state,
                'fail_type' => $params['type'],
                'created' => date('Y-m-d H:i:s', time()),
            )
        );
        // 2014-03-29 END

        $task = array(
            'method' => 'driver_reject_order_process',
            'params' => $params,
        );
        Queue::model()->putin($task, 'dalorder');
//        QueueProcess::model()->driver_reject_order_process($params);
        return true;
    }
}

?>
