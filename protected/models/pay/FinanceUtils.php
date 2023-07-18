<?php

/**
 * 封装财务公用的方法
 *
 * User: tuan
 * Date: 14/10/28
 * Time: 15:17
 */
final class FinanceUtils
{
    /** 财务报警相关人员手机人  */
    private static $FINANCE_MAIL_TO_LIST = array(
        'liutuanwang@edaijia-inc.cn', //TODO ... 测试期间只用自己的
        'zhangxiaoyin@edaijia-inc.cn',
        'wenqiang@edaijia-inc.cn'
    );

    /** 对外相关人员手机人  */
    private static $ROOT_MAIL_TO_LIST = array(
        //'liutuanwang@edaijia-inc.cn', //TODO ... 测试期间只用自己的
        'zhangxiaoyin@edaijia-inc.cn',
    );

    /**
     * 获取调整费的配置
     *加上恶劣天气加价的配置0330
     * @param $orderSource | @see Order 里面的 source
     * @param $cityId |
     * @param array $order |order 属性, 作为扩展使用
     * @return mixed
     */
    public static function getModifyFeeConfig($orderSource, $cityId, $order = array()){
        $phone = isset($order['phone']) ? $order['phone'] : '';
        $fee = OrderExt::model()->getBadWeatherSurchargeByCity($cityId);//是否恶劣天气加价的金额
        EdjLog::info("--getModifyFeeConfig333--badWeatherPrice: $fee :--cityID: $cityId : phone : $phone --");
        if($fee){
            $isVip = CustomerMain::model()->isVip($phone);
            if($isVip){
                return array();//如果是vip客户不给客户加价但是钱会以红包方式发给司机
            }
           $badWeatherArr = array(
                'type' => FinanceConstants::TYPE_MODIFY_INCOME,
                'name' => '恶劣天气加收',
                'amount' => $fee,
            );
            return $badWeatherArr;
        }
        $financeOrderType   =  self::getOrderType(array('source' => $orderSource));
        if(FinanceConstants::ORDER_TYPE_DAYTIME == $financeOrderType){ //日间业务没有调整费
            return array();
        }
        if(FinanceConstants::ORDER_TYPE_UNIT == $financeOrderType){//洗车业务没有调整诶
            return array();
        }
        $financeOrderSource = self::getOrderSource($orderSource);
        return FinanceConfigUtil::getModifyPriceConfig($financeOrderSource, $cityId, $order);
    }

    /**
     *
     * 获取订单来源
     *
     * @param $orderSource
     * @return int
     */
    public static function getOrderSource($orderSource){
        if(in_array($orderSource, Order::$callcenter_sources)){
            return FinanceConstants::ORDER_SOURCE_CALL_CENTER;
        }
        if(in_array($orderSource, Order::$client_source)){
            return FinanceConstants::ORDER_SOURCE_APP;
        }
    }

    /**
     * 检查一个订单的状态是否已经是结束状态
     *
     * @param $orderStatus 表 order 里面的 status 值
     * @return bool
     */
    public static function isOrderFinish($orderStatus)
    {
        $finish_status = array(
            Order::ORDER_COMPLATE,
            Order::ORDER_NOT_COMFIRM,
        );
        return in_array($orderStatus, $finish_status);
    }


    /**
     * 返回是否走参数结账接口
     *
     * @param $orderType
     * @return bool
     */
    public static function isParamSettle($orderType)
    {
        $typeArray = array(
            FinanceConstants::ORDER_TYPE_DAYTIME,
            FinanceConstants::ORDER_TYPE_UNIT,
        );
        return in_array($orderType, $typeArray);
    }

    /**
     * 按照规则,过滤掉某些 key
     *
     * @param array $var | 要过滤的数组 , 一维的可以 ,形势类似 $k=>$v
     * @param array $filterArr | 需要过滤的 key 集合
     */
    public static function paramsFilter(array &$var, $filterArr = array())
    {
        if (empty($filterArr)) {
            return;
        }
        foreach ($var as $k => $v) {
            if (array_key_exists($k, $filterArr)) {
                if ($v == $filterArr[$k]) {
                    unset($var[$k]);
                }
            }
        }
    }

