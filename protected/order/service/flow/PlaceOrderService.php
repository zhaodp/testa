<?php

/**
 * 下单服务
 * 包含选司机下单，换一个，一键下单
 *
 */
class PlaceOrderService extends BaseFlowService
{

    private static $instance;

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new PlaceOrderService();
        }
        return self::$instance;
    }

    /**
     * API入口
     * 选司机下单
     */
    public function chooseDriver($params)
    {
        $orderInfo = new OrderForm($params, OrderDriverMatchType::CHOOSE_DRIVER);
        if (empty($orderInfo->lng) || empty($orderInfo->lat)) {
            return array('code' => 2, 'data' => '', 'message' => '参数有误');
        }
        $ret = $this->validateCustomer($orderInfo);
        if (is_array($ret)) {
            return $ret;
        }
        $orderInfo = $this->setLocation($orderInfo);
        $ret = $this->validateOrder($orderInfo);
        if (is_array($ret)) {
            return $ret;
        }
        $ret = $this->validateDriverStatus($orderInfo->type, $orderInfo->driver_id);
        if (is_array($ret)) {
            return $ret;
        }
        //生成booking_id
        $booking_id = Tools::getUniqId('high');
        $orderInfo->booking_id = $booking_id;
        //存储google坐标 END
        $bonus_use_limit = in_array($orderInfo->source, Order::$daytime_sources) ? 5 : 4;
        $orderInfo->bonus_use_limit = $bonus_use_limit;
        $task = $this->getTaskChooseDriver($orderInfo);
        $this->initPhonebooking($task, $orderInfo);
        //支持客户端取消读秒,生成订单前,预先在redis中写入部分信息
        Queue::model()->putin($task, 'apporder');
        $ret = $this->getSuccessData($orderInfo,OrderDriverMatchType::CHOOSE_DRIVER);
        return $ret;
    }

    /**
     * API入口
     * 一键下单
     */
    public function automatic($params)
    {
        $orderForm = new OrderForm($params, OrderDriverMatchType::AUTOMATIC);
        $ret = $this->validateSample($orderForm);
        if (is_array($ret)) {
            return $ret;
        }
        $ret = $this->validateCustomer($orderForm);
        if (is_array($ret)) {
            return $ret;
        }
        // 生成booking_id
        $orderForm->booking_id = Common::genCallId();
        $ret = $this->setCustomerLocation($orderForm);
        if (is_array($ret)){
            return $ret;
        }
        // 日间暂不支持远程单,为了方便用户端
        // 如果channel是远程单,把source替换成SOURCE_CLIENT
        if ($orderForm->channel == CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER) {
            $orderForm->source = Order::SOURCE_CLIENT;
        }
        $ret = $this->validateOrder($orderForm);
        if (is_array($ret)) {
            return $ret;
        }
        // 一键下单下一单 预约时间是10分钟
        if ($orderForm->channel == CustomerApiOrder::QUEUE_CHANNEL_BOOKING && $orderForm->number == 1) {
            $orderForm->booking_time = 600;
        }
        // 下单处理(队列处理：判定司机状态，有误重复下单)
        $bonus_use_limit = in_array($orderForm->source, Order::$daytime_sources) ? 5 : 4;
        $orderForm->bonus_use_limit = $bonus_use_limit;
        $task = $this->getTaskAutomatic($orderForm);
        $this->initPhonebooking($task, $orderForm);
        Queue::model()->putin($task, 'apporder');
        $ret = $this->getSuccessData($orderForm, OrderDriverMatchType::AUTOMATIC);
        return $ret;
    }

    /**
     * 简单的校验，非空，channel,验证联系人电话号码是否正确,验证一个预约信息最大人数不超过5人
     * @param $orderForm
     * @return array
     */
