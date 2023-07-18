<?php
/**
 * 保险费扣除
 *
 * User: tuan
 * Date: 14/10/27
 * Time: 16:01
 */

class InsuranceSettlement extends OrderSettle{

	/** 信息费流水的type */
	private static $EMPLOYEE_ACCOUNT_TYPE = EmployeeAccount::TYPE_ORDER_INSURANCE;

	/** 信息费流水的channel 参考orderSettlement::order_information */
	private static $EMPLOYEE_ACCOUNT_CHANNEL = EmployeeAccount::TYPE_ORDER_INSURANCE;

	private static $COMMENT_FORMAT = '保险费 单号:%s';

	public function settlement(){
		$this->orderValidator();
		return $this->orderDriver();
	}

	private function orderDriver(){
		$code = FinanceConstants::CODE_FAIL;
		$order = $this->getOrder();
		$cast = $this->getCast();
//		$delta = $cast * -1;
		$employeeAccountAttributes = array();
		$employeeAccountAttributes['type'] = self::$EMPLOYEE_ACCOUNT_TYPE;
		$employeeAccountAttributes['channel'] = self::$EMPLOYEE_ACCOUNT_CHANNEL;
		$employeeAccountAttributes['city_id'] = $order['city_id'];
		$employeeAccountAttributes['user'] = $order['driver_id'];
		$employeeAccountAttributes['order_id'] = $order['order_id'];
		$employeeAccountAttributes['order_date'] = $order['created'];
		$employeeAccountAttributes['cast'] = $cast;
		$employeeAccountAttributes['comment'] = $this->getComment();
		$ret = $this->chargeDriver($employeeAccountAttributes);
		if($ret){
			$code = FinanceConstants::CODE_SUCCESS;
		}else{
			$message = 'charge driver in insurance information settlement fail';
			$this->alarm($message);
		}
		return array(
			'code'	=> $code,
		);
	}

	/**
	 * 返回计算好的信息费
	 */
	private function getCast(){
		//TODO ... implements  保险费,是否收取
		$driver_fee_discount = Common::driver_fee_discount($this->getDriverId());
		//重庆不收保险费  --mtx 2013-09-16
		//南京 2015-01-01 00:00:00 前不收保险费 duke modified 改版前是写到了
		$cityId = $this->getCityId();
		if ($cityId == 7 || ($cityId == 8 && (time() < strtotime('2015-01-01 00:00:00')))) {
			$driver_fee_discount = 0;
		}
		return 2 * $driver_fee_discount;
	}

	/**
	 * 返回写入流水表的备注信息
	 *
	 * @return string
	 */
	private function getComment(){
		return sprintf(self::$COMMENT_FORMAT, $this->getOrderId());
	}

}