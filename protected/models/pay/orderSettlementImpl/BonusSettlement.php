<?php
/**
 * 处理优惠券
 *
 * Created by PhpStorm.
 * User: tuan
 * Date: 14/10/28
 * Time: 10:48
 */

class BonusSettlement extends OrderSettle{

	private static $BONUS_COST_TYPE_LIST  = array(2, 4);

	private $customerBonus = null;

	private static $DRIVER_ACCOUNT_FORMAT = '优惠券充值 号码:%s';

	public function settlement(){
//		$this->orderValidator();
//		check_cost_type
		$costType = $this->getCostType();
		if(!in_array($costType, self::$BONUS_COST_TYPE_LIST)){
			return array(
				'code'	=> FinanceConstants::CODE_SUCCESS,
			);
		}
		//bonus use
		$ret =  $this->orderBonus();
		//对于新客优惠码进行处理
		$this->orderDriverBonus();
		return $ret;
	}

	private function orderBonus(){
		$code = FinanceConstants::CODE_FAIL;
		if($this->legalBonus()){
			$bonus = $this->getCustomerBonus();
			$bonusSn 	= $bonus['bonus_sn'];
			$userPhone 	= $this->getUserPhone();
			$orderId	= $this->getOrderId();
			$income		= FinanceCastHelper::getOrderIncome($this->getOrder(), $this->getOrderExt());//优惠券应该是可以抵消补贴和小费的
			$balance 	= 0;
			try {
				if ($bonus['back_type'] == 1) {//按照消费金额返还
					//优惠劵使用
					$balance = BonusLibrary::model()->BonusUsed($userPhone, $orderId, $income);
				} else {
					$balance = $bonus['balance'];
					//优惠劵使用
					BonusLibrary::model()->BonusUsed($userPhone, $orderId, $income);
				}
			} catch (Exception $e) {
				$this->alarm('bonus used error');
				EdjLog::error('bonus used error'.$e->getMessage());
			}
			if($balance > 0){
				$type = EmployeeAccount::TYPE_BONUS_CASH;
				$channel = EmployeeAccount::TYPE_BONUS_CASH;
				$comment = sprintf(self::$DRIVER_ACCOUNT_FORMAT, $bonusSn);
				$code = FinanceConstants::CODE_SUCCESS;
				$this->orderDriver($type, $channel, $balance, $comment);
			}
		}
		return array(
			'code'	=> $code,
		);
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
			$message = 'charge driver in bonus settlement fail';
			$this->alarm($message);
		}
	}

	private function orderDriverBonus(){
		try{
			$driverBonusSettlement = new DriverBonusSettlement($this->getOrder(), $this->getOrderExt(), 0);
			$driverBonusSettlement->setCustomerBonus($this->getCustomerBonus());
			$driverBonusSettlement->settlement();
		}catch (Exception $e){
			EdjLog::error($e->getMessage());
		}
	}

	private function getDriverComment(){
		return sprintf(self::$DRIVER_ACCOUNT_FORMAT, $this->getBonusSn());
	}

	private function getDelta(){
		return $this->getBonusCast();
	}

	/**
	 * 优惠券是否合法
	 */
	private function legalBonus(){
		return !(is_null($this->getCustomerBonus()));
	}

	/**
	 * @param null $customerBonus
	 */
	public function setCustomerBonus($customerBonus)
	{
		$this->customerBonus = $customerBonus;
	}

	/**
	 * @return null
	 */
	public function getCustomerBonus()
	{
		if(is_null($this->customerBonus)){
			$bonus = CustomerBonus::model()->getBonusUse($this->getUserPhone(), $this->getOrderId());
			$this->setCustomerBonus($bonus);
		}
		return $this->customerBonus;
	}
}