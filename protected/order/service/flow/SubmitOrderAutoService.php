<?php

Yii::import("application.models.redis.*");

/**
 * 自动报单服务类
 * 
 * @author zhaoguifu 2015年4月28日
 *
 */
class SubmitOrderAutoService extends BaseFlowService
{
    
    private static $instance;
    public static function getInstance()
    {
        if (empty ( self::$instance ))
        {
            self::$instance = new SubmitOrderAutoService ();
        }
        return self::$instance;
    }
    
    /**
     * 自动报单
     * 
     * @param unknown $param
     * @return string
     */
    public function submit($params)
    {
     //接受参数
     if( isset($params['token'])
             && isset($params['order_number'])
             && isset($params['name'])
             && isset($params['distance'])
             && isset($params['income'])
             && isset($params['price'])
             && isset($params['car_number'])
             && isset($params['cost_type'])
             && isset($params['log'])
             && isset($params['waiting_time'])
             && isset($params['lng'])
             && isset($params['lat'])
             && isset($params['card'])
             && isset($params['other_cost'])
             && isset($params['cost_mark'])){
     
         if(empty($params['token'])) {
             $ret=array('code'=>2, 'message'=>'参数不正确!');
             return $ret;
         }
     
         EdjLog::info('submit order params is '.json_encode($params));
         //判断token存在
         $driver_id = DriverToken::model()->getDriverIdByToken($params['token']);
         if(!$driver_id){
             $ret = array (
                             'code'=>1,
                             'message'=>'请重新登录');
             return $ret;
         }else{
             $params['driver_id'] = $driver_id;
         }
          
         //判断 order_number order_id 是否存在
         if(empty($params['order_id']) && empty($params['order_number'])){
             $ret = array (
                             'code'=>2,
                             'message'=>'订单ID或订单号不能为空');
             return $ret;
         }
          
         $params['start_time'] = isset($params['start_time']) ? $params['start_time'] : 0;
         $params['end_time'] = isset($params['end_time']) ? $params['end_time'] : date('Y-m-d H:i:s');
         $params['gps_type'] = isset($params['gps_type']) ? $params['gps_type'] : "wgs84";
         $params['tip'] = isset($params['tip']) ? $params['tip'] : 0;
         $params['car_cost'] = isset($params['car_cost']) ? $params['car_cost'] : 0;
         $params['log_time'] = $params['end_time'];
          
         //增加等候时间和优惠券张数 BY AndyCong 2013-11-05
         $params['stop_wait_time'] = isset($params['midway_wait_time']) ? trim($params['midway_wait_time']) : ''; //中途等候时间
         $params['coupon'] = isset($params['cash_card']) ? intval($params['cash_card']) : '';                     //现金/定额卡张数
     
         //添加实物劵金额  --mengtianxue 修改时间：2014-03-20
         $params['coupon_money'] = isset($params['cash_card_balance']) ? intval($params['cash_card_balance']) : 0;                     //定额卡可抵扣金额
     
         $params['car_type'] = isset($params['car_type']) ? $params['car_type'] : '';                     //车型
          
         $invoice = isset($params['invoice']) ? $params['invoice'] : false;
         if ($invoice == 'false'){
             $attributes['invoiced'] = 0;
         }else{
             $attributes['invoiced'] = 1;
         }
         //       $params['invoiced'] = $invoice ? '1' : '0'; //发票
     
         $params['cost_mark'] = $params['cost_mark']."[自动]";
         //增加等候时间和优惠券张数 BY AndyCong 2013-11-05 END
     
         $params['ready_time']     = isset($params['ready_time']) ? intval($params['ready_time']) : 0;
         $params['ready_distance'] = isset($params['ready_distance']) ? floatval($params['ready_distance']) : 0.00;
     
         //       $queueProcess = QueueProcess::model()->order_submit($params);
         //选司机下单订单报单 ，将虚拟的order_id转化成数据库中的order_id才可以报单
         if (strlen($params['order_id']) > 11 && is_numeric($params['order_id'])) {
             //获取数据库中的order_id
             $order_id = ROrder::model()->getOrder($params['order_id'] , 'order_id');
             if (empty($order_id)) {
                 $ret = array('code' => 2 , 'message' => '订单异常,请稍后再报单');
                 return $ret;
             }
             $params['unique_order_id'] = $params['order_id'];
             $params['order_id'] = $order_id;
         }
         //校验余额是否足够
         $order=Order::model()->findByPk($params['order_id']);
         // 
         if(empty($order)){
             $order = Order::model()->getOrderByOrderNumberOnly($params['order_number']);//用order_number再查询一遍
             if(empty($order)){
                 $ret=array('code'=>2, 'message'=>'订单ID异常,暂时无法报单!');
                 return $ret;
             }
         }
         
         $booking_time=isset($params['start_time'])?strtotime($params['start_time']):'';
         $driver=Driver::getProfile($params['driver_id']);
         $city_id=$order['city_id'];
         $distance=isset($params['distance'])?$params['distance']:0;
         $wait_time=isset($params['waiting_time'])?$params['waiting_time']:0;
         //计算应该收取的代驾费用 $income
         $income = CityConfigService::calculatorFee($city_id, $distance, $booking_time, $wait_time);
         if(abs($income-$params['income'])>5){
             EdjLog::info("order_id ".$params['order_id']." income $income get arg income ".$params['income']." diff too much");
         }
         if($income<$params['income']){
             $income=$params['income'];
         }
         //计算报单时用户有的钱 
         $deducte_money=0;//余额和优惠券可抵扣的金额，也考虑到了信用额度
         if(!empty($params['cost_type'])){
             // 获取订单优惠信息
             $favorable = Order::model()->getOrderFavorable($order->phone, $order->booking_time, $order->source, $params['order_id']);
             EdjLog::info('favorable for order '.$params['order_id'].' is '.serialize($favorable));
             if($favorable){
                 $deducte_money=$favorable['money']+$favorable['user_money'];
             }
         }
         $user_has_money=$deducte_money+$params['price'];
         if(!empty($params['cash_card_balance'])){
             $user_has_money+=$params['cash_card_balance'];
         }
         $submitRedisTag='';
         if(!empty($params['order_id'])){
             $submitRedisTag.=$params['order_id'];
         }
         if(!empty($params['order_number'])){
             $submitRedisTag.=$params['order_number'];
         }
         
         if(!empty($params['order_id']) && !empty($params['order_number']) ){
             RSubmitOrder::model()->setOrderNumberByOrderId($params['order_id'],$params['order_number']);
         }
         if(!RSubmitOrder::model()->addOrderIdIfNotExist($submitRedisTag)){
             $ret = array (
                             'code'=>0,
                             'message'=>'提交成功！');
             return $ret;
         }
         
         // 余额不足时发送短信 
         $enough = FinanceCastHelper::isMoneyEnough($params['order_id'], $params['income'], $params['price'] + $params['coupon_money']);//需要增加实体券, 这一次查询貌似是多余的
         if(!$enough && $order->status == 0){//避免重复发短信
             $send_phone=$order->phone;
             $vip_info=VipService::service()->getVipInfoByPhone($order->phone);
             if($vip_info&&!empty($vip_info['phone'])&&!empty($vip_info['vipid'])){
                 $vip=VipService::service()->getVipInfo($vip_info['vipid']);
                 if($vip){
                     $send_phone=$vip['phone'];
                 }
             }
             $orderExt = OrderExt::model()->getPrimary($params['order_id']);
             //报单的时候这两个值还没有写入数据库,需要手动写入
             $order['income'] = $params['income'];
             $order['price']  = $params['price'];
             $orderIncome = FinanceCastHelper::getOrderIncome($order, $orderExt);
             $needCharge = $orderIncome - $user_has_money;
             if($needCharge > 0 ){
                 $content=sprintf("尊敬的用户，您的订单因为您余额不足而无法报单支付，订单号".$order->order_id."，预计缺少".sprintf("%.2f", $needCharge)."元人民币,请您及时充值，以便我们更好为您服务");
                 Sms::SendSMS($send_phone,$content);
             }
         }
         //添加task队列向数据中添加
         $task = array(
           'class' => __CLASS__,
           'method'=>'orderSubmitJob',
           'params'=>$params
         );
         Queue::model()->putin($task,'settlement');
         
         //
         $snapshootAttributes = array();
         $startTime = strtotime($params['start_time']);
         $endTime   = strtotime($params['end_time']);
         //司机端在某些极端情况会出现没有 end_time 的时候,这里进行兼容
         if($endTime < $startTime){
             $endTime = $startTime;
         }
         $snapshootAttributes['income'] = $params['income'];
         $snapshootAttributes['start_time']  = $startTime;
         $snapshootAttributes['end_time']    = $endTime;
         $snapshootAttributes['wait_time']   = $wait_time;
         $snapshootAttributes['price']       = $params['price'];
         $snapshootAttributes['coupon_money'] = $params['coupon_money'] ;
         $snapshootAttributes['serve_time']   = $endTime - $startTime; //服务时间
         $waitPrice = isset($params['wait_price']) ? $params['wait_price'] : 0.00;
         $snapshootAttributes['wait_price']   = $waitPrice;
         $modifyType  = isset($params['modify_type']) ? $params['modify_type'] : 0;
         $modifyAmount = isset($params['modify_price']) ? $params['modify_price'] : 0;
         $modifyName   = isset($params['modify_name']) ? $params['modify_name'] : '';
         $fee = OrderExt::model()->getBadWeatherSurchargeByOrderId($params['order_id']);//是否恶劣天气加价的金额
         if(!$fee){
             //如果没有恶劣天气加价那么$modifyType就代表调整费
             if(0 != $modifyType){//保存调整价
                 $modifyArr = array(
                                 'type'	=> $modifyType,
                                 'amount' => $modifyAmount,
                                 'name'   => $modifyName,
                 );
                 $metaArr = array();
                 $metaArr['modify_fee'] = $modifyArr;
                 $snapshootAttributes['meta'] = json_encode($metaArr);
             }
         }
         @OrderSnapshoot::model()->saveSnapshoot($params['order_id'], $order['source'], $order['channel'], $params['income'], $snapshootAttributes);
         // TODO refactor 
         FinanceUtils::orderLog($params['order_id'],$driver_id,$order->source,strtotime($params['start_time']));
         //保存的额外信息
         $dumpTask = array(
                         'class'     => __CLASS__,
                         'method'    => 'saveOrderInfoJob',
                         'params'    => array(
                                         'order_id' => $order['order_id'],
                                         'car_number' => isset($params['car_number']) ? $params['car_number'] : '',
                                         'car_type' => isset($params['car_type']) ? $params['car_type'] : '',
                                         'order_detail' => isset($params['order_detail']) ? $params['order_detail'] : '',
                                         'driver_id'    => $driver_id,
                                         'comment' => isset($params['remark']) ? $params['remark'] : '',
                                         'customer_status' => isset($params['customer_status']) ? $params['customer_status'] : '',
                                         'deduct_wealth'   => isset($params['deduct_wealth']) ? $params['deduct_wealth'] : 0 ,
                         ),
         );
     
         Queue::model()->putin($dumpTask,'dumplog');
         
         // 
         $ret = array (
                         'code'=>0,
                         'message'=>'提交成功！');
         return $ret;
          
     }else{
     
         //添加task队列向数据中添加
         $task = array( 
            'class'     => __CLASS__,
            'method'    => 'orderSubmitTrackingJob',
            'params'    => $params
         );
         Queue::model()->putin($task,'tmporder');
     
         // 
         $ret = array(
                         'code' => 2,
                         'message' => '参数不正确');
         return $ret;
     }
     
    }
    
