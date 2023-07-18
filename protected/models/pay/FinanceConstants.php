<?php

/**
 * 订单财务常量
 *
 * User: tuan
 * Date: 14/10/27
 * Time: 15:02
 */

final class FinanceConstants {
	/** 默认成功的code */
	const  CODE_SUCCESS = 0;

	/** 默认失败的code */
	const CODE_FAIL		= 1;
	/**  默认的订单类型 */
	const ORDER_TYPE_DEFAULT = 0;
	/** 日间业务 */
	const ORDER_TYPE_DAYTIME = 1;
	/** 一口价业务 */
	const ORDER_TYPE_UNIT    = 2;
	/** 司机下发 push类型 红包 */
	const DRIVER_PUSH_TYPE_ENVELOPE = 'driver_envelope_deliver';

    /** 默认失败的code */
    const CODE_NO_RECORD 	= 2;
    /** 重复操作 code */
    const CODE_REPEATED	= 3;

	/** 调整费 收入 */
	const TYPE_MODIFY_INCOME = 1;
	/** 调整费 支出 */
	const TYPE_MODIFY_OUTCOME = 2;

	/** 呼叫中心来源订单 */
	const ORDER_SOURCE_CALL_CENTER = 1;

	/** 呼叫中心来源订单 */
	const ORDER_SOURCE_APP = 2;

	// cache key

	const CACHE_KEY_MODIFY_SWITCH = 'MODIFY_FEE_';

	/**
	 * 判断调用是否成功
	 *
	 * @param array $ret
	 * @return bool
	 */
	public static function isSuccess($ret = array()){
		if(empty($ret)){
			return false;
		}

		return isset($ret['code']) && self::CODE_SUCCESS == $ret['code'];
	}

	/**
	 * 判断调用是否失败
	 *
	 * @param array $ret
	 * @return bool
	 */
	public static function isNotSuccess($ret = array()){
		return !self::isSuccess($ret);
	}
} 