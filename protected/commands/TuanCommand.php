<?php
/**
 * 用来做自己写的代码的基本测试
 *
 * User: tuan
 * Date: 9/29/14
 * Time: 9:03
 */
Yii::import('application.models.pay.activitySettlementImpl.*');
Yii::import('application.models.pay.orderSettlementImpl.*');
Yii::import('application.models.pay.calculator.*');
Yii::import('application.models.pay.settlement.*');
Yii::import('application.models.schema.customer.*');
Yii::import('application.models.pay.param_settle.*');
Yii::import('application.models.pay.subsidy.*');
class TuanCommand extends LoggerExtCommand{

    public function actionIsDriver($phone){
        if(Driver::model()->isDriver($phone)){
            echo "is driver";
        }
    }

    /**
     *
     * 初始化一个活动的配置
     *
     * @param $act_name
     * @param $bonusSn
     * @param $endTime
     * @param $sms
     * @param $total | 如果 total 大于0 ,会把剩余优惠券的数目设置为 total
     */
    public function actionInitActivityConfig($act_name, $bonusSn, $endTime, $sms, $total = 0){
        $bonus_library = BonusLibrary::model()->getBonusByBonus_sn($bonusSn, 1);
        if(!$bonus_library){
            echo 'bonus_sn --- '.$bonusSn.'不存在';
            return;
        }
        if($total > 0){
            $this->actionSetNum($act_name, $total, '18610994686');
        }
        ActivityConfig::model()->initInstance($act_name, $bonusSn, $sms, $endTime);
    }

    public function actionUpdateActivityConfig($act_name, $bonusSn, $endTime, $sms, $total = 0 ){
        $model = ActivityConfig::model()->find('act_name = :act_name', array(':act_name' => $act_name));
        if($model){
            $config = array();
            $config['bonus_sn'] = $bonusSn;
            $config['sms']  = $sms;
            $config['end_time'] = $endTime;
            $json = json_encode($config);
            $model->config = $json;
            if( $model->save()){
                RActivity::model()->delActivityConfig($act_name);
            }
        }else{
            $this->actionInitActivityConfig($act_name, $bonusSn, $endTime, $sms, $total);
        }
    }

	public function actionBatchNum($actName, $initNum, $fileName){
		if (empty($fileName)) {
			return;
		}
		$fileContent = file($fileName);
		foreach ($fileContent as $line => $content) {
			$phone = trim($content);
			$this->actionSetNum($actName, $initNum, $phone);
		}
	}

	public function actionSetNum($actName, $initNum, $phone){
		RActivity::model()->resetActivity($actName, $initNum, $phone);
	}

	public function actionLockActivity($actName, $value, $endDate){
		$key = 'activity_cache_'.$actName;
		$expiredTime = strtotime($endDate) - time();
		FinanceCacheUtils::setLock($key, $value, $expiredTime);

		echo FinanceCacheUtils::getLock($key, false);
	}

	/**
	 *
	 * 设置调整费的 redis 开关
	 *
	 * @param $financeOrderSource
	 * @param $cityId
	 * @param $value | 1表示开, 0表示关
	 */
	public function actionSetModifyFeeStatus($financeOrderSource, $cityId, $value){
		$expired = 60 * 60 * 24 * 30 * 365;
		$key = FinanceUtils::modifyFeeKeyGen($financeOrderSource, $cityId);
		echo 'the old is ---- '.serialize(FinanceCacheUtils::getLock($key, 'default'));
		echo "\n";
		$ret = FinanceCacheUtils::setLock($key, $value, $expired);
		if($ret){
			echo "success, now is ".serialize(FinanceCacheUtils::getLock($key, 'default'));
			echo "\n";
		}else{
			echo "fail . \n";
		}
	}

	public function actionGetHour($orderId){
		$order = Order::model()->getOrderById($orderId);
		$hours = FinanceUtils::getDayTimeServeHour($order);
		$orderSnapshoot = OrderSnapshoot::model()->getLatestSnapshoot($orderId);
		echo 'order_snapshoot --- '.json_encode($orderSnapshoot->attributes);
		echo "\n";
		echo "hours ---- ".$hours;
		echo "\n";
		$extraCast = FinanceCastHelper::getDayTimeExtraTimeSubsidy($order['city_id'], $hours);
		echo 'money --- '.$extraCast;
		echo "\n";
	}

