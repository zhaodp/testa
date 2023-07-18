<?php
/**
 * 扣除用户里程费
 *
 * User: tuan
 * Date: 14/10/27
 * Time: 16:19
 */

class IncomeSettlement extends OrderSettle{

	private $vip;

	private $customer;

	private static $VIP_TRADE_FORMAT = 'VIP消费 单号:%s';

	private static $VIP_DRIVER_FORMAT = 'VIP订单收入 单号:%s';

	private static $CUSTOMER_TRANS_FORMAT = '订单号:%s';

	private static $CUSTOMER_DRIVER_FORMAT = '个人用户消费 单号:%s';

	public function init()
	{
		$customerPhone = $this->getUserPhone();
		$vipCard = $this->getVipCard();
		if (!empty($vipCard)) {
            $vip = VipService::service()->getVipInfoByVipCard($vipCard, false);
            $this->setVip($vip);
		} else {
            $customerAccount = NormalAccountService::forceGetUserAmount($customerPhone);
            $this->setCustomer(array(
                'id'	=> $customerAccount['user_id'],
                'amount'=> $customerAccount['amount'],
            ));
		}
	}

	public function settlement(){
		$format = 'order_id|%s|order_money|%s|income|%s|price|%s|subsidy|%s|fee|%s';
		$orderMoney = FinanceCastHelper::getOrderIncome($this->getOrder(), $this->getOrderExt());
		$subsidyMoney = FinanceCastHelper::getSubsidy($this->getOrder(), $this->getOrderExt());
		$log = sprintf($format, $this->getOrderId(), $orderMoney,
			$this->getIncome(), $this->getPrice(), $subsidyMoney, $this->getFee());
		EdjLog::info('customer settle get in '.$log);
		$this->orderValidator();
		//init
		$this->init();
		//order vip
		$isVip = CustomerMain::model()->isVip($this->getUserPhone());
		$ret = null;
		if($isVip){
			$ret = $this->orderVip();
		}else{
			$ret = $this->orderNormal();
		}
		if(FinanceConstants::isNotSuccess($ret)){
			$message = 'charge user and driver in income  settlement fail';
			$this->alarm($message);
		}
//		$this->callBack();
	}

	/**
	 * 扣vip用户的款
	 *
	 * @return array
	 */
	private function orderVip(){
		$vip  = $this->getVip();
		if(empty($vip)){
			return array(
				'code'	=> FinanceConstants::CODE_SUCCESS,
			);
		}
		$vipCard = $vip['vipid'];
		$costType = $this->getCostType();
		$costArray = array(1);// vip余额付款  @see OrderSettlement ::orderSettle
		if(!in_array($costType, $costArray)){
			return array(
				'code'	=> FinanceConstants::CODE_FAIL,
			);
		}
		//get and set delta/comment
		$amount  = $this->getUserDelta(); //需要付款的
		if ($amount != 0) {
			$delta = $amount * -1;
			$vipBalance = $vip['amount'];
			//charge vip
			$vipTradeAttributes = array(
				'vipcard' => $vipCard,
				'order_id' => $this->getOrderId(),
				'type' => VipTrade::TYPE_ORDER,
				'source' => VipTrade::TRANS_SOURCE_S,
				'amount' => $amount, //VipTrade :: addTrade 里面给*-1了
				'comment' => $this->getVipTradeComment(),
				'balance' => $vipBalance + $delta,
				'order_date' => $this->getOrderCreated(),
			);

			$vipBalanceAttributes = array(
				'vipCard' => $vipCard,
				'delta' => $delta,
			);
			$code = FinanceConstants::CODE_FAIL;
			$ret = $this->chargeVip($vipTradeAttributes, $vipBalanceAttributes);
			if ($ret) {
				$this->notify($this->getUserPhone());
				$code = FinanceConstants::CODE_SUCCESS;
				$type = EmployeeAccount::TYPE_ORDER_VIP;
				$channel = EmployeeAccount::TYPE_ORDER_VIP;
				$comment = sprintf(self::$VIP_DRIVER_FORMAT, $this->getOrderId());
				$this->orderDriver($type, $channel, $amount, $comment);
			} else {
				$message = 'charge vip in income  settlement fail';
				$this->alarm($message);
			}
			return array(
				'code' => $code,
			);
		}else{
			return array(
				'code' => FinanceConstants::CODE_SUCCESS,
			);
		}
	}

