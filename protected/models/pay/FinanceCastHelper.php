<?php
/**
 * 财务用来计算钱的一个帮助类
 *
 * Created by PhpStorm.
 * User: tuan
 * Date: 14/10/29
 * Time: 13:54
 */

final class FinanceCastHelper {


	/**
	 * 返回日间业务额外需要补贴的钱
	 *
	 * @param $cityId
	 * @param $hours
	 * @return int
	 */
	public static function getDayTimeExtraTimeSubsidy($cityId, $hours){
		$price = FinanceConfigUtil::getDayTimeSubsidyPrice($cityId);
		return $price * $hours;
	}

	/**
	 * 返回一个订单的明细
	 *
	 * @param $orderId
	 * @return array
	 */
	public static function getOrderFeeDetail($orderId, $show = 0){
		$ret = array();
		$order = Order::model()->getOrderById($orderId);
		$orderExt = OrderExt::model()->getPrimary($orderId);
		$isRemote = FinanceUtils::isRemoteOrder($order, $orderExt);
        $isShowDisTance = 0;//是否显示里程
		if($isRemote){
			//补贴 小费 里程费
			$ret = self::getRemoteOrderDetail($order, $orderExt, $show);
		}else{
			$orderType = FinanceUtils::getOrderType($order);
			if(FinanceConstants::ORDER_TYPE_DEFAULT == $orderType){//普通订单
				$ret = self::getDefaultOrderDetail($order, $orderExt, $show);
			}
			if(FinanceConstants::ORDER_TYPE_DAYTIME == $orderType){//日间业务的订单
				$ret = self::getDaytimeOrderDetail($orderId, true, $order['phone'], $show);
                $isShowDisTance = 1;
			}
			if(FinanceConstants::ORDER_TYPE_UNIT == $orderType){
			}
		}
		//获取等候费
		//获取收入项 和结算项
		$ret['collection_fee'] = self::getCollectionFee($order, $orderExt, $show,$isShowDisTance);
		$ret['settle_fee']     = self::getSettleFee($order, $orderExt, $show);
		return $ret;
	}

	/**
	 * 获得订单的收入项
	 *
	 * @param $order
	 * @param $orderExt
	 * @param $show
	 */
	public static function getCollectionFee($order, $orderExt, $show,$isShowDisTance=0){
		$ret  = array();
        $fee = OrderExt::model()->getBadWeatherSurchargeByOrderId($order['order_id']);//调用董坤接口获取恶劣天气加价的金额
        if($fee){
            if($show){
                $format = '(%s)%s元';
                $ret[] = array(
                    'key' => '调整费',
                    'value' => sprintf($format, '恶劣天气加价',$fee),
                );
            }
        }
		$modify = self::getOrderModifyFee($order['order_id']);
		if($modify){
			if($show){
				$format = '(%s)%s元';
				$ret[] = array(
								'key' => '调整费',
								'value' => sprintf($format, $modify['name'], $modify['amount'] * -1), //* -1 是为了展示
				);
			}
		}
        if($isShowDisTance && $order['distance'] > 0){
            $ret[] = array(
                'key' => '里程',
                'value' => $order['distance']."km",
            );
        }
		return $ret;
	}

	/**
	 * 获得订单的结算项
	 *
	 * @param $order
	 * @param $orderExt
	 * @param $show
	 */
	public static function getSettleFee($order, $orderExt, $show){
		$ret = array();
		return $ret;
	}

