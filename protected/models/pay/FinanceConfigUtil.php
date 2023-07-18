<?php
/**
 * 提供财务配置相关的接口支持,比如开关,比率等
 *
 * Created by PhpStorm.
 * User: tuan
 * Date: 14/10/29
 * Time: 14:02
 */
Yii::import('application.models.schema.customer.*');
Yii::import('application.models.driver.*');
Yii::import('application.models.customer.*');
final class FinanceConfigUtil {

	/** 调整费配置文件 */
	private static $MODIFY_CONFIG = array(
		74 => array(//对于北京 呼叫中心订单,有如下配置
			FinanceConstants::ORDER_SOURCE_CALL_CENTER => array(
				'type' => FinanceConstants::TYPE_MODIFY_INCOME,
				'name' => '400调度费,司机代收',
				'amount' => 4,
			),
//			FinanceConstants::ORDER_SOURCE_APP			=> array(
//				'type'	=> FinanceConstants::TYPE_MODIFY_OUTCOME,
//				'name'  => 'app下单,费用减免',
//				'amount' => 5,
//			),
		),
	);
    /** 400加价城市配置  沈阳19、福州27、石家庄25、贵阳33、洛阳61、汕头 103*/
    private static $ADDPRICE_CITY = array(19,27,25,33,61,103);

    /** 400加价城市配置 */
    private static $ADDPRICE_MODIFY = array(
        FinanceConstants::ORDER_SOURCE_CALL_CENTER => array(
				'type' => FinanceConstants::TYPE_MODIFY_INCOME,
				'name' => '400调度费,司机代收',
				'amount' => 10,
			),
    );

	private static $CONFIG = array(
		//constant ok
		FinanceConstants::ORDER_TYPE_DAYTIME => array(
			'time_cost' => array(
					'type_vip'      => VipTrade::TYPE_ORDER,
					'type_normal'   => CarCustomerTrans::TRANS_TYPE_F,
					'type_driver_vip'   => EmployeeAccount::TYPE_ORDER_VIP,
					'type_driver_normal'   => EmployeeAccount::TYPE_ORDER_NORMAL,
					'source_vip'    => VipTrade::TRANS_SOURCE_DAYTIME_TIME_COST,
					'source_normal' => CarCustomerTrans::TRANS_SOURCE_DAYTIME_TIME_COST,
					'source_driver_vip' => EmployeeAccount::CHANNEL_DAYTIME_ORDER_TIME_COST,
					'source_driver_normal' => EmployeeAccount::CHANNEL_DAYTIME_ORDER_TIME_COST,
					'comment_user'         => '日间业务服务费,单号:%s',
					'comment_driver'         => '日间业务服务费,单号:%s',
			),
			'subsidy' => array(
					'type_vip'      => VipTrade::TYPE_ORDER,
					'type_normal'   => CarCustomerTrans::TRANS_TYPE_F,
					'type_driver_vip'   =>  EmployeeAccount::TYPE_ORDER_VIP,
					'type_driver_normal'   =>  EmployeeAccount::TYPE_ORDER_NORMAL,
					'source_vip'    => VipTrade::TRANS_SOURCE_DAYTIME_SUBSIDY,
					'source_normal' => CarCustomerTrans::TRANS_SOURCE_DAYTIME_SUBSIDY,
					'source_driver_vip' => EmployeeAccount::CHANNEL_DAYTIME_ORDER_SUBSIDY,
					'source_driver_normal' => EmployeeAccount::CHANNEL_DAYTIME_ORDER_SUBSIDY,
					'comment_user'         => '日间业务晚间补贴,单号:%s',
					'comment_driver'         => '日间业务晚间补贴,单号:%s',
			),
			'subsidy_back'  => array(
					'type_vip'      => VipTrade::TYPE_ORDER,
					'type_normal'   => CarCustomerTrans::TRANS_TYPE_F,
					'type_driver_vip'   =>  EmployeeAccount::TYPE_ORDER_VIP,
					'type_driver_normal'   => EmployeeAccount::TYPE_ORDER_NORMAL,
					'source_vip'    => VipTrade::TRANS_SOURCE_DAYTIME_SUBSIDY_BACK,
					'source_normal' => CarCustomerTrans::TRANS_SOURCE_DATETIME_SUBSIDY_BACK,
					'source_driver_vip' => EmployeeAccount::CHANNEL_DAYTIME_ORDER_SUBSIDY_BACK,
					'source_driver_normal' => EmployeeAccount::CHANNEL_DAYTIME_ORDER_SUBSIDY_BACK,
					'comment_user'         => '日间业务返程补贴,单号:%s',
					'comment_driver'         => '日间业务返程补贴,单号:%s',
			),
		),
		FinanceConstants::ORDER_TYPE_UNIT => array(
			'unit_cost'  =>  array(
				'type_vip'      => VipTrade::TYPE_ORDER,
				'type_normal'   => CarCustomerTrans::TRANS_TYPE_F,
				'type_driver_vip'   => EmployeeAccount::TYPE_ORDER_VIP,
				'type_driver_normal'   => EmployeeAccount::TYPE_ORDER_NORMAL,
				'source_vip'    => VipTrade::TRANS_SOURCE_UNIT,
				'source_normal' => CarCustomerTrans::TRANS_SOURCE_UNIT,
				'source_driver_vip' => EmployeeAccount::CHANNEL_ORDER_UNIT,
				'source_driver_normal' => EmployeeAccount::CHANNEL_ORDER_UNIT,
				'comment_user'         => '一口价业务服务费,单号:%s',
				'comment_driver'         => '一口价业务服务费,单号:%s',
			),
		),
	);