    /**
     * 自动报单结算
     * 
     * @param unknown $params
     * @return string
     */
    public function submitSettle($params) {
     EdjLog::info('submit_settle '.json_encode($params));
     //1.check params
     if($this->checkParams($params)){
         $ret=array('code'=>2, 'message'=>'参数不正确!');
         return $ret;
     }
     //2.校验 driver token
     $token  = $params['token'];
     $driver_id = DriverToken::model()->getDriverIdByToken($token);
     if(!$driver_id){
         $ret = array (
                         'code'=>1,
                         'message'=>'请重新登录');
         return $ret;
     }else{
         $params['driver_id'] = $driver_id;
     }
     
     //判断 order_number order_id 是否存在
     if(empty($params['order_id']) && empty($params['order_number'])){
         $ret = array (
                         'code'=>2,
                         'message'=>'订单ID或订单号不能为空');
         return $ret;
     }
     
     //选司机下单订单报单 ，将虚拟的order_id转化成数据库中的order_id才可以报单
     if (strlen($params['order_id']) > 11 && is_numeric($params['order_id'])) {
         //获取数据库中的order_id
         EdjLog::info('order_id too long , order_id is  '.$params['order_id']);
         $order_id = ROrder::model()->getOrder($params['order_id'] , 'order_id');
         if (empty($order_id)) {
             $ret = array('code' => 2 , 'message' => '订单异常,请稍后再报单');
             return $ret;
         }
         $params['unique_order_id'] = $params['order_id'];
         $params['order_id'] = $order_id;
     }
     
     
     $submitRedisTag='';
     if(!empty($params['order_id'])){
         $submitRedisTag.=$params['order_id'];
     }
     if(!empty($params['order_number'])){
         $submitRedisTag.=$params['order_number'];
     }
     
     if(!empty($params['order_id']) && !empty($params['order_number']) ){
         RSubmitOrder::model()->setOrderNumberByOrderId($params['order_id'],$params['order_number']);
     }
     if(!RSubmitOrder::model()->addOrderIdIfNotExist($submitRedisTag)){
         $ret = array (
                         'code'=>0,
                         'message'=>'提交成功！');
         return $ret;
     }
     
     $orderId = $params['order_id'];
     $channel = $params['channel'];
     $source  = $params['source'];
     EdjLog::info('before  ger order ---- '.$orderId);
     $order   = Order::model()->getOrderById($orderId);
     if (empty($order)) {
         $ret = array('code' => 2 , 'message' => '订单异常,请稍后再报单');
         return $ret;
     }
     // check money
     
     //3.锁用户/和订单
     //4.看用户的余额是否足够,不够返回错误提示,已经发提示短信  
     $price = $params['price'];
     $couponMoney = $params['cash_card_balance'];
     $totalExcepted = $params['income'];//总费用,也是必须传的
     $userMoney = FinanceCastHelper::getMoneyOfUser($orderId, $order['phone']) + $price + $couponMoney;//TODO ... 现金和实体卡是否应该后面来加
     if(($userMoney < $totalExcepted) && ($order['status'] == Order::ORDER_READY )){
         $send_phone = $order['phone'];
         $vip_info=VipService::service()->getVipInfoByPhone($order['phone']);
         if($vip_info&&!empty($vip_info['phone'])&&!empty($vip_info['vipid'])){
             $vip=VipService::service()->getVipInfo($vip_info['vipid']);
             if($vip){
                 $send_phone=$vip['phone'];
             }
         }
         $orderExt = OrderExt::model()->getPrimary($params['order_id']);
         //报单的时候这两个值还没有写入数据库,需要手动写入
         $needCharge = $totalExcepted - $userMoney;
         if($needCharge > 0 ){
             $content=sprintf("尊敬的用户，您的订单因为您余额不足而无法报单支付，订单号".$order['order_id']."，预计缺少".sprintf("%.2f", $needCharge)."元人民币,请您及时充值，以便我们更好为您服务");
             Sms::SendSMS($send_phone, $content);
         }
     }
     $waitingTime = isset($params['waiting_time']) ? $params['waiting_time'] : 0;

     $endTime = round($params['end_time'] / 1000);// 司机端上传上来的是毫秒级的
     $startTime = round($params['start_time'] / 1000);
     //司机端在某些极端情况会出现没有 end_time 的时候,这里进行兼容
     if($endTime < $startTime){
         $endTime = $startTime;
     }
        $snapshootAttributes = SettleService::getLegalParamList($order, $params);
     $tmpArr = $snapshootAttributes;
     $snapshootAttributes['income'] = $totalExcepted;
     $snapshootAttributes['start_time']  = $startTime;
     $snapshootAttributes['end_time']    = $endTime;
     $snapshootAttributes['wait_time']   = $waitingTime;
     $snapshootAttributes['price']       = $price;
     $snapshootAttributes['coupon_money'] = $couponMoney;
     $snapshootAttributes['serve_time']   = $endTime - $startTime; //服务时间
     $waitPrice = isset($params['wait_price']) ? $params['wait_price'] : 0.00;
     $snapshootAttributes['wait_price']   = $waitPrice;
     $modifyType  = isset($params['modify_type']) ? $params['modify_type'] : 0;
     $modifyAmount = isset($params['modify_price']) ? $params['modify_price'] : 0;
     $modifyName   = isset($params['modify_name']) ? $params['modify_name'] : '';
     $beyond_time_cost = isset($params['beyond_time_cost']) ? $params['beyond_time_cost'] : 0;// 超过基础时间60分钟收取的费用
     $beyond_distance_cost = isset($params['beyond_distance_cost']) ? $params['beyond_distance_cost'] : 0;//超过基础距离10km收取的费用
     $distance = isset($params['distance']) ? $params['distance'] : 0;//订单总距离
     $cityId = isset($order['city_id']) ? $order['city_id'] : 0;//取司机的城市id从$driver取改为取订单的cityid
     $app_ver = isset($params['app_ver'])?$params['app_ver']:'0';
     $subsidy_hour = 0;
     $beyond_time = 0;
     $beyond_distance = 0;
     $metaArr = array();//专门用来保存meta的json
     if(version_compare($app_ver,'2.5.4') > 0){
         $subsidy_hour = 1;//为1的时候取消补贴
         $daytime_type = CityConfigService::dayTimeStatus($cityId);
         if($daytime_type && isset(Yii::app()->params['daytime_price_new'][$daytime_type])){
             $day_time_data = Yii::app()->params['daytime_price_new'][$daytime_type];
             $basic_time  = $day_time_data['basic_time']; // 基础时间60
             $basic_distance  = $day_time_data['basic_distance']; // 基础距离10
     
             $beyond_distance = $distance - $basic_distance;//超过的距离
             if((($endTime - $startTime)/60) -$basic_time > 0){
                 $beyond_time = (($endTime - $startTime)/60) - $basic_time;//超过的时间
             }
         }
         if($beyond_time_cost > 0 || $beyond_distance_cost > 0){
             $metaArr = @OrderSnapshoot::model()->appendSnapshootDetail($beyond_time,$beyond_time_cost,$beyond_distance,$beyond_distance_cost,$metaArr);
         }
     }
     $fee = OrderExt::model()->getBadWeatherSurchargeByOrderId($params['order_id']);//是否恶劣天气加价的金额
     if(!$fee){
         if(0 != $modifyType){
             //保存调整价
             $modifyArr = array(
                             'type'	=> $modifyType,
                             'amount' => $modifyAmount,
                             'name'   => $modifyName,
             );
             $metaArr['modify_fee'] = $modifyArr;
         }
     }
     if(!empty($metaArr)){
         $snapshootAttributes['meta'] = json_encode($metaArr);//统一保存meta信息如:{"modify_fee":{"type":1,"amount":2,"name":3},"daytime_beyondcost":{"beyond_time_cost":"a","beyond_distance_cost":"b"},}
     }
     $status = @OrderSnapshoot::model()->saveSnapshoot($orderId, $source, $channel, $totalExcepted, $snapshootAttributes);
     if(!$status){
         $ret = array(
                         'code' => 2,
                         'message' => '提交失败,请重试',
         );
         return $ret;
     }
     //添加task队列向数据中添加//6.发布异步结账队列
     $taskParams = array(
                     'order_id' => $orderId,
                     'order_number' => isset($params['order_number']) ? $params['order_number'] : 0,
                     'driver_id'    => $driver_id,
                     'lat'          => $params['lat'],
                     'lng'          => $params['lng'],
                     'gps_type'     => isset($params['gps_type']) ? $params['gps_type'] : 'baidu',
                     'end_time'     => date('Y-m-d H:i:s', $endTime),
                     'start_time'     => date('Y-m-d H:i:s', $startTime),
                     'distance'     => $params['distance'],
                     'cost_type'    => $params['cost_type'],
                     'log_time'     => date('Y-m-d H:i:s', $endTime),
                     'name'         => $params['name'],
                     'car_type'     => isset($params['car_type']) ? $params['car_type'] : 0,
                     'car_number'   => isset($params['car_number']) ? $params['car_number'] : 0 ,
                     'income'       => $totalExcepted,
                     'price'        => isset($params['price']) ? $params['price'] : 0,
                     'waiting_time' => $waitingTime,
                     'tip'          => isset($params['tip']) ? $params['tip'] : 0,
                     'car_cost'     => isset($params['car_cost']) ? $params['car_cost'] : 0, //order_ext 里面 没有使用
                     'other_cost'   => isset($params['other_cost']) ? $params['other_cost'] : 0,//order_ext 里面插入都是最后一次是13年
                     'cost_mark'    => isset($params['cost_mark']) ? $params['cost_mark'] : 0,
                     'card'		   => isset($params['card']) ? $params['card'] : 0,
                     'invoiced'	   => isset($params['invoice']) ? intval($params['invoice']) : '', //发票
                     'coupon_money' => $couponMoney,
                     'subsidy_hour' => $subsidy_hour,
     );
     
     $taskParams = array_merge($taskParams, $tmpArr);
     
     $task = array( 
        'class' => __CLASS__,
        'method'=>'orderSubmitJob',
        'params'=>$taskParams,
     );
     
     Queue::model()->putin($task,'settlement');
     
     $dumpTask = array(
                     'class'     => __CLASS__,
                     'method'    => 'saveOrderInfoJob',
                     'params'    => array(
                                     'order_id' => $orderId,
                                     'car_number' => isset($params['car_number']) ? $params['car_number'] : '',
                                     'car_type' => isset($params['car_type']) ? $params['car_type'] : '',
                                     'order_detail' => isset($params['order_detail']) ? $params['order_detail'] : '',
                                     'driver_id'    => $driver_id,
                                     'comment' => isset($params['remark']) ? $params['remark'] : '',
                                     'customer_status' => isset($params['customer_status']) ? $params['customer_status'] : '',
                     ),
     );
     Queue::model()->putin($dumpTask,'dumplog');
     
     // TODO refactor 
     FinanceUtils::orderLog($orderId,$driver_id,$source,$startTime);
     $ret = array (
                     'code'=>0,
                     'message'=>'提交成功！');
     return $ret;
    }
    