	/**
	 * 获得普通订单的详细
	 *
	 * @param $order
	 * @param $orderExt
	 * @param int $show
	 * @return array
	 */
	public static function getDefaultOrderDetail($order, $orderExt, $show = 0){
		$ret = array();
		$kiloFee = FinanceCastHelper::getOrderTotalMoney($order, $orderExt, null, true);
		$orderId = $order['order_id'];
		$phone   = $order['phone'];
		$bonus   = CustomerBonus::getOrderBonus($phone, $orderId);
		$bonusSn = '';
		if($bonus && ($bonus['used'] > 0)  && (BonusCode::SN_TYPE_COUNPON != $bonus['sn_type'])){
			$bonusSn = $bonus['bonus'];
		}
		$bonusMessage  = '';
		if(!empty($bonusSn)){
			$bonusInfo = BBonus::model()->getBonusInfoBySn($bonusSn, $orderId, $phone);
			if($bonusInfo ){
				$bonusMessage  = $bonusInfo;
			}
		}
		if($show){
			$waitTime = isset($orderExt['wait_time']) ? $orderExt['wait_time'] : 0;
			if($waitTime > 30){ //如果大于30分钟,就需要展示等候费
				$orderSnapshoot = OrderSnapshoot::model()->getLatestSnapshoot($orderId);
				$waitPrice = isset($orderSnapshoot['wait_price']) ? $orderSnapshoot['wait_price'] : 0;
				$kiloFee  = $kiloFee -$waitPrice;
				if($waitPrice > 0){
					$tmp = FinanceUtils::convertTimeString($waitTime);
					$format = '(%s)%s元';
					$waitPrice = sprintf($format, $tmp, $waitPrice);
					$ret['waiting_fee'] = $waitPrice;
				}
			}
			if($kiloFee < 0){
				$kiloFee = 0;
			}
			$kiloFee = $kiloFee.'元';
		}
		$ret['kilo_fee'] = $kiloFee;
		$ret['coupon_detail'] = $bonusMessage;
		EdjLog::info(' default order detail ---- order_id'.$order['order_id'].' result ---'.json_encode($ret));
		return $ret;
	}

	/**
	 * 获取远程订单的明细
	 *
	 * @param $order | 必须包含order_id
	 * @param $orderExt
	 * @param $show | 如果为 true 就返回直接用来展示的字符串
	 * @return array
	 */
	public function getRemoteOrderDetail($order, $orderExt, $show = 0){
		$ret = array();
		$kiloFee = $order['income'];//远程订单的里程费 直接是 order 里面的 income
		$subsidy = self::getSubsidy($order, $orderExt);
		$tip     = self::getOrderFeeByExt($orderExt);
		$tip     = (int)$tip;//转为整型,客户端展示需要
		$orderId = $order['order_id'];
		$phone   = $order['phone'];
		$bonus   = CustomerBonus::getOrderBonus($phone, $orderId);
		$bonusSn = '';
		if($bonus && ($bonus['used'] > 0)  && (BonusCode::SN_TYPE_COUNPON != $bonus['sn_type'])){
			$bonusSn = $bonus['bonus'];
		}
		$modifyFee = 0;
		$meta = FinanceCastHelper::getOrderModifyFee($orderId);
		if($meta){
			$modifyFee = isset($meta['amount']) ? $meta['amount'] : 0;
		}
		$kiloFee = $kiloFee + $modifyFee;
		$bonusMessage  = '';
		if(!empty($bonusSn)){
			$bonusInfo = BBonus::model()->getBonusInfoBySn($bonusSn, $orderId, $phone);
			if($bonusInfo ){
				$bonusMessage  = $bonusInfo;
			}
		}
		if($show){
			$waitTime = isset($orderExt['wait_time']) ? $orderExt['wait_time'] : 0;
			if($waitTime > 30){ //如果大于30分钟,就需要展示等候费
				$orderSnapshoot = OrderSnapshoot::model()->getLatestSnapshoot($orderId);
				$waitPrice = isset($orderSnapshoot['wait_price']) ? $orderSnapshoot['wait_price'] : 0;
				$kiloFee  = $kiloFee - $waitPrice;
				if($waitPrice > 0){
					$tmp = FinanceUtils::convertTimeString($waitTime);
					$format = '(%s)%s元';
					$waitPrice = sprintf($format, $tmp, $waitPrice);
					$ret['waiting_fee'] = $waitPrice;
				}
			}

			if($kiloFee < 0){
				$kiloFee = 0;
			}
			$kiloFee = $kiloFee.'元';
			if(0 == $subsidy){
				$subsidy = '(超时)0元';
			}else{
				$subsidy = $subsidy.'元';
			}
		}
		$ret['kilo_fee']  = $kiloFee;
		$ret['subsidy']   = $subsidy;
		$ret['tip']       = $tip;
		$ret['coupon_detail'] = $bonusMessage;
		EdjLog::info(' remote order detail ---- order_id'.$order['order_id'].' result ---'.json_encode($ret));
		return $ret;
	}