	/**
	 * 获取每个类型订单支持扣费参数定义,以及对应的各种 type source
	 *
	 * @param $orderType
	 * @return array
	 */
	public static function getConfigByOrderType($orderType){
		$config = self::$CONFIG;
		if(array_key_exists($orderType, $config)){
			return $config[$orderType];
		}else{
			return array();
		}
	}

	/**
	 * 远程订单的配置
	 *
	 * @param $cityId | 城市id
	 * @param $readyDistance | 就位距离
	 * @return array
	 */
	public static function remoteOrderConfig($cityId, $readyDistance){
		//这里统一走国信司机的配置
		$config = Common::getLongDistanceCity($cityId);
		$reachTime = isset($config['reach_time']) ? $config['reach_time'] : 6 * 60; //默认每公里需要走6分钟
		$distanceCondition = isset($config['start_kilometer']) ? $config['start_kilometer'] : 5; //默认远程订单需要大于5公里
		$unit = isset($config['per_kilometer_price']) ? $config['per_kilometer_price'] : 2; //每公里应该收取的费用
		$readyTime = round($readyDistance) * $reachTime;
		return array(
			'distance'	=> $distanceCondition,
			'readyTime'	=> $readyTime,
			'unit'		=> $unit,
		);
	}

	/**
	 * 返回信息费比率
	 *
	 * @param $orderType
	 * @param $cityId
	 * @return int
	 */
	public static function getInformationRate($orderType, $cityId){
		if(FinanceConstants::ORDER_TYPE_DAYTIME  == $orderType){
            $daytime_type = CityConfigService::dayTimeStatus($cityId);
			if($daytime_type && isset(Yii::app()->params['daytime_price'][$daytime_type])){
                $cast = CityConfigService::dayTimeCast($cityId);
                $function_name = $cast ? 'DaytimeCast'.ucfirst($cast): 'DaytimeCastDefault';
                if(!method_exists('Common',$function_name)){
                    $function_name = 'DaytimeCastDefault';
                }
                $cast_fee = Common::$function_name();
                return $cast_fee;
			}
			return 0;//默认不收取
		}
	}

	/**
	 * 获取价格固定的订单的价格
	 *
	 * @param $orderType | 财务定义的 orderType
	 * @param $cityId
	 * @return int
	 */
	public static function getUnitOrderPrice($orderType, $cityId){
		if(FinanceConstants::ORDER_TYPE_UNIT == $orderType){
            $wash_info = CityConfigService::washCarStatus($cityId);
			if($wash_info){
                $wash = CityConfig::getWashCarPrice($wash_info);
				return ($wash !== false) ? $wash : 19;//默认走19
			}
			return 0;//默认不收取
		}
	}

	/**
	 * 增加日间业务额外补贴的价钱
	 *
	 * @param $cityId
	 * @return int
	 */
	public static function getDayTimeSubsidyPrice($cityId){
        $daytime_type = CityConfigService::dayTimeStatus($cityId);
        if($daytime_type){
            $price = isset(Yii::app()->params['daytime_price'][$daytime_type]) ? Yii::app()->params['daytime_price'][$daytime_type]['subsidy_price']:0;
		    return $price;
        }
		return 0;//默认不补钱
	}

	/**
	 *
	 * 根据订单类型, 获得调整费
	 *
	 * @param $orderSource
	 * @param $cityId
	 * @param array $order
	 * @return mixed
	 */
	public static function getModifyPriceConfig($orderSource, $cityId, $order = array())
	{
		//利用 redis 开关的机制, 达到开启关闭缓存的作用,目前还是需要人工介入
		$switchStatus =  FinanceUtils::isModifyFeeOpen($orderSource, $cityId);
		if(!$switchStatus){
			return array();
		}
//        20150316 1、400加价城市开通沈阳19福州27石家庄25贵阳33洛阳61汕头 103 2、400加价费用10元。（之前丽水城市的4元维持不变 3、VIP用户通过400下单不加价。
        $phone = isset($order['phone']) ? $order['phone'] : '';
        $isVip = false;
        if(!empty($phone)) {
            $isVip = CustomerMain::model()->isVip($phone);
        }
        if($isVip){
            return array();
        }
        if(in_array($cityId, self::$ADDPRICE_CITY)){
            $addprice_modify = self::$ADDPRICE_MODIFY;
            if(isset($addprice_modify[$orderSource])){
                return $addprice_modify[$orderSource];
            }
        }
		if(array_key_exists($cityId, self::$MODIFY_CONFIG)){
			$config = self::$MODIFY_CONFIG[$cityId];
			if(isset($config[$orderSource])){
				return $config[$orderSource];
			}
		}
		return array();
	}
} 