	public function actionRedisLock($phone, $token){
		$ret = RPay::model()->lockUserFeedBack($phone, $token);
		echo serialize($ret);
	}

	public function actionIncRedis($phone ,$token)
	{
		echo RPay::model()->incrUserFeedBack($phone, $token);
	}
	public function actionGetFeelist($phone){
		$isvip=false;
		$vipPhone=VipPhone::model()->getPrimary($phone);
		$vip=false;
		if($vipPhone&&isset($vipPhone['vipid'])){
			$vip=Vip::model()->getPrimary($vipPhone['vipid']);
		}
		if($vipPhone && $vip && isset($vipPhone['vipid']) && !empty($vipPhone['vipid'])){
			$user_id=$vipPhone['vipid'];
			$vipType=$vipPhone['type'];
			$isvip=true;
		}
		$min_id = 0;
		$count = 20;
		$type = 0;
		if($isvip){
			$feesList = VipTrade::model()->getVipTradeListApi($user_id,$phone,$vipType, $min_id, $count, $type);
		}else{
			//普通用户流水
			//接口如果没有传user_id
			if (empty($user_id)) {
				$customer = BCustomers::model()->getCustomerInfo(array('phone' => $phone));
				if ($customer['code'] === 0) {
					$customer_data = $customer['data'];
					$user_id = $customer_data->id;
				}
			}

			$feesList = BCustomers::model()->getCustomerTradeListApi($user_id, $min_id, $count, $type);
		}
		foreach($feesList as $fee ){
			echo json_encode($fee);
			echo "\n";
		}
	}

	public function actionOrderDetail($orderId){
		$ret = FinanceCastHelper::getOrderFeeDetail($orderId);
		echo json_encode($ret);
	}

	public function actionDriverPush($driverId){
		FinanceCastHelper::sendDriverEnvelope($driverId);
	}

	public function actionEnvelopeUse($driverId, $sn){
		$cityId = 1;
		$cast    = 5;
		$status = OrderSettlement::model()->envelopeSettle($driverId, $cityId, $sn, $cast);
	}

	public function actionEmployeeList($driverId, $tabledate = ''){
		if(empty($tabledate)){
			$tabledate = date('Ym');
		}
		$page = 1;
		$pageSize = 20;
		$list = EmployeeAccount::model()->getInfoAccountList($driverId, $page,$pageSize,$tabledate);
		foreach($list as $item){
			echo json_encode($item);
			echo "\n";
		}
	}

	public function actionParamSettlement($orderId){
		$order = Order::model()->getOrderById($orderId);
		$orderExt = OrderExt::model()->getPrimary($orderId);
		$orderSnapshoot = OrderSnapshoot::model()->getLatestSnapshoot($orderId);
		$params = $orderSnapshoot->attributes;
		$settle = new ParamSettlement($$params, $order, $orderExt, $this->actionFavorable($orderId));
		$settle->settlement();
	}

	public function actionCalCulator($orderId){
		$snapshoot = OrderSnapshoot::model()->getLatestSnapshoot($orderId);
		$orderType = FinanceUtils::getOrderType($snapshoot);
		$city_id = 1;
		$startTime = $snapshoot['start_time'];
		$endTime   = $snapshoot['end_time'];
		$distance  = $snapshoot['distance'];
		if(FinanceConstants::ORDER_TYPE_DAYTIME == $orderType){
			$timeCostCal = new TimeCostCalculator($city_id, $orderType, ($endTime - $startTime));
			$subsidyCal  = new SubsidyCalculator($city_id, $orderType, $startTime, $endTime);
			$backSubsidyCal = new BackSubsidyCalculator($city_id, $distance, $orderType);
			$snapshootAttributes['time_cost'] = $timeCostCal->calculator();
			$snapshootAttributes['subsidy']   = $subsidyCal->calculator();
			$snapshootAttributes['subsidy_back'] = $backSubsidyCal->calculator();
		}
		if(FinanceConstants::ORDER_TYPE_UNIT == $orderType){
			$snapshootAttributes['unit_cost']  =  FinanceConfigUtil::getUnitOrderPrice($orderType, $city_id);
		}
		echo json_encode($snapshootAttributes);
		echo "\n\n";

		$income = $total_money = array_sum($snapshootAttributes);
		echo $income;
		echo "\n\n";
	}

