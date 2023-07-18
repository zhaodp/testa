<?php
Yii::import("application.models.redis.*");
//需要写清楚注释，增加爱缓存，封装业务逻辑 add by sunhongjing at 2013-5-19

//
if(Yii::app()->params['order_architecture_refactor_on']) {
    $result = SubmitOrderManualService::getInstance()->submit($params);
    echo json_encode($result);
    return;
}

if(empty($params['token'])){
    $ret=array('code'=>2 , 'message'=>'参数不正确!');
    echo json_encode($ret);return;
}

EdjLog::info('driver.order.report params is '.json_encode($params));
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
        echo json_encode($ret);
        return;
    }
    if ($params['order_id']) {
        $order = Order::model()->getOrderInfomation($params['order_id'], $driver_id);

        if ($order) {
            if (($order->status == Order::ORDER_COMPLATE && EmployeeAccount::model()->getOrderfee($order) > 0)
                || $order->status == Order::ORDER_CANCEL || $order->status == Order::ORDER_COMFIRM
                || $order->status == Order::ORDER_NOT_COMFIRM
            ) {
                $ret = array(
                    'code' => 0,
                    'message' => '订单已处理');
                echo json_encode($ret);
				RSubmitOrder::model()->delOrderId($submitRedisTag);
                return;
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
                    echo json_encode($ret);
					RSubmitOrder::model()->delOrderId($submitRedisTag);
                    return;
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

                $city_id = $driver['city_id'];
                $wait_time = empty($attributes['wait_time']) ? 0 : $attributes['wait_time'];

                //计算价格时候的时间  默认是开车时间，如果没有开车时间或者格式不对，选预约时间  --孟天学 2013-09-02
                $booking_time = $attributes['start_time'];

                // 代驾费用
                $income = CityConfig::model()->calculatorFee($city_id, $attributes['distance'], $booking_time, $wait_time);//Common::calculator($city_id, $attributes['distance'], $booking_time, $wait_time);
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
					echo json_encode($ret);
                    return;
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
                    echo json_encode($ret);return;
                }
                $order_status = Order::model()->report($order, $attributes, TRUE);
                if ($order_status) {
                    $orderId = $order['order_id'];
                    //清除订单详情缓存
                    OrderCache::model()->delOrderInfoAttribute($orderId);
                    //清除客户端历史订单缓存
                    CustomerApiOrder::model()->deleteOrderInfoByOrderID($orderId);
                    //更新redis BY AndyCong
                    $task = array(
                        'method' => 'update_order_redis',
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
                        'method'    => 'saveOrderInfo',
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
echo json_encode($ret);
