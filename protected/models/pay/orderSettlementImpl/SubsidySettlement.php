<?php
/**
 * 补贴结账
 *
 * Created by PhpStorm.
 * User: tuan
 * Date: 14/10/28
 * Time: 11:35
 */

class SubsidySettlement extends  OrderSettle{

	private $subsidyMoney	= 0.00;

	private $vip;

	private $customer;

	private static $CUSTOMER_TRANS_FORMAT = '远程订单补贴 订单号:%s';

	private static $DRIVER_ACCOUNT_FORMAT = '订单远程补贴收入 订单号:%s';

	public function init()
	{
		$order = $this->getOrder();
		$customerPhone = $order['phone'];
		$vipCard = $order['vipcard'];
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
		$this->orderValidator();
		$ret =  null;
		$this->init();
		$isVip = CustomerMain::model()->isVip($this->getUserPhone());
		$ret = null;
		if($isVip){
			$ret = $this->orderVip();
		}else{
			$ret = $this->orderUser();
		}
		return $ret;
	}

	private function orderVip(){
		$vip = $this->getVip();
		if(empty($vip)){
			return array(
				'code'	=> FinanceConstants::CODE_SUCCESS,
			);
		}
		//get order subsidy
		$subsidyMoney = $this->getOrderSubsidy();
		$money = $this->getUserDelta($subsidyMoney);
//		$delta = ($money == 0 ) ? 0 : $money * -1;
		$ret = false;
		if (0 != $money) {
			$delta = $money;//如果 type 为 TYPE_ORDER 插入流水的时候会 * -1
			$vipBalance = $vip['amount'];
			$vipCard = $vip['vipid'];
			$vipTradeAttributes = array(
				'vipcard' => $vipCard,
				'order_id' => $this->getOrderId(),
				'type' => VipTrade::TYPE_ORDER,
				'source' => VipTrade::TRANS_SOURCE_S,
				'amount' => $delta,
				'comment' => $this->getCustomerTransComment(),
				'balance' => $vipBalance - $delta,
				'order_date' => $this->getOrderCreated(),
			);

			$vipBalanceAttributes = array(
				'vipCard' => $vipCard,
				'delta' => $delta * -1,
			);
			$ret = $this->chargeVip($vipTradeAttributes, $vipBalanceAttributes);
			if($ret){
				$type = EmployeeAccount::TYPE_INFOMATION;
				$channel = EmployeeAccount::CHANNEL_REMOTE_ORDER_VIP;
				if(0 == $subsidyMoney){
					$channel = EmployeeAccount::CHANNEL_REMOTE_ORDER_TIMEOUT;
					$money = 0;
				}
				$comment = $this->getDriverAccountComment();
				$this->orderDriver($type, $channel, $money, $comment);
			}else{
				$message = 'charge vip user in subsidy information settlement fail';
				$this->alarm($message);
			}
		}else{
			//如果 不需要从客户的余额里面扣除金额,那么仍然要收取补贴的信息费
			$type = EmployeeAccount::TYPE_INFOMATION;
			$channel = EmployeeAccount::CHANNEL_REMOTE_ORDER_VIP;
			if(0 == $subsidyMoney){
				$channel = EmployeeAccount::CHANNEL_REMOTE_ORDER_TIMEOUT;
				$money = 0;
			}
			$comment = $this->getDriverAccountComment();
			$this->orderDriver($type, $channel, $money, $comment);
		}
	}

	private function orderUser(){
		//checkout
		$customer = $this->getCustomer();
		if(empty($customer)){
			return array(
				'code'	=> FinanceConstants::CODE_SUCCESS,
			);
		}
		$userId = $customer['id'];
		$customerBalance = $customer['amount'];
		$subsidyMoney = $this->getOrderSubsidy();
		$money = $this->getUserDelta($subsidyMoney);
		if (0 != $money) {
			$delta = $money * -1;
			$orderId = $this->getOrderId();
			$customerTransAttributes = array(
				'user_id' => $userId,
				'trans_order_id' => $orderId,
				'trans_type' => CarCustomerTrans::TRANS_TYPE_F,
				'amount' => $delta,
				'balance' => $customerBalance + $delta,
				'source' => CarCustomerTrans::TRANS_SOURCE_REMOTE_ORDER,
				'remark' => $this->getCustomerTransComment(),
			);
			$customerAccountAttributes = array(
				'user_id' => $userId,
				'amount' => $delta,
			);
			$code = FinanceConstants::CODE_FAIL;
			$ret = $this->chargeNormal($customerTransAttributes, $customerAccountAttributes);
			if ($ret) {
				$type = EmployeeAccount::TYPE_INFOMATION;
				$channel = EmployeeAccount::CHANNEL_REMOTE_ORDER_NORMAL;
				if (0 == $subsidyMoney) {
					$channel = EmployeeAccount::CHANNEL_REMOTE_ORDER_TIMEOUT;
					$money = 0;
				}
				$comment = $this->getDriverAccountComment();
				$this->orderDriver($type, $channel, $money, $comment);
			} else {
				$message = 'charge normal user in subsidy settlement fail';
				$this->alarm($message);
			}
			return $ret;
		}else{
			//如果 不需要从客户的余额里面扣除金额,那么仍然要收取补贴的信息费
			$type = EmployeeAccount::TYPE_INFOMATION;
			$channel = EmployeeAccount::CHANNEL_REMOTE_ORDER_NORMAL;
			if(0 == $subsidyMoney){
				$channel = EmployeeAccount::CHANNEL_REMOTE_ORDER_TIMEOUT;
				$money = 0;
			}
			$comment = $this->getDriverAccountComment();
			$this->orderDriver($type, $channel, $money, $comment);
		}
	}