	public function actionFavorable($orderId){
		$order = Order::model()->findByPk($orderId);
		$favorable = Order::model()->getOrderFavorable($order->phone, $order->booking_time, $order->source, $orderId);
		echo 'favorable is ---- '.json_encode($favorable);
		echo "\n\n";
		$userMoney = FinanceCastHelper::getMoneyOfUser($orderId, $order['phone']);

		echo 'user money is ---- '.$userMoney;
		echo "\n\n";
	}

	public function actionConfig(){
		echo FinanceConfigUtil::getInformationRate(FinanceConstants::ORDER_TYPE_DAYTIME, 1);
		echo FinanceConfigUtil::getUnitOrderPrice(FinanceConstants::ORDER_TYPE_UNIT, 1);
	}

	public function actionTestCustomerHistory($phone, $needle = 0){
		ROrderHistory::model()->loadCustomerOrder($phone);
		$orders=ROrderHistory::model()->getOrderList($phone, 0, 10);
		foreach($orders as $order ){
//			$orderId = $order['order_id'];
//			if($orderId == $needle){
				echo serialize($order);
				echo "\n";
//			}
//			echo $orderId."\n";
		}
	}

	public function actionTestSubsidyCalculator($dateStart, $dateEnd){
		echo json_encode(func_get_args());
		$timeStart = strtotime($dateStart);
		$timeEnd   = strtotime($dateEnd);

		$calculator = new SubsidyCalculator(1, 1, $timeStart, $timeEnd);
		echo $calculator->calculator();
	}

	public function actionA($orderId){
		echo ROrder::model()->getOrder($orderId , 'order_id');
	}

	public function actionBatchBonus( ){
		$sql = 'select customer_phone from t_customer_bonus where order_id > 0 and bonus_type_id = 748';
		$list = Yii::app()->db_finance->createCommand($sql)->queryAll();
		$date = '2014-12-11 17:49:51';
		echo count($list[0]);
		echo "\n\n";
		$this->actionBonus($list[0], $date);
	}

	public function actionBonus($phoneList, $date){
		$criteria = new CDbCriteria();
		$criteria->addCondition('created < :date');
		$criteria->params[':date'] = $date;
		$criteria->addInCondition('phone', $phoneList);

		$count = CustomerClient::model()->count($criteria);
		echo $count;
	}

	/**
	 * 客户取订单详细
	 *
	 * @param $orderId
	 */
	public function actionTestCustomerOrderDetail($orderId){
		$detail = CustomerApiOrder::model()->getOrderInfoByOrderID($orderId);
		echo json_encode($detail);
	}

	public function actionTestReportSettle($orderId){
		$params = array(
			'order_id' => $orderId,
			'cost_type' => 0,
			'vipcard'   => 0,
			'order_number' => $orderId,
			'name'          => 'tuan',
			'location_start'    => 'x',
			'location_end'      => 'z',
			'distance'          => 10,
			'price'             => 0,
			'car_number'        => 0,
			'log'               => '',
			'wait_time'         => 1,
			'end_time'          => 2,
			'start_time'        => 3,
			'coupon_money'      => 0,
			'invoiced'          => 0,
			'city_id'           => 1,
			'ready_time'        => 0,
			'ready_distance'    => 1,
		);
		$order = Order::model()->findByPk($orderId);
		$timeCostCal = new TimeCostCalculator(1, 1, 1000);
		$subsidyCal  = new SubsidyCalculator(1, 1, time(), time());
		$backSubsidyCal = new BackSubsidyCalculator(1, 60, 1);
		$timeCost = $timeCostCal->calculator();
		$subsidy  = $subsidyCal->calculator();
		$backSubsidy = $backSubsidyCal->calculator();
		$settleParams = array(
			'time_cost'     => $timeCost,
			'subsidy'       => $subsidy,
			'subsidy_back'  => $backSubsidy,
		);
		echo json_encode($settleParams);
		echo "\n";
		$params = array_merge($params, $settleParams);
		echo json_encode($params);
		$order_status = Order::model()->report($order, $params, TRUE);
//		OrderSettlement::model()->submitSettle($orderId, $settleParams);
//		$order_status = true;
		if($order_status){
			echo 'success'."\n";
		}

	}

