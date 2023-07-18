<?php
/**
 * 消费结算
 *
 *
 * Created by PhpStorm.
 * User: tuan
 * Date: 14/10/28
 * Time: 13:26
 */

class TipSettlement extends OrderSettle{

	private $tipMoney = 0.00;

	private $vip;

	private $customer;

	private static $USER_FORMAT = '订单小费 订单号:%s';
	private static $DRIVER_FORMAT = '订单小费 订单号:%s';

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
		$tips = $this->getTipMoney();
		if(0 == $tips){
			return array(
				'code'	=> FinanceConstants::CODE_SUCCESS,
			);
		}
		$this->init();
        $isVip = VipService::service()->isVip($this->getUserPhone());
		$ret = null;
		if($isVip){
			$ret = $this->orderVip();
		}else{
			$ret = $this->orderUser();
		}
//		$this->callBack();
		return $ret;
	}

	private function orderVip()
	{
		$vip = $this->getVip();
		if (empty($vip)) {
			return array(
				'code' => FinanceConstants::CODE_SUCCESS,
			);
		}
		//get order subsidy
		$tipMoney = $this->getTipMoney();
		$money = $this->getUserDelta($tipMoney);
		if (0 != $money) {
			$delta  = $money;//如果 type 为 TYPE_ORDER 插入流水的时候会 * -1
			$vipBalance = $vip['amount'];
			$vipCard = $vip['vipid'];
			$vipTradeAttributes = array(
				'vipcard' => $vipCard,
				'order_id' => $this->getOrderId(),
				'type' => VipTrade::TYPE_ORDER,
				'source' => VipTrade::TRANS_SOURCE_S,
				'amount' => $delta,
				'comment' => $this->getUserComment(),
				'balance' => $vipBalance - $delta,
				'order_date' => $this->getOrderCreated(),
			);

			$vipBalanceAttributes = array(
				'vipCard' => $vipCard,
				'delta' => $delta * -1,
			);
			$code = FinanceConstants::CODE_FAIL;
			$ret = $this->chargeVip($vipTradeAttributes, $vipBalanceAttributes);
			if ($ret) {
				$this->notify($this->getUserPhone());
				$type = EmployeeAccount::TYPE_INFOMATION;
				$channel = EmployeeAccount::CHANNEL_REMOTE_ORDER_VIP_TIPS;
				$comment = $this->getDriverComment();
				$code = FinanceConstants::CODE_SUCCESS;
				if(0 != $delta){//小费为0 ,不记入流水
					$this->orderDriver($type, $channel, $money, $comment);
				}
			} else {
				$message = 'charge vip user in tip  settlement fail';
				$this->alarm($message);
			}
			return array(
				'code' => $code,
			);
		}else{
			return array(
				'code'	=> FinanceConstants::CODE_SUCCESS,
			);
		}
	}

	private function orderUser(){
		//checkout
		$customer = $this->getCustomer();
		if (empty($customer)) {
			return array(
				'code' => FinanceConstants::CODE_SUCCESS,
			);
		}
		$userId = $customer['id'];
		$customerBalance = $customer['amount'];
		$tipMoney = $this->getTipMoney();
		$money = $this->getUserDelta($tipMoney);
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
				'remark' => $this->getUserComment(),
			);
			$customerAccountAttributes = array(
				'user_id' => $userId,
				'amount' => $delta,
			);
			$code = FinanceConstants::CODE_FAIL;
			$ret = $this->chargeNormal($customerTransAttributes, $customerAccountAttributes);
			if ($ret) {
				$this->notify($this->getUserPhone());
				$type = EmployeeAccount::TYPE_INFOMATION;
				$channel = EmployeeAccount::CHANNEL_REMOTE_ORDER_NORMAL_TIPS;
				$comment = $this->getDriverComment();
				$code = FinanceConstants::CODE_SUCCESS;
				if(0 != $delta){//小费为0 ,不记入流水
					$this->orderDriver($type, $channel, $money, $comment);
				}
			} else {
				$message = 'charge normal user in tip  settlement fail';
				$this->alarm($message);
			}
			return array(
				'code' => $code,
			);
		}else{
			return array(
				'code'	=> FinanceConstants::CODE_SUCCESS,
			);
		}
	}

	private function orderDriver($type, $channel, $delta, $comment){
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
		$ret  = $this->chargeDriver($employeeAccountAttributes);
		if(!$ret){
			$message = 'charge driver in tip  settlement fail';
			$this->alarm($message);
		}
	}


	/**
	 * 返回能从用户账户扣除的钱
	 *
	 * @param $expected | 期望要扣除的钱
	 */
	private function getUserDelta($expected)
	{
		$delta = 0;
		$phone = $this->getUserPhone();
		$price = $this->getPrice();
		$orderMoney = FinanceCastHelper::getOrderIncome($this->getOrder(), $this->getOrderExt());
		$subsidyMoney = FinanceCastHelper::getSubsidy($this->getOrder(), $this->getOrderExt());
		$balanceCast = $orderMoney - $price - $this->getIncome() - $subsidyMoney;
		if (0 >= $balanceCast) {
			$delta = 0; //不需要从用户的余额里面扣除了, 现金抵消了小费
		} else {
			//TODO... 是否需要判断用户余额
			if ($expected > $balanceCast) { // 现金已经可以抵消部分小费了
				$delta = $balanceCast;
			} else {
				$delta = $expected; //现金支付仍然不够,需要全部从余额里面扣除补贴
			}
		}
		//能够享受到的优惠
		$favorable = $this->getFavorable();
		if(0 < $favorable){
			//优惠 加起来,已经超过了里程费 + 补贴
			$tmpSum = $this->getIncome() + $subsidyMoney;
			if($favorable > $tmpSum){
				$favorable = $favorable - $tmpSum;
				if($favorable > $delta){
					$delta = 0;
				}else{
					$delta = $delta - $favorable;
				}
			}
		}
		$format = 'tip user delta, order_id|%s|order_money|%s|price|%s|income|%s|subsidy_money|%s| favorable|%s|expected|%s|delta|%s';
		EdjLog::info(sprintf($format, $this->getOrderId(), $orderMoney, $price, $this->getIncome(), $subsidyMoney, $favorable, $expected, $delta));
		return $delta;
	}

	private function getUserComment(){
		return sprintf(self::$USER_FORMAT, $this->getOrderId());
	}

	private function getDriverComment(){
		return sprintf(self::$DRIVER_FORMAT, $this->getOrderId());
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


	/**
	 * @param float $tipMoney
	 */
	public function setTipMoney($tipMoney)
	{
		$this->tipMoney = $tipMoney;
	}

	/**
	 * @return float
	 */
	public function getTipMoney()
	{
		return $this->getFee();
	}

} 