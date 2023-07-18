<?php
/**
 * 结账的基类,定义一些基本的方法
 *
 * TODO ... 是否需要锁账户
 *
 * User: tuan
 * Date: 9/28/14
 * Time: 16:11
 */

abstract class Settlement {

	/**
	 * 结账,必须重写
	 */
	abstract  protected function settlement();

	/**
	 * 通知余额变化
	 *
	 * @param $customerPhone
	 */
	protected function notify($customerPhone){
		try{
			BUpmpPayOrder::model()->noticeBalanceChange($customerPhone, false);
		}catch (Exception $e){
			EdjLog::error('noticeBalanceChange --'.$customerPhone);
		}
	}

	/**
	 * 打印日志
	 *
	 * @param $message
	 */
	protected function printLog($message){
		if(empty($message)){
			return false;
		}
		EdjLog::info($message);
	}

	/**
	 * vip的款项
	 */
	protected function chargeVip($vipTradeAttributes, $vipBalanceAttributes){
        // TODO ... return value maybe not ok
        return VipAccountService::orderChargeV2($vipTradeAttributes, $vipBalanceAttributes);
	}

	/**
	 * 普通用户的款项
	 */
	protected function chargeNormal($customerTransAttributes, $customerAccountAttributes){
        // TODO ... return value maybe not ok

        return NormalAccountService::orderChargeV2($customerTransAttributes, $customerAccountAttributes);
	}

	/**
	 * 司机的款项
	 */
	protected function chargeDriver($employeeAccountAttributes){
        // TODO ... return value maybe not ok

        return DriverAccountService::orderChargeV2($employeeAccountAttributes);
	}

} 