    /**
     * 根据订单里面的 channel 和 source,返回财务这边配置文件支持的 order type
     *
     * @param $order
     * @return int
     */
    public static function getOrderType($order)
    {
        $lightOrderSource = array(
            Order::SOURCE_DAYTIME_CLIENT,
            Order::SOURCE_DAYTIME_CALLCENTER,
            Order::SOURCE_DAYTIME_CLIENT_INPUT,
            Order::SOURCE_DAYTIME_CALLCENTER_INPUT
        );
        $unitOrderSource = array(
            Order::SOURCE_WASHCAR_CLIENT,
            Order::SOURCE_WASHCAR_CALLCENTER,
            Order::SOURCE_WASHCAR_CLIENT_INPUT,
            Order::SOURCE_WASHCAR_CALLCENTER_INPUT,
        );
        $source = isset($order['source']) ? $order['source'] : -1;
        if (in_array($source, $lightOrderSource)) {
            return FinanceConstants::ORDER_TYPE_DAYTIME;//日间业务
        }
        if (in_array($source, $unitOrderSource)) {
            return FinanceConstants::ORDER_TYPE_UNIT;
        }
        return FinanceConstants::ORDER_TYPE_DEFAULT;
    }

    /**
     * 从司机端传入参数里面获取满足要求的参数
     *
     * @param $orderType
     * @param $params
     * @return array
     */
    public static function getLegalParamList($orderType, $params)
    {
        $legalList = FinanceConfigUtil::getConfigByOrderType($orderType);
        $ret = array();
        $keys = array_keys($legalList);
        foreach ($keys as $item) {
            $exist = array_key_exists($item, $params);
            if ($exist) {
                $ret[$item] = $params[$item];
            }
        }
        return $ret;
    }

    /**
     * 财务发送短信工具
     *
     * @param $phone | 接收人的点电话号码
     * @param $content | 短信内容
     * @return bool | 空号码,空内容的时候返回 false
     */
    public static function sendSMS($phone, $content, $channel = '')
    {
        EdjLog::info('finance message get in ,to phone ' . $phone . ' content is ' . $content);
        $ret = false;
        if (empty($content) || empty($phone)) {
            return false;
        }
        try {
            if(empty($channel)){
                $ret = Sms::SendSMS($phone, $content);
            }else{
                $ret = Sms::SendSMS($phone, $content, $channel);
            }
        } catch (Exception $e) {
            EdjLog::error('send phone exception ' . $e->getMessage());
        }
        if (!$ret) {
            $log = 'finance send message fail -- phone ' . $phone . '  message is ' . $content;
            EdjLog::info($log);
            self::sendFinanceAlarm('财务发送短信报警', $log);
        }
        return $ret;
    }

    /**
     * 校验一个订单真的cost_type
     *
     * @param $order
     */
    public static function checkAndResetCostType($order)
    {
        //更改订单的cost_type，因为司机端传过来的cost_type可能不准确，会有客户有余额进行余额扣款但cost_type=0的
        //get customerBalance
        $oldBalance = BUpmpPayOrder::model()->forceGetBalance($order['phone']);
        $realCostType = $order['cost_type'];
        if (($order['cost_type'] == 0 || $order['cost_type'] == 2) && $order['income'] > $order['price'] && $oldBalance > 0) {
            if ($oldBalance < $order['income'] - $order['price']) {
                EdjLog::info('order ' . $order['order_id'] . ' error oldBalance ' . $oldBalance . ' order ' . serialize($order));
            }
            $real_cost_type = 0;
            if ($order['cost_type'] == 0) {
                $vip_card = VipPhone::model()->getPrimary($order['phone']);
                if (!empty($vip_card) && !empty($vip_card['vipid'])) {
                    $order_vipcard_update = Order::model()->updateByPk($order['order_id'], array('vipcard' => $vip_card['vipid']));
                    if ($order_vipcard_update) {
                        $real_cost_type = 1; //vip
                    }
                } else {
                    $real_cost_type = 8; //个人账户
                }
            } else if ($order['cost_type'] == 2) {
                $real_cost_type = 4;
            }
            $order_cost_type_update = Order::model()->updateByPk($order['order_id'], array('cost_type' => $real_cost_type));
            if (!$order_cost_type_update) {
                EdjLog::info('update order_id ' . $order['order_id'] . ' cost_type ' . $real_cost_type . ' fail');
            }
            $order = Order::model()->getOrderById($order['order_id']);
            $order['cost_type'] = $real_cost_type;
        }
    }