    /**
     * 检查参数，供submitSettle方法调用 
     * 
     * @param unknown $params
     * @return boolean
     */
    function checkParams($params = array())
    {
        return (!isset($params['token'])
                || !isset($params['order_number'])
                || !isset($params['order_id'])
                || !isset($params['source'])
                || !isset($params['channel'])
                || !isset($params['income'])
                || !isset($params['lat'])
                || !isset($params['lng'])
                || !isset($params['cost_type'])
                || !isset($params['name'])
        );
    }
    
    /**
     * 司机报单
     * 
     * copy from QueueProcess 
     * 
     * @param $params
     * @return bool
     */
    public function orderSubmitJob($params){
        // Check if the driver id is bind with the order
        $order = NULL;
        if(empty($params['order_id'])){
            // TODO refactor move sql to model 
            $order = Order::model()->getOrderByNumber($params['order_number']);
            if(empty($order)){
                EdjLog::info('|'.$params['order_number'].
                '|Cannot find order with order number' , 'console');
                return false;
            }else{
                $params['order_id'] = $order['order_id'];
            }
        } else {
            $order =  Order::model()->getOrderById($params['order_id']);
        }
        if(empty($order)){
            EdjLog::info('|'.$params['order_id'].'|Cannot find order with order id' , 'console');
            return false;
        }
    
        if($order['driver_id'] == Push::DEFAULT_DRIVER_INFO &&
                $params['driver_id'] != Push::DEFAULT_DRIVER_INFO){
            // Rebind
            // TODO refactor move sql to model 
            $map = OrderQueueMap::model()->getMapsByOrderId($params['order_id']);
            if(!empty($map)) {
                EdjLog::info('|'.$params['order_id'].'|'.$params['driver_id'].
                '|Rebind order receveive actions' , 'console');
                Push::model()->redoOrderReceive($map['queue_id'],
                $params['order_id'], $params['driver_id']);
            }
        }else if($order['driver_id'] == Push::DEFAULT_DRIVER_INFO &&
                $params['driver_id'] == Push::DEFAULT_DRIVER_INFO){
            EdjLog::info('|'.$params['order_id'].'|Cannot bind the driver, Both driver ids are default' , 'console');
        } else {
            // Normal case, ignore it
        }
    
        // End bind
    
        //记录log
        EdjLog::info($params['order_id'].'|501司机保单|'.$params['driver_id'].'|begin' , 'console');
    
        $gps_position=array(
                        'latitude'=>$params['lat'],
                        'longitude'=>$params['lng']
        );
    
        $gps = GPS::model()->convert($gps_position, $params['gps_type']);
        $params['location_end'] = empty($gps) ? '' : $gps['street'];
        $params['end_time'] = isset($params['end_time']) ? $params['end_time'] : date('Y-m-d H:i:s');
    
        $return = Order::model()->submit_order($params);
    
        //选司机下单 推送虚拟的order_id
        if (isset($params['unique_order_id'])) {
            AutoOrder::model()->push_order_submit($params['driver_id'], $params['unique_order_id'], $return);
            DalOrder::model()->updateOldCacheData('' , $params['unique_order_id'] , '' , OrderProcess::ORDER_PROCESS_FINISH, '', $params['location_end']);
    
            //更新订单状态机 by wangjian 2014-03-26
            // 2014-03-26 BEGIN
            if ($return) {
                $real_order_id = ROrder::model()->getOrder($params['unique_order_id'] , 'order_id');
                if (empty($real_order_id)) {
                    EdjLog::warning($params['order_id'].'|501司机自动报单|'.$params['driver_id'].'|状态机更新获取real_order_id失败|end' , 'console');
                    $real_order_id = $params['order_id'];
                }
                OrderProcess::model()->genNewOrderProcess(
                array( 'queue_id'  => $real_order_id,
                'order_id'  => $real_order_id,
                'driver_id' => $params['driver_id'],
                'state'     => OrderProcess::PROCESS_AUTO_SUBMIT,
                'created'=>date('Y-m-d H:i:s' , time()),
                )
                );
            }
            // 2014-03-26 END
        } else {
            AutoOrder::model()->push_order_submit($params['driver_id'], $params['order_id'], $return);
            CustomerApiOrder::model()->updateOrderRedisByOrderFlag(
            $params['order_id'] , OrderProcess::ORDER_PROCESS_FINISH, $params['driver_id'], '', 0,
            $params['location_end']
            );
        }
    
        if ($return) {
            //记录log
            EdjLog::info($params['order_id'].'|501司机保单|'.$params['driver_id'].'|报单成功|end' , 'console');
            $this->badWeatherChargeDrvier($order);
        } else {
            //记录log
            EdjLog::warning($params['order_id'].'|501司机保单|'.$params['driver_id'].'|报单失败|end' , 'console');
        }
    
        return $return;
    }
    
