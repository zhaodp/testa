<?php

/**
 * 订单收入,信息费扣除
 *
 * User: tuan
 * Date: 14/10/27
 * Time: 14:51
 */
class DefaultInformationSettlement  extends OrderSettle{

	/** 信息费流水的type */
	private static $EMPLOYEE_ACCOUNT_TYPE = EmployeeAccount::TYPE_ORDER_INFOMATION;
	/** 信息费流水的channel 参考orderSettlement::order_information */
	private static $EMPLOYEE_ACCOUNT_CHANNEL = EmployeeAccount::TYPE_ORDER_INFOMATION;
	/** 司机流水表备注 */
	private static $COMMENT_FORMAT = '信息费 单号:%s';
	/** log 记录 订单号|订单金额(信息费基数)|计算的信息费|实际扣除信息费 */
	private static $LOG_FORMAT	   = '司机信息费扣除 ---- order_id|%s|income|%s|cast|%s|actual_cast|%s|';

	/**
	 * 结账
	 *
	 * @return array
	 */
	public function settlement(){
		$this->printLog('DefaultInformationSettlement get in ---- '.$this->getOrderId());
		$this->orderValidator();
		//order driver
		return $this->orderDriver();
	}

	/**
	 * 扣司机的信息费
	 */
	private function orderDriver(){
		$cast = $this->getCast();
		$delta = $cast;
		$employeeAccountAttributes = array();
		$employeeAccountAttributes['type'] = self::$EMPLOYEE_ACCOUNT_TYPE;
		$employeeAccountAttributes['channel'] = self::$EMPLOYEE_ACCOUNT_CHANNEL;
		$employeeAccountAttributes['city_id'] = $this->getCityId();
		$employeeAccountAttributes['user'] = $this->getDriverId();
		$employeeAccountAttributes['order_id'] = $this->getOrderId();
		$employeeAccountAttributes['order_date'] = $this->getOrderCreated();
		$employeeAccountAttributes['cast'] = $delta;
		$employeeAccountAttributes['comment'] = $this->getComment();
		$code = FinanceConstants::CODE_FAIL;
		$actualCast = 0;
		try {
			$ret = $this->chargeDriver($employeeAccountAttributes);
			if ($ret) {
				//update order
				$code = FinanceConstants::CODE_SUCCESS;
				$actualCast = $cast;
				$this->updateOrder($cast);
			} else {
				$message = 'charge driver in default information settlement fail';
				$this->alarm($message);
			}
		} catch (Exception $e) {
			$message = sprintf(self::$LOG_FORMAT,$this->getOrderId(), $this->getIncome(), $cast, $actualCast);
			$this->alarm($message.$e->getMessage());
			EdjLog::error($message.$e->getMessage());
		}
		return array(
			'code'	=> $code,
		);
	}

	/**
	 * 返回计算好的信息费
	 */
	private function getCast(){
		$cast = CityConfig::model()->calculatorCast($this->getOrder());
		return $cast;
	}

	/**
	 * 更新order里面字段
	 *
	 * @param $cast
	 */
	private function updateOrder($cast){
		$order = $this->getOrder();
		// 订单信息费写回order表中cast字段
		Order::model()->updateByPk($order['order_id'], array(
			'cast' => $cast
		));
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