	public function actionTestSubmitOrder($orderId){
		$params = array(
			'order_id'  => $orderId,
			'token' => 100,
			'order_number' => 100,
			'source'        => 11,
			'channel'        => 11,
			'income'          => 11,
			'lat'         => 11,
			'lng'  => 90,
			'driver_id'       => 'BJ9025',
			'cash_card_balance'     => 10,
			'price'                 => 99,
			'cost_type'             => 8,
			'distance'              => 1,
			'end_time'            => time() + 60 * 60 * 3,
			'start_time'        => time(),
			'car_type'             => '0',
			'car_number'              => 'baidu',
			'gps_type'              => 'baidu',
			'time_cost'             => 1,
			'subsidy'               => 2,
			'subsidy_back'          => 3,
			'waiting_time'          => 1,
			'tip'          => 0,
			'car_cost'          => 0,
			'other_cost'          => 0,
			'cost_mark'          => '',
			'log_time'          => 0,
		);
		$order = Order::model()->getOrderById($orderId);
		//5.订单快照
		$orderType = FinanceUtils::getOrderType($order);
		$snapshootAttributes = FinanceUtils::getLegalParamList($orderType, $params);
		$endTime = $params['end_time'];
		$startTime = $params['start_time'];
		$snapshootAttributes['start_time'] = $startTime;
		$snapshootAttributes['end_time']   = $endTime;
		$snapshootAttributes['serve_time']   = $endTime - $startTime; //服务时间
		$status = OrderSnapshoot::model()->saveSnapshoot($orderId, 9, 99,  10, $snapshootAttributes);
		if(!$status){
			$ret = array(
				'code' => 2,
				'message' => '提交失败,请重试',
			);
			echo json_encode($ret);return;
		}
		$params = array_merge($params, $snapshootAttributes);
		QueueProcess::model()->order_submit($params);
	}

	public function actionTestOrderSnapshoot($orderId){
		$income = 160;
		$params = array(
			'time_cost' => 100,
			'subsidy' => 50,
			'subsidy_back' => 10,
		);
		OrderSnapshoot::model()->saveSnapshoot($orderId, Order::SOURCE_DAYTIME_CLIENT, 1, $income, $params);
	}



	public function actionParamSettle($orderId)
	{
		$params = array(
			'time_cost' => 100,
			'subsidy' => 50,
			'subsidy_back' => 10,
		);
		$orderSnapshoot = OrderSnapshoot::model()->getLatestSnapshoot($orderId);
		$params = $orderSnapshoot->attributes;
		$order = Order::model()->getOrderById($orderId);
		if ($order) {
			$orderExt = OrderExt::model()->getPrimary($orderId);
			$settleList = SettlementFactory::getSettleListWithParam($order, $orderExt, $params);
			foreach ($settleList as $settle) {
				$settle->settlement();
			}
		}
	}

	public function actionFinanceConfig(){
		$config = array(
			'cast'	=> 30,
		);
		$financeConfig = new FinanceConfig();
		$financeConfig['type']  = 1;
		$financeConfig['config'] = json_encode($config);
		if(!$financeConfig->save()){
			echo json_encode($financeConfig->getErrors());

		}
	}

	public function actionSubsidyRecord($driverId, $date = ''){
		if (empty($date)) {
			$date = date('Y-m-d', strtotime('-1 day'));
		}
		$record = new SubsidyRecord();
		$record['type']     = 0;
		$record['user_id']  = $driverId;
		$record['city_id']  = 1;
		$record['cast']  = 30;
		$record['date']  = $date;
		$record['created'] = date('Y-m-d H:i:s');
		if(!$record->save()){
			echo json_encode($record->getErrors());
		}
	}

	public function actionNewRefund($orderId){
		$order = Order::model()->getOrderById($orderId);
		OrderSettlement::model()->refundOrder($order);
	}

