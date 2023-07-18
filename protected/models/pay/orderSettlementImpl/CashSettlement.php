<?php
/**
 * 对于订单收入,插入一个流水
 * 订单收入,包含这个订单的所有收入()
 *
 * Created by PhpStorm.
 * User: tuan
 * Date: 14/10/27
 * Time: 16:11
 */

class CashSettlement extends OrderSettle{
	/** 信息费流水的type */
	private static $EMPLOYEE_ACCOUNT_TYPE = EmployeeAccount::TYPE_ORDER_CASH;

	/** 信息费流水的channel 参考orderSettlement::order_information */
	private static $EMPLOYEE_ACCOUNT_CHANNEL = EmployeeAccount::TYPE_ORDER_CASH;

	/** 现金收入的 channel  */
	private $channel = EmployeeAccount::TYPE_ORDER_CASH;

	private static $COMMENT_FORMAT = '现金收入 单号:%s';

	/** @var 额外的一些要记入收入的钱  */
	private $extraCast = 0;

	public function settlement(){
		$this->orderValidator();
		return $this->orderDriver();
	}

	private function orderDriver(){
		$code = FinanceConstants::CODE_FAIL;
		$cast = $this->getCast();
		$employeeAccountAttributes = array();
		$employeeAccountAttributes['type'] = self::$EMPLOYEE_ACCOUNT_TYPE;
		$employeeAccountAttributes['channel'] = $this->getChannel();
		$employeeAccountAttributes['city_id'] = $this->getCityId();
		$employeeAccountAttributes['user'] = $this->getDriverId();
		$employeeAccountAttributes['order_id'] = $this->getOrderId();
		$employeeAccountAttributes['order_date'] = $this->getOrderCreated();
		$employeeAccountAttributes['cast'] = $cast;
		$employeeAccountAttributes['comment'] = $this->getComment();
		$ret = $this->chargeDriver($employeeAccountAttributes);
		if($ret){
			$code = FinanceConstants::CODE_SUCCESS;
		}else{
			$message = 'charge driver in cash  settlement fail';
			$this->alarm($message);
		}
		return $ret;
	}

	/**
	 * 返回计算好的花费
	 */
	private function getCast(){
		$total  = FinanceCastHelper::getOrderTotalMoney($this->getOrder(), $this->getOrderExt(), null, true);
		return $total + $this->getExtraCast();
	}


	/**
	 * 返回写入流水表的备注信息
	 *
	 * @return string
	 */
	private function getComment(){
		return sprintf(self::$COMMENT_FORMAT, $this->getOrderId());
	}

	/**
	 * @return mixed
	 */
	public function getChannel()
	{
		return $this->channel;
	}

	/**
	 * @param mixed $channel
	 */
	public function setChannel($channel)
	{
		$this->channel = $channel;
	}

	/**
	 * @return | 金额
	 */
	public function getExtraCast()
	{
		return $this->extraCast;
	}

	/**
	 * @param |金额 $extraCast
	 */
	public function setExtraCast($extraCast)
	{
		$this->extraCast = $extraCast;
	}
}