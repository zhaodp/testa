<?php
/**
 *
 * 订单扣款结账,控制逻辑
 *
 * 分月记录司机结账单信息
 * 结账记录插入表为订单创建时间
 * 订单表里面添加了balance report_time
 * 时时修改 t_employee_account_settle 信息
 */
Yii::import('application.models.pay.*');
Yii::import('application.models.redis.*');
Yii::import("application.models.customer.*");
Yii::import("application.models.schema.customer.*");
Yii::import('application.models.pay.settlement.*');
Yii::import('application.models.pay.refund.*');
Yii::import('application.models.pay.subsidy.*');
class OrderSettlement
{
    private static $_models;

    public static function model($className = __CLASS__)
    {
        $model = null;
        if (isset(self::$_models[$className]))
            $model = self::$_models[$className];
        else {
            $model = self::$_models[$className] = new $className(null);
        }
        return $model;
    }

    /**
     * 每天为结账异常的单子重新结账
     * author mengtianxue
     */
    public function orderThrowYearday()
    {

        EmployeeAccount::$table_name = 'throw';
        $table = EmployeeAccount::model()->tableName();

        $pagesize = 500;
        $offset = 0;
        $settle_date = date('Ym', strtotime("-1 day"));
        while (true) {
            $command = Yii::app()->db_finance->createCommand();
            $command->select('*')
                ->from($table)
                ->where('settle_date = :settle_date', array(':settle_date' => $settle_date))
                ->group('order_id')
                ->limit($pagesize, $offset);
            $rows = $command->queryAll();

            if ($rows) {
                //循环订单列表
                foreach ($rows as $item) {
                    echo $item['order_id'];

                    //结账信息  order_account(‘订单号’，‘是否重新结账’)；
                    $account = $this->orderSettle($item['order_id'], 1);
                    if ($account) {
                        echo "结账\n";
                    } else {
                        echo "\n";
                    }
                }
                $offset += $pagesize;
            } else {
                break;
            }
        }

    }


    /**
     * 每个订单结账
     * @param $order_id 订单id
     * @param int $again_settle 是否重新结账 0:为不重新结账 1:为退单结账
     * @return bool
     * author mengtianxue
     */

    public function oldorderSettle($order_id, $again_settle = 0)
    {
        $order = Order::model()->getOrderById($order_id);

        if ($order['status'] == Order::ORDER_COMPLATE || $order['status'] == Order::ORDER_NOT_COMFIRM) {

            //检查是否已经结账,  没结账返回false; 否则返回表明
            $checkOrderSettle = $this->checkOrderSettleByOrderID($order);

            if ($checkOrderSettle) {
                switch ($again_settle) {
                    //退单结账
                    case 1:
                        //记录重结日期
                        $this->orderAgainInvoicing($order_id);
                        //数据重结
//                        $this->delEmployeeAccount($order);
						$this->refundOrder($order);
                        break;
                    default:
                        if ($order['cast'] == 0) {
                            $this->setOrderCast($order);
                        }
                        return false;
                        break;
                }
            }

            //把代驾费添加订单列表中、扣除信息费、扣除保险费
            $this->order_infomation($order);

            if (!empty($order['vipcard'])) {
                //扣除vip金额
                $this->orderVip($order);
            } else {
                //优惠劵返现
                $this->orderBonusUsed($order);
            }

            //更新redis
            EmployeeAccount::model()->ReloadDriverAmountRedis($order['driver_id']);

            $data = array('phone' => $order['phone'], 'order_id' => $order['order_id']);
            //添加task队列更新历史订单缓存
            //redis 历史订单 bidong 2014-1-22
            $task = array(
                'method' => 'load_customer_order',
                'params' => $data,
            );
            Queue::model()->putin($task, 'test');

            /**
             * 第三方合作，司机报单后操作
             * author : zhangtingyi 2013-11-04
             */
            $order_channel = Order::model()->getOrderChannel($order_id);
            if ($order_channel) {
                $partner = PartnerFactory::factory($order_channel);
                if ($partner instanceof AbstractPartner) {
                    if (method_exists($partner, 'completeOrderHandler')) {
                        $partner->completeOrderHandler($order_id);
                    }
                }
            }

            return true;

        }
        return false;
    }

    /**
     * 每个订单结账
     * @param $order_id 订单id
     * @param $order_id 订单id
     * @param int $again_settle 是否重新结账 0:为不重新结账 1:为退单结账
     * @return bool
     * author mengtianxue
     */
	public function orderSettle($order_id, $again_settle = 0)
	{
		$order = Order::model()->getOrderById($order_id);
		EdjLog::info('order settlement orderSettle:' . serialize($order));
		if ($order['status'] == Order::ORDER_COMPLATE || $order['status'] == Order::ORDER_NOT_COMFIRM) {
			//结账时必须加锁
			if (!empty($order['vipcard'])) {
				$sleep_cnt = 0;
				while (!RPay::model()->addSettleVipCardIfNotExist($order['vipcard'])) {
					sleep(1);
					++$sleep_cnt;
					if ($sleep_cnt >= 60) {
						RPay::model()->delSettleVipCard($order['vipcard']);
					}
				}
			}
			if (!empty($order['phone'])) {
				$sleep_cnt = 0;
				while (!RPay::model()->addSettlePhoneIfNotExist($order['phone'])) {
					sleep(1);
					++$sleep_cnt;
					if ($sleep_cnt >= 60) {
						RPay::model()->delSettlePhone($order['phone']);
					}
				}
			}
			if (!empty($order['order_id'])) {
				if (!RSubmitOrder::model()->addOrderIdIfNotExist('settle_tag_' . $order['order_id'])) {
					EdjLog::info("order_id $order_id double settle params " . serialize($order));
					RPay::model()->delSettleVipCard($order['vipcard']);
					RPay::model()->delSettlePhone($order['phone']);
					return false;
				}
			}
			$oldBalance = BUpmpPayOrder::model()->forceGetBalance($order['phone']);
			//更改订单的cost_type，因为司机端传过来的cost_type可能不准确，会有客户有余额进行余额扣款但cost_type=0的
			if (($order['cost_type'] == 0 || $order['cost_type'] == 2) && $order['income'] > $order['price'] && $oldBalance > 0) {
				if ($oldBalance < $order['income'] - $order['price']) {
					EdjLog::info('order ' . $order['order_id'] . ' error oldBalace ' . $oldBalance . ' order ' . serialize($order));
				}
				$real_cost_type = 0;
				if ($order['cost_type'] == 0) {
					$vip_card = VipPhone::model()->getPrimary($order['phone']);
					if (!empty($vip_card) && !empty($vip_card['vipid'])) {
						$order_vipcard_update = Order::model()->updateByPk($order['order_id'], array('vipcard' => $vip_card['vipid']));
						if ($order_vipcard_update) {
							$real_cost_type = 1; //vip
						}
					} else {
						$real_cost_type = 8; //个人账户
					}
				} else if ($order['cost_type'] == 2) {
					$real_cost_type = 4;
				}
				$order_cost_type_update = Order::model()->updateByPk($order['order_id'], array('cost_type' => $real_cost_type));
				if (!$order_cost_type_update) {
					EdjLog::info('update order_id ' . $order['order_id'] . ' cost_type ' . $real_cost_type . ' fail');
				}
				$order = Order::model()->getOrderById($order['order_id']);
				$order['cost_type'] = $real_cost_type;
			}

			//检查是否已经结账,  没结账返回false; 否则返回表明
			$checkOrderSettle = $this->checkOrderSettleByOrderID($order);
			if ($checkOrderSettle) {
				//记录重结日期
				$this->orderAgainInvoicing($order_id);
			}
			//把代驾费添加订单列表中、扣除信息费、扣除保险费
			$this->order_infomation($order);
			switch ($order['cost_type']) {
				//扣除vip金额
				case 1:
					$this->orderVip($order);
					break;
				//优惠劵返现
				case 2:
					$this->orderBonusUsed($order);
					break;
				//先扣优惠劵，再扣个人帐户
				case 4:
					$balance = $this->orderBonusUsed($order);
					$this->orderUser($order, $balance);
					break;
				//扣个人帐户
				case 8:
					$this->orderUser($order);
					break;
			}
			EdjLog::info(' order settle money over ');
            //TODO ... see IAsyn
            $aysnChain = new AsynChain($order);
            $aysnChain->afterSettle();
			//更新redis
            DriverAccountService::reloadRedisDriverBalance($order['driver_id']);
            //更新司机信息费
            $driver_id = $order['driver_id'];
            $driverAmount = DriverAccountService::getDriverAmount($driver_id);
            DriverService::updateRedisAccount($driver_id, $driverAmount);
            //TODO ... add account service
			$newBalance = BUpmpPayOrder::model()->forceGetBalance($order['phone']);
			if ($newBalance < $oldBalance) {
				BUpmpPayOrder::model()->noticeBalanceChange($order['phone'], false);
			}
			RPay::model()->delSettleVipCard($order['vipcard']);
			RPay::model()->delSettlePhone($order['phone']);
			return true;

		}
		return false;
	}