	/**
	 * 扣普通用户的款
	 *
	 * @return array
	 */
	private function orderNormal(){
		//illegal normal
		$customer  = $this->getCustomer();
		if(empty($customer)){
			return array(
				'code'	=> FinanceConstants::CODE_SUCCESS,
			);
		}
		//checkout cost type
		$costType = $this->getCostType();
		$costArray = array(4, 8);//0 现金 4 bonus+balance 8 balance only
		if(!in_array($costType, $costArray)){
			return array(
				'code'	=> FinanceConstants::CODE_SUCCESS, 
			);
		}
		//get and set delta/comment
		$amount = $this->getUserDelta();
		if ($amount != 0) {
			$delta = $amount * -1;
			$customerBalance = $customer['amount'];
			$userId = $customer['id'];
			//charge normal
			$customerTransAttributes = array(
				'user_id' => $userId,
				'trans_order_id' => $this->getOrderId(),
				'trans_type' => CarCustomerTrans::TRANS_TYPE_F,
				'amount' => $delta,
				'balance' => $customerBalance + $delta,
				'source' => CarCustomerTrans::TRANS_SOURCE_S,
				'remark' => $this->getNormalTransComment(),
			);
			$customerAccountAttributes = array(
				'user_id' => $userId,
				'amount' => $delta,
			);
			$code = FinanceConstants::CODE_FAIL;
			$ret = $this->chargeNormal($customerTransAttributes, $customerAccountAttributes);
			if ($ret) {
				$this->notify($this->getUserPhone());
				$type = EmployeeAccount::TYPE_ORDER_NORMAL;
				$channel = EmployeeAccount::TYPE_ORDER_NORMAL;
				$comment = sprintf(self::$CUSTOMER_DRIVER_FORMAT, $this->getOrderId());
				$code = FinanceConstants::CODE_SUCCESS;
				$this->orderDriver($type, $channel, $amount, $comment);
			} else {
				$message = 'charge normal user in income  settlement fail';
				$this->alarm($message);
			}
			return array(
				'code' => $code,
			);
		} else {
			return array(
				'code' => FinanceConstants::CODE_SUCCESS,
			);
		}
	}

	private function orderDriver($type, $channel, $delta, $comment){
		$employeeAccountAttributes = array();
		$employeeAccountAttributes['channel'] = $channel;
		$employeeAccountAttributes['comment'] = $comment;
		$employeeAccountAttributes['order_date'] = time();
		$employeeAccountAttributes['type'] = $type;
		$employeeAccountAttributes['city_id'] = $this->getCityId();
		$employeeAccountAttributes['operator'] = 'system';
		$employeeAccountAttributes['user'] = $this->getDriverId();
		$employeeAccountAttributes['cast'] = $delta;
		$employeeAccountAttributes['order_id'] = $this->getOrderId();
		$ret = $this->chargeDriver($employeeAccountAttributes);
		if(!$ret){
			$message = 'charge driver in income settlement fail';
			$this->alarm($message);
		}
	}

	private function getVipTradeComment(){
		return sprintf(self::$VIP_TRADE_FORMAT, $this->getOrderId());
	}

	private function getNormalTransComment(){
		return sprintf(self::$CUSTOMER_TRANS_FORMAT, $this->getOrderId());
	}

	/**
	 * 返回应该从用户账户里面扣除的钱
	 *
	 * @return float
	 */
	private function getUserDelta(){
		$delta = 0.00;
		$orderMoney = FinanceCastHelper::getOrderIncome($this->getOrder(), $this->getOrderExt());
		$price = $this->getPrice();
		$income = $this->getIncome();
		$balanceCast = $orderMoney - $price;//需要从余额里面扣除的钱
		if(0 >= $balanceCast){
			$delta = 0;
		}else{
			if($income < $balanceCast){
				$delta = $income; //如果里程费用,则余额优先扣除里程费
			}else{
				$delta = $balanceCast; //现金支付得足够多, 余额全部用来做订单费
			}
		}
		$favorable = $this->getFavorable();
		if( $favorable >= $delta){
			$delta = 0;//如果 实体卡和优惠券能够抵消余额
		}else{
			$delta = $delta - $favorable;
		}
		$format = 'income user delta, order_id|%s|order_money|%s|price|%s|income|%s|favorable|%s|expected|%s|delta|%s';
		EdjLog::info(sprintf($format, $this->getOrderId(), $orderMoney, $price, $income, $favorable, $income, $delta));
		return $delta;
	}

	/**
	 * 返回普通用户应该支付的钱数
	 *
	 * @return mixed 订单金额 - 所付现金 - 实体卡金额 - 优惠券抵消部分
	 *
	 */
	private function getNormalAmount(){
		$costType = $this->getCostType();
		$income = $this->getIncome();
		$price  = $this->getPrice();
		$couponMoney = $this->getCoupon();
		$bonusMoney  = 0;
		if(4 == $costType){//先扣优惠劵，再扣个人帐户
			$bonusMoney = $this->getBonusMoney();
		}
		$total  = $income - $price - $couponMoney - $bonusMoney;
		return $total;
	}

	/**
	 * 返回应该从vip账户里面扣除的钱数
	 *
	 * @return mixed 订单金额 - 所付现金 - 实体卡金额
	 */
	private function getVipAmount($excepted){
		//get counpon
		$couponMoney = $this->getCoupon();
		$price		 = $this->getPrice();
		$income 	 = $this->getIncome();
		//本次代驾服务要从vip中扣除的费用
		$total = $income - $price - $couponMoney;
		return $total;
	}

	private function getBonusMoney(){
		//TODO ... impl get used bonus money
	}

	/**
	 * @param mixed $customer
	 */
	public function setCustomer($customer)
	{
		$this->customer = $customer;
	}

	/**
	 * @return mixed
	 */
	public function getCustomer()
	{
		return $this->customer;
	}



	/**
	 * @param mixed $vip
	 */
	public function setVip($vip)
	{
		$this->vip = $vip;
	}

	/**
	 * @return mixed
	 */
	public function getVip()
	{
		return $this->vip;
	}
}