private function validateSample($orderForm){
    if ($orderForm->channel !== CustomerApiOrder::QUEUE_CHANNEL_BOOKING
        && $orderForm->channel !== CustomerApiOrder::QUEUE_CHANNEL_FIFTEEN_MIN_BOOKING
        && $orderForm->channel !== CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER
    ) {
        return array('code' => 2, 'data' => '', 'message' => '参数有误');
    }
    if (empty($orderForm->number) || empty($orderForm->contact_phone) || empty($orderForm->address)
        || empty($orderForm->lng) || empty($orderForm->lat)
    ) {
        return array('code' => 2, 'data' => '', 'message' => '参数有误');
    }
    // 验证联系人电话号码是否正确
    $is_mobile = Common::checkPhone($orderForm->contact_phone);
    if (!$is_mobile) {
        return array('code' => 2, 'data' => '', 'message' => '请输入正确的联系人手机号');
    }
    // 验证一个预约信息最大人数不超过5人
    if ($orderForm->number > 5) {
        return array('code' => 2, 'data' => '', 'message' => '预约人数不能超过5人');
    }
}

    /**
     * 设置用户位置
     * @param $orderForm
     * @return array
     */
    private function setCustomerLocation(&$orderForm)
    {
        // 通过gps反推城市及地址
        if (!empty($orderForm->customer_lng) && !empty($orderForm->customer_lat)
            && ($orderForm->customer_lng != $orderForm->lng || $orderForm->customer_lat != $orderForm->lat)
        ) {
            $customer_gps = $this->gpsConvert($orderForm->customer_lng, $orderForm->customer_lat, $orderForm->gps_type);
            if (isset($customer_gps ['baidu_lng']) && isset($customer_gps ['baidu_lat'])) {
                $customer_lng = $customer_gps ['baidu_lng'];
                $customer_lat = $customer_gps ['baidu_lat'];
            }
        }
        $gps = $this->gpsConvert($orderForm->lng, $orderForm->lat, $orderForm->gps_type);
        if (isset($gps ['baidu_lng']) && isset($gps ['baidu_lat'])) {
            $orderForm->lng = $gps ['baidu_lng'];
            $orderForm->lat = $gps ['baidu_lat'];
        }
        $city = $this->setCity($orderForm,$gps);
        // 注意下面的逻辑到赤道或子午线处是走不通的
        // 如果预约坐标和下单坐标一致,使用客户坐标下单
        if (floatval($orderForm->customer_lng) != 0 && floatval($orderForm->customer_lat != 0)
            && $orderForm->customer_lng == $orderForm->lng && $orderForm->customer_lat == $orderForm->lat) {
            EdjLog::info('CustomerLocate|' . $orderForm->lng . ',' . $orderForm->lat);
        } else if (false !== stristr($orderForm->os, 'android') && !empty($app_ver)
            && strcmp($app_ver, '5.0.0') < 0 && abs($customer_lng) > 0.0 && abs($customer_lat) > 0.0
        ) {
            // 留空的逻辑，android老版本直接用customer_lng,customer_lat参数
            $orderForm->lng = $customer_lng;
            $orderForm->lat = $customer_lat;
            EdjLog::info('old android customer location|' . $orderForm->lng . ',' . $orderForm->lat);
        } else if ((false === stristr($orderForm->os, 'android') || (!empty($app_ver) && strcmp($app_ver, '5.0.0') >= 0))
                     && (abs($orderForm->lng) > 0.0 && abs($orderForm->lat) > 0.0)) {
            // ios 所有版本，android 新版本 都用 lng lat 参数
            EdjLog::info('new customer location|' . $orderForm->lng . ',' . $orderForm->lat);
        } else {
            $gps_baidu = Helper::getBaiduGPSByAddress($city, $orderForm->address);
            if (!empty($gps_baidu ['location'])) {
                EdjLog::info('AddressLocate|' . $orderForm->lng . ',' . $orderForm->lat . '|' . $gps_baidu ['location'] ['lng'] . ',' . $gps_baidu ['location'] ['lat']);
                $orderForm->lng = $gps_baidu ['location'] ['lng'];
                $orderForm->lat = $gps_baidu ['location'] ['lat'];
            } else {
                EdjLog::info('customer location not good ' . json_encode($orderForm));
                return array('code' => 2, 'message' => '该城市暂时还未开通e代驾服务!');
            }
        }
        $new_address = GPS::model()->getStreetByBaiduGPS($orderForm->lng, $orderForm->lat, 4);
        if (!empty($new_address) && isset($new_address ['name'])) {
            $address = $new_address ['name'];
            $orderForm->address = $address;
        }
        $this->setGoogleLocation($orderForm,$orderForm->lng, $orderForm->lat);
    }

    /**
     * 设置谷歌地址
     * @param $orderForm
     * @param $lng
     * @param $lat
     */
    private function setGoogleLocation(&$orderForm,$lng, $lat){
        if ($orderForm->edited == 0) {
            $google_lng = isset($gps ['google_lng']) ? $gps ['google_lng'] : $lng;
            $google_lat = isset($gps ['google_lat']) ? $gps ['google_lat'] : $lat;
        } else {
            $google_lng = $lng;
            $google_lat = $lat;
        }
        $orderForm->google_lng = $google_lng;
        $orderForm->google_lat = $google_lat;
    }

    /**
     * 设置城市信息
     * @param $orderForm
     * @param $gps
     * @return bool
     */
