<?php

/**
 * 司机发卡优惠券, 针对司机发出去的新客优惠码结算
 *
 * Created by PhpStorm.
 * User: tuan
 * Date: 14/11/17
 * Time: 22:05
 */
class DriverBonusSettlement extends OrderSettle {

	private $customerBonus = null;

	private static $LOG_FORMAT = ' driver bonus --- |order_id|%s|bonus_sn|%s|order_driver_id|%s|bonus_driver_id|%s|city_id|%s|cast|%s|status|%s';

	/**
	 * 结算入口
	 *
	 * @return array|void
	 */
	public function settlement(){
		//check is new customer bonus
		if($this->legalBonus()){
			 return $this->orderBonus();
		}
		return array(
			'code' => FinanceConstants::CODE_SUCCESS,
		);
	}

	/**
	 * 新客优惠券扣款
	 *
	 * @return array
	 */
	private function orderBonus(){
		$customerBonus = $this->getCustomerBonus();
		$bonusTypeId = isset($customerBonus['bonus_type_id']) ? $customerBonus['bonus_type_id'] : 0;
		$bonusSn = $customerBonus['bonus_sn'];
		$cityId = 0;
		$type = EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN;
		$channel = EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN;
		$cast = 0;
		$driverId  = $this->getDriverId();
		if(8 == $bonusTypeId){
			//get city id
			$bonusLibrary	= BonusLibrary::model()->getBonusByBonus_sn($bonusSn, 0);//返回的是一个数组
			if(!empty($bonusLibrary)){
				$cityId = $bonusLibrary['city_id'];
			}
			if(empty($cityId)){
				$driver_id = $this->getDriverId();
			}else{
				$driver_num = substr($bonusSn, strlen(trim($cityId)));
				$cityPrefix = Dict::item("bonus_city", $cityId);
				$driver_id = $cityPrefix . $driver_num;
			}
			$driver = Driver::model()->getDriver($driver_id);
			if ($driver && $driver['city_id'] != 0) {
				$city = $driver['city_id'];
			} else {
				$city = $this->getCityId();
			}

			//起步价为19的 反司机10元
			$cast = 20;
			$city_arr = RCityList::model()->getCityFeeEq(19); //Common::getCityFeeEq19();
			if (in_array($city, $city_arr)) {
				$cast = 10;
			}
		}
		$ret = array();
		if(0 != $cast){
			$ret = $this->orderDriver($type, $channel, $cast, $cityId, $driverId, $bonusSn);
		}else{
			$ret =  array(
				'code' => FinanceConstants::CODE_SUCCESS,
			);
		}
		$status = 'fail';
		if(FinanceConstants::isSuccess($ret)){
			$status = 'success';
		}
		$log = sprintf(self::$LOG_FORMAT, $this->getOrderId(), $bonusSn, $this->getDriverId(), $driverId, $cityId, $cast, $status);
		$this->printLog($log);
		return $ret;
	}

	/**
	 * 是否可以正常使用的优惠券
	 *
	 * @return bool
	 */
	private function legalBonus(){
		$customerBonus = $this->getCustomerBonus();
		return (!is_null($customerBonus)) && (!empty($customerBonus)) && (!empty($customerBonus['bonus_sn']));
	}

	/**
	 * 新客优惠码给司机返现
	 *
	 * @param $type
	 * @param $channel
	 * @param $cast
	 * @param $cityId
	 * @param $driverId
	 * @param $bonusSn
	 * @return array
	 */
	private function orderDriver($type, $channel, $cast, $cityId, $driverId, $bonusSn){
		$code = FinanceConstants::CODE_FAIL;
		$order = $this->getOrder();
		$employeeAccountAttributes = array();
		$employeeAccountAttributes['type'] = $type;
		$employeeAccountAttributes['channel'] = $channel;
		$employeeAccountAttributes['city_id'] = $cityId;
		$employeeAccountAttributes['user'] = $driverId;
		$employeeAccountAttributes['order_id'] = $this->getOrderId();
		$employeeAccountAttributes['order_date'] = $order['created'];
		$employeeAccountAttributes['cast'] = $cast;
		$employeeAccountAttributes['comment'] = $this->getComment($bonusSn);
		$ret = $this->chargeDriver($employeeAccountAttributes);
		if($ret){
			$code = FinanceConstants::CODE_SUCCESS;
		}else{
			$message = 'charge driver in driver bonus settlement fail';
			$this->alarm($message);
		}
		return array(
			'code'	=> $code,
		);
	}

	/**
	 * 返回写入流水表的备注信息
	 *
	 * @return string
	 */
	private function getComment($bonusSn){
		$format = '优惠券返现 号码:%s';
		return sprintf($format, $bonusSn);
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