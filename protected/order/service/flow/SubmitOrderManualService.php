<?php

Yii::import("application.models.redis.*");
Yii::import('application.models.pay.calculator.*');

/**
 * 手工报单服务类 
 * 
 * @author zhaoguifu 2015年4月28日
 *
 */
class SubmitOrderManualService extends BaseFlowService
{
    private static $instance;
    public static function getInstance()
    {
        if (empty ( self::$instance ))
        {
            self::$instance = new SubmitOrderManualService ();
        }
        return self::$instance;
    }
    
    /**
     * 手动报单
     * 
     * @param unknown $param
     * @return string
     */
    public function submit($params) {
     if(empty($params['token'])){
         $ret=array('code'=>2 , 'message'=>'参数不正确!');
         return $ret;
     }
     
     EdjLog::info('driver.order.report params is '.json_encode($params));
     // TODO refactor 
     $driver_id = DriverToken::model()->getDriverIdByToken($params['token']);
     $ret = array();
     if ($driver_id) {
         $driver = Driver::getProfile($driver_id);
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
                             'message'=>'订单已处理');
             return $ret;
         }
         if ($params['order_id']) {
             $order = Order::model()->getOrderInfomation($params['order_id'], $driver_id);
     
             if ($order) {
                 $canSettle = SettleService::canSettle($order);
                 if (!$canSettle) {
                     $ret = array(
                                     'code' => 0,
                                     'message' => '订单已处理');
                     RSubmitOrder::model()->delOrderId($submitRedisTag);
                     return $ret;
                 }
                 if ($params['location_start'] != ''
                         && $params['location_end'] != '' && $params['distance'] != '' && $params['price'] != ''
                 ) {
     
                     if (empty($params['order_number'])) {
                         $params['order_number'] = $order->order_id;
                     }
     
                     //检查工单号是否已经使用
                     $ret = Order::model()->find('order_number=:order_number and order_id != :order_id', array(
                                     ':order_number' => $params['order_number'],
                                     ':order_id' => $order->order_id));
     
                     if ($ret) {
                         $ret = array(
                                         'code' => 2,
                                         'message' => '工单号已使用');
                         RSubmitOrder::model()->delOrderId($submitRedisTag);
                         return $ret;
                     }
                     //给字段赋信息
                     $attributes = array();
                     $attributes['cost_type'] = 0;
                     //$deducte_money vip和优惠劵可抵扣的金额  $income 是根据时间和公里算数的代驾费用
                     // $price 是代驾受到的实际现金
                     $deducte_money = $income = 0;
                     if (isset($params['cost_type']) && !empty($params['cost_type'])) {
                         $favorable = Order::model()->getOrderFavorable($order->phone, $order->booking_time, $order->source, $params['order_id']);
                         if ($favorable) {
                             $attributes['cost_type'] = $favorable['code'];
                             $order->cost_type = $favorable['code'];
                             $deducte_money = $favorable['money'] + $favorable['user_money'];
     
                             switch ($favorable['code']) {
                                 case 1:
                                     $order->vipcard = $favorable['card'];
                                     $order->name = $favorable['name'];
                                     $attributes['vipcard'] = $favorable['card'];
                                     break;
                                 case 2:
                                     $order->bonus_code = $favorable['card'];
                                     break;
                                 case 4:
                                     $order->bonus_code = $favorable['card'];
                                     break;
                                 case 8:
                                     break;
                             }
                         }
                     }
     
                     $orderId = $params['order_id'];
                     $attributes['order_number'] = $params['order_number'];
                     $attributes['name'] = $params['name'];
                     $attributes['location_start'] = $params['location_start'];
                     $attributes['location_end'] = $params['location_end'];
                     $attributes['distance'] = $params['distance'];
                     $attributes['price'] = empty($params['price']) ? 0 : $params['price'];
                     $attributes['car_number'] = $params['car_number'];
                     $attributes['log'] = $params['log'];
                     $attributes['wait_time'] = $params['waiting_time'];
                     $attributes['end_time'] = isset($params['end_time']) ? strtotime($params['end_time']) : 0;
                     $attributes['start_time'] = isset($params['start_time']) ? strtotime($params['start_time']) : 0;
     
                     //添加实物劵金额  --mengtianxue 修改时间：2014-03-20
                     $attributes['coupon_money'] = isset($params['cash_card_balance']) ? intval($params['cash_card_balance']) : 0.00; //定额卡可抵扣金额
                     $attributes['price']=$attributes['price']-$attributes['coupon_money'];
     
                     $invoice = isset($params['invoice']) ? $params['invoice'] : false;
                     //                $attributes['invoiced'] = $invoice ? '1' : '0'; //发票
                     if ($invoice == 'false'){
                         $attributes['invoiced'] = 0;
                     }else{
                         $attributes['invoiced'] = 1;
                     }
                     //$attributes['coupon']   = isset($params['cash_card']) ? intval($params['cash_card']) : '';//现金/定额卡张数
     
                     $city_id = $order['city_id'];
                     $wait_time = empty($attributes['wait_time']) ? 0 : $attributes['wait_time'];
     
                     //计算价格时候的时间  默认是开车时间，如果没有开车时间或者格式不对，选预约时间  --孟天学 2013-09-02
                     $booking_time = $attributes['start_time'];
     
                     // 代驾费用
                     $income = CityConfigService::calculatorFee($city_id, $attributes['distance'], $booking_time, $wait_time);
                     //计算了 income 之后, 还应该考虑原有的调整费, 调整费,在退单时候会退
                     $tmpMeta = FinanceCastHelper::getOrderModifyFee($orderId);
                     if($tmpMeta){
                         $modifyFee = isset($tmpMeta['amount']) ? $tmpMeta['amount'] : 0;
                         $income = $income + $modifyFee * -1;
                     }
                     // cost_type 是否用vip或优惠劵结账    0是不用 否则为使用
                     $price = $attributes['price'] + $attributes['coupon_money'];
                     if ($attributes['cost_type'] == 0) {
                         $total_money = $price;
                     } else {
                         if($income > $price){
                             //要收取帐户金额
                             $balance = $income - $price;
                             if ($balance > $deducte_money) {
                                 $total_money = $deducte_money + $price;
                             } else {
                                 $total_money = $income;
                             }
                         }else{
                             $total_money = $price;
                         }
     
                         //                    if ($income > $deducte_money) {
                         //                        $total_money = $deducte_money + $attributes['price'] + $attributes['coupon_money'];
                         //                    } else {
                         //                        $total_money = $income + $attributes['price'] + $attributes['coupon_money'];
                         //                    }
                     }
                     EdjLog::info(sprintf(' report money infoes |calculate income|%s|price|%s|deducte_money|%s|total_money|%s',
                     $income, $price, $deducte_money,$total_money));
                     //添加录入代驾费 小于（计算费用) 提示错误
                     $enough = FinanceCastHelper::isMoneyEnough($params['order_id'], $income, $price);
                     $orderExt = OrderExt::model()->getPrimary($params['order_id']);
                     //TODO ... 这个是应该采用新上传上来的值
                     if($attributes['cost_type'] == 0){//如果是纯现金支付,那么现金不能小于里程费
                         $order['income'] = $income;
                         $order['price']  = $attributes['price'];//这里加上了实体卡金额
                         $orderMoney = FinanceCastHelper::getOrderIncome($order, $orderExt);
                         if($price < $orderMoney){
                             $enough = false;
                         }
                     }
                     if (!$enough) {
                         $ret = array(
                                         'code' => 2,
                                         'message' => '输入的代驾费有误'
                         );
                         RSubmitOrder::model()->delOrderId($submitRedisTag);
                         //					//朝用户推送短信
                         //					echo json_encode($ret);
                         //					$send_phone = $order->phone;
                         //					$vip_info = VipPhone::model()->getVipInfoByPhone($order->phone);
                         //					if ($vip_info && !empty($vip_info['phone']) && !empty($vip_info['vipid'])) {
                         //						$vip = Vip::model()->getPrimary($vip_info['vipid']);
                         //						if ($vip) {
                         //							$send_phone = $vip['phone'];
                         //						}
                         //					}
                         //					$content = sprintf("尊敬的用户，您的订单因为您余额不足而无法报单支付，订单号" . $order->order_id . "，预计缺少" . sprintf('%.2f', $income - $total_money) . "元人民币,请您及时充值，以便我们更好为您服务");
                         //					Sms::SendSMS($send_phone, $content);
                         return $ret;
                 }
     
                 $attributes['income'] = $total_money;
                 $isRemote = FinanceUtils::isRemoteOrder($order, $orderExt);
                 if($isRemote){
                     $attributes['income'] = $income;//远程订单的 price 可能大于 income
                 }
                 $attributes['city_id'] = $city_id;
     
                 $attributes['ready_time']     = isset($params['ready_time']) ? intval($params['ready_time']) : 0;
                 $attributes['ready_distance'] = isset($params['ready_distance']) ? floatval($params['ready_distance']) : 0.00;
     
                 $snapshootAttributes = array();
                 $startTime = $attributes['start_time'];
                 $endTime   = $attributes['end_time'];
                 //司机端在某些极端情况会出现没有 end_time 的时候,这里进行兼容
                 if($endTime < $startTime){
                     $endTime = $startTime;
                 }
                 $snapshootAttributes['income'] = $attributes['income'];
                 $snapshootAttributes['start_time']  = $startTime;
                 $snapshootAttributes['end_time']    = $endTime;
                 $snapshootAttributes['wait_time']   = $attributes['wait_time'];
                 $snapshootAttributes['price']       = $attributes['price'];
                 $snapshootAttributes['coupon_money'] = $attributes['coupon_money'] ;
                 $snapshootAttributes['serve_time']   = $endTime - $startTime; //服务时间
                 $waitPrice = isset($attributes['wait_price']) ? $attributes['wait_price'] : 0.00;
                 $snapshootAttributes['wait_price']   = $waitPrice;
                 $snapshootAttributes['meta']         = json_encode(OrderSnapshoot::model()->getSnapshootMeta($orderId));//如果 meta 字段包含了其他的需要更新
                 $source = isset($order['source'])? $order['source'] : 0;
                 $channel = isset($order['channel'])? $order['channel'] : 0;
                 $retStatus = OrderSnapshoot::model()->saveSnapshoot($orderId, $source, $channel, $snapshootAttributes['income'], $snapshootAttributes);
                 if(!$retStatus){
                     $ret = array(
                                     'code' => 2,
                                     'message' => '提交失败,请重试',
                     );
                     return $ret;
                 }
                 $order_status = Order::model()->report($order, $attributes, TRUE);
                 if ($order_status) {
                     $orderId = $order['order_id'];
                     //清除订单详情缓存
                     OrderCache::model()->delOrderInfoAttribute($orderId);
                     //清除客户端历史订单缓存
                     CustomerApiOrder::model()->deleteOrderInfoByOrderID($orderId);
                     //更新redis BY AndyCong
                     $task = array(  'class'  => __CLASS__,
                                     'method' => 'updateOrderRedisJob',
                                     'params' => array(
                                                     'order_id' => $order->order_id,
                                                     'order_state' => OrderProcess::ORDER_PROCESS_FINISH,
                                                     'order_process_flag' => OrderProcess::FLAG_PROCESS_DRIVER_SUBMIT,
                                                     'driver_id' => $driver_id,
                                     ),
                     );
                     Queue::model()->putin($task, 'apporder');
                     //更新redis BY AndyCong
                     //保存的额外信息
                     $dumpTask = array(
                                     'class'  => 'SubmitOrderAutoService',
                                     'method'    => 'saveOrderInfoJob',
                                     'params'    => array(
                                                     'order_id' => $order['order_id'],
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
                     FinanceUtils::orderLog($params['order_id'],$driver_id,$order->source,$attributes['start_time']);
     
                     $ret = array(
                                     'code' => 0,
                                     'message' => '报单完成');
                 } else {
                     $ret = array(
                                     'code' => 2,
                                     'message' => '信息不完善');
                 }
             } else {
                 $ret = array(
                                 'code' => 2,
                                 'message' => '信息不完善');
             }
         } else {
             $ret = array(
                             'code' => 2,
                             'message' => '订单号有误');
         }
     } else {
         $ret = array(
                         'code' => 2,
                         'message' => '请传入订单号');
     }
     } else {
         $ret = array(
                         'code' => 1,
                         'message' => '请重新登录');
     }
     $code = isset($ret['code']) ? $ret['code'] : 0;
     //报错就删除redis里面的锁
     if( 0 != $code){
         RSubmitOrder::model()->delOrderId($submitRedisTag);
     }
     return $ret;
        
    }
    