private function setCity(&$orderForm,$gps){
    if (!empty($orderForm->city_id)) { // gps.location给返回的city_id 直接用
        $city = Dict::item('city', $orderForm->city_id);
        $city = !$city && GPS::model()->getCityByBaiduGPS($gps ['baidu_lng'], $gps ['baidu_lat']);
    } else {
        $city = GPS::model()->getCityByBaiduGPS($gps ['baidu_lng'], $gps ['baidu_lat']);
        $orderForm->city_id = CityConfig::getIdByName($city);
    }
    $open_city = RCityList::model()->getOpenCityList();
    // 验证城市是否开通,未开通城市city_id 返回默认 '未知' 的城市id
    if (!array_key_exists($orderForm->city_id, $open_city)) {
        $orderForm->city_id = CityConfig::getIdByName($city);
    }
    return $city;
}

    /**
     * 一键下单任务参数组装
     * @param $orderForm
     * @return array
     */
    private function getTaskAutomatic($orderForm)
    {
        $task = array(
            'class' => __CLASS__,
            'method' => 'automaticJob',
            'params' => array('cash_only' => $orderForm->cash_only,
                'phone' => $orderForm->phone,
                'contact_phone' => $orderForm->contact_phone,
                'city_id' => $orderForm->city_id,
                'address' => $orderForm->address,
                'callid' => $orderForm->booking_id,
                'booking_time' => date('Y-m-d H:i:s', time() + ($orderForm->booking_time != 0 ? $orderForm->booking_time : 1200)),
                'number' => $orderForm->number,
                'lng' => $orderForm->lng, 'lat' => $orderForm->lat,
                'google_lng' => $orderForm->google_lng,
                'google_lat' => $orderForm->google_lat,
                'bonus_use_limit' => $orderForm->bonus_use_limit,
                'app_ver' => $orderForm->app_ver < BonusCode::APP_VER ? 0 : 1,
                'bonus_sn' => $orderForm->bonus_sn,
                'is_use_bonus' => $orderForm->is_use_bonus,
                'channel' => $orderForm->channel,
                'source' => $orderForm->source,
                'fee' => $orderForm->fee,
                'from' => $orderForm->from)
        );
        return $task;
    }

    /**
     * 将成功返回数据组装
     * @param $orderInfo
     * @param int $type
     * @return array
     */
    private function getSuccessData($orderInfo, $type)
    {
        $order_time = time();
        if ($type == OrderDriverMatchType::CHOOSE_DRIVER) {
            $orderInfo->channel = ($orderInfo->type == DalOrder::SINGLE_PUSH_DRIVER) ?
                DalOrder::QUEUE_CHANNEL_SINGLE_DRIVER : DalOrder::QUEUE_CHANNEL_SINGLE_CHANGE;

        }
        if ($orderInfo->channel == DalOrder::QUEUE_CHANNEL_SINGLE_DRIVER) {
            $timeout = DalOrder::POLLING_SECOND_DRIVER;
        } elseif ($orderInfo->channel == CustomerApiOrder::QUEUE_CHANNEL_SINGLE_CHANGE) {
            $timeout = DalOrder::POLLING_SECOND_CHANGE;
        } elseif ($orderInfo->channel == CustomerApiOrder::QUEUE_CHANNEL_FIFTEEN_MIN_BOOKING) {
            $timeout = CustomerApiOrder::POLLING_SECOND_FIFTEEN_MIN_BOOKING;
        } else {
            $timeout = CustomerApiOrder::POLLING_SECOND_BOOKING;
        }
        $booking_type = $orderInfo->channel;
        $ret = array(
            'code' => 0,
            'data' =>
                array('booking_id' => $orderInfo->booking_id,
                    'booking_type' => $booking_type,
                    'order_time' => $order_time,
                    'timeout' => $timeout),
            'message' => '下单成功');

        return $ret;

    }

    /**
     * 验证订单属性
     * @param $orderInfo
     * @return array|bool|int
     */
    private function validateOrder(&$orderInfo)
    {
        //普通订单验证           $source
        if (in_array($orderInfo->source, Order::$distance_sources)) {
            $ret = $this->validateNormal($orderInfo);
            if (is_array($ret)) {
                return $ret;
            }
            
            $orderInfo->source = $ret;
        }

        //洗车
        if (in_array($orderInfo->source, Order::$washcar_sources)) {
            $check_ret = Order::CheckSpecialOrderSource($orderInfo->source, $orderInfo->city_id, time());
            if (!$check_ret['flag']) {
                $ret = array('code' => $check_ret['code'],
                    'data' => '', 'message' => '下单失败');

                EdjLog::info("CheckSpecialOrderSource|" . $orderInfo->token . '|'
                    . $orderInfo->driver_id . '|' . $orderInfo->source
                    . '|' . $orderInfo->city_id . '|' . $ret['code']);
                return $ret;
            }
        }

        //日间模式校验
        if (in_array($orderInfo->source, Order::$daytime_sources)) {
            $ret = $this->validateDayTime($orderInfo->city_id, $orderInfo->token, $orderInfo->driver_id, $orderInfo->source);
            if (is_array($ret)) {
                return $ret;
            }

        }
    }

    /**
     * 设置位置信息，包括位置，城市
     * @param $orderInfo
     * @return $orderInfo
     */
    private function setLocation($orderInfo)
    {
        $city_id = $orderInfo->city_id;
        $open_city = RCityList::model()->getOpenCityList();
        //通过坐标获取
        $gps_location = array(
            'longitude' => $orderInfo->lng,
            'latitude' => $orderInfo->lat,
        );
        $gps = GPS::model()->convert($gps_location, $orderInfo->gps_type);
        $orderInfo->gps = $gps;
        if (empty($city_id)) {
            $city = GPS::model()->getCityByBaiduGPS($gps['baidu_lng'], $gps['baidu_lat']);
            $city_id = CityConfig::getIdByName($city);
        }


        //验证城市是否开通,未开通城市city_id 返回默认 '未知' 的城市id
        if (!array_key_exists($city_id, $open_city)) {
            $city_id = CityConfig::getIdByName($city);
        }
        $orderInfo->city_id = $city_id;
        $new_address = GPS::model()->getStreetByBaiduGPS($gps['baidu_lng'], $gps['baidu_lat'], 4);
        if (!empty($new_address) && isset($new_address['name'])) {
            $orderInfo->address = $new_address['name'];
        }
        //存储google坐标
        if ($orderInfo->gps_type == 'google') {
            $google_lng = $orderInfo->lng;
            $google_lat = $orderInfo->lat;
        } else {
            $google_lng = $orderInfo->gps['google_lng'];
            $google_lat = $orderInfo->gps['google_lat'];
        }
        $orderInfo->google_lat = $google_lat;
        $orderInfo->google_lng = $google_lng;
        return $orderInfo;
    }

    /**
     * 验证用户的有效性,是否登录,是否在黑名单，是否达到下单次数上限5
     * @param $orderInfo
     * @return array|bool
     */
    private function validateCustomer(&$orderInfo)
    {
        //验证token
        //需要修改   ok
        $validate = CustomerService::getInstance()->validate($orderInfo->token);
        //$validate = array('phone' => '13998999647');
        if (!$validate) {
            return array('code' => 1, 'data' => '', 'message' => '验证失败');
        }

        $orderInfo->phone = $validate['phone'];
        //验证是否在黑名单里面(包括V2后台黑名单，订单流黑名单)
        //需要修改   ok
        $black_customer = CustomerService::getInstance()->isBlack($validate['phone'])
            || OrderBlackListRedis::model()->isInBlacklist($validate['phone'], $orderInfo->udid);
        if ($black_customer) {
            return array('code' => 2, 'data' => '', 'message' => '尊敬的客户，由于您短时间内取消多名司机，造成系统无法为您提供正常服务；如急需代驾，可拨打热线4006913939');
        }

        //验证订单数量是否到达上限(规则待定)
        $order_number = DalOrder::model()->validateOrderNumber($validate['phone']);    //获取订单数量方法走redis
        if ($order_number >= DalOrder::QUEUE_MAX) {
            return array('code' => 2, 'data' => '', 'message' => '最多同时预约5名司机噢~如有另外需要请拨打4006913939');
        }
        return true;
    }

    /**
     * 将订单数据插入redis
     * @param $task
     * @param $orderInfo
     */
    private function initPhonebooking($task, $orderInfo)
    {
        $key = $orderInfo->phone . "_" . $orderInfo->booking_id;
        try {
            $tmp_info = CustomerApiOrder::model()->init_phonebooking($task['params']);
            QueueApiOrder::model()->insert($key, $tmp_info);
        } catch (Exception $e) {
            EdjLog::warning('预先写入redis出错|' . $key . '|' . $e->getMessage());
        }
    }

    /**
     * 选司机下单，JOB参数组装
     * @param $orderInfo
     * @return array
     */
    private function getTaskChooseDriver($orderInfo)
    {
        if (!empty($orderInfo->driver_id)) {
            $task = array(
                'class' => __CLASS__,
                'method' => 'chooseDriverJob',
                'params' => array(
                    'phone' => $orderInfo->phone,
                    'city_id' => $orderInfo->city_id,
                    'address' => $orderInfo->address,
                    'callid' => $orderInfo->booking_id,
                    'driver_id' => $orderInfo->driver_id,
                    'booking_time' => date('Y-m-d H:i:s',
                        time() + ($orderInfo->booking_time != 0 ? $orderInfo->booking_time : 600)),
                    'lng' => $orderInfo->gps['baidu_lng'],
                    'lat' => $orderInfo->gps['baidu_lat'],
                    'google_lng' => $orderInfo->google_lng,
                    'google_lat' => $orderInfo->google_lat,
                    'type' => $orderInfo->type,
                    'source' => $orderInfo->source,
                    'bonus_use_limit' => $orderInfo->bonus_use_limit,
                    'app_ver' => $orderInfo->app_ver < BonusCode::APP_VER ? 0 : 1,
                    'bonus_sn' => $orderInfo->bonus_sn,
                    'is_use_bonus' => $orderInfo->is_use_bonus,
                )
            );
        } else {
            $task = array(
                'class' => __CLASS__,
                'method' => 'chooseAnotherJob',
                'params' => array(
                    'phone' => $orderInfo->phone,
                    'city_id' => $orderInfo->city_id,
                    'address' => $orderInfo->address,
                    'callid' => $orderInfo->booking_id,
                    'driver_id' => $orderInfo->driver_id,
                    'booking_time' => date('Y-m-d H:i:s',
                        time() + ($orderInfo->booking_time != 0 ? $orderInfo->booking_time : 600)),
                    'lng' => $orderInfo->gps['baidu_lng'],
                    'lat' => $orderInfo->gps['baidu_lat'],
                    'google_lng' => $orderInfo->google_lng,
                    'google_lat' => $orderInfo->google_lat,
                    'type' => $orderInfo->type,
                    'source' => $orderInfo->source,
                    'bonus_use_limit' => $orderInfo->bonus_use_limit,
                    'app_ver' => $orderInfo->app_ver < BonusCode::APP_VER ? 0 : 1,
                    'bonus_sn' => $orderInfo->bonus_sn,
                    'is_use_bonus' => $orderInfo->is_use_bonus,
                )
            );
        }
        return $task;
    }

    /**
     * 检验司机状态，并且锁定司机
     * @param $type
     * @param $driver_id
     * @return array|bool
     */
    private function validateDriverStatus($type, $driver_id)
    {
        //验证司机是否是空闲 add by sunhongjing 2013-10-22
        if ($type == DalOrder::SINGLE_PUSH_DRIVER) {
            //需要修改   ok
            $driver_info = DriverService::getInstance()->get($driver_id);
            if (empty($driver_info) || '0' != $driver_info->status) {
                echo 'driver empty'.$driver_info->status.PHP_EOL;
                return array('code' => 2, 'data' => '', 'message' => '司机服务中或已下班');
            }

            //验证司机有没有被400订单锁定
            $timestamp = QueueDispatchDriver::model()->get($driver_id);
            if ($timestamp != 0) {
                $lock_time = abs(time() - $timestamp);
                $lock_gap = QueueDispatchDriver::DRIVER_LOCK_GAP;
                if ($lock_time < $lock_gap) { //验证时间改成70s 2014-01-13 11:55
                    return array('code' => 2, 'data' => '', 'message' => '真不巧,司机刚被选走,烦请再选一名!');
                }
            }

            //改成锁司机，而不直接改司机的状态。add by sunhongjing 2014-01-17
            //$driver_info->status = 1;
            $is_lock = QueueDispatchDriver::model()->insert($driver_id);
            return true;
        }
    }

    /**
     * 日间单检验
     * @param $city_id
     * @param $token
     * @param $driver_id
     * @param $source
     * @return array|bool
     */
    private function validateDayTime($city_id, $token, $driver_id, $source)
    {
        // 用当前服务器时间做校验
        $check_ret = Order::CheckSpecialOrderSource($source, $city_id, time());
        if ($check_ret['flag']) {
            return true;
        }

        if ($check_ret['code'] == ApiErrorCode::ORDER_CITY_ERROR) {
            $ret = array('code' => $check_ret['code'],
                'data' => '', 'message' => '当前城市没有开通日间服务，将按普通订单为您下单');
        } elseif ($check_ret['code'] == ApiErrorCode::ORDER_TIME_ERROR) {
            $ret = array('code' => $check_ret['code'],
                'data' => '', 'message' => '当前时段所下订单为普通按里程计费方式，继续为您下单？');
        } else {
            $ret = array('code' => $check_ret['code'],
                'data' => '', 'message' => '下单失败');
        }

        EdjLog::info("CheckSpecialOrderSource|" . $token . '|' . $driver_id . '|' . $source
            . '|' . $city_id . '|' . $ret['code']);
        return $ret;

    }

    /**
     * 普通单检验
     * @param $orderInfo
     * @return array|int
     */
    private function validateNormal($orderInfo)
    {
        $check_ret = Order::CheckSpecialOrderSource(
            Order::SOURCE_DAYTIME_CLIENT, $orderInfo->city_id, time(),$orderInfo->channel);
        //符合日间单
        if ($check_ret['flag']) {
            if (Helper::compareVersion('5.3.0', $orderInfo->app_ver)) {
                EdjLog::info("force change source to daytime|" . $orderInfo->token . '|' . $orderInfo->driver_id . '|' . $orderInfo->source
                    . '|' . $orderInfo->city_id);
                $source = Order::SOURCE_DAYTIME_CLIENT;
                return $source;
            } else {
                $ret = array('code' => ApiErrorCode::ORDER_TIME_ERROR,
                    'data' => '', 'message' => '当前时段所下订单为日间按时间计费方式，继续为您下单？');

                EdjLog::info("CheckSpecialOrderSource|" . $orderInfo->token . '|' . $orderInfo->driver_id . '|' . $orderInfo->source
                    . '|' . $orderInfo->city_id . '|' . $ret['code']);
                return $ret;
            }
        }
    }


    /**
     * 选司机下单后台Job
     */
    public function chooseDriverJob($params)
    {
        if (CustomerApiOrder::model()->isBlockCustomerByConfig($params)) {
            return false;
        }

        return DalOrder::model()->OrderSingleFactory($params);
    }

    /**
     * 选司机下单换一个后台job
     */
    public function chooseAnotherJob($params)
    {
        if (CustomerApiOrder::model()->isBlockCustomerByConfig($params)) {
            return false;
        }

        return CustomerApiOrder::model()->single_order($params);
    }

    /**
     * 一键下单后台job
     */
    public function automaticJob($params)
    {
        if (CustomerApiOrder::model()->isBlockCustomerByConfig($params)) {
            return false;
        }

        $params['channel'] = isset($params['channel']) ? $params['channel'] : CustomerApiOrder::QUEUE_CHANNEL_BOOKING;
        $source = isset($params['source']) ? $params['source'] : Order::SOURCE_CLIENT;
        if (!empty($source) && $source == Order::SOURCE_WEIXIN) {
            $queue = CustomerApiOrder::model()->save_order_queue($params,
                Order::SOURCE_WEIXIN, OrderQueue::QUEUE_AGENT_WEIXIN);
        } else {
            $queue = CustomerApiOrder::model()->save_order_queue($params,
                $source, OrderQueue::QUEUE_AGENT_KEYBOOKING);
        }

        if (empty($queue)) {
            return false;
        }

        $queue_id = isset($queue['id']) ? $queue['id'] : '';
        //输出log
        EdjLog::info($queue_id . '|101等待派单', 'console');

        //增加优惠券 2014-03-19
        //需要修改   ok
        $bonus_sn = isset($params['bonus_sn']) ? $params['bonus_sn'] : '';
        if (!empty($bonus_sn)) {  //有优惠券 将优惠券加到缓存中
            $bonus_cache = QueueDispatchOrder::model()->queueBonusBind($queue_id, $bonus_sn);
            if ($bonus_cache) {
                EdjLog::info($queue_id . '|bonus_sn:' . $bonus_sn . '缓存添加成功|101等待派单', 'console');
            } else {
                EdjLog::info($queue_id . '|bonus_sn:' . $bonus_sn . '缓存添加失败|101等待派单', 'console');
            }

        }

        // Add remote order fee for queue
        $fee = isset($params['fee']) ? $params['fee'] : '';
        if (!empty($fee)) {
            $set_rst = QueueDispatchOrder::model()->
            setQueueRemoteOrderFee($queue_id, $fee);
            if ($set_rst) {
                EdjLog::info('Success|' . $queue_id . '|Fee:' . $fee .
                    ' Cache fee for remote order', 'console');
            } else {
                EdjLog::info('Fail|' . $queue_id . '|Fee:' . $fee .
                    ' Cache fee for remote order', 'console');
            }
        }

        $params['source'] = $source;
        $result = CustomerApiOrder::model()->multi_push($queue_id, $params);
        return $result;
    }

    private function gpsConvert($lng, $lat, $gps_type)
    {
        $gps_location = array(
            'longitude' => $lng,
            'latitude' => $lat,
        );
        $gps = GPS::model()->convert($gps_location, $gps_type);
        if (!isset($gps['baidu_lng']) || !isset($gps['baidu_lng'])) {
            EdjLog::info('GPS convert to baidu error|' . $lng . ',' . $lat);
        }
        return $gps;
    }
}