	/**
	 * 获取日间业务的明细
	 *
	 * @param $orderId
	 * @param bool $needBonusInfo | 是否需要优惠券名称, 如果是 false ,后面的 phone 可以不传
	 * @param int $phone
	 * @return array  返回明细的数组,该数组的 key 需要和调用方约定,同时是改业务支持的 key
	 */
	public static function getDaytimeOrderDetail($orderId, $needBonusInfo = true, $phone = 0, $show = 0){
		$ret = array();
		$orderSnapshoot = OrderSnapshoot::model()->getLatestSnapshoot($orderId);
		if($orderSnapshoot){
			$income = isset($orderSnapshoot['income']) ? (Integer)$orderSnapshoot['income'] : 0;
			$price = isset($orderSnapshoot['price']) ? (Integer)$orderSnapshoot['price'] : 0;
			$couponMoney = isset($orderSnapshoot['coupon_money']) ? $orderSnapshoot['coupon_money'] : 0;
			$bonus   = CustomerBonus::getOrderBonus($phone, $orderId);
			$bonusSn = '';
			if($bonus && ($bonus['used'] > 0) && (BonusCode::SN_TYPE_COUNPON != $bonus['sn_type'])){
				$bonusSn = $bonus['bonus'];
			}
			$bonusInfo = array();
			$bonusMessage  = '';
			if(!empty($bonusSn) && $needBonusInfo){
				$bonusInfo = BBonus::model()->getBonusInfoBySn($bonusSn, $orderId, $phone);
				if($bonusInfo){
					$bonusMessage  = $bonusInfo;
				}
			}
			$serveTime      = isset($orderSnapshoot['serve_time']) ? $orderSnapshoot['serve_time'] : 0.00;//单位为秒
			$waitTime      = isset($orderSnapshoot['wait_time']) ? $orderSnapshoot['wait_time'] : 0;//单位为分
			$serveFee       = isset($orderSnapshoot['time_cost']) ? (Integer)$orderSnapshoot['time_cost'] : 0;
			$nightSubsidy   = isset($orderSnapshoot['subsidy']) ? (Integer)$orderSnapshoot['subsidy'] : 0;
			$backSubsidy    = isset($orderSnapshoot['subsidy_back']) ? (Integer)$orderSnapshoot['subsidy_back'] : 0;
			$totalMoney     = $income;

			$meta = self::getOrderModifyFee($orderId);
			if(!empty($meta)){
				$modifyFee = isset($meta['amount']) ? $meta['amount'] : 0;
				$serveFee = $serveFee + $modifyFee;
			}
			if($show){//直接拿过去展示
				if($waitTime > 15){ //等候的时间小余15分钟不计算 15分钟司机端写死了,这里也写死
					$waitTime = $waitTime - 15;
				}else{
					$waitTime = 0;
				}
				$minutes = ceil($serveTime / 60) + $waitTime;//分钟数
				if($minutes < 60){
					$serveTime = $minutes.'分钟';
				}else{
					$hours     = $minutes / 60;//小时数
					if(ceil($hours) == $hours){
						$serveTime = $hours.'小时'.'0分钟';
					}else{
						$serveTime = floor($hours).'小时'.ceil(($hours - floor($hours)) * 60).'分钟';
					}
				}
				$serveFee   = $serveFee.'元';
				$nightSubsidy = $nightSubsidy.'元';
				$backSubsidy  = $backSubsidy.'元';
			}

			 $ret = array(
				'serve_time' => $serveTime,
				'time_cost'     => $serveFee,
				'subsidy' => $nightSubsidy,
				'subsidy_back'  => $backSubsidy,
//				'income'            => $totalMoney,
				'coupon_detail'            => $bonusMessage,
				'price'             => $price,
			);
		}
		EdjLog::info(' day time order detail ---- order_id'.$orderId.' result ---'.json_encode($ret));
		return $ret;
	}

