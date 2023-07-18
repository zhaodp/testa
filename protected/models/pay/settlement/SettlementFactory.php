<?php
/**
 * 结账简单工厂
 *
 * User: tuan
 * Date: 9/28/14
 * Time: 19:32
 */
Yii::import('application.models.pay.activitySettlementImpl.*');
Yii::import('application.models.pay.orderSettlementImpl.*');
Yii::import('application.models.pay.param_settle.*');
Yii::import('application.models.pay.subsidy.*');
class SettlementFactory {

	/**
	 * 返回纯司机账户操作的问题
	 *
	 * @param $order
	 * @return array
	 */
	public static function getDriverSettleList($order){
		$orderId = $order['order_id'];
		$modify = FinanceCastHelper::getOrderModifyFee($orderId, 'modify_fee');
		$settleList = array();
		if($modify && ($modify['amount'] != 0)){
			$channel = 1;
			$settle = new DriverSettlement($order['city_id'], $channel, $order['driver_id']);
			$settle->setOrderId($orderId);
			$settle->setCast($modify['amount']);
			$settle->setComment($modify['name']);
			$settle->setChannel(EmployeeAccount::CHANNEL_DRIVER_MODIFY_FEE);
			$settleList[] = $settle;
		}
		return $settleList;
	}

	public static function getCalculators($orderType, $params){
		$ret = array();
		if(FinanceConstants::ORDER_TYPE_DAYTIME == $orderType){
			$startTime = $params['start_time'];
			$endTime   = $params['end_time'];
			$cityId    = $params['city_id'];
			$ret[]     = new TimeCostCalculator($cityId, $orderType, $endTime - $startTime);
		}
	}

	public static function getSettleListWithParam($order, $orderExt, $params = array()){
		$favorable = FinanceCastHelper::getOrderFavorable($order['order_id'], $order['phone']);
		$settleList = array();
		$totalMoney = FinanceCastHelper::getOrderTotalMoney($order, $orderExt, $params, true);
		if(!empty($params)){
			//根据上传上来的参数扣款
			$settleList[] = new ParamSettlement($params, $order, $orderExt, $favorable);
		}
		$settleList[] = new BonusSettlement($order, $orderExt, $favorable);
		$orderType = FinanceUtils::getOrderType($order);
		$driverChannel = EmployeeAccount::TYPE_ORDER_CASH;
		$extraCast     = 0; //额外的金额
		if(FinanceConstants::ORDER_TYPE_DAYTIME == $orderType){
			$driverChannel = EmployeeAccount::CHANNEL_DRIVER_CASH_DAYTIME;
			//日间业务收取保险费, 一口价不收
			$settleList[]  = new InsuranceSettlement($order, $orderExt, $favorable); //保险费
			$informationSettle = new SpecialInformationSettlement($order, $orderExt, $favorable);
			$informationSettle->setBase($totalMoney - 2);//去掉两块钱保险费
			$informationSettle->setRate(FinanceConfigUtil::getInformationRate($orderType, $order['city_id']));//0,也在司机信息费里面插入
			$comment = '日间业务信息费 订单号:'.$order['order_id'];
			$informationSettle->setComment($comment);
			$informationSettle->setChannel($driverChannel);
			$settleList[] = $informationSettle; // 信息费
			$driverId     = $order['driver_id'];
			$cityId       = $order['city_id'];
            $subsidy_hour = isset($params['subsidy_hour']) ? $params['subsidy_hour'] : 0;
            if(!$subsidy_hour){//新版本2.5.4日间单去掉每小时10元的补贴
                $hours = FinanceUtils::getDayTimeServeHour($order);
                $extraCast = FinanceCastHelper::getDayTimeExtraTimeSubsidy($cityId, $hours);
                if( 0 < $extraCast){
                    $companySubsidySettlement = new CompanySubsidySettlement($driverId, $cityId);
                    $companySubsidySettlement->setChannel(EmployeeAccount::CHANNEL_DRIVER_DAYTIME_EXTRA_SUBSIDY);
                    $companySubsidySettlement->setCast($extraCast);
                    $orderId = $order['order_id'];
                    $companySubsidySettlement->setComment('日间优惠补贴 单号:'.$orderId);
                    $companySubsidySettlement->setOrderId($orderId);
                    $settleList[] = $companySubsidySettlement; // 额外的补贴
                }
            }
		}
		if(FinanceConstants::ORDER_TYPE_UNIT == $orderType){
			$driverChannel = EmployeeAccount::CHANNEL_DRIVER_CASH_UNIT;
		}
		$cashSettlement = new CashSettlement($order, $orderExt, $favorable);
		$cashSettlement->setChannel($driverChannel);
		$cashSettlement->setExtraCast($extraCast);
		$settleList[]  = $cashSettlement;
		$settleCount  = count($settleList);
		EdjLog::info('settle count is '.count($settleList));
		if($settleCount > 0){
			$settleName = '';
			foreach($settleList as $item){
				$settleName .= get_class($item).'+';
			}
			EdjLog::info('settle class name '.$settleName);
		}
		return array_merge($settleList, self::getDriverSettleList($order));
	}