    /**
     * 发送报警邮件,给财务相关人员
     *
     * @param $title
     * @param $content
     */
    public static function sendFinanceAlarm($title, $content, $type = 0)
    {
        Helper::mailAlarm($title, $content, $type == 0 ? (self::$FINANCE_MAIL_TO_LIST) : (self::$ROOT_MAIL_TO_LIST));
    }

    /**
     * 是否是远程叫单
     *
     * @param $order
     * @param $orderExt
     * @return bool
     */
    public static function isRemoteOrder($order, $orderExt)
    {
        if (empty($orderExt)) {
            return 0;
        }
        $channel = isset($order['channel']) ? $order['channel'] : 0;
        $cityId = isset($order['city_id']) ? $order['city_id'] : 0;
        $readyDistance = isset($orderExt['linear_ready_distance']) ? $orderExt['linear_ready_distance'] : 0;
        $config = FinanceConfigUtil::remoteOrderConfig($cityId, $readyDistance);
        $distance = $config['distance'];
        //和建平沟通,通过如下方式判断 2014-10-30 客户端按照1和0的字符串进行匹配,1表示远程，0表示其他的
        $isRemote = (CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER == $channel)
            && ($readyDistance > $distance);
        $format = 'order_id|%s|ready_distance|%s|city_id|%s|channel|%s|config|%s|---- is_remote|%s';
        $orderId = isset($order['order_id']) ? $order['order_id'] : 0;
        EdjLog::info(sprintf($format, $orderId, $readyDistance, $cityId, $channel, json_encode($config), $isRemote));
        return $isRemote ? '1' : '0';
    }

    /**
     * 是否有小费
     *
     * @param $order
     * @param $orderExt
     */
    public static function havingTips($order, $orderExt)
    {
        $fee = 0.00;
        if ($orderExt) {
            $fee = FinanceCastHelper::getOrderFeeByExt($orderExt);
        }
        return $fee > 0.00;
    }

    /**
     * 给vip账户加锁
     *
     * @param $vipCard
     */
    public static function lockVip($vipCard)
    {
        //结账时必须加锁
        if (!empty($vipCard)) {
            $sleep_cnt = 0;
            while (!RPay::model()->addSettleVipCardIfNotExist($vipCard)) {
                sleep(1);
                ++$sleep_cnt;
                if ($sleep_cnt >= 60) {
                    RPay::model()->delSettleVipCard($vipCard);
                }
            }
        }
    }

    /**
     * 解锁vip账户
     *
     * @param $vipCard
     */
    public static function unLockVip($vipCard)
    {

    }

    /**
     * 锁普通用户账户
     *
     * @param $phone
     */
    public static function lockCustomer($phone)
    {
        if (!empty($phone)) {
            $sleep_cnt = 0;
            while (!RPay::model()->addSettlePhoneIfNotExist($phone)) {
                sleep(1);
                ++$sleep_cnt;
                if ($sleep_cnt >= 60) {
                    RPay::model()->delSettlePhone($phone);
                }
            }
        }
    }

    /**
     * 解锁普通用户账户
     *
     * @param $phone
     */
    public static function unLockCustomer($phone)
    {
    }