	/**
	 * 返回订单的总金额
	 *
	 * @param $order
	 * @param $orderExt
	 * @param $params
	 * @param bool $flag | 用来标致不同的渠道获取
	 * @return int|订单里程
	 */
	public static function getOrderTotalMoney($order, $orderExt, $params, $flag = false){
		$orderType = FinanceUtils::getOrderType($order);
		$orderId = $order['order_id'];
		$orderSnapshoot = OrderSnapshoot::model()->getLatestSnapshoot($orderId);
		$isRemote = FinanceUtils::isRemoteOrder($order, $orderExt); //对于远程订单,由于历史遗留问题 income 只表示了里程费,不使用 orderSnapshoot 值
		$extraFee = 0;
		if($flag){
			$meta = self::getOrderModifyFee($orderId);
			if($meta){
				$extraFee = isset($meta['amount']) ? $meta['amount'] : 0;
			}
		}
		if($orderSnapshoot && !$isRemote){
			return (Integer)$orderSnapshoot['income'] + $extraFee;
		}
		return self::getOrderIncome($order, $orderExt) + $extraFee;
	}

	/**
	 * 根据订单号回去 订单的小费
	 *
	 * @param $orderId
	 */
	public static function getOrderFeeById($orderId){
		$fee = 0.00;
		$orderExt = OrderExt::model()->getPrimary($orderId);
		if($orderExt){
			$fee = self::getOrderFeeByExt($orderExt);
		}
		return $fee;
	}

	/**
	 * 从 orderExt 里面取小费
	 *
	 * @param array $orderExt
	 * @param  orderId
	 */
	public static function getOrderFeeByExt($orderExt = array()){
		$fee = 0.00;
		try{
			$orderId = isset($orderExt['order_id']) ? $orderExt['order_id'] : 0;
			$fee = OrderExt::getFee($orderExt, $orderId);
		}catch (Exception $e){
		}
		return $fee;
	}

	/**
	 * 获得司机在一段时间之类的总收入 和 订单数
	 *
	 * @param $driverId
	 * @param $timeStart
	 * @param $timeEnd
	 * @return array | total_count | 订单数 | total_income |总收入
	 */
	public static  function getDriverIncome($driverId, $timeStart, $timeEnd){
		$tableName = date("Ym", $timeStart);
		EmployeeAccount::$table_name = $tableName;
		$criteria = new CDbCriteria();
		$criteria->compare('user', $driverId);
		$criteria->compare('type', EmployeeAccount::TYPE_ORDER_CASH);
		$criteria->addCondition('order_id > 0');
		$criteria->addBetweenCondition('created', $timeStart, $timeEnd);
		$criteria->select = 'sum(cast) as cast, count(id) as id';
		$ret = array(
			'total_count' => 0,
			'total_income'  => 0,
		);
		$model = EmployeeAccount::model()->find($criteria);
		if($model){
			$total_count = $model['id'];//因为用 model 无法设置值,就用 id 替代了
			$total_income = $model['cast'];
			$ret['total_count'] = $total_count;
			$ret['total_income'] = $total_income;
		}
		return $ret;
	}

	/**
	 * 返回一个订单能够享受的优惠
	 *
	 * @param $orderId
	 * @param $phone
	 * @return float 实体卡 + 优惠券
	 */
	public static function getOrderFavorable($orderId, $phone){
		$couponMoney = OrderExt::model()->getCouponMoney($orderId);
		$bonusMoney = 0.00;
		$isVip = CustomerMain::model()->isVip($phone);
		if(!$isVip){
			$bonusMoney = BBonus::model()->getBonusMoneyByOrderId($orderId, $phone);
		}
		$favorable = $couponMoney + $bonusMoney;
		return $favorable;
	}