    /**
     * 记录重新结账信息
     * @param $order_id
     * @param string $description
     * author mengtianxue
     */
    public function orderAgainInvoicing($order_id, $description = null)
    {
        try {
            $operator = Yii::app()->user->getId();
        } catch (Exception $e) {
            $operator = '系统操作';
        }
        $description = $description === null ? "重新结账" : $description;

        $params = array(
            'order_id' => $order_id,
            'description' => $description,
            'operator' => $operator,
            'created' => date('Y-m-d H:i:s'),
        );
        $orderAgainInvoicing = new OrderAgainInvoicing();
        $orderAgainInvoicing->setAttributes($params);
        $orderAgainInvoicing->insert();
    }

	/**
	 * 检查订单是否已经结账
	 * @param $order
	 * @return bool|string
	 * author mengtianxue
	 */
	public function checkOrderSettleByOrderID($order)
	{
		$orderLog = OrderLog::model()->find('order_id = :order_id and description = :description', array(':order_id' => $order['order_id'], ':description' => '报单'));
		if ($orderLog) {
			$table_date = date('Ym', $orderLog->created);
		} else {
			$table_date = date('Ym');
		}
		EmployeeAccount::$table_name = $table_date;
		$employeeAccount = new EmployeeAccount();
		$employeeAccount->refreshMetaData();
		$checked = $employeeAccount->find('order_id = :order_id', array(':order_id' => $order['order_id']));
		if ($checked) {
			return true;
		} else {
			return false;
		}
	}


    /**
     * 重新结账 需要先把订单扣费金额退还用户（优惠劵返现除外），然后先把优惠劵先添加
     * @param $order        order对象
     * @return mixed
     * author mengtianxue
     */
    public function delEmployeeAccount($order)
    {
        if (!empty($order)) {
            //所有信息返还
			//返回钱
			//返回给司机并记录
			$orderId = $order['order_id'];
			$refundLog = RefundLog::model()->findByPk($orderId);
			$boardId = 0;
			if(!empty($refundLog)){
				$boardId = $refundLog->employee_account_id;
				$refundLog->refund_count += 1;
			}else{
				$refundLog = new RefundLog();
				$refundLog->order_id = $orderId;
				$refundLog->refund_count = 1;
			}
			$employeeAccountRecord = $this->getOrderCastByOrderID($order, $boardId);
			if (!empty($employeeAccountRecord)) {
                foreach ($employeeAccountRecord as $record) {
					$type = $record->type;
                    // 把退单金额还给用户
                    $params = array(
                        'type' => $type,
                        'channel' => $record->channel,
                        'city_id' => $record->city_id,
                        'user' => $record->user,
                        'order_id' => $record->order_id,
                        'comment' => $record->order_id . '重结退还:' . $record->comment,
                        'cast' => $this->refundCast($record, $order['vipcard']),
                        'order_date' => $order['created'],
                    );
					//
					if( EmployeeAccount::TYPE_ORDER_VIP == $record->type){
                        VipAccountService::refundOrder($order['vipcard'], $record->cast, $record->order_id);
					}
                    DriverAccountService::orderChargeV2($params);
				}

            }

            //如果是优惠劵订单，把原来的信息重新插入当前月
			//返回给客户
			if(empty($order['vipcard'])){
				BCustomers::model()->refundOrderCost($order['user_id'], $order['order_id'],$order['vipcard'], $order['phone']);

			}
			//不区分 vip 和普通用户
			CustomerBonus::model()->refundCustomerBonus($order['phone'], $order);
            $refundLog->employee_account_id = DriverAccountService::getLastEmployeeAccountId($orderId);
			$refundLog->create_time = time();
			if($refundLog->save()){
				EdjLog::info('refund log save success '.$orderId.'|'.$refundLog->employee_account_id);
			}else{
				EdjLog::info('refund log save fail '.$orderId.'|'.$refundLog->employee_account_id);
			}

            //清除报单锁
            RSubmitOrder::model()->delOrderId('settle_tag_'.$order['order_id']);

            return true;
        }
        return false;
    }

    /**
     * 格式化退还的金额
     * @param $record
     * @param int $vipcard
     * @return mixed
     * author mengtianxue
     */
    public function refundCast($record, $vipcard = 0)
    {
        $cast = $record->cast;
		// 坑
        switch ($record->type) {
            case EmployeeAccount::TYPE_ORDER_VIP :
            case EmployeeAccount::TYPE_ORDER_CASH :
            case EmployeeAccount::TYPE_INFOMATION :
            case EmployeeAccount::TYPE_BONUS_CASH :
            case EmployeeAccount::TYPE_BONUS_RETUEN :
            case EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN :
            case EmployeeAccount::TYPE_BONUS_FIXED :
            case EmployeeAccount::TYPE_ORDER_NORMAL :
            case EmployeeAccount::TYPE_ACTIVE :
                $cast = $record->cast * -1;
                break;
            case EmployeeAccount::TYPE_ORDER_INFOMATION :
            case EmployeeAccount::TYPE_ORDER_INVOICE :
            case EmployeeAccount::TYPE_FORFEIT :
            case EmployeeAccount::TYPE_ORDER_INSURANCE :
                break;
        }
        return $cast;
    }


	/**
	 * 获取订单扣信息费金额
	 * @param $order
	 * @param $boardId
	 * @return bool
	 * author mengtianxue
	 */
    public function getOrderCastByOrderID($order, $boardId = 0)
    {
        if (!empty($order)) {
            //获取插入的表  新客返现的优惠劵不重新结算
            EmployeeAccount::$table_name = date('Ym', $order['created']);
			$objEmployeeAccount=new EmployeeAccount();
			$objEmployeeAccount->refreshMetaData();
            $employeeAccount = $objEmployeeAccount->findAll('order_id = :order_id and type != :type and id > :boardId',
                array(
					':order_id' => $order['order_id'],
					':type'		=> EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN,
					':boardId'	=> $boardId,
				)
			);
            return $employeeAccount;
        }
        return false;
    }


	/**
     * 没有调用了 TODO ...
     *
	 * 获取优惠劵信息（不包扣新课邀请码返现）
     * @param $order
	 * @param $boardId
	 * @return array|bool|CActiveRecord|mixed|null
	 * author mengtianxue
	 */
    public function getOrderBonusCastByOrderID($order, $boardId = 0)
    {
        if (!empty($order)) {
            //获取要插入的表
            EmployeeAccount::$table_name = date('Ym', $order['created']);
			$objEmployeeAccount=new EmployeeAccount();
			$objEmployeeAccount->refreshMetaData();
            $employeeAccount = $objEmployeeAccount::model()->findAll('order_id = :order_id and type in (7,8,10) and id > :boardId',
                array(
					':order_id' => $order['order_id'],
					':boardId'	=> $boardId,
				)
			);
            return $employeeAccount;
        }
        return false;
    }


