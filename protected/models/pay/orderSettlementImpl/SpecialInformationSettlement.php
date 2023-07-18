<?php
/**
 * 提供信息费设置方法,只会修改司机余额
 *
 * Created by PhpStorm.
 * User: tuan
 * Date: 14/10/29
 * Time: 10:58
 */

class SpecialInformationSettlement extends OrderSettle{
	/** 扣信息费的基数  */
	private $base = 0.00;
	/** 扣信息费的比率 默认20% */
	private $rate = 0.2; //TODO ...

	/** 信息费流水的type */
	private $channel = EmployeeAccount::TYPE_ORDER_INFOMATION;

	private $comment = '';

	/** 信息费流水的type */
	private static $EMPLOYEE_ACCOUNT_TYPE = EmployeeAccount::TYPE_ORDER_INFOMATION;

	/**
	 * 结账
	 *
	 * @return array|void
	 */
	public function settlement(){
		if($this->illegal()){
			return;
		}
		return $this->orderDriver();
	}

	/**
	 * 检查这次扣除信息费是否正确
	 *
	 * @return bool
	 */
	private function illegal(){
		return ($this->getBase()) == 0;
	}

	private function orderDriver(){
		$cast = $this->getCast();
//		$delta = $cast * -1;
		$delta = $cast;
		$employeeAccountAttributes = array();
		$employeeAccountAttributes['type'] = self::$EMPLOYEE_ACCOUNT_TYPE;
		$employeeAccountAttributes['channel'] = $this->getChannel();
		$employeeAccountAttributes['city_id'] = $this->getCityId();
		$employeeAccountAttributes['user'] = $this->getDriverId();
		$employeeAccountAttributes['order_id'] = $this->getOrderId();
		$employeeAccountAttributes['order_date'] = $this->getOrderCreated();
		$employeeAccountAttributes['cast'] = $delta;
		$employeeAccountAttributes['comment'] = $this->getComment();
		$ret = $this->chargeDriver($employeeAccountAttributes);
		$code = FinanceConstants::CODE_FAIL;
		if($ret){
			 $code = FinanceConstants::CODE_SUCCESS;
		}
		return  array(
			'code'	=> $code,
		);
	}

	private function getCast(){
		return $this->getBase() * $this->getRate();
	}


	/**
	 * @param mixed $base
	 */
	public function setBase($base)
	{
		$this->base = $base;
	}

	/**
	 * @return mixed
	 */
	public function getBase()
	{
		return $this->base;
	}

	/**
	 * @param int $channel
	 */
	public function setChannel($channel)
	{
		$this->channel = $channel;
	}

	/**
	 * @return int
	 */
	public function getChannel()
	{
		return $this->channel;
	}

	/**
	 * @param mixed $rate
	 */
	public function setRate($rate)
	{
		$this->rate = $rate;
	}

	/**
	 * @return mixed
	 */
	public function getRate()
	{
		return $this->rate;
	}

	/**
	 * @param string $comment
	 */
	public function setComment($comment)
	{
		$this->comment = $comment;
	}

	/**
	 * @return string
	 */
	public function getComment()
	{
		return $this->comment;
	}
}