	public function actionOldRefund($orderId){
		$order = Order::model()->getOrderById($orderId);
		OrderSettlement::model()->delEmployeeAccount($order);
	}

	public function actionDriverBonus($orderId, $bonusSn){
		$order = Order::model()->getOrderById($orderId);
		$orderExt = OrderExt::model()->getPrimary($orderId);
		$bonus = CustomerBonus::model()->findByPk(549341);
		$driverBonusSettlement = new DriverBonusSettlement($order, $orderExt, 0);
		$driverBonusSettlement->setCustomerBonus($bonus);
		$driverBonusSettlement->settlement();
	}

	/**
	 * 模拟工作台
	 *
	 * @param $driverId
	 */
	public function actionDriverSum($driverId){
		$timeStart = date("Y-m-d 07:00:00",strtotime("-1 day"));
		$timeEnd   = date("Y-m-d 07:00:00");
		$hour = date('H');
		if($hour >= 7){
			$timeStart = $timeEnd;
			$timeEnd   = date('Y-m-d H:i:s');
		}else{

		}
   		$newRet = FinanceCastHelper::getDriverIncome($driverId, strtotime($timeStart), strtotime($timeEnd));
		$newCount = $newRet['total_count'];
		$newSum   = $newRet['total_income'];
		echo json_encode($newRet);
		echo "\n";
	}

	/**
	 * 模拟已报订单详细
	 *
	 * @param $driverId
	 * @param $orderId
	 */
	public function actionSubmitDetail($driverId, $orderId){
		$condition = array();
		$condition['order_id'] = $orderId;
		$condition['driver_id'] = $driverId;
		$order = Order::model()->getSubmitOrderDetail($condition);
		echo json_encode($order);
		echo "\n\n\n";
		echo json_encode(FinanceCastHelper::getOrderFeeDetail($orderId));
	}

	/**
	 * 模拟已报订单列表
	 *
	 * @param $driverId
	 * @param $orderId
	 */
	public function actionShowSubmit($driverId, $orderId){
		$condition = array();
		$condition['driver_id'] = $driverId;
		$condition['order_id'] = $orderId;
		$condition['pageSize'] = 20;
		$condition['offset'] = 0;
		$list = Order::model()->getSubmitOrderList($condition);
		if(empty($list)){
			echo 'have no order'."\n";
		}
		if(empty($orderId)){
			echo json_encode($list);
		}else{
			foreach($list as $item){
				if($item['order_id'] == $orderId){
					echo json_encode($item);
				}
			}
		}

	}

	public function actionOop($orderId,  $income = 0, $price = 0 , $subsidy = 0, $fee = 0, $balance= 0, $isTimeout = false){
		$sql_order_1 = "update t_order set income = $income where order_id = $orderId";
		$sql_order_2 = "update t_order set price  = $price where order_id = $orderId";
		$sql_order_ext_1 = "update t_order_ext set fee = $fee where order_id = $orderId";
		Yii::app()->db->createCommand($sql_order_1)->execute();
		Yii::app()->db->createCommand($sql_order_2)->execute();
		$subsidy = $subsidy / 4;
		$sql_order_ext_2 = "update t_order_ext set linear_ready_distance = $subsidy where order_id = $orderId";
		Yii::app()->db->createCommand($sql_order_ext_2)->execute();
		Yii::app()->db->createCommand($sql_order_ext_1)->execute();
		if($isTimeout){
			$sql_order_ext_3 = "update t_order_ext set driver_ready_time = 10000 where order_id = $orderId";
			Yii::app()->db->createCommand($sql_order_ext_3)->execute();
		}else{
			$sql_order_ext_3 = "update t_order_ext set driver_ready_time = 100 where order_id = $orderId";
			Yii::app()->db->createCommand($sql_order_ext_3)->execute();
		}
		EmployeeAccount::$table_name = '201411';
		EmployeeAccount::model()->deleteAll('order_id = :order_id', array(':order_id' => $orderId));
		VipTrade::model()->deleteAll('order_id = :order_id', array(':order_id' => $orderId));
		CarCustomerTrans::model()->deleteAll('trans_order_id = :order_id', array(':order_id' => $orderId));
		if(!empty($balance)){
			$sql_vip  = "update t_vip set balance = $balance where id = 8888";
			Yii::app()->db->createCommand($sql_vip)->execute();
			$sql_normal = "update t_customer_account set amount = $balance where user_id = 1128889";
			Yii::app()->db->createCommand($sql_normal)->execute();
		}

		$this->actionNewSettle($orderId);
	}