    /**
     * 结账记录 （收入，信息费，保险费）
     * @param $order
     * @return bool
     * author mengtianxue
     */
    public function order_infomation($order)
    {
		//先扣调整费
		@self::model()->modifyFeeSettle($order['order_id']);
        $type_array = array(EmployeeAccount::TYPE_ORDER_CASH, EmployeeAccount::TYPE_ORDER_INFOMATION, EmployeeAccount::TYPE_ORDER_INSURANCE);
        foreach ($type_array as $type) {
            $params = array();
            $params['type'] = $type;
            $params['channel'] = $type;
            $params['city_id'] = $order['city_id'];
            $params['user'] = $order['driver_id'];
            $params['order_id'] = $order['order_id'];
            $params['order_date'] = $order['created'];

            switch ($type) {
                case EmployeeAccount::TYPE_ORDER_CASH:
					$orderExt = OrderExt::model()->getPrimary($order['order_id']);
                    $params['cast'] = FinanceCastHelper::getOrderTotalMoney($order, $orderExt, null, true);
                    $params['comment'] = '现金收入 单号：' . $order['order_id'];
                    break;
                case EmployeeAccount::TYPE_ORDER_INFOMATION:
					$meta = OrderSnapshoot::model()->getSnapshootMeta($order['order_id'], 'modify_fee');
					if($meta){
						$income = $order['income'];
						$type = isset($meta['type']) ? $meta['type'] : 0;
						$amount = isset($meta['amount']) ? $meta['amount'] : 0;
						if(FinanceConstants::TYPE_MODIFY_INCOME == $type){ //如果是司机代收的话,那么后面扣信息费需要排除
							$income = $income - $amount;
						}
						if(FinanceConstants::TYPE_MODIFY_OUTCOME == $type){//如果是公司代付的话,那么后面扣信息费需要加上收取
							$income = $income + $amount;
						}
						$order['income'] = $income;
					}
                    $cast = CityConfig::model()->calculatorCast($order); //Common::cast($order);
                    $params['cast'] = $cast;
                    $params['comment'] = '信息费 单号：' . $order['order_id'];

                    // 订单信息费写回order表中cast字段
                    Order::model()->updateByPk($order['order_id'], array(
                        'cast' => $cast
                    ));

                    break;
                case EmployeeAccount::TYPE_ORDER_INSURANCE:
                    //查看优惠信息
                    $driver_id = $order['driver_id'];
                    $city_id = $order['city_id'];
                    $driver_fee_discount = Common::driver_fee_discount($driver_id);
                    //重庆不收保险费  --mtx 2013-09-16
                    //南京 2015-01-01 00:00:00 前不收保险费 duke modified 改版前是写到了
                    if ($city_id == 7 || ($city_id == 8 && (time() < strtotime('2015-01-01 00:00:00')))) {
                        $driver_fee_discount = 0;
                    }

                    $params['cast'] = 2 * $driver_fee_discount;
                    $params['comment'] = '保险费 单号：' . $order['order_id'];
                    break;

            }

            $this->insertAccount($params);
        }
        return true;
    }

    /**
     * 如果订单结账成功cast回写到order表中
     * @param $order
     * @return bool
     * author mengtianxue
     */
    public function setOrderCast($order)
    {
        $cast = CityConfig::model()->calculatorCast($order); //Common::cast($order);

        // 订单信息费写回order表中cast字段
        $order_update = Order::model()->updateByPk($order['order_id'], array(
            'cast' => $cast
        ));

        if ($order_update) {
            echo "重写\n";
            return true;

        } else {
            return false;
        }

    }


    /**
     * VIP订单，扣除VIP帐户余额，增加司机收入记录,扣除发票税点
     * @param $order
     * author mengtianxue
     */
    public function orderVip($order)
    {
        $vip =  VipService::service()->isVip($order['phone']);
        //vip不存在时不结账
        if (!$vip) {
    	    EdjLog::info("not vip for order ".serialize($order));
            return;
        } else {
        }
        $coupon_money = 0;
        // TODO ... add orderExtService
        $order_ext = OrderExt::model()->getPrimary($order['order_id']);
        if ($order_ext) {
            if (empty($order_ext['coupon_money'])) {
                $coupon = $order_ext['coupon'];
                $coupon_money = $coupon * 39;
            } else {
                $coupon_money = $order_ext['coupon_money'];
            }
        }

        //本次代驾服务要从vip中扣除的费用
        $total = $order['income'] - $order['price'] - $coupon_money;
        if ($total > 0) {
            //TODO ... add transaction here
            $vipServiceRet = VipAccountService::orderCharge($order['vipcard'], $total, $order);
			//增加vip金额
			if ($vipServiceRet) {
                // VIP订单划账给司机账户
                $comment = 'VIP订单收入 单号：' . $order['order_id'];
                DriverAccountService::orderCharge($total, $order, $comment);
            }
        }
    }


    /**
     * 充值用户结账
     * @param $order //订单详情
     * @param int $bonus_money //优惠劵已优惠金额
     * @auther mengtianxue
     */
    public function orderUser($order, $bonus_money = 0)
    {
        $coupon_money = 0;
        //TODO ... need coupon service
        $order_ext = OrderExt::model()->getPrimary($order['order_id']);
        if ($order_ext) {
            if (empty($order_ext['coupon_money'])) {
                $coupon = $order_ext['coupon'];
                $coupon_money = $coupon * 39;
            } else {
                $coupon_money = $order_ext['coupon_money'];
            }
        }
        $phone = $order['phone'];
        //本次代驾服务要从用户中扣除的费用
        $money = $order['income'] - $order['price'] - $coupon_money - $bonus_money;
        if ($money >= 0) {
            $accountRet = NormalAccountService::orderCharge($phone, $money, $order);
            if ($accountRet) {
                $comment = '个人用户消费 单号：' . $order['order_id'];
                DriverAccountService::orderCharge($money, $order,  $comment);
                $data_time = date('m月d日H时i分', $order['created']);
                $sms_message = '尊敬的用户，您的e代驾账户于' . $data_time . '消费人民币' . $money . '元。打开 e代驾客户端了解详情。';
                FinanceUtils::sendSMS($phone, $sms_message, Sms::CHANNEL_GUODU);
            }
        }
    }

    /**
     * 优惠劵返现
     * @param $order
     * @return bool
     * author mengtianxue
     */
    public function orderBonusUsed($order)
    {
        $bonus_used = CustomerBonus::model()->getBonusUse($order['phone'], $order['order_id']);
        if ($bonus_used) {
//            if ($order['cost_type'] != 2 && $order['cost_type'] != 4) {
//                //取消优惠劵使用
//                BonusLibrary::model()->BonusUsed($order['phone'], $order['order_id'], 0, 2);
//                return false;
//            }

            $bonus_sn = $bonus_used['bonus_sn'];
            if ($bonus_used['back_type'] == 1) {
                //优惠劵使用
                $balance = BonusLibrary::model()->BonusUsed($order['phone'], $order['order_id'], $order['income']);
            } else {
                $balance = $bonus_used['balance'];
                //优惠劵使用
                BonusLibrary::model()->BonusUsed($order['phone'], $order['order_id'], $order['income']);
            }

            //优惠劵返现
            $params = array(
                'type' => EmployeeAccount::TYPE_BONUS_CASH,
                'channel' => EmployeeAccount::TYPE_BONUS_CASH,
                'city_id' => $order['city_id'],
                'user' => $order['driver_id'],
                'order_id' => $order['order_id'],
                'comment' => '优惠券充值 号码：' . $bonus_sn, //$bonus['bonus'].$bonus['parity_bit'],
                'cast' => $balance,
                'order_date' => $order['created']
            );
            $this->insertAccount($params);
			//新客优惠
            if ($bonus_used['bonus_type_id'] == 8) {
				//get city id
				$bonusLibrary	= BonusLibrary::model()->getBonusByBonus_sn($bonus_sn, 0);//返回的是一个数组
				if(empty($bonusLibrary)){
					$city_id = 0;
				}else{
					$city_id = $bonusLibrary['city_id'];
				}
				if(empty($city_id)){
					$driver_id = $order['driver_id'];
				}else{
					$driver_num = substr($bonus_sn, strlen(trim($city_id)));
					$cityPrefix = Dict::item("bonus_city", $city_id);
					$driver_id = $cityPrefix . $driver_num;
				}
                $driver = Driver::model()->getDriver($driver_id);
                if ($driver && $driver['city_id'] != 0) {
                    $city = $driver['city_id'];
                } else {
                    $city = $order['city_id'];
                }

                //起步价为19的 反司机10元
                $back_cast = 20;
                $city_arr = RCityList::model()->getCityFeeEq(19); //Common::getCityFeeEq19();
                if (in_array($city, $city_arr)) {
                    $back_cast = 10;
                }

                $params = array(
                    'type' => EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN, //EmployeeAccount::TYPE_BONUS_RETUEN,
                    'channel' => EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN,
                    'city_id' => $city,
                    'user' => $driver_id,
                    'order_id' => $order['order_id'],
                    'comment' => '优惠券返现 号码：' . $bonus_sn, //$bonus['bonus'].$bonus['parity_bit'],
                    'cast' => $back_cast, //5,
                    'order_date' => $order['created']
                );
                $this->insertAccount($params);
            }

            //广发银行贵宾，抛出队列，发送还有几张优惠劵未使用 start  --mengtianxue
            if ($bonus_sn == '2312661977') {
                $guangfa = array('bonus_sn' => $bonus_sn, 'phone' => $order['phone']);
                $task = array(
                    'method' => 'bonus_number_sms',
                    'params' => $guangfa,
                );
                Queue::model()->putin($task, 'test');
            }
            //广发银行贵宾，抛出队列，发送还有几张优惠劵未使用 end

            return $balance;
        }
        return false;
    }