    /**
     * 恶劣天气加价 但是vip客户下单不能加价，所以加价费公司以信息费冲入司机账户
     * @param $order
     */
    public  function  badWeatherChargeDrvier($order){
        $fee = OrderExt::model()->getBadWeatherSurchargeByOrderId($order['order_id']);//是否恶劣天气加价的金额
        $phone = isset($order['phone']) ? $order['phone'] : '';
        if($fee){
            $isVip = CustomerMain::model()->isVip($phone);
            if($isVip){
                //如果该单符合恶劣天气条件下单但是下单客户为vip客户，那么加价的金额以信息费冲入司机账户
                $smsComment = "师傅您好单号:".$order['order_id']."的订单满足恶劣天气加价要求，加价金额".$fee."元作为信息费已经充入您的信息费账户!";
                $channel = EmployeeAccount::CHANNEL_DRIVER_MODIFY_FEE;
                $comment = "恶劣天气加价但客户为vip下单加价金额作为信息费冲入司机账户 单号:".$order['order_id'].".";
                //TODO ...  need charge driver, 是一种充值的类型
                $status = DriverAccountService::orderCharge($fee, $order, $comment, $channel);
                if($status){
                    $driver_phone = $order['driver_phone']; 
                    Sms::SendSMS($driver_phone,$smsComment); 
                }
            }
        }
    }
    