	/**
	 * 给订单流生成结账处理
	 *
	 * @param $order
	 * @param $orderExt
	 * @return array
	 */
	public static function getSettleChainFromOrder($order, $orderExt){
		$format = 'oop settle get in, order_id|%s|get settle count|%s| order_status|%s';

		$settleList = array();
		$status = isset($order['status']) ? $order['status'] : 0;
		//只能是结束的订单才能结帐
		if (FinanceUtils::isOrderFinish($status)) {
			$costType = $order['cost_type'];
			//如果是全部现金支付,那么就只需要扣除司机的信息费,记录订单收入
			//如果是vip余额,那么不能有优惠券结账
			//是远程叫单 有小费
			$favorable = FinanceCastHelper::getOrderFavorable($order['order_id'], $order['phone']);
			if (4 == $costType || 2 == $costType) {
				$settleList[] = new BonusSettlement($order, $orderExt, $favorable);
			}
			//纯优惠券消费,不需要走扣用户余额流程
			if (2 != $costType) {
				$settleList[] = new IncomeSettlement($order, $orderExt, $favorable);
			}
			if (FinanceUtils::isRemoteOrder($order, $orderExt, $favorable)) {
				$settleList[] = new SubsidySettlement($order, $orderExt, $favorable);
			}
			if (FinanceUtils::havingTips($order, $orderExt)) {
				$settleList[] = new TipSettlement($order, $orderExt, $favorable);
			}
			$settleList[] = new InsuranceSettlement($order, $orderExt, $favorable);
			$meta = OrderSnapshoot::model()->getSnapshootMeta($order['order_id'], 'modify_fee');
			$oldIncome = $order['income'];
			if($meta){ // 如果有调整费,需要修饰一下信息费收取的基数,
				$income = $order['income'];
				$type = isset($meta['type']) ? $meta['type'] : 0;
				$amount = isset($meta['amount']) ? $meta['amount'] : 0;
				if(FinanceConstants::TYPE_MODIFY_INCOME == $type){ //如果是司机代收的话,那么后面扣信息费需要排除
					$income = $income - $amount;
				}
				if(FinanceConstants::TYPE_MODIFY_OUTCOME == $type){//如果是公司代付的话,那么后面扣信息费需要加上收取
					$income = $income + $amount;
				}
				$order['income'] = $income;
			}

			$settleList[] = new DefaultInformationSettlement($order, $orderExt, $favorable);
			$order['income']  = $oldIncome;
			$settleList[] = new CashSettlement($order, $orderExt, $favorable);
		}
		$settleCount  = count($settleList);
		$log = sprintf($format, $order['order_id'], $settleCount, $status);
		EdjLog::info($log);
		if($settleCount > 0){
			$settleName = '';
			foreach($settleList as $item){
				$settleName .= get_class($item).'+';
			}
			EdjLog::info('settle class name '.$settleName);
		}
		return array_merge($settleList, self::getDriverSettleList($order));
	}

	/**
	 * 根据活动返回对应结账的方式
	 *
	 * @param $order
	 * @param $orderExt
	 * @return array
	 */
	public static  function getActivitySettlementList($order, $orderExt){
		$settleList = array();
		$activityList = CityBasedActivity::getAppliedActivities($order);
		if(empty($activityList)){
			return $settleList;
		}
		//一个订单可能同时满足多个活动
		foreach($activityList as $activity){
			$settle = self::getSettleInstanceByActivityType($activity, $order, $orderExt);
			if($settle){
				$settleList[] = $settle;
			}
		}
		//返回一个settlement的array
		return $settleList;
	}

	/**
	 * 根据活动类型返回对于的结算方式对象
	 *
	 * @param $activity
	 * @param $order
	 * @param $orderExt
	 * @return NewCustomerActivitySettlement|null
	 */
	private static function getSettleInstanceByActivityType($activity, $order, $orderExt){
		$activityTye = $activity->getActivityType();
		$cityId = $order['city_id'];
		if(empty($activityTye)){
			return null;
		}
		switch($activityTye){
			case CityBasedActivity::ACTIVITY_NEW_CUSTOMER_FREE:
//				if(8 == $cityId){//南京的新客免单,不走这里的实现,避免测试点扩大,2014-10-09,上线之后其他城市没问题,可以考虑切过来
//					return null;
//				}
				return new NewCustomerActivitySettlement($activity, $order, $orderExt);
			default:
				return null;
		}
	}
} 