    /**
     * 优惠劵返现
     * @param $order
     * @param bool $notify_sms
     * author mengtianxue
     */
    public function orderBonus($order, $notify_sms = false)
    {
        //如果用户有绑定优惠券，金额返信息费
        $un_used_bonus = CustomerBonus::existsUnusedCustomerBonus($order['phone']);

        if ($un_used_bonus) {
            //有app用户优惠劵但用呼叫中心呼叫的
            if (($un_used_bonus['is_limited'] == BonusType::BONUS_TYPE_LIMIT_APP || $un_used_bonus['is_limited'] == BonusType::BONUS_TYPE_LIMIT_APP_FRESH)
                && ($order['source'] == Order::SOURCE_CALLCENTER || $order['source'] == Order::SOURCE_CALLCENTER_INPUT)
            ) {
//                $sms_message = "您昨天通过拨打400电话享受e代驾服务,您的账户中还有" . $un_used_bonus['money'] . "元代金券,仅限通过e代驾客户端找代驾使用,免费下载>> http://wap.edaijia.cn ";
//                Sms::SendSMS($order['phone'], $sms_message);
            } else {
                //优惠劵结账状态  默认是false不结账
                $status = FALSE;
                if ($order['source'] == Order::SOURCE_CALLCENTER || $order['source'] == Order::SOURCE_CALLCENTER_INPUT || $order['source'] == Order::SOURCE_CLIENT_INPUT) {
                    $status = TRUE;
                } else {
                    //app呼叫  有呼入记录 返回true 否则false
                    $status = CallHistory::model()->getIncomeCallHistory($order['phone'], $order['call_time']);
                }

                //app用户在订单生成之前两个小时之内有呼入记录
                if ($status) {
                    //订单和优惠劵都存在使用优惠劵
                    CustomerBonus::customerBonusUse($order['phone'], $un_used_bonus['bonus'], $order['order_id'], $order['booking_time']);

                    //获取订单的优惠劵信息
                    $bonus = CustomerBonus::getOrderBonus($order['phone'], $order['order_id']);

                    if ($bonus) {
                        echo '---' . $bonus['bonus'] . "\n";
                        $arrStaticBonus = BonusType::model()->getStaticBonus();

                        if (in_array($bonus['bonus_type_id'], $arrStaticBonus)) {
                            $bonus_sn = $bonus['bonus'];
                        } else {
                            $bonus_sn = $bonus['bonus'] . $bonus['parity_bit'];
                        }

                        $params = array(
                            'type' => EmployeeAccount::TYPE_BONUS_CASH,
                            'channel' => EmployeeAccount::TYPE_BONUS_CASH,
                            'city_id' => $order['city_id'],
                            'user' => $order['driver_id'],
                            'order_id' => $order['order_id'],
                            'comment' => '优惠券充值 号码：' . $bonus_sn, //$bonus['bonus'].$bonus['parity_bit'],
                            'cast' => $bonus['money'],
                            'order_date' => $order['created']
                        );
                        $this->insertAccount($params);

                        //给发卡司机返点
                        //司机发卡优惠券
                        $city = $order['city_id']; //返现时city_id  初始值是订单city_id，然后赋值为司机城市
                        if ($bonus['bonus_type_id'] == 8) {
                            $city_id = substr($bonus_sn, 0, 2);
                            $driver_id = substr($bonus_sn, 2, 4);
                            $cityPrefix = Dict::items("bonus_city");
                            $driver = array();

                            if (isset($cityPrefix[$city_id])) {
                                $driver_id = $cityPrefix[$city_id] . $driver_id;

//                                $model = Driver::model()->find('user = :driver_id and mark < :mark', array(
//                                    ':driver_id' => $driver_id, ':mark' => Driver::MARK_LEAVE,
//                                ));

                                //读重库
                                $model = Driver::model()->getDriver($driver_id);

                                if ($model) {
                                    $driver['driver_id'] = $model['user'];
                                    $city = empty($model['city_id']) ? $city : $model['city_id'];
                                }
                                $return_type = EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN;
                                $return_cast = 20;
                            }
                        } else {
                            $driver = DriverBonus::model()->getDriverID($bonus_sn); //$bonus['bonus'].$bonus['parity_bit']);
                            $return_type = EmployeeAccount::TYPE_BONUS_RETUEN;
                            $return_cast = 5;
                        }

                        if ($driver) {
                            $params = array(
                                'type' => $return_type, //EmployeeAccount::TYPE_BONUS_RETUEN,
                                'channel' => $return_type,
                                'city_id' => $city,
                                'user' => $driver['driver_id'],
                                'order_id' => $order['order_id'],
                                'comment' => '优惠券返现 号码：' . $bonus_sn, //$bonus['bonus'].$bonus['parity_bit'],
                                'cast' => $return_cast, //5,
                                'order_date' => $order['created']
                            );
                            $this->insertAccount($params);
                        }
                        $bonus_name = BonusType::model()->getBonusName($bonus['bonus_type_id']);

                        if ($notify_sms) {
                            $cash = $order['income'] - $bonus['money'];
//                            $sms_message = MessageText::getFormatContent(MessageText::CUSTOMER_BONUS_USED, date("Y-m-d", $order['created']), $order['income'], $bonus_name, $bonus_sn, $bonus['money'], $cash);
//                            Sms::SendSMS($order['phone'], $sms_message);
                        }
                    }
                }
            }
        }
    }


    /**
     * 司机信息费充值
     * @param $attributes
     * @param $type
     * @return bool
     * author mengtianxue
     */
    public function driverRecharge($attributes, $type = false)
    {
        if (!$type) {
            $user = Yii::app()->user->getID();
        } else {
            $user = '系统添加';
        }
        $dataAccount = array();
        $dataAccount['channel'] = $attributes['channel'];
        $attributes['comment'] = empty($attributes['comment']) ? Dict::item('cast_channel', $attributes['channel']) : $attributes['comment'];
        $dataAccount['order_date'] = time();
        $dataAccount['type'] = EmployeeAccount::TYPE_INFOMATION;
        $dataAccount['city_id'] = $attributes['city_id'];
        $dataAccount['operator'] = $user;
        $order_num = isset($attributes['order_id']) ? $attributes['order_id'] : 0;
        $dataAccount['order_id'] = $order_num;
        if (!empty($order_num)) {
            $order_comment = '单号：' . $order_num;
        } else {
            $order_comment = '';
        }
        $dataAccount['comment'] = '信息费充值 ' . $order_comment . ' 操作人：' . $user . ' 备注：' . $attributes['comment'];
        $dataAccount['user'] = $attributes['user'];
        $dataAccount['cast'] = $attributes['cast'];
        return $this->insertAccount($dataAccount);
    }

