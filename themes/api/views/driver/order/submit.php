<?php
Yii::import("application.models.redis.*");
    /**
     * 判断有走不到的逻辑，小孟看到注释，检查一遍，找不到逻辑错误来问我，add by sunhongjing 2013-06-12
     * 
     * 报单
     * 调用的url
     * @author mengtianxue 2013-05-26
     * @param $params
     */

// 
if(Yii::app()->params['order_architecture_refactor_on']) {
    $result = SubmitOrderAutoService::getInstance()->submit($params);
    echo json_encode($result);
    return;
}

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
           echo json_encode($ret);return;
       }
      
	EdjLog::info('submit order params is '.json_encode($params));
       //判断token存在
       $driver_id = DriverToken::model()->getDriverIdByToken($params['token']);
//       $driver_id = $params['token'];
       if(!$driver_id){
           $ret = array (
                         'code'=>1,
                         'message'=>'请重新登录');
           echo json_encode($ret);return;
       }else{
           $params['driver_id'] = $driver_id;
       }
       
       //判断 order_number order_id 是否存在
       if(empty($params['order_id']) && empty($params['order_number'])){
           $ret = array (
                         'code'=>2,
                         'message'=>'订单ID或订单号不能为空');
           echo json_encode($ret);
           return;
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
       	   	   echo json_encode($ret);return ;
       	   }
       	   $params['unique_order_id'] = $params['order_id'];
       	   $params['order_id'] = $order_id;
       }
	//校验余额是否足够
	$order=Order::model()->findByPk($params['order_id']);
	$booking_time=isset($params['start_time'])?strtotime($params['start_time']):'';
	$driver=Driver::getProfile($params['driver_id']);
	$city_id=$driver['city_id'];
	$distance=isset($params['distance'])?$params['distance']:0;
	$wait_time=isset($params['waiting_time'])?$params['waiting_time']:0;
	//计算应该收取的代驾费用 $income
	$income = CityConfig::model()->calculatorFee($city_id, $distance, $booking_time, $wait_time);
	if(abs($income-$params['income'])>5){
		EdjLog::info("order_id ".$params['order_id']." income $income get arg income ".$params['income']." diff too much");
	}
	if($income<$params['income']){
		$income=$params['income'];
	}	
	//计算报单时用户有的钱
	$deducte_money=0;//余额和优惠券可抵扣的金额，也考虑到了信用额度
	if(!empty($params['cost_type'])){
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
        	echo json_encode($ret);
		return;
	}
	$enough = FinanceCastHelper::isMoneyEnough($params['order_id'], $params['income'], $params['price'] + $params['coupon_money']);//需要增加实体券, 这一次查询貌似是多余的
	if(!$enough && $order->status == 0){//避免重复发短信
		$send_phone=$order->phone;
		$vip_info=VipPhone::model()->getVipInfoByPhone($order->phone);
		if($vip_info&&!empty($vip_info['phone'])&&!empty($vip_info['vipid'])){
			$vip=Vip::model()->getPrimary($vip_info['vipid']);
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
                     'method'=>'order_submit',
                     'params'=>$params
                     );

		Queue::model()->putin($task,'settlement');
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
       FinanceUtils::orderLog($params['order_id'],$driver_id,$order->source,strtotime($params['start_time']));
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
               'deduct_wealth'   => isset($params['deduct_wealth']) ? $params['deduct_wealth'] : 0 ,
           ),
       );

       Queue::model()->putin($dumpTask,'dumplog');
       $ret = array (
                     'code'=>0,
                     'message'=>'提交成功！');
       echo json_encode($ret);
       
   }else{
   	
   	   //添加task队列向数据中添加
       $task = array(
                     'method'=>'order_submit_tracking',
                     'params'=>$params
               );

	   Queue::model()->putin($task,'tmporder');

       $ret = array(
           'code' => 2,
           'message' => '参数不正确');
       echo json_encode($ret);
       return;
   }