    public static function lockOrder($orderId, $vipCard, $phone)
    {
        if (!empty($orderId)) {
            if (!RSubmitOrder::model()->addOrderIdIfNotExist('settle_tag_' . $orderId)) {
                RPay::model()->delSettleVipCard($vipCard);
                RPay::model()->delSettlePhone($phone);
                return false;
            }
        }
    }

    /**
     * 封装财务发送 push 的方法
     *
     * @param $userId
     * @param $type
     * @param array $content
     * @return bool
     */
    public static function sendPush($userId, $type, $content = array())
    {
        $format = 'finance push ---- | user_id|%s|type|%s|content|%s|status|%s';
        //get clinet id
        $client = GetuiClient::model()->getDriverInfo($userId);
        $status = false;
        if ($client) {
            $params = array(
                'content' => $content,
            );
            //build message
            $content = PushMsgFactory::model()->orgPushMsg($params, $type);
            //send
            $result = EPush::model('driver')->send($client['client_id'], $content);
            if (empty($result['result'])
                || $result['result'] != 'ok'
            ) {
                EdjLog::info(serialize($result));
                $status = false;
            } else {
                $status = true;
            }
            if (!$status) {
                EdjLog::info('财务下发 push 失败:' . serialize($content));
            }
        }
        EdjLog::info(sprintf($format, $userId, $type, serialize($content), $status));
        return $status;
    }


    /**
     * 封装财务用报单log
     *
     * @return bool
     */
    public static function orderLog($order_id, $driver_id, $source, $order_start)
    {
        $envelopeOrderTask = array(
            'method' => 'envelopeOrder',
            'params' => array(
                'order_id' => $order_id,
                'driver_id' => $driver_id,
                'day' => $order_start
            ),
        );

        Queue::model()->putin($envelopeOrderTask, 'orderLog');
    }

    /**
     * 获取日间业务的服务时长
     *
     * @param $order
     * @return float 小时数
     */
    public static function getDayTimeServeHour($order)
    {
        $orderId = isset($order['order_id']) ? $order['order_id'] : 0;
        $orderSnapshoot = OrderSnapshoot::model()->getLatestSnapshoot($orderId);
        if ($orderSnapshoot) {
            $serveTime = isset($orderSnapshoot['serve_time']) ? $orderSnapshoot['serve_time'] : 0.00;//单位为秒
            $waitTime = isset($orderSnapshoot['wait_time']) ? $orderSnapshoot['wait_time'] : 0;//单位为分
            $minutes = ceil($serveTime / 60);//分钟数
            return ceil($minutes / 60);
        }
    }

    /**
     * 输入分钟数,返回小时和分钟的展示
     *
     * @param $minute
     * @return string
     */
    public static function convertTimeString($minute){
        if($minute < 60){
            return $minute.'分钟';
        }else{
            $hour = $minute / 60;
            if(ceil($hour) == $hour){
                return $hour.'小时';
            }else{
                $minute = ($hour - floor($hour)) * 60;
                return floor($hour).'小时'.ceil($minute).'分钟';
            }
        }
    }

    /**
     *
     * 判断某个订单类型,在某个城市是否打开
     *
     * @param $financeOrderSource
     * @param $cityId
     * @return bool
     */
    public static function isModifyFeeOpen($financeOrderSource, $cityId){
        $ret = false;
        $key = self::modifyFeeKeyGen($financeOrderSource, $cityId);
        $lock = FinanceCacheUtils::getLock($key, $ret);
        if(1 == $lock){
            $ret =  true;
        }
        FinanceCacheUtils::setLock($key, $lock, 0);//该 key 永不过期,获取了又设置回去
        return $ret;
    }

    /**
     * 生成调整费的 key
     *
     * @param $financeOrderSource
     * @param $cityId
     * @return string
     */
    public static function modifyFeeKeyGen($financeOrderSource, $cityId){
        return FinanceConstants::CACHE_KEY_MODIFY_SWITCH.$financeOrderSource.'_'.$cityId;
    }

}