	public function actionGetSubsidy($orderId){
		$order = Order::model()->getOrderById($orderId);
		$orderExt = OrderExt::model()->getPrimary($orderId);
		$isRemote = FinanceUtils::isRemoteOrder($order, $orderExt);
		if($isRemote){
			echo "order id ".$orderId. 'is remote order'."\n";
		}
		$cast = FinanceCastHelper::getSubsidy($order, $orderExt);
		$format = 'orderID|%s|就位时间|%s|就位距离|%s|补贴金额|%s|';
		echo sprintf($format, $orderId, $orderExt['driver_ready_time'] / 60,
					$orderExt['linear_ready_distance'], $cast);
	}

	public function actionAlarm(){
		$orderId = '';
		$order = Order::model()->getOrderById($orderId);
		$orderExt = OrderExt::model()->getPrimary($orderId);
		$settle = new SpecialInformationSettlement($order, $orderExt, 0);
//		$settle->alarm('hhh');
	}


	public function actionOrderList(){
		$order = array();
		$order['driver_id'] = 'BJ9003';
		$order['status'] = 1;
		$order['pageSize'] = 1;
		$order['offset'] =  0;
		$orderList =  $orderList = Order::model()->getDriverOrderListByType($order);
		echo json_encode($orderList);
	}

	public function actionNewSettle($orderId){
		$order = Order::model()->getOrderById($orderId);
		$orderExt = OrderExt::model()->getPrimary($orderId);
		$settleList = SettlementFactory::getSettleChainFromOrder($order, $orderExt);
		foreach($settleList as $settle){
			$settle->settlement();
		}
	}

	public function actionSettlement($sourceOrderId, $orderStatus, $income = 39,
									 $price = 10, $couponMoney = 0, $driverReadyTime = 0, $driverReadyDistance = 0 ){
		$order = $this->mockOrder($sourceOrderId, $orderStatus, $income, $price);
		$order = Order::model()->getOrderById($order['order_id']);
//		$orderExt = $this->mockOrderExt($sourceOrderId, $order['order_id'], $couponMoney,$driverReadyTime, $driverReadyDistance);
		$activitySettlement = new NewCustomerActivitySettlement(new NewCustomerFreeActivity(), $order, null);
		$activitySettlement->settlement();
	}

	public function actionDeleteAll($orderId = 0){
		Order::model()->deleteByPk($orderId);

	}
	public function actionCoupon($phone , $orderId){
		$ret = CustomerBonus::model()->getBonusUseCount($$phone, $orderId, 2);//2为实体卡
		print_r($ret);
	}

	public function actionSettlementId($orderId){
		$order = Order::model()->getOrderById($orderId);
		OrderSettlement::model()->activitySettlement($order, null);
//		OrderSettlement::model()->orderSettle($orderId);
	}

	private function mockOrder($sourceOrderId, $status, $income, $price){
		$oldOrder = Order::model()->findByPk($sourceOrderId);
		$order = new Order();
		unset($order->order_id);
		$order->attributes = $oldOrder->attributes;
		$order->order_number = 'tuanBJ_test';
		$order->phone = '18610994686';
		$order->status = $status;
		$order->income = $income;
		$order->price = $price;
		$order->insert();
		return $order;
	}

	private function mockOrderExt($sourceOrderId, $orderId, $couponMoney, $driverReadyTime, $driverReadyDistance){
		$oldOrderExt = OrderExt::model()->getPrimary($sourceOrderId);
		$orderExt = new OrderExt();
		$orderExt->attributes = $oldOrderExt;
		$orderExt->order_id = $orderId;
		$orderExt->coupon_money = $couponMoney;
		$orderExt->driver_ready_time = $driverReadyTime;
		$orderExt->driver_ready_distance = $driverReadyDistance;
		$orderExt->insert();
		return $orderExt;
	}

//	private function
} 