    /**
     * 手动报单结算 
     * 
     * @param unknown $params
     */
    public function submitSettle($params) {
     
     EdjLog::info('driver.order.report_settel import args is '.json_encode($params));
     // check params
     if ($this->checkParams($params)) {
         $ret = array('code' => 2, 'message' => '参数不正确!');
         return $ret;
     }
     //check driver token
     $driver_id = DriverToken::model()->getDriverIdByToken($params['token']);
     if(empty($driver_id)){
         $ret = array(
                         'code' => 1,
                         'message' => '请重新登录');
         return $ret;
     }
     //lock order
     $orderId = $params['order_id'];
     $order = Order::model()->getOrderInfomation($orderId, $driver_id);
     $orderNumber = isset($order['order_number']) ? $order['order_number'] : 0;
     if(empty($order)){
         $ret = array(
                         'code' => 2,
                         'message' => '订单号有误');
         return $ret;
     }
     $submitRedisTag = '';
     $submitRedisTag .= $orderId;
     if(!empty($orderNumber)){
         $submitRedisTag .= $orderNumber;
     }
     
     if(!empty($params['order_id']) && !empty($params['order_number']) ){
         RSubmitOrder::model()->setOrderNumberByOrderId($params['order_id'],$params['order_number']);
     }
     if(!RSubmitOrder::model()->addOrderIdIfNotExist($submitRedisTag)){
         $ret = array (
                         'code'=>0,
                         'message'=>'订单已处理');
         return $ret;
     }
     $driver = Driver::getProfile($driver_id);
     //check order
     $status = $order['status'];
        $canSettle = SettleService::canSettle($order);
     if (!$canSettle) {
         $ret = array(
                         'code' => 0,
                         'message' => '订单已处理');
         RSubmitOrder::model()->delOrderId($submitRedisTag);
         return $ret;
     }
     
     if(empty($orderNumber)){
         $orderNumber = $orderId;
     }
     
     //检查工单号是否已经使用
     $ret = Order::model()->find('order_number=:order_number and order_id != :order_id', array(
                     ':order_number' => $orderNumber,
                     ':order_id' => $orderId));
     
     if ($ret) {
         $ret = array(
                         'code' => 2,
                         'message' => '工单号已使用');
         RSubmitOrder::model()->delOrderId($submitRedisTag);
         return $ret;
     }
     
     $attributes = array();
     $attributes['cost_type'] = 0;
     $deducte_money = $income = 0;
     if (isset($params['cost_type']) && !empty($params['cost_type'])) {
         $favorable = Order::model()->getOrderFavorable($order->phone, $order->booking_time, $order->source, $params['order_id']);
         if ($favorable) {
             $attributes['cost_type'] = $favorable['code'];
             $order->cost_type = $favorable['code'];
             $deducte_money = $favorable['money'] + $favorable['user_money'];
     
             switch ($favorable['code']) {
                 case 1:
                     $order->vipcard = $favorable['card'];
                     $order->name = $favorable['name'];
                     $attributes['vipcard'] = $favorable['card'];
                     break;
                 case 2:
                     $order->bonus_code = $favorable['card'];
                     break;
                 case 4:
                     $order->bonus_code = $favorable['card'];
                     break;
                 case 8:
                     break;
             }
         }
     }
     
     $attributes['order_number'] = $orderNumber;
     $attributes['name'] = $params['name'];//用户姓名
     $attributes['location_start'] = $params['location_start'];
     $attributes['location_end'] = $params['location_end'];
     $attributes['distance'] = !empty($params['distance']) ? $params['distance'] : 0;
     $attributes['price'] = empty($params['price']) ? 0 : $params['price'];
     $attributes['car_number'] = isset($params['car_number']) ? $params['car_number'] : 0;
     $attributes['log'] = isset($params['log']) ? $params['log'] : '';
     $attributes['wait_time'] = $params['waiting_time'];
     $attributes['end_time'] = isset($params['end_time']) ? strtotime($params['end_time']) : 0;
     $attributes['start_time'] = isset($params['start_time']) ? strtotime($params['start_time']) : 0;
     
     //添加实物劵金额  --mengtianxue 修改时间：2014-03-20
     $attributes['coupon_money'] = isset($params['cash_card_balance']) ? intval($params['cash_card_balance']) : 0.00; //定额卡可抵扣金额
     $attributes['price']=$attributes['price']-$attributes['coupon_money'];
     
     EdjLog::info('whytest=invoiced update  before4:'.$params['invoice'],'console');
     $invoice = isset($params['invoice']) ? $params['invoice'] : false;
     EdjLog::info('whytest=invoiced update  before3:'.$invoice,'console');
     if ($invoice == 'false'){
         $attributes['invoiced'] = 0;
     }else{
         $attributes['invoiced'] = 1;
     }
     //$attributes['invoiced'] = $invoice ? '1' : '0'; //发票
     EdjLog::info('whytest=invoiced update  before2:'.$attributes['invoiced'],'console');
     $city_id = isset($order['city_id']) ? $order['city_id'] : 0 ;//cityid由从司机对象取改为从order对象取
     $wait_time = empty($attributes['wait_time']) ? 0 : $attributes['wait_time'];
     
     //计算价格时候的时间  默认是开车时间，如果没有开车时间或者格式不对，选预约时间  --孟天学 2013-09-02
     $booking_time = $attributes['start_time'];
     
     $income  = ''; //TODO... 计算
     
     $price = $attributes['price'] + $attributes['coupon_money'];
     $total_money = 0 ;//TODO... 计算
     // TODO refactor
     $orderType = FinanceUtils::getOrderType($order);
     $snapshootAttributes = array();
     $startTime = $attributes['start_time'];
     $endTime   = $attributes['end_time'];
     $distance   = $attributes['distance'];//订单实际距离
     //司机端在某些极端情况会出现没有 end_time 的时候,这里进行兼容
     if($endTime < $startTime){
         $endTime = $startTime;
     }
     $beyondTimeCost = 0;
     $beyondTime = 0;
     $beyondDistance = 0;
     $beyondDistanceCost = 0;
     $app_ver = isset($params['app_ver'])?$params['app_ver']:'0';
     if(FinanceConstants::ORDER_TYPE_DAYTIME == $orderType){
         if(version_compare($app_ver,'2.5.4') > 0){
             //2.5.4版本开始日间单收费规则开始调整为时间距离双向收费
             $timeCostCal = new TimeDistanceCostCalculator($city_id, $orderType, ($endTime - $startTime),$distance);
             $attributes['subsidy_hour'] = 1;//该参数判断是否每小时补贴司机10元的补贴  新版本去掉
             $costArr = $timeCostCal->calculator();
             if(!empty($costArr)){
                 $snapshootAttributes['time_cost'] = $costArr['totalFee'];
                 $beyondTime = $costArr['beyondTime'];
                 $beyondTimeCost = $costArr['beyondTimeCost'];
                 $beyondDistance = $costArr['beyondDistance'];
                 $beyondDistanceCost = $costArr['beyondDistanceCost'];
             }
         }else{
             $attributes['subsidy_hour'] = 0;
             $timeCostCal = new TimeCostCalculator($city_id, $orderType, ($endTime - $startTime));
             $snapshootAttributes['time_cost'] = $timeCostCal->calculator();
             $snapshootAttributes['subsidy_back'] = isset($params['subsidy_back']) ? $params['subsidy_back'] : 0;//返补贴在新2.5.4版本去掉返程补贴由司机端上传
         }
         $subsidyCal  = new SubsidyCalculator($city_id, $orderType, $startTime, $endTime);
         //	$backSubsidyCal = new BackSubsidyCalculator($city_id, $attributes['distance'], $orderType);
         //计算了 income 之后, 还应该考虑原有的调整费, 调整费,在退单之后会重结
         $tmpMeta = FinanceCastHelper::getOrderModifyFee($orderId);
         if($tmpMeta){
             $modifyFee = isset($tmpMeta['amount']) ? $tmpMeta['amount'] : 0;
             $tmpTimeCost = $snapshootAttributes['time_cost'] + $modifyFee * -1;
             if($tmpTimeCost < 0){
                 $tmpTimeCost = 0;
             }
             $snapshootAttributes['time_cost'] = $tmpTimeCost;
     
         }
         $snapshootAttributes['subsidy']   = $subsidyCal->calculator();//夜间补贴仍然保留
         //	$snapshootAttributes['subsidy_back'] = $backSubsidyCal->calculator();
     
     }
     if(FinanceConstants::ORDER_TYPE_UNIT == $orderType){
         // TODO refactor
         $snapshootAttributes['unit_cost']  =  FinanceConfigUtil::getUnitOrderPrice($orderType, $city_id);
         $tmpMeta = FinanceCastHelper::getOrderModifyFee($orderId);
         if($tmpMeta){
             $modifyFee = isset($tmpMeta['amount']) ? $tmpMeta['amount'] : 0;
             $snapshootAttributes['unit_cost'] = $snapshootAttributes['unit_cost'] + $modifyFee * -1;
         }
     }
     $income = $total_money = array_sum($snapshootAttributes);
     $attributes = array_merge($attributes, $snapshootAttributes);//把需要扣款的参数,给合并到数组里面
     if ($attributes['cost_type'] == 0) {
         $total_money = $price;
     } else {
         if($income > $price){
             //要收取帐户金额
             $balance = $income - $price;
             if ($balance > $deducte_money) {
                 $total_money = $deducte_money + $price;
             } else {
                 $total_money = $income;
             }
         }else{
             $total_money = $price;
         }
     }
     $snapshootAttributes['income'] = $income;
     $snapshootAttributes['start_time']  = $startTime;
     $snapshootAttributes['end_time']    = $endTime;
     $snapshootAttributes['wait_time']   = $attributes['wait_time'];
     $snapshootAttributes['price']       = $attributes['price'];
     $snapshootAttributes['coupon_money'] = $attributes['coupon_money'];
     $snapshootAttributes['serve_time']   = $endTime - $startTime;//服务时间
     EdjLog::info(sprintf(' report money infoes |calculate income|%s|price|%s|deducte_money|%s|total_money|%s',
     $income, $price, $deducte_money, $total_money));
     //添加录入代驾费 小于（计算费用) 提示错误
     $enough = FinanceCastHelper::isMoneyEnough($params['order_id'], $income, $price);
     $orderExt = OrderExt::model()->getPrimary($params['order_id']);
     //TODO ... 这个是应该采用新上传上来的值
     if($attributes['cost_type'] == 0){//如果是纯现金支付,那么现金不能小于里程费
         $order['income'] = $income;
         $order['price']  = $attributes['price'];//这里加上了实体卡金额
         $orderMoney = FinanceCastHelper::getOrderIncome($order, $orderExt);
         if($price < $orderMoney){
             $enough = false;
         }
     }
     if (!$enough) {
         $ret = array(
                         'code' => 2,
                         'message' => '输入的代驾费有误'
         );
         RSubmitOrder::model()->delOrderId($submitRedisTag);
         return $ret;
     }
     
     $attributes['income'] = $total_money;
     $isRemote = FinanceUtils::isRemoteOrder($order, $orderExt);
     if($isRemote){
         $attributes['income'] = $income;//远程订单的 price 可能大于 income
     }
     $attributes['city_id'] = $city_id;
     
     $attributes['ready_time']     = isset($params['ready_time']) ? intval($params['ready_time']) : 0;
     $attributes['ready_distance'] = isset($params['ready_distance']) ? floatval($params['ready_distance']) : 0.00;
     
     $metaArr = array();//专门用来保存meta的json
     if($app_ver && $app_ver >= '2.5.4'){
         if($beyondTimeCost > 0 || $beyondDistanceCost > 0){
             $metaArr = @OrderSnapshoot::model()->appendSnapshootDetail($beyondTime,$beyondTimeCost,$beyondDistance,$beyondDistanceCost,$metaArr);
         }
     }
     //$metaArr['modify_fee'] = OrderSnapshoot::model()->getSnapshootMeta($orderId);
     $modifyArr = OrderSnapshoot::model()->getSnapshootMeta($orderId);
     if(!empty($modifyArr)){
         if(isset($modifyArr['modify_fee'])){
             $metaArr['modify_fee'] = $modifyArr['modify_fee'];//只有里面存在modify_fee的时候才放进去
         }
     }
     if(!empty($metaArr)){
         $snapshootAttributes['meta']  = json_encode($metaArr); //如果 meta 字段包含了其他的需要更新
     }
     $ret = OrderSnapshoot::model()->saveSnapshoot($orderId, $params['source'], $order['channel'], $income, $snapshootAttributes);
     if(!$ret){
         $ret = array(
                         'code' => 2,
                         'message' => '提交失败,请重试',
         );
         return $ret;
     }
     $order_status = Order::model()->report($order, $attributes, TRUE);
     $order_status = true;
     if ($order_status) {
         //清除订单详情缓存
         OrderCache::model()->delOrderInfoAttribute($orderId);
         //清除客户端历史订单缓存
         CustomerApiOrder::model()->deleteOrderInfoByOrderID($orderId);
     
         //更新redis BY AndyCong
         $task = array(  'class'     => __CLASS__,
                         'method' => 'updateOrderRedisJob',
                         'params' => array(
                                         'order_id' => $orderId,
                                         'order_state' => OrderProcess::ORDER_PROCESS_FINISH,
                                         'order_process_flag' => OrderProcess::FLAG_PROCESS_DRIVER_SUBMIT,
                                         'driver_id' => $driver_id,
                         ),
         );
         Queue::model()->putin($task, 'apporder');
         //更新redis BY AndyCong
         // TODO refactor
         FinanceUtils::orderLog($orderId,$driver_id,$params['source'],$startTime);
         $ret = array(
                         'code' => 0,
                         'message' => '报单完成');
     }
     return $ret;
    }
    
    /**
     * 参数检查，供submitSettle方法调用 
     * 
     * @param unknown $params
     * @return boolean
     */
    function checkParams($params){
        return (
                !isset($params['token'])
                ||!isset($params['order_id'])
                ||!isset($params['source'])
                ||empty($params['location_start'])
                ||empty($params['location_end'])
                ||!isset($params['distance'])
                ||empty($params['price'])
        );
    }
    
    /**
     * 更新order REDIS
     * 
     * copy from QueueProcess 
     * 
     * @param array $params
     * @return boolean
     */
    public function updateOrderRedisJob($params) {
        $order_id = isset($params['order_id']) ? $params['order_id'] : '';
        $order_state = isset($params['order_state']) ? $params['order_state'] : '';
        if (empty($order_id) || empty($order_state)) {
            return false;
        }
        $driver_id = Push::DEFAULT_DRIVER_INFO;
        if(isset($params['driver_id'])) {
            $driver_id =  $params['driver_id'];
        }
        $order_process_flag = isset($params['order_process_flag']) ? $params['order_process_flag'] : 0;
        $result = CustomerApiOrder::model()->updateOrderRedisByOrderFlag($order_id ,
                $order_state, $driver_id, '', $order_process_flag);
        return $result;
    }
    
}