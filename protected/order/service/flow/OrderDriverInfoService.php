<?php
/**
 * 订单司机位置等信息
 *
 *
 */
class OrderDriverInfoService extends BaseFlowService {

    private static $instance;
    const QUEUE_CHANNEL_BOOKING       = '01003'; //一键预约
    const QUEUE_CHANNEL_REMOTEORDER  = '01007'; //远程叫单

    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new OrderDriverInfoService();
        }
        return self::$instance;
    }

    /**
     * 入口，API调用
     * 通过booking_id和phone获得司机的位置，订单的详情等
     */
    public function getDriverPosition($params)
    {
        $driverOrder = new DriverOrderPosition($params);

        if( empty($driverOrder->booking_id) || empty($driverOrder->driver_id) ){
            $ret = array('code' => 2 , 'data' => '' , 'message' => '参数有误');
            return  $ret;
        }
//验证token
//需要修改 ok
        $validate = CustomerService::getInstance()->validate($driverOrder->token);
        if (!$validate) {
            $ret = array('code' => 1 , 'data' => '' , 'message' => '验证失败');
            return($ret);
        }

//获取订单详情
        $driver = $this->getDriverPositionByBookingId($driverOrder, $validate['phone']);
        if (empty($driver)) {
            $ret = array('code' => 2 , 'data' => '' , 'message' => '订单已超时');
            return $ret;
        }


        $data = array(
            'driver' => $driver,
            'next' => 10,
            'polling_count' => isset($params['polling_count']) ? $params['polling_count'] : 1,
            'timeout' => 600,
        );

//H5端反应状态码与司机端不一致，我们初步判断是H5端的polling终止了。
//在这里加上一条log验证我们的想法——曾坤  2015/3/16
        EdjLog::info("edaijia-h5: ".$driverOrder->order_id." ".$driverOrder->booking_id." ".
            $driver['order_state_code']);

        $ret = array('code' => 0 , 'data' => $data , 'message' => '成功',);
        return $ret;
    }

    /**
     * 获取司机司机位置信息
     * @param string $driver_id
     * @param string $gps_type
     * @param string $phone
     * @param string $booking_id
     * @return array
     */
    public function getDriverPositionByBookingId($driverOrder ,$phone) {
        if (empty($driverOrder->driver_id) || empty($driverOrder->gps_type)
            || empty($phone) || empty($driverOrder->booking_id)) {
            return array();
        }


        //注意啦，这个是从redis中获得订单数据，属于DAO层
        //需要修改  ok
        $cache = CustomerViewDataLogic::getInstance()->getAllFields($phone,$driverOrder->booking_id);
        if (empty($cache)) {
            EdjLog::info( 'empty $cache');
            return array();
        }
        //得到司机的基本信息
        $driver = Helper::foramt_driver_detail($driverOrder->driver_id , $driverOrder->gps_type, 0, 'default');
        if (empty($driver)) {
            EdjLog::info( 'empty $driver');
            return array();
        }

        $orders = isset($cache['orders']) ? json_decode($cache['orders'] , true) : '';
        if (empty($orders)) {
            EdjLog::info( 'empty orders');
            return array();
        }
        //默认角色通过预约人数判定 END
        $driverOrder->booking_type = isset($cache['booking_type'])?
            $cache['booking_type']:self::QUEUE_CHANNEL_BOOKING;

        $driver  = $this->initOrderList($orders,$driver,$driverOrder,$cache);


        $driver['source'] = isset($cache['source']) ? $cache['source'] : Order::SOURCE_CLIENT;
        $driver['customer_lng'] = $cache['lng'];
        $driver['customer_lat'] = $cache['lat'];
        if ($driverOrder->gps_type == 'google' || $driverOrder->gps_type == 'wgs84') {
            $driver['customer_lng'] = (isset($cache['google_lng']) && $cache['google_lng'] != '0.000000') ? $cache['google_lng'] : $cache['lng'];
            $driver['customer_lat'] = (isset($cache['google_lat']) && $cache['google_lat'] != '0.000000') ? $cache['google_lat'] : $cache['lat'];
        }

        //Add realtime_distance  2014-12-22
        $prefix = 'LAST_POSITION';
        $hashKey = $prefix."_".$driverOrder->driver_id;
        //注意啦,redis 读位置数据
        //注意需要修改  ok
        $data = DriverPositionService::getInstance()->getPositionData($hashKey, 'data');

        if(isset($driver['customer_lat']) && isset($driver['customer_lng'])
            && ($driver['customer_lng'] + $driver['customer_lat'] > 10))
        {
            if(!empty($data))
            {
                $data_arr = json_decode($data, True);
                $gps_data = $data_arr['gps'];
                $distance = @Helper::Distance($gps_data['baidu_lat'], $gps_data['baidu_lng'],
                    $driver['customer_lat'], $driver['customer_lng']);
                $driver['realtime_distance'] = $distance;
                EdjLog::info("getDriverPosition:realtime_distance=".$distance.".\n");
            } else {
                EdjLog::info("getDriverPosition:Get driver " . $driverOrder->driver_id . " gps position error.\n");
            }
        } else {
            EdjLog::info("getDriverPosition:customer lat: " . $driver['customer_lat'] . " or lng: " . $driver['customer_lng'] . " nonvalid.\n");
        }

        return $driver;
    }

    /**
     * 初始化order
     * @param $orders
     * @param $driver
     * @param $driver_id
     * @param $app_ver
     * @param $cache
     * @param $booking_type
     * @return mixed
     */
    private function initOrderList($orders,$driver,$driverOrder,$cache){
        $city_id = isset($cache['city_id'])?$cache['city_id']:0;
        $driverOrder =$driverOrder->setByOrders($orders);
        foreach ($orders as $order) {
            if ($driverOrder->driver_id == $order['driver_id'] &&
                    ( empty($driverOrder->order_id) ||
                    (!empty($driverOrder->order_id)
                        && $order['order_id'] == $driverOrder->order_id))) {
                $driverOrder->role = $order['role'];//这块需要想办法
                $driverOrder->order_all_states = isset($order['order_states']) ?
                    $order['order_states'] : $driverOrder->order_all_states;
                $states_data = $this->getOrderAllStates($order['order_state'], $driverOrder->order_all_states, $driverOrder->app_ver);
                $order_state_code = $states_data['order_state'];
                $driverOrder->order_all_states = $states_data['order_all_states'];
                $driverOrder->order_state= $this->getOrderStateInfo($order['order_state']);
                $ready_time = isset($order['ready_time']) ? $order['ready_time'] : '';
                $driverOrder->cancel_type= isset($order['cancel_type']) ? $order['cancel_type'] : '';//这块注意

                $driverOrder->order_id  = $order['order_id'];
                // Set data for remote order
                if (!empty($order_states)) {
                    $driverPosition = $this->setTime($order_states,$driverOrder);
                }
                // For remote order, wait time should be later than expect ready time

                $driverPosition = $this->setExpectReadyTime($cache,$driverOrder->booking_type,$driverPosition,$city_id);

                //注意啦，已经COPY
                $wait_time = $this->getWaitTime($order_state_code ,
                    $ready_time, $driverPosition->expect_ready_time,$driverPosition->drive_time);
                $driverPosition->wait_time = $wait_time;
                if ($order['order_state'] == OrderProcess::ORDER_PROCESS_READY ) {
                    $driverPosition->order_state .= ' 等候'.$wait_time."分钟";//注意这块
                }
                //如果有已到达的状态 要获取等候时间
                if (!empty($order_states)) {
                    foreach ($order_states as $key=>$state) {
                        if ($state['order_state_code'] == OrderProcess::ORDER_PROCESS_READY) {
                            $order_states[$key]['order_state_content'] = '司机已到达 等候'.intval($wait_time).'分';
                        }
                    }
                }
                $driverPosition = $this->setRemoteOrderData($order,$driverPosition);
                $driver = $this->setOrderData($order,$driver);//这个需要确认逻辑
                unset($tmp_arr);
            }
        }
        $driver = $this->setDriverByDriverPosition($driverPosition,$driver);
        return $driver;
    }

    /**
     * @param $driverPosition
     * @param $driver
     * @return $driver
     */
    private function  setDriverByDriverPosition($driverPosition,$driver){
        $driver['cancel_type'] = $driverPosition->cancel_type;
        $driver['role'] = $driverPosition->role;
        $driver['order_all_states']=$driverPosition->order_all_states;
        $driver['order_id'] = $driverPosition->order_id;
        $driver['order_state_code'] = $driverPosition->order_state_code;

        $driver['order_state'] = $driverPosition->order_state;
        if(empty($driverPosition->remote_order_data)){
            return $driver;
        }

        if(isset($driverPosition->remote_order_data['ready_dist'])) {
            $driver['ready_dist'] = round($driverPosition->remote_order_data['ready_dist']);
        }

        if(isset($driverPosition->remote_order_data['ready_intime'])) {
            $driver['ready_intime'] = $driverPosition->remote_order_data['ready_intime'];
        }

        if(isset($driverPosition->remote_order_data['is_remote'])) {
            $driver['is_remote'] = $driverPosition->remote_order_data['is_remote'];
        }

        if(isset($driverPosition->remote_order_data['subsidy'])) {
            $driver['subsidy'] = $driverPosition->remote_order_data['subsidy'];
        }

        if(isset($driverPosition->remote_order_data['fee'])) {
            $driver['fee'] = $driverPosition->remote_order_data['fee'];
        }
        return $driver;
    }

    /**
     * @param $order
     * @param $remote_order_data
     * @param $driverPosition
     * @return $remote_order_data
     */
