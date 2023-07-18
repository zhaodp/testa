<?php

/**
 * 新客优惠活动结账
 * User: tuan
 * Date: 9/28/14
 * Time: 16:46
 */
class NewCustomerActivitySettlement extends ActivitySettlement
{

	public function __construct($activity, $order, $orderExt)
	{
		parent::__construct($activity, $order, $orderExt);
	}

	protected function init()
	{
		parent::init();
		if(null == $this->getActivity()){
//			$this->setActivity();
		}
	}

	/**
	 * 新客优惠活动结账
	 */
	public function settlement()
	{
		if (!$this->legalActivity() || !$this->legalOrder()) {
			throw new Exception('illegal activity or illegal order');
		}
		$activityStatus = $this->isActivityOn();
		if ($activityStatus) {
			$this->init();
			$this->orderVip();
			$this->orderNormal();
			$this->orderDriver();
		} else {
			EdjLog::info('activity is not turn on '.serialize($this));
		}
	}

	/** 处理vip */
	function orderVip()
	{
		$vip = $this->getVip();
		if (empty($vip)) {
			return;
		}
		$order = $this->getOrder();
		$activity = $this->getActivity();
		$activityName = $activity->getName();
		$delta = $this->getVipOrderBalance();
		//
		$vipBalance = $vip['amount'];
		$vipCard = $order['vipcard'];
		$vipTradeAttributes = array(
			'vipcard' => $vipCard,
			'order_id' => $order['order_id'],
			'type' => VipTrade::TYPE_SUBSIDY,
			'source' => VipTrade::TRANS_SOURCE_RE,
			'amount' => $delta,
			'comment' => $activityName . ' 单号：' . $order['order_id'],
			'balance' => $vipBalance + $delta,
			'order_date' => $order['created']
		);

		$vipBalanceAttributes = array(
			'vipCard' => $vipCard,
			'delta' => $delta,
		);
		$ret = $this->chargeVip($vipTradeAttributes, $vipBalanceAttributes);
//		//如果余额少了,增加通知给其他司机
//		if($delta < 0 ){
//			$this->notify($order['phone']);
//		}
		//TODO ... add log;
	}

	/** 处理normal customer */
	function orderNormal()
	{
		$customer = $this->getCustomer();
		if (empty($customer)) {
			return;
		}
		$order = $this->getOrder();
		$userId = $customer['id'];
		$orderId = $order['order_id'];
		$customerBalance = $customer['amount'];
		$delta = $this->getCustomerOrderBalance();
		$activity = $this->getActivity();
		$activityName = $activity->getName();
		$customerTransAttributes = array(
			'user_id' => $userId,
			'trans_order_id' => $orderId,
			'trans_type' => CarCustomerTrans::TRANS_TYPE_RE,
			'amount' => $delta,
			'balance' => $customerBalance + $delta,
			'source' => CarCustomerTrans::TRANS_SOURCE_RE,
			'remark' => $activityName . ' 订单号：' . $orderId,
		);
		$customerAccountAttributes = array(
			'user_id' => $userId,
			'amount' => $delta,
		);
		$ret = $this->chargeNormal($customerTransAttributes, $customerAccountAttributes);
	}

	/** 处理 driver */
	function orderDriver()
	{
		$driver = $this->getDriver();
		if (empty($driver)) {
			return;
		}
		$order = $this->getOrder();
		if ($order['price'] <= 0) {
			return;
		}
		$activity = $this->getActivity();
		$activityName = $activity->getName();
		$activityChannel = EmployeeAccount::CHANNEL_NANJING_ACTIVE;
		$employeeAccountAttributes = array();
		$employeeAccountAttributes['channel'] = $activityChannel;
		$employeeAccountAttributes['comment'] = $activityName . ' 单号 ' . $order['order_id'];
		$employeeAccountAttributes['order_date'] = time();
		$employeeAccountAttributes['type'] = EmployeeAccount::TYPE_ACTIVE;
		$employeeAccountAttributes['city_id'] = $order['city_id'];
		$employeeAccountAttributes['operator'] = 'system';
		$employeeAccountAttributes['user'] = $order['driver_id'];
		$employeeAccountAttributes['cast'] = $order['price'];
		$employeeAccountAttributes['order_id'] = $order['order_id'];
		$this->chargeDriver($employeeAccountAttributes);
	}

	/**
	 * 得到vip某个订单将扣除的余额是多少
	 *
	 * @param $order
	 * @return float
	 */
	private function getVipOrderBalance()
	{
		$order = $this->getOrder();
		//实体卡金额
		$coupon_money = 0.0;
		$order_ext = $this->getOrderExt();
		if ($order_ext && (!empty($order_ext['coupon_money']))) {
			$coupon_money = ($order_ext['coupon_money']);
		}
		$total = $order['income'] - $order['price'] - $coupon_money;
		return $total > 0.0 ? $total : 0.0;
	}

	/**
	 * 得到普通用户某个订单要扣除的余额是多少
	 *
	 * @param $order
	 * @return float
	 */
	public function getCustomerOrderBalance()
	{
		$order = $this->getOrder();
		//实体卡金额
		$coupon_money = 0.0;
		$order_ext = $this->getOrderExt();
		if ($order_ext && (!empty($order_ext['coupon_money']))) {
			$coupon_money = $order_ext['coupon_money'];
		}
		$bonus_money = 0.0;
		$bonus = CustomerBonus::model()->checkedBonusUseByOrderID($order['order_id']);
		if ($bonus && (!empty($bonus['use_money']))) {
			$bonus_money = $bonus['use_money'];
		}
		//本次代驾服务要从用户中扣除的费用
		$money = $order['income'] - $order['price'] - $coupon_money - $bonus_money;
		return $money > 0 ? $money : 0.0;
	}
}