	/**
	 * 给司机充值补贴,并且扣除所有补贴的信息费
	 *
	 * @param $type
	 * @param $channel
	 * @param $delta
	 * @param $comment
	 * @return array
	 */
	private function orderDriver($type, $channel, $delta, $comment){
		$code = FinanceConstants::CODE_FAIL;
		$order = $this->getOrder();
		$employeeAccountAttributes = array();
		$employeeAccountAttributes['channel'] = $channel;
		$employeeAccountAttributes['comment'] = $comment;
		$employeeAccountAttributes['order_date'] = time();
		$employeeAccountAttributes['type'] = $type;
		$employeeAccountAttributes['city_id'] = $order['city_id'];
		$employeeAccountAttributes['operator'] = 'system';
		$employeeAccountAttributes['user'] = $order['driver_id'];
		$employeeAccountAttributes['cast'] = $delta;
		$employeeAccountAttributes['order_id'] = $order['order_id'];
		$ret = false;
		//避免插入 非超时补贴的为0的流水,否则会引起司机端信息费账户明细展示错误
		if((EmployeeAccount::CHANNEL_REMOTE_ORDER_TIMEOUT != $channel) && (0 == $delta)){
			$ret = true;
		}else{
			$ret = $this->chargeDriver($employeeAccountAttributes);
		}
		$needAlarm = false;
		if($ret){
			$subsidyMoney = $this->getOrderSubsidy();
			$informationRet = $this->orderInformation($subsidyMoney);
			if(FinanceConstants::isNotSuccess($informationRet)){
				$needAlarm = true;
			}else{
				$code = FinanceConstants::CODE_SUCCESS;
			}
		}else{
			$needAlarm = true;
		}
		if($needAlarm){
			$message = 'charge driver in subsidy information settlement fail';
			$this->alarm($message);
		}
		return array(
			'code'	=> $code,
		);
	}

	/**
	 * 收取补贴的信息费
	 *
	 * @param $delta
	 * @return array|void
	 */
	private function orderInformation($delta){
		if(0 >= $delta){
			return array(
				'code'	=> FinanceConstants::CODE_SUCCESS,
			);
		}
		$informationSettle = new SpecialInformationSettlement($this->getOrder(), $this->getOrderExt(), $this->getFavorable());
		$delta =  FinanceCastHelper::getSubsidy($this->getOrder(), $this->getOrderExt());//即使是现金支付,也需要收取全部补贴的信息费
		$informationSettle->setBase($delta);
		$informationSettle->setChannel(EmployeeAccount::CHANNEL_REMOTE_ORDER_INFORMATION);
		$format = '订单远程补贴信息费 订单号:%s';
		$comment = sprintf($format, $this->getOrderId());
		$informationSettle->setComment($comment);
		return $informationSettle->settlement();
	}

	/**
	 * 获得补贴的金额
	 *
	 * @return int
	 */
	private function getOrderSubsidy(){
		return FinanceCastHelper::getSubsidy($this->getOrder(), $this->getOrderExt());
	}

	private function getCustomerTransComment(){
		return sprintf(self::$CUSTOMER_TRANS_FORMAT, $this->getOrderId());
	}

	private function getVipTradeComment(){

	}

	/**
	 * 返回能从用户账户扣除的钱
	 *
	 * @param $expected 期望要扣除的钱
	 */
	private function getUserDelta($expected)
	{
		$delta = 0;
		$phone = $this->getUserPhone();
		$price = $this->getPrice();
		$orderMoney = FinanceCastHelper::getOrderIncome($this->getOrder(), $this->getOrderExt());
		$balanceCast = $orderMoney - $price - $this->getIncome();
		if (0 >= $balanceCast) {
			$delta = 0; //不需要从用户的余额里面扣除了, 现金抵消了 补贴和小费
		} else {
			//TODO... 是否需要判断用户余额
			if ($expected > $balanceCast) { // 现金已经可以抵消部分补贴了
				$delta = $balanceCast;
			} else {
				$delta = $expected; //现金支付仍然不够,需要全部从余额里面扣除补贴
			}
		}
		//能够享受到的优惠
		$favorable = $this->getFavorable();
		if(0 < $favorable){
			 //优惠 加起来,已经超过了里程费
			 if($favorable > $this->getIncome()){
				$favorable = $favorable - $this->getIncome();
				 if($favorable > $delta){
					$delta = 0;
				 }else{
					 $delta = $delta - $favorable;
				 }
			 }
		}
		$format = 'subsidy user delta, order_id|%s|order_money|%s|price|%s|income|%s|favorable|%s|expected|%s|delta|%s';
		EdjLog::info(sprintf($format, $this->getOrderId(), $orderMoney, $price,
									$this->getIncome(), $favorable, $expected, $delta));
		return $delta;
	}

	private function getDriverAccountComment(){
		return sprintf(self::$DRIVER_ACCOUNT_FORMAT, $this->getOrderId());
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