	/**
	 *
	 * 看看一个用户的钱,是否足够支付
	 *
	 * @param $orderId
	 * @param $income
	 * @param $price
	 * @return bool
	 */
	public static function isMoneyEnough($orderId, $income, $price){
		$enough = true;
		$order = Order::model()->getOrderById($orderId);
		$order['income'] = $income;//因为报单的接口,里程费是由司机端上传上来的,库里还没有
		$orderExt = OrderExt::model()->getPrimary($orderId);
		if($order){
			$orderMoney = self::getOrderIncome($order, $orderExt);
			$phone = isset($order['phone']) ? $order['phone'] : 0;
			$userMoney  = self::getMoneyOfUser($orderId, $phone);
			$balanceCast = $orderMoney - $price;
			if($balanceCast > $userMoney){
				$enough = false;
			}
		}
		$format = 'isMoneyEnough order_id|%s|income|%s|price|%s|order_money|%s|user_money|%s| result|%s|';
		EdjLog::info(sprintf($format, $orderId, $income, $price, $orderMoney, $userMoney, $enough));
		return $enough;
	}

	/**
	 * 返回一个用户某个订单时刻的钱
	 *
	 * @param $orderId
	 * @param $phone
	 */
	public static function getMoneyOfUser($orderId, $phone){
		$isVip  = CustomerMain::model()->isVip($phone);
		if($isVip){
			return self::getMoneyOfVip($orderId, $phone);
		}else{
			return self::getMoneyOfNormalUser($orderId, $phone);
		}
	}

	/**
	 * 返回一个普通用户,某个订单有多少钱
	 *
	 * @param $orderId
	 * @param $phone
	 * @return
	 */
	public static function getMoneyOfNormalUser($orderId, $phone){
		$format = 'user|%s|order|%s|sum|%s|balance|%s|bonus_money|%s|coupon_money|%s';
		$sum = 0.00;
		$balance = BUpmpPayOrder::model()->forceGetBalance($phone);
		//优惠券
		$bonusMoney = BBonus::model()->getBonusMoneyByOrderId($orderId, $phone);
		//实体卡
		$couponMoney = OrderExt::model()->getCouponMoney($orderId);
		$sum = $balance + $bonusMoney + $couponMoney;
		EdjLog::info(sprintf($format, $phone, $orderId, $sum, $balance, $bonusMoney, $couponMoney));
		return $sum;
	}

	/**
	 * 返回vip 在一个订单的时候拥有的钱
	 *
	 * @param $orderId
	 * @param $phone
	 * @return (余额 + 实体卡)
	 */
	public static function getMoneyOfVip($orderId, $phone){
		$format = 'user|%s|order|%s|sum|%s|balance|%s|bonus_money|%s|coupon_money|%s';
		$sum  = 0.00;
		$balance = BUpmpPayOrder::model()->forceGetBalance($phone);
		$couponMoney = OrderExt::model()->getCouponMoney($orderId);
		$sum  = $balance + $couponMoney;
		EdjLog::info(sprintf($format, $phone, $orderId, $sum, $balance, 0, $couponMoney));
		return $sum;
	}


	/**
	 * 返回一个订单应该扣用户多少钱 (订单里程 + 补贴 + 小费)
	 *
	 * @param $order
	 * @param $orderExt
	 * @return 订单里程 + 补贴 + 小费
	 */
	public static function getOrderIncome($order, $orderExt){
		if(empty($order)){
			return 0.00;
		}
		// income + subsidy + fee
		$income = isset($order['income']) ? $order['income'] : 0.00;
		$fee	= self::getOrderFeeByExt($orderExt);
		$subsidyMoney = self::getSubsidy($order, $orderExt);
		$format = 'get order income orderId|%s|income|%s|subsidy_money|%s|fee|%s|';
		$orderId = isset($order['order_id']) ? $order['order_id'] : 0;
		if(empty($orderId)){
			EdjLog::info('order_id is empty --- '.serialize($order));
		}
		EdjLog::info(sprintf($format, $orderId, $income, $subsidyMoney, $fee));
		return $income + $fee + $subsidyMoney;
	}