private function setRemoteOrderData($order,$driverPosition){
    //如果有已到达的状态 要获取等候时间 END
    if(isset($order['subsidy'])) {
        $driverPosition->remote_order_data['subsidy'] = $order['subsidy'];
    }
    if(isset($order['ready_dist'])) {
        $driverPosition->remote_order_data['ready_dist'] = $order['ready_dist'];
        $driverPosition->remote_order_data['is_remote'] = 1;
        $driverPosition->remote_order_data['ready_intime'] = 1;
        if(!empty($driverPosition->accept_time) && !empty($driverPosition->ready_time)) {
            $ready_time_cost = strtotime($driverPosition->ready_time) - $driverPosition->accept_time;
            if($ready_time_cost > $driverPosition->expect_ready_time_cost) {
                $driverPosition->remote_order_data['ready_intime'] = 0;
                $driverPosition->remote_order_data['subsidy'] = 0;
            }
        }
    }
    if(isset($order['fee'])) {
        $driverPosition->remote_order_data['fee'] = $order['fee'];
    }
    return $driverPosition;
}

    /**
     * 额外等待时间和价钱设置
     * @param $cache
     * @param $booking_type
     * @param $driverPosition
     * @param $city_id
     * @return mixed
     */
   private function setExpectReadyTime($cache,$booking_type,&$driverPosition,$city_id){
       $expect_ready_time = $cache['booking_time'];
       $expect_ready_time_cost = 0;
       if(isset($order['ready_dist']) && $booking_type == self::QUEUE_CHANNEL_REMOTEORDER) {
           //这个需要修改，商业组
           $expect_value = FinanceService::getInstance()->remoteOrderConfig($city_id, $order['ready_dist']);
           $expect_ready_time_cost = isset($expect_value['readyTime'])?$expect_value['readyTime']:0;

           $expect_ready_time = date('Y-m-d H:i:s', $driverPosition->accept_time + $expect_ready_time_cost);
           if(strcmp($cache['booking_time'], $expect_ready_time) > 0) {
               $expect_ready_time = $cache['booking_time'];

           }
       }

       $driverPosition->expect_ready_time = $expect_ready_time;
       $driverPosition->expect_ready_time_cost =$expect_ready_time_cost;
       return $driverPosition;
   }

    /**
     * 设置accept_time，drive_time
     * @param $order_states
     * @param $driverPosition
     */
    private function setTime($order_states,$driverPosition){
        foreach ($order_states as $key=>$state) {
            if ($state['order_state_code'] == OrderProcess::ORDER_PROCESS_ACCEPT) {
                $driverPosition->accept_time = $state['order_state_timestamp'] ;
            }
            if ($state['order_state_code'] == OrderProcess::ORDER_PROCESS_DRIVING) {
                $driverPosition->drive_time = $state['order_state_timestamp'] ;
            }
        }
        return $driverPosition;
    }

    /**
     * 根据order设置driver
     * @param $order
     * @param $driver
     * @return $driver
     */
    private function setOrderData($order,$driver){
        if (isset($order['location_start'])) {
            $driver['location_start'] = $order['location_start'];
        }

        if (isset($order['location_end'])) {
            $driver['location_end'] = $order['location_end'];
        }

        if (isset($order['pay_status'])) {
            $driver['pay_status'] = $order['pay_status'];
        }

        if (isset($order['cash_only'])) {
            $driver['cash_only'] = $order['cash_only'];
        }
        return $driver;
    }



    // Set data fro remote order data

    /**
     *  Get order states by app version
     */
    private function getOrderAllStates($order_state, $order_states, $app_ver) {
        $ret = array('order_state' => $order_state,
            'order_all_states' => $order_states);

        $old_ver_state = $order_state;
        // 因为5.2以下的app版本不支持这个状态码，所以在这里判断版本过低的话就
        // 过滤掉了这个状态
        if($order_state == OrderProcess::PROCESS_DEST &&
            $app_ver < '5.2.0') {
            $arr_tmp = array();
            $old_ver_state = OrderProcess::ORDER_PROCESS_ACCEPT;
            foreach ($order_states as $state) {
                if($state['order_state_code'] != OrderProcess::PROCESS_DEST &&
                    OrderProcess::model()->validOrderState($old_ver_state,
                        $state['order_state_code'])) {
                    $old_ver_state = $state['order_state_code'];
                }

                if($state['order_state_code'] !=
                    OrderProcess::PROCESS_DEST) {
                    array_push($arr_tmp , $state);
                }
            }
            $ret = array('order_state' => $old_ver_state,
                'order_all_states' => $arr_tmp);
        }

        return $ret;
    }

    /**
     * 获取等候时间
     * @param string $order_state
     * @param string $ready_time
     * @return string $wait_time
     */
    private function getWaitTime($order_state , $ready_time, $booking_time = '', $drive_time = '') {
        $wait_time = '';
        $state_arr = array(
            OrderProcess::ORDER_PROCESS_READY,
            OrderProcess::ORDER_PROCESS_DRIVING,
            OrderProcess::PROCESS_DEST,
        );

        $start_time = $booking_time;
        if(strcmp($ready_time,$booking_time) >0) {
            $start_time = $ready_time;
        }

        if (in_array($order_state , $state_arr) && !empty($start_time)) {
            if($order_state != OrderProcess::ORDER_PROCESS_READY
                && !empty($drive_time)) {
                $wait_time = intval(($drive_time - strtotime($start_time))/60);
            }
            else {
                $wait_time = intval((time() - strtotime($start_time))/60);
            }
        }
        if(!empty($wait_time) && $wait_time < 0) {
            EdjLog::info("Wrong wait time|Current time:".
                time()."| Read time:".strtotime($start_time));
            $wait_time = 0;
        }
        return $wait_time;
    }

    /**
     * 获取司机状态信息
     * @param string $state
     * @return string $order_state
     */
    private function getOrderStateInfo($state) {
        switch ($state) {
            case OrderProcess::ORDER_PROCESS_NEW :
                $order_state = '派单中';
                break;
            case OrderProcess::ORDER_PROCESS_ACCEPT :
                $order_state = '已接单';
                break;
            case OrderProcess::ORDER_PROCESS_READY :
                $order_state = '已就位';
                break;
            case OrderProcess::ORDER_PROCESS_DRIVING :
                $order_state = '已开车';
                break;
            case OrderProcess::PROCESS_DEST:
                $order_state = '到达目的地';
                break;
            case OrderProcess::ORDER_PROCESS_DRIVER_DESTORY :
                $order_state = '已取消';
                break;
            case OrderProcess::ORDER_PROCESS_FINISH :
                $order_state = '已完成';
                break;
            default:
                $order_state = '已接单';
                break;
        }
        return $order_state;
    }
}