    /**
     * 增加一笔司机的账务
     * 定义帐务的收入支出标志
     * @param $data
     * @return bool
     * author mengtianxue
     */
    public function insertAccount($data)
    {
		EdjLog::info('insert account args is '.serialize($data));
        switch ($data['type']) {
            case EmployeeAccount::TYPE_ORDER_INFOMATION :
            case EmployeeAccount::TYPE_ORDER_INVOICE :
            case EmployeeAccount::TYPE_FORFEIT :
            case EmployeeAccount::TYPE_ORDER_INSURANCE :
                $data['cast'] = $data['cast'] * -1;
                break;
            default:
                break;
        }
        try {
            //要插入的表
            $table_date = date('Ym');
            $data['settle_date'] = date('Ym');
            $data['created'] = time();

            //更改师傅余额，如果type是0，属于代驾费，不更新师傅余额
            $balance = DriverBalance::model()->updateBalance($data, $data['type']);
			EdjLog::info('update driver balance ----- return balance is '.$balance);
            if ($balance !== false) {
                $data['balance'] = $balance;
                $res = $this->addEmployeeAccount($data, $table_date);
                if ($res) {
                    if ($data['type'] == 5) {
                        $driver_id = $data['user'];
                        $driver = DriverStatus::model()->get($driver_id);
                        $driverAmount = EmployeeAccount::model()->getDriverAmount($driver_id);
                        $driver->account = $driverAmount;
						EdjLog::info('after save employee account ok. driver is '.serialize($driver));
                        if ($driver->mark == 1 && $driver->block_mt == 0) {
                            //根据城市获取城市基础金额
                            $city_id = $data['city_id'];
                            $money = 100;
                            if (in_array($city_id, RCityList::model()->getDriverCityLt(200))) {
                                $money = 200;
                            }
							EdjLog::info('driver is block balance is '.$balance.' and city money is '.$money);
                            //司机的金额 大于该城市的基础金额
                            if ($balance >= $money) {
                                DriverBankResult::model()->activationDriver($data);
                            }
                        }
                    }
                    return true;
                } else {
					EdjLog::info('update driver balance return false');
                    return false;
                }

            }
        } catch (Exception $e) {
			EdjLog::info('add employee account exception '.$e->getMessage());
            $this->addEmployeeAccountThrow($data);
            return false;
        }

    }