	/**
	 * 根据订单和订单扩展返回订单的补贴信息
	 *
	 * @param $order
	 * @param $orderExt
	 */
	public static function getSubsidy($order, $orderExt){
		if(empty($orderExt)){
			return 0.00;
		}
		$channel = isset($order['channel']) ? $order['channel'] : 0;
		if(CustomerApiOrder::QUEUE_CHANNEL_REMOTEORDER != $channel){ //如果 channel 不满足,那么就不是远程订单
			return 0.00;
		}
		$orderId = isset($order['order_id']) ? $order['order_id'] : 0;
		$cityId = isset($order['city_id']) ? $order['city_id'] : 0;
		$distance = $orderExt['linear_ready_distance'];
		$readyTime = $orderExt['driver_ready_time'];
		$format = 'getSubsidy -- order_id | %s ';
		EdjLog::info(sprintf($format, $orderId));
		return self::getSubsidyOfRemoteOrder($cityId, $distance, $readyTime);
	}

	/**
	 * 返回远程订单
	 *
	 * @param $cityId | 城市id
	 * @param $readyDistance | 叫单的时候里程
	 * @param $readyTime | 就位的时间
	 */
	public static function getSubsidyOfRemoteOrder($cityId, $readyDistance, $readyTime){
		$config = FinanceConfigUtil::remoteOrderConfig($cityId, $readyDistance);
		$distanceCondition = $config['distance'];
		$readyTimeCondition = $config['readyTime'];
		$unit				= $config['unit'];
		$subsidyMoney 		= 0;
		if(($readyDistance > $distanceCondition) && ($readyTime < $readyTimeCondition)){
			$subsidyMoney = round($readyDistance) * $unit;
		}
		$format = 'getSubsidyOfRemoteOrder |ready_distance|%s|city_id|%s|readyTime|%s|config|%s|---- subsidyMoney|%s';
		EdjLog::info(sprintf($format, $readyDistance, $cityId, $readyTime, json_encode($config), $subsidyMoney));
		return $subsidyMoney;
	}

	/**
	 * 返回订单的调整费信息
	 *
	 * @param $orderId
	 * @param string $index
	 * @return array|mixed
	 */
	public static function getOrderModifyFee($orderId)
	{
		$meta = OrderSnapshoot::model()->getSnapshootMeta($orderId, 'modify_fee');
		if (!empty($meta)) {
			$type = $meta['type'];
			$amount = $meta['amount'];
			if (FinanceConstants::TYPE_MODIFY_INCOME == $type) {//如果调整费是增加,司机代收,那么就需要从司机账户扣除
				$meta['amount'] = $amount * -1;
			}
		}
		return $meta;
	}

    /**
     * 给司机充值或扣款
     * @param $driverId
     * @param $cityId
     * @param $comment
     * @param $cast
     * @param $channel
     * @return bool
     */
    public static  function  chargeDriver($driverId, $cityId, $comment, $cast, $channel,$order_id)
    {

        $employeeAccountAttributes = array();
        $employeeAccountAttributes['type'] = ($cast > 0) ? EmployeeAccount::TYPE_INFOMATION : EmployeeAccount::TYPE_FORFEIT;//根据传值正负判断是充值还是扣款
        $employeeAccountAttributes['channel'] = $channel;
        $employeeAccountAttributes['city_id'] = $cityId;
        $employeeAccountAttributes['user'] = $driverId;
        $employeeAccountAttributes['order_id'] = $order_id;
        $employeeAccountAttributes['order_date'] = time();
        $employeeAccountAttributes['cast'] = abs($cast);
        $employeeAccountAttributes['comment'] = $comment;

        try {
            $driverPriceRet = OrderSettlement::model()->insertAccount($employeeAccountAttributes);
            if (!$driverPriceRet) {
                EdjLog::info('----update driver account fail--- ' . json_encode($employeeAccountAttributes));
                return false;
            } else {
                EmployeeAccount::model()->ReloadDriverAmountRedis($employeeAccountAttributes['user']);
                return true;
            }
        } catch (Exception $e) {
            EdjLog::error($e->getMessage());
        }
    }
} 