    /**
     * 异步保存订单额外信息
     * 
     * copy from QueueProcess 
     *
     * @param $params
     */
    public function saveOrderInfoJob($params){
        $orderId = isset($params['order_id']) ? $params['order_id'] : 0;
        $driver_id = isset($params['driver_id']) ? $params['driver_id'] : 0;
        //如果车牌不符合规则则扣除50e币  约定:deduct_Wealth为0不扣  为1扣除
        $deduct_Wealth = isset($params['deduct_wealth']) ? $params['deduct_wealth'] : 0 ;
        if($deduct_Wealth){
            DriverWealthLog::model()->deductWealth($driver_id,$orderId);
        }
        if(!empty($orderId)){
            $attributes = $params;
            $driverId = $params['driver_id'];
            OrderExtra::model()->saveOrderInfo($orderId, $driverId, $attributes);
            $order_detail = isset($params['order_detail']) ?  $params['order_detail'] : '';
            if(!empty($order_detail)){
                $res = DriverWealthLog::model()->addDayOrderWealth($driverId, $orderId);
                if(1 != $res){
                    EdjLog::error("add e money error order_id $orderId, driver_id: $driverId ");
                }
            }
        }
    }
    
    /**
     * tracking 报单
     * 
     * copy from QueueProcess 
     *
     * @param unknown_type $params
     */
    public function orderSubmitTrackingJob($params)
    {
        echo "\n---";
        echo isset($params['order_id']) ? "---order_id:".$params['order_id'] : '';
        echo isset($params['order_number']) ? "---order_num:".$params['order_number'] : '';
        echo "---\n";
    }

}