    /**
     * 获取司机当月的账单
     * @param $driver_id
     * author mengtianxue
     */
    public function getDriverNowMonth($driver_id)
    {
        EmployeeAccount::$table_name = date('Ym');
        $tableName = EmployeeAccount::model()->tableName();

        $monthBill = Yii::app()->db_finance->createCommand()
            ->select("id,
                    sum(if(type=0,cast,0)) AS t0,
                    sum(if(type=1,cast,0)) AS t1,
                    sum(if(type=2,cast,0)) AS t2,
                    sum(if(type=3,cast,0)) AS t3,
                    sum(if(type=4,cast,0)) AS t4,
                    sum(if(type=5,cast,0)) AS t5,
                    sum(if(type=6,cast,0)) AS t6,
                    sum(if(type=7,cast,0)) AS t7,
                    sum(if(type=8,cast,0)) AS t8,
                    sum(if(type=9,cast,0)) AS t9,
                    sum(if(type=10,cast,0)) AS t10,
                    sum(cast)-sum(if(type=0,cast,0))  as total")
            ->from($tableName)
            ->where('user = :user', array(':user' => $driver_id))
            ->queryRow();
        if ($monthBill)
            $monthBill['settle_date'] = date('Y-m');
        return $monthBill;
    }


    /**
     * 在employee_account添加一条数据
     * @param $data
     * @param null $table_date
     * @return bool
     * author mengtianxue
     */
    public function addEmployeeAccount($data, $table_date = null)
    {
        //如果默认值为0时，city_id设置为1.
        if ($data['city_id'] == 0) {
            $data['city_id'] = 1;
        }

        //指定要插入的表
        if ($table_date !== null) {
            EmployeeAccount::$table_name = $table_date;
        }
        $employeeAccount = new EmployeeAccount();
        $employeeAccount->refreshMetaData();
        $employeeAccount->attributes = $data;
        $insert_num = $employeeAccount->insert();
        if ($insert_num) {
            return true;
        } else {
			EdjLog::info('add employeeAccount error '.json_encode($employeeAccount->getErrors()));
            return false;
        }
    }

    /**
     * 记录异常抛出的扣款信息
     * @param $data
     * @return bool
     * author mengtianxue
     */
    public function addEmployeeAccountThrow($data)
    {
        //指定要插入的表
        EmployeeAccount::$table_name = 'throw';
        $employeeAccount = new EmployeeAccount();
		$employeeAccount->refreshMetaData();
        $employeeAccount->attributes = $data;

        $insert_num = $employeeAccount->insert();
        if ($insert_num) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 优惠劵已经存在    优惠劵结账
     * @param $order_id
     * @param $bonus_sn
     * @return bool
     * @auther mengtianxue
     */
    public function couponSettle($order_id, $bonus_sn = 0)
    {
        $order = Order::model()->getOrdersById($order_id);
        if ($order) {
            if ($bonus_sn == 0) {
                $customerBonus = CustomerBonus::model()->CheckCustomerBonusUsed($order['phone'], $order['source']);
                if (empty($customerBonus)) {
                    return false;
                }

                foreach ($customerBonus as $bonus) {
                    $bonus_sn = $bonus['bonus_sn'];
                    break;
                }
            }

            //优惠劵标为已用
            $bonus_used = CustomerBonus::model()->couponUse($order['phone'], $bonus_sn, $order_id);
            if ($bonus_used) {
                $balance = $bonus_used['balance'];

                //优惠劵返现
                $params = array(
                    'type' => EmployeeAccount::TYPE_BONUS_CASH,
                    'channel' => EmployeeAccount::TYPE_BONUS_CASH,
                    'city_id' => $order['city_id'],
                    'user' => $order['driver_id'],
                    'order_id' => $order['order_id'],
                    'comment' => '优惠券充值 号码：' . $bonus_sn, //$bonus['bonus'].$bonus['parity_bit'],
                    'cast' => $balance,
                    'order_date' => $order['created']
                );
                $this->insertAccount($params);
            }
            return true;
        }
        return false;
    }

    /**
     * 优惠劵绑定并返现，提供前端接口
     * @param $order_id
     * @param $bonus_sn
     * @return array
     * @auther mengtianxue
     * 2014-03-20  进行梳理修改
     */
    public function couponUsed($order_id, $bonus_sn)
    {
        if (empty($order_id) && empty($bonus_sn)) {
            $ret = array(
                'code' => 2,
                'message' => '参数错误'
            );
            return $ret;
        }
        //检查订单是否存在
        $order_ext = OrderExt::model()->getPrimary($order_id);
        if ($order_ext) {
            $num_coupon = $order_ext['coupon'];
            $coupon_money = $order_ext['coupon_money'];
            $coupon_income = $order_ext['coupon_income'];

            //检查订单是否上报实体卡
            if ($num_coupon > 0 || $coupon_money > 0) {
                //上报的是金额，否则上报的是张数
                if ($coupon_money > 0) {
                    if ($coupon_income >= $coupon_money) {
                        $ret = array(
                            'code' => 2,
                            'message' => '绑定的金额已经大于或等于上报金额'
                        );
                        return $ret;
                    }
                    //绑定 绑定成功后反写回orderExt
                    $bonus_back = $this->couponBindAndBackMoney($order_id, $bonus_sn);
                    if ($bonus_back['code'] == 0) {
                        $bonus_money = $bonus_back['cash_balance'];
                        //更新OrderExt表中数据
                        OrderExt::model()->updateIncome($order_id, $bonus_money);
                    }
                    return $bonus_back;
                } else {
                    //获取当前已使用张数
                    $bonus_num = CustomerBonus::model()->getBonusNumByOrderID($order_id);
                    if ($bonus_num >= $num_coupon) {
                        $ret = array(
                            'code' => 2,
                            'message' => '绑定张数已经大于或等于上报张数'
                        );
                        return $ret;
                    }
                    //绑定
                    $bonus_back = $this->couponBindAndBackMoney($order_id, $bonus_sn);
                    return $bonus_back;
                }
            } else {
                $ret = array(
                    'code' => 2,
                    'message' => '绑定实体卡优惠劵'
                );
                return $ret;
            }
        } else {
            $ret = array(
                'code' => 2,
                'message' => '订单不存在'
            );
            return $ret;
        }
    }

    /**
     * 优惠劵绑定后返现
     * @param $order_id
     * @param $bonus_sn
     * @return array
     * @auther mengtianxue
     */
    public function couponBindAndBackMoney($order_id, $bonus_sn)
    {
        $bonusLibrary = BonusLibrary::model()->getBonusByBonus_sn($bonus_sn);
        if ($bonusLibrary) {
            if ($bonusLibrary['status'] == 1) {
                $ret = array(
                    'code' => 2,
                    'message' => '此贵宾卡已经绑定'
                );
                return $ret;
            }

            $date_now = date('Y-m-d H:i:s');
            //绑定日期必须在生效时间之后
            if ($bonusLibrary['effective_date'] > $date_now) {
                $ret = array(
                    'code' => 2,
                    'message' => '贵宾卡尚未生效');
                return $ret;
            }

            //绑定日期必须在绑定时间之内
            if (strtotime($bonusLibrary['binding_deadline']) < strtotime($date_now)) {
                $ret = array(
                    'code' => 2,
                    'message' => '贵宾卡已过有效绑定期限');
                return $ret;
            }

            $order = Order::model()->getOrdersById($order_id);
            if ($order) {
                $phone = $order['phone'];

                $bonus = array();
                $bonus['id'] = $bonusLibrary['bonus_id'];
                $bonus['channel'] = $bonusLibrary['channel'];
                $bonus['sn_type'] = $bonusLibrary['sn_type'];
                $bonus['bonus_sn'] = $bonusLibrary['bonus_sn'];
                $bonus['money'] = $bonusLibrary['money'];
                $bonus['parityBit'] = substr($bonusLibrary['bonus_sn'], -1);
                $bonus['order_id'] = $order_id;
                $bonus['end_date'] = $bonusLibrary['end_date'];
                $bonus['user_limited'] = '';
                $bonus['channel_limited'] = '';
                $bonus['back_type'] = 1;
                $bonus['used'] = time();
                $bonus['order_id'] = $order_id;

                $add_customer_bonus = CustomerBonus::model()->AddCustomerBonus($bonus, $phone);
                if ($add_customer_bonus) {
                    BonusLibrary::model()->updateStatus($bonus_sn, BonusLibrary::STATUS_USE);
                    $bonus_money = $bonusLibrary['money'];
                    $cast = $bonus_money * ((100 - BonusLibrary::RATE_COUNPON) / 100); //扣除 6% 的税点
                    //优惠劵返现
                    $params = array(
                        'type' => EmployeeAccount::TYPE_BONUS_FIXED,
                        'channel' => EmployeeAccount::TYPE_BONUS_FIXED,
                        'city_id' => $order['city_id'],
                        'user' => $order['driver_id'],
                        'order_id' => $order_id,
                        'comment' => '贵宾卡返现（扣除税点） 号码：' . $bonus_sn, //$bonus['bonus'].$bonus['parity_bit'],
                        'cast' => $cast,
                        'order_date' => $order['created']
                    );

                    if ($this->insertAccount($params)) {
						EmployeeAccount::model()->ReloadDriverAmountRedis($order['driver_id']);
                        $ret = array(
                            'code' => 0,
                            'message' => '绑定成功',
                            'cash_balance' => (string)$bonus_money
                        );
                        return $ret;
                    }
                }
            } else {
                $ret = array(
                    'code' => 2,
                    'message' => '订单信息有误'
                );
                return $ret;
            }
        } else {
            $ret = array(
                'code' => 2,
                'message' => '错误的贵宾卡号，请联系司管现场验卡'
            );
            return $ret;
        }
    }


    /**
     * 修改settle里面的数据
     * @param $data
     * @return bool|int
     * author mengtianxue
     */
    public function updateAccountSettle($data)
    {
        $settle = new EmployeeAccountSettle();

        $settle_date = date('Y-m');
        //检测是否存在
        $settle_data = $this->check_settle_date($data['user'], $settle_date);

        $column_name = 't' . $data['type']; //要修改的字段
        if (!empty($settle_data)) {
            $attr = array($column_name => $data['cast']);
            if ($data['type'] != 0) {
                $attr['total'] = $data['cast'];
            }
            $settle_save = $settle->updateCounters($attr, 'user = :user and settle_date = :settle_date',
                array(':user' => $data['user'], ':settle_date' => $settle_date));
        } else {
            $attr = array();
            $attr['user'] = $data['user'];

            //获取师傅名字
            $driver_info = Driver::model()->getDriver($data['user']);
            if ($driver_info) {
                $attr['name'] = $driver_info['name'];
                $attr['city_id'] = $driver_info['city_id'];
            } else {
                $attr['name'] = '';
                $attr['city_id'] = 0;
            }
            $attr[$column_name] = $data['cast'];
            if ($data['type'] != 0) {
                $attr['total'] = $data['cast'];
            } else {
                $attr['total'] = 0;
            }
            $attr['settle_date'] = $settle_date;
            $settle->attributes = $attr;
            $settle_save = $settle->insert();
        }
        return $settle_save;
    }

    /**
     * 检察是否已经存在当月数据
     * @param $user
     * @param $settle_date
     * @return CActiveRecord
     * author mengtianxue
     */
    public function check_settle_date($user, $settle_date)
    {
        $settle = new EmployeeAccountSettle();
        $settle_month = $settle->find('user = :user and settle_date = :settle_date',
            array(':user' => $user, ':settle_date' => $settle_date));
        return $settle_month;
    }

    /**
     * 南京活动
     * @param $params
     * @return bool
     * author zhangtongkai 2014-04-04
     */
    public function loadOrderExt($params)
    {
        //print_r($params);
        echo "城市补贴活动start"."\n";
		EdjLog::info('arrive in time activity '.json_encode($params));
        $order = Order::model()->getOrderById($params['order_id']);
		$costType = $order['cost_type'];
        switch ($costType) {
            //补偿vip金额
            case 1:
                $this->orderVipRecharge($order);
                break;
            //补偿个人帐户
            case 4:
            case 8:
                $this->orderUserRecharge($order, $params['bonus_money']);
                break;
        }
        echo "城市补贴活动end"."\n";
    }
    /**
     * vip客户返款
     * @param $order
     * author zhangtongkai 2014-04-04
     */
    public function orderVipRecharge($order)
    {
        echo "城市补贴活动-Vip返款start"."\n";
        $vip = VipService::service()->getVipInfoByVipCard($order['vipcard']);
        //vip不存在时不返款
        if (!$vip) {
            return;
        }
		$costType = $order['cost_type'];
        $order_ext = OrderExt::model()->getPrimary($order['order_id']);
        if ($order_ext) {
            //本次代驾服务要返给vip的费用
            $total = $this->check_order_ext(1, $order, $order_ext);
        }
        if($total > 0 && $costType != 0){
            //判断是不是现金支付
            $back_money=$order['income']-$order['price'];
			if($back_money <= 0){
				return true;
			}
            // 添加VIP客户返款明细
            $attributes = array(
                'vipcard' => $order['vipcard'],
                'order_id' => $order['order_id'],
                'type' => VipTrade::TYPE_SUBSIDY,//3-返款
                'amount' => (int)$total,
                'comment' => 'VIP返款单号:' . $order['order_id'],
                'balance' => $vip->balance + $total,
                'order_date' => $order['created']
            );
            $vipBalanceAttributes = array(
                'vipCard'    => $order['vipcard'],
                'delta'      => $total,
            );
            VipAccountService::orderChargeV2($attributes, $vipBalanceAttributes);
        }
        echo "城市补贴活动-Vip返款end"."\n";
    }
    /**
     * 充值用户返款
     * @param $order //订单详情
     * @auther zhangtongkai 2014-04-04
     */
    public function orderUserRecharge($order, $bonus_money)
    {
        echo "城市补贴活动-普通用户返款start"."\n";
        $order_ext = OrderExt::model()->getPrimary($order['order_id']);
		$costType = $order['cost_type'];
        if ($order_ext) {
            //本次代驾服务要补偿给用户的费用
            $money = $this->check_order_ext(1, $order, $order_ext);
            //扣除优惠券金额
//            $money -= $bonus_money;
        }
        if ($money > 0 && $costType != 0) {
            //减去现金支付/优惠券金额
            $back_money = $order['income'] - $order['price'] - $bonus_money;
			if($back_money <= 0){
				return true;
			}
            $user_id = 0;
            $user_balance=0;
            $phone = $order['phone'];
            $user_info = NormalAccountService::getUserAmount($phone);
            if ($user_info['code'] === 0) {
                $data_user = isset($user_info['data_user'])?$user_info['data_user']:"";
                $user_id = isset($data_user->id)?$data_user->id:0;
                //用户余额
                $user_balance = $user_info['amount'];
            }
            //返款后余额
            $balance = $user_balance + $money;
            // 添加客户消费明细
            $attributes = array(
                'user_id' => $user_id,
                'trans_order_id' => $order['order_id'],
                'trans_type' => CarCustomerTrans::TRANS_TYPE_FV,
                'amount' => $money,//返款金额
                'balance' => $balance,//余额
                'source' => CarCustomerTrans::TRANS_SOURCE_RE,
                'operator'=>'系统',
                'remark' => '订单号：' . $order['order_id'],
                'update_time' => date("Y-m-d H:i:s",time()),
            );
            $add_trans = BCustomers::model()->addCustomerTrade($attributes);
            if ($add_trans['code'] === 0) {
                //返款 给个人帐户
                $arr = array();
                $arr['user_id'] = $user_id;
                $arr['amount'] = $money;
                $update_account = BCustomers::model()->updateAccount($arr);
                if ($update_account['code'] != 0) {
					EdjLog::info('update normal customer account fail '.$order['order_id']);
                }
            }
        }
        echo "城市补贴活动-普通用户返款end"."\n";
    }
    /**
     * 根据用户类型检查t_order_ext表返回补偿金额
     * @param $type(1-用户,2-司机)
     * @param $order_ext
     * @return money
     * author zhangtongkai 2014-04-04
     */
    public function check_order_ext($type, $order, $order_ext)
    {
		$orderId = $order['order_id'];
		$costType = isset($order['cost_type']) ? $order['cost_type'] : 0;
		$driverReadyTime = isset($order_ext['driver_ready_time']) ? $order_ext['driver_ready_time'] : 0;
		$distance = isset($order_ext['driver_ready_distance']) ? $order_ext['driver_ready_distance'] : 0;
		$income = $order['income'];
		$price = $order['price'];
		$condition = $this->getArriveInTimeCondition($order);
		EdjLog::info(sprintf('driverReadyTime:%s |distance:%s|condition:%s', $driverReadyTime, $distance, $condition));
		if(-1 == $condition){//-1为错误情况下的默认值,不返钱
			return 0;
		}
        if(!empty($type)){
            switch($type){
            	case 1://customer
                    $customerSubsidyMoney = 0;
					if($driverReadyTime > $condition){
							switch($costType){
								case 0://cash
								case 2://cash + bonus只返回现金部分
									$customerSubsidyMoney = $price;
									return $customerSubsidyMoney;
									break;
								case 1://vip
									if($income == $price){
										$customerSubsidyMoney = $price;
										return $customerSubsidyMoney;
									}
									$vipTrade = VipTrade::model()->getByOrderID($orderId, VipTrade::TYPE_ORDER, VipTrade::TRANS_SOURCE_D);
									$vipAmount = isset($vipTrade['amount']) ? $vipTrade['amount'] : 0;
									if(empty($vipTrade) || $vipAmount > 0){
										$customerSubsidyMoney = 0;
									}else{
										$customerSubsidyMoney = $vipAmount * -1;
									}
									break;
								case 4:
								case 8:
									$customerTrans = CarCustomerTrans::model()->getCustomerTrans($orderId, CarCustomerTrans::TRANS_TYPE_F, CarCustomerTrans::TRANS_SOURCE_S);
									if($customerTrans){
										$amount = isset($customerTrans->amount) ? $customerTrans->amount : 0;
										if($amount < 0){
											$customerSubsidyMoney = $amount * -1;
										}
									}
									break;
								default:

							}
					}
					return $customerSubsidyMoney;
                    break;
                case 2://driver
					$driverSubsidyMoney = 0;
					if($driverReadyTime <= $condition && $driverReadyTime > 0 ){
						if($distance <= 1){
							return 5;
						}
						if($distance > 1 && $distance <= 2 ){
							return  16;
						}
						if($distance > 2 && $distance <= 3 ){
							return  19;
						}
						if($distance > 3 && $distance <= 4 ){
							return  29;
						}
						if($distance > 4 && $distance <= 5){
							return  39;
						}
						if($distance > 5){
							return  59;
						}
					}
					return $driverSubsidyMoney;
                    break;
                default:
                    return 0;
            }
        }else{
        	return 0;
        }
    }
	
	/*
		得到vip某个订单将扣除的余额是多少
	*/
	public function getVipOrderBalance($order){
        //实体卡金额
        $coupon_money = 0.0;
        $order_ext = OrderExt::model()->getPrimary($order['order_id']);
        if ($order_ext && !empty($order_ext['coupon_money'])) {
			$coupon_money=$order_ext['coupon_money'];
        }
        $total = $order['income'] - $order['price'] - $coupon_money;
		return $total>0.0?$total:0.0;
	}

	/*
		得到普通用户某个订单要扣除的余额是多少
	*/
	public function getCustomerOrderBalance($order){
        //实体卡金额
        $coupon_money = 0.0;
        $order_ext = OrderExt::model()->getPrimary($order['order_id']);
        if ($order_ext && !empty($order_ext['coupon_money'])) {
			$coupon_money=$order_ext['coupon_money'];
        }
		$bonus_money=0.0;
		$bonus=CustomerBonus::model()->checkedBonusUseByOrderID($order['order_id']);
       	if($bonus&&!empty($bonus['use_money'])){
			$bonus_money=$bonus['use_money'];
		}
		//本次代驾服务要从用户中扣除的费用
        $money = $order['income'] - $order['price'] - $coupon_money - $bonus_money;
		return $money>0?$money:0.0;
	}

    /**
     * 新客免单活动，业务都问刘震林
     *
     * @已经不调用了
     *
     * @param $order
     * @param $free
     */
	public function newCustomerFree($order, $free){
		//补上司机的现金
		if($order['price']>0){
        	$dataAccount = array();
        	$dataAccount['channel'] = $free['channel'];
        	$dataAccount['comment'] = $free['name'] . ' 单号 '.$order['order_id'];
        	$dataAccount['order_date'] = time();
        	$dataAccount['type'] = EmployeeAccount::TYPE_ACTIVE;
        	$dataAccount['city_id'] = $order['city_id'];
        	$dataAccount['operator'] = 'system';
        	$dataAccount['user'] = $order['driver_id'];
        	$dataAccount['cast'] = $order['price'];
			$dataAccount['order_id']=$order['order_id'];
			$driverPriceRet=$this->insertAccount($dataAccount);
			if(!$driverPriceRet){
				EdjLog::info("new customer free activity for city " . $order['city_id'] . " add price for driver ".$order['driver_id']." money ".$order['price']." fail order_id ".$order['order_id']);
			}			
		}
		if(!empty($order['vipcard'])){
			$addMoney=$this->getVipOrderBalance($order);
			$vip=Vip::model()->findByPk($order['vipcard']);
			if(!$vip){
				EdjLog::info("why no vip here ".$order['vipcard']);
			}
			$beforeBalance=$vip->balance;
			$affectRows=Vip::model()->updateBalance($order['vipcard'],$addMoney);
			if($affectRows){
            			$attributes = array(
                			'vipcard' => $order['vipcard'],
                			'order_id' => $order['order_id'],
                			'type' => VipTrade::TYPE_SUBSIDY,
					'source' => VipTrade::TRANS_SOURCE_RE, 
                			'amount' => $addMoney,
                			'comment' => $free['name'] . ' 单号：' . $order['order_id'],
                			'balance' => $beforeBalance + $addMoney,
                			'order_date' => $order['created']
            			);
            			$vipTrade = new VipTrade();
            			$amount = $vipTrade->addTrade($attributes);
				if($amount===null || $amount === false){
					EdjLog::info("add trade log fail ".serialize($attributes));
				}
			}
		}
		else{
			$addMoney=$this->getCustomerOrderBalance($order);
		        $user_id = 0;
        		$phone = $order['phone'];
			$customer_info=CustomerService::service()->getCustomerInfo($phone,1);
        		if(!$customer_info){
				$customer_info=CustomerService::service()->forceGetCustomerInfo($phone);
				if(!$customer_info){
					EdjLog::info("for phone $phone no customer_info");
					return false;
				}
			}
			$user_id=$customer_info->id;
			if(!$user_id){
				EdjLog::info("no user_id for phone $phone");
				return false;
			}
            $user_info = NormalAccountService::getUserAmount($phone);
        		if ($user_info['code'] == 0) {
            			//$data_user = $user_info['data_user'];
            			//$user_id = $data_user->id;
            			//用户余额
				if(isset($user_info['data_user'])&&isset($user_info['amount'])){
            				$user_balance = $user_info['amount'];
				}
				else{
					$user_balance=0;
					$params=array('user_id'=>$user_id,'city_id'=>$customer_info->city_id,'type'=>CarCustomerAccount::ACCOUNT_TYPE_S,'amount'=>0,'vip_card'=>0);
					$addAccountRet=BCustomers::model()->addAccount($params);
                        		if(!isset($addAccountRet['code'])||$addAccountRet['code']!=0){
                                		EdjLog::info("add account for user_id $user_id fail");
                                		return false;
                        		}
				}
        		}
			$arr = array();
                	$arr['user_id'] = $user_id;
                	$arr['amount'] = $addMoney;
                	$update_account = BCustomers::model()->updateAccount($arr);
                	if ($update_account['code'] === 0) {
            			//消费后余额
            			$balance = $user_balance + $addMoney;
            			// 添加客户消费明细
            			$attributes = array(
                			'user_id' => $user_id,
                			'trans_order_id' => $order['order_id'],
                			'trans_type' => CarCustomerTrans::TRANS_TYPE_RE,
                			'amount' => $addMoney,
                			'balance' => $balance,
                			'source' => CarCustomerTrans::TRANS_SOURCE_RE,
                			'remark' => $free['name'] . ' 订单号：' . $order['order_id'],
            			);
            			$add_trans = BCustomers::model()->addCustomerTrade($attributes);				
				if ($add_trans['code'] !== 0) {
					EdjLog::info("add trans fail ".serialize($attributes));
				}
			}				
		}		
	}

	/**
	 * 用于发布及时到达的活动的队列
	 *
	 * @param $order
	 * @param $cityId
	 */
	public function arriveInTimeActivity($order, $bonusMoney){
		EdjLog::info(' order settlement get in  '.serialize($order));
		$status = $this->isArriveInTimeActivityTurnOn($order);
		if(!$status){
			return;
		}
		$data = array(
			'order_id' 		=> $order['order_id'],
			'bonus_money'	=> $bonusMoney,
		);
		$task = array(
			'method' => 'load_order_ext',
			'params' => $data,
		);
		Queue::model()->putin($task, 'orderext');
	}

	/**
	 *
	 * 即时到达活动是否开启
	 *
	 * @param $order
	 * @return mixed
	 */
	public function isArriveInTimeActivityTurnOn($order){
		try{
			return ArriveInTimeActivity::model()->isActivityApplied($order);
		}catch (Exception $e){
			EdjLog::error('isArriveInTimeActivityTurnOn order_id '.$order['order_id'].' exception is '.$e->getMessage());
			return false;
		}
	}

	/**
	 * 根据订单获得城市即时到达活动的时间要求
	 *
	 * @param $order
	 */
	private function getArriveInTimeCondition($order){
		try{
			$activity = ArriveInTimeActivity::model()->getByCityID($order['city_id']);
			$condition = $activity['time_range'];
			return $condition;
		}catch (Exception $e){
			return -1;
		}
	}

	/**
	 * 活动计算
	 *
	 * @param $order
	 * @param $orderExt
	 */
	public function activitySettlement($order, $orderExt){
		if(null == $orderExt){
			$orderExt = OrderExt::model()->getPrimary($order['order_id']);
		}
		$settleList = SettlementFactory::getActivitySettlementList($order, $orderExt);
		if(empty($settleList)){
			EdjLog::info('find no activity settlement '.serialize($order));
			return;
		}
		foreach($settleList as $settle){
			try{
				$settle->settlement();
			}catch (Exception $e){
				EdjLog::error('activity settle error '.$e->getMessage());
				EdjLog::info('settle is '.serialize($settle));
			}
		}
	}

	/**
	 * 远程叫单结账
	 *
	 * @param $orderId
	 */
	public function remoteOrderSettle($orderId){
		EdjLog::info('remote order settle get in --- '.$orderId);
		//reset cost_type
		$order = Order::model()->getOrderById($orderId);
		$orderExt = OrderExt::model()->getPrimary($orderId);
		$vipCard  = isset($order['vipcard']) ?  $order['vipcard'] : 0;
		$phone = isset($order['phone']) ? $order['phone'] : 0;
		if(empty($vipCard)){
			FinanceUtils::lockCustomer($phone);
		}else{
			FinanceUtils::lockVip($vipCard);
		}
		//lock order
		FinanceUtils::lockOrder($orderId, $vipCard, $phone);
		$settleList = SettlementFactory::getSettleChainFromOrder($order, $orderExt);
		$total = count($settleList);
		$failCount = 0;
		foreach($settleList as $settle){
			$ret = $settle->settlement();
			$status = FinanceConstants::isSuccess($ret);
			if(!$status){
				$failCount += 1;
			}
		}
		EdjLog::info('remote order settle end --- '.$orderId.'failCount:'.$failCount);
        $aysnChain = new AsynChain($order);
        $aysnChain->afterSettle();
	}

	/**
	 * 退单重结
	 *
	 * @param $order
	 * @return bool
	 */
	public function refundOrder($order){
		$status = FinanceUtils::isOrderFinish($order['status']);
		if($status){
			$refundSettlement = new RefundSettlement($order);
			$refundSettlement->settlement();
			return true;
		}else{
			return false;
		}
	}

	/**
	 * 报单结账
	 *
	 * @param $orderId
	 * @param array $params
	 * @return bool
	 */
	public function submitSettle($orderId, $params = array())
    {
        $order = Order::model()->getOrderById($orderId);
        $status = $order['status'];
        if (!FinanceUtils::isOrderFinish($status)) {
            EdjLog::info('order is not finish ' . serialize($order));
            return true;
        }
        $orderExt = OrderExt::model()->getPrimary($orderId);
        $settleList = SettlementFactory::getSettleListWithParam($order, $orderExt, $params);
        if (empty($settleList)) {
            EdjLog::info('get no settle list  order is ' . $orderId . ' params is' . json_encode($params));
        }
        foreach ($settleList as $settle) {
            $settle->settlement();
        }
        $aysnChain = new AsynChain($order);
        $aysnChain->afterSettle();
    }
    /**
	 * 红包结账
	 *
	 * @param $driverId
	 * @param $cityId
	 * @param $sn
	 * @param $cast
	 * @return bool
	 */
	public function envelopeSettle($driverId, $cityId, $sn, $cast,$order_id=0){
		$companySettle = new CompanySubsidySettlement($driverId, $cityId);
		$companySettle->setCast($cast);
		$companySettle->setComment('红包充值, 序列号:'.$sn);
		$companySettle->setChannel(EmployeeAccount::CHANNEL_DRIVER_INFORMATION_ENVELOPE);
        $companySettle->setOrderId($order_id);
		return $companySettle->settlement();
	}

	/**
	 * 调整费结算
	 *
	 * @param $orderId
	 */
	public function modifyFeeSettle($orderId){
		$order = Order::model()->getOrderById($orderId);
		$settleList = SettlementFactory::getDriverSettleList($order);
		if(!empty($settleList)){
			foreach($settleList as $settle){
				$settle->settlement();
			}
		}
	}
}
