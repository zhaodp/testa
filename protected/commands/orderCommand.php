<?php
class OrderCommand extends LoggerExtCommand {
	/**
	* 修改销单类型，将原有销单类型变成五个
	*
	*/
	public function actionResetCancelType() {
		//原取消类型与新取消类型对应（key 为原类型 value 为新类型）
		$type_array = array(
			1 => 1,
			2 => 1,
			3 => 1,
			4 => 1,
			5 => 2,
			6 => 2,
			7 => 3,
			8 => 4,
			10 => 4,
			9 => 5,
		);
		$cancel_type_new = array(
			0 => '请选择',
			1 => '客人单方面取消',
			2 => '客人咨询或拨错电话',
			3 => '未接听到客人电话',
			4 => '骚扰电话',
			5 => '其它原因',
		);
		//清除缓存
		$dictname = 'cancel_type';
		$cache_key = 'SYSTEM_DICT_'.$dictname;
		Yii::app()->cache->delete($cache_key);
		$cancel_type_old = Dict::model ()->findAll ( array (
				'condition' => 'dictname=:dictname',
				'params' => array (
						':dictname' => 'cancel_type'
				)
		) );
		foreach ($cancel_type_old as $_model) {
			$_model->delete();
		}
		foreach ($cancel_type_new as $code=>$name) {
			$_model = new Dict();
			$_model->dictname = 'cancel_type';
			$_model->name = $name;
			$_model->code = $code;
			$_model->postion = $code;
			echo intval($_model->save())."\n";
		}
	}
	/**
	 *
	 * 生成每日订单记录
	 */
	public function actionGenOrder() {
		//扫描最近10分钟的通话记录
		$start=date('Y-m-d H:i:s.000', time()-600);
		$end=date('Y-m-d H:i:s.999', time());

		//$start = '2013-01-14 00:00:00';
		//$end = '2013-01-14 02:00:00';


		$offset=0;
		$pagesize=50;
		while(true) {
			/**
select c.imei,c.phone,c.gap,c.type,c.insert_time, from_unixtime(unix_timestamp(insert_time) - gap) calling_time
from t_call_history c LEFT JOIN t_driver d ON c.phone = d.phone
where  insert_time between '2012-07-30 00:00:00' and '2012-07-30 01:00:00'
order by  calling_time
			 */

			$criteria=new CDbCriteria(array(
					'select'=>'c.id,c.imei,c.phone,c.simcard,c.gap,c.type,d.city_id,c.insert_time, (unix_timestamp(insert_time) - gap) as call_time',
					'join'=>'LEFT JOIN t_driver d ON c.phone = d.phone',
					'order'=>'call_time',
					'offset'=>$offset,
					'limit'=>$pagesize
			)); //'group'=>'c.phone,c.imei'


			$criteria->alias='c';
			$criteria->addBetweenCondition('insert_time', $start, $end);
			//$criteria->addCondition("d.mark = :mark");
   			//$criteria->params[':mark']=0;

			$ret=CallHistory::model()->findAll($criteria);

			//添加测试工号不生成订单 BY AndyCong 2013-08-02
//			$test_drivers = Common::getCallOrderAutoTestDriverIds();

			if ($ret) {
				foreach($ret as $item) {
					$driver=Driver::getProfileByImei($item->imei);

					//测试工号不生成订单 BY AndyCong 2013-08-02
//					$driver_id = isset($driver->user) ? $driver->user : '';
//					if (in_array($driver_id , $test_drivers)) {
//						echo '测试工号不生成订单'.$driver_id."\n";
//						continue;
//					}

                    //智能计费版本不生成订单 By AndyCong 2013-08-22
                    $driver_id = !empty($driver->user) ? $driver->user : '';
                    $simcard = !empty($item->simcard) ? $item->simcard : '';
                    if ('ZNJF' == $simcard) {
                    	echo '智能计费版本不生成订单'.$driver_id."\n";
                    	continue;
                    }
					//智能计费版本不生成订单 By AndyCong 2013-08-22 END

					//检查司机是否在工作中或者已下班
					if ($driver&&$driver->mark==0) {
						echo $item->phone."\n";
						$params=array(
								'phone'=>($item->phone) ? $item->phone : '',
								'driver'=>$driver->name,
								'driver_id'=>$driver->user,
								'source'=>0,
								'driver_phone'=>$driver->phone,
								'imei'=>$item->imei,
								'city_id'=>$driver->city_id,
								'call_type'=>$item->type,
								'call_time'=>$item->call_time,
								'order_date'=>date('Ymd', $item->call_time),
								'booking_time'=>$item->call_time+60*20,
								'created'=>time(),
								'description'=>'客户直接呼叫',
								'status'=>Order::ORDER_READY
						);
						$order=new Order();
						$order->attributes=$params;
						if (!$order->save()) {
							print_r($order->errors);
						}else{
							$content = MessageText::getFormatContent(MessageText::CUSTOMER_DRIVER_DIRECT, $driver->user, $driver->name, $driver->phone);
							$result = Sms::SendSMS($item->phone, $content);
							echo 'success:'. json_encode($result) ."\n";
						}
					}
				}
			} else {
				break;
			}
			$offset+=$pagesize;
		}
	}

	/**
	 *
	 * 检查接单时前后上报的状态
	 */
	public function actionorderDriverStatus() {
		return true;
		$offset=0;

		//扫描10分钟前的订单，查找司机接单时的状态
		$start=date('Y-m-d H:i:s.000', time()-1200);
		$end=date('Y-m-d H:i:s.000', time()-600);

		//		$start = '2012-08-01 00:00:00';
		//		$end = '2012-08-01 13:25:27';


		while(true) {
			$criteria=new CDbCriteria(array(
					'limit'=>50,
					'offset'=>$offset,
					'order'=>'order_id'
			));
			$criteria->addBetweenCondition('call_time', strtotime($start), strtotime($end));
			$orders=Order::model()->findAll($criteria);
			if ($orders) {
				foreach($orders as $order) {
					echo $order->order_id."\n";
					OrderDriverState::setState($order->order_id, $order->imei, $order->call_time);
				}
			} else {
				break;
			}

			$offset+=50;
		}
	}

	public function actionAccountVip($date=null){
	// $sql='select o.order_id,order_number,vipcard,income,l.created
		// 		from t_order o join t_order_log l on o.order_id = l.order_id
		// 		where status in(1,4) and date(from_unixtime(l.created)) = :date
		// 		and o.driver_id = l.operator';

		if ($date) {
			$end_time=date('Y-m-d 15:00:00', strtotime($date));
			$begin_time=date('Y-m-d 15:00:00', strtotime($date)-86400);
		} else {
			$end_time=date('Y-m-d 15:00:00', time());
			$begin_time=date('Y-m-d 15:00:00', time()-86400);
		}

		//		$begin_time = '2013-01-29 00:00:00';
		//		$end_time = '2013-01-29 00:30:00';
		$pagesize=500;
		$offset=0;

		while(true) {
			$command=Yii::app()->db->createCommand();
			$command->select('o.order_id,order_number,vipcard,phone,call_time,booking_time,location_start,location_end,distance,driver_id,city_id,income,price,l.created')->from('t_order o')->join('t_order_log l', 'o.order_id = l.order_id')->where('status in(1,4) and l.created between unix_timestamp(:begin_time) and unix_timestamp(:end_time) and o.driver_id = l.operator', array(
					':begin_time'=>$begin_time,
					':end_time'=>$end_time
			))->group('order_id')->limit($pagesize, $offset);

			$rows=$command->queryAll();
			if ($rows) {
				foreach($rows as $item) {
					if ($item['vipcard']) {
						$order_params=array(
								':order_id'=>$item['order_id']
						);

						if (EmployeeAccount::model()->exists('order_id=:order_id', $order_params)) {
							EmployeeAccount::model()->deleteAll('order_id=:order_id', $order_params);
							echo 'delete '.$item['order_id']."\n";
						}

						echo $item['order_id']."\n";
						//每订单扣除信息费
						self::order_infomatin_fee($item);
						//扣除每单保险费(仅北京司机)，2013-04-23 日开始全国都收取保险费
						self::order_insurance($item);


						self::order_vip($item);
					}
				}
				$offset+=$pagesize;
			} else {
				break;
			}
		}
	}

	/**
	 *
	 * 生成每日15点前完成报单的订单对账单中优惠券返现
	 * @param 日期 $date
	 */
	public function actionAccountBonus($date=null) {
		if ($date) {
			$end_time=date('Y-m-d 15:00:00', strtotime($date));
			$begin_time=date('Y-m-d 15:00:00', strtotime($date)-86400);
		} else {
			$end_time=date('Y-m-d 15:00:00', time());
			$begin_time=date('Y-m-d 15:00:00', time()-86400);
		}

		$pagesize=50;
		$offset=0;

		while(true) {
			$command=Yii::app()->db->createCommand();
			$command->select('o.order_id,order_number,vipcard,phone,call_time,booking_time,location_start,location_end,distance,driver_id,income,source,l.created')
					->from('t_order o')
					->join('t_order_log l', 'o.order_id = l.order_id')
					->where('status in(1,4) and l.created between unix_timestamp(:begin_time) and unix_timestamp(:end_time) and o.driver_id = l.operator',
						array(':begin_time'=>$begin_time, ':end_time'=>$end_time))
					->group('order_id')
					->limit($pagesize, $offset);
			$rows = $command->queryAll();

			if ($rows) {
				foreach($rows as $item) {
					if (!$item['vipcard']) {
						echo $item['order_id']."\n";
						self::order_bonus($item, true);
					}
				}
				$offset+=$pagesize;
			} else {
				break;
			}
		}
	}

	/**
	 *
	 * 生成每日15点前完成报单的订单对账单中优惠券返现
	 * @param 日期 $date
	 */
	public function actionAccountBonusReload($date=null) {
		if ($date) {
			$end_time=date('Y-m-d 15:00:00', strtotime($date));
			$begin_time=date('Y-m-d 15:00:00', strtotime($date)-86400);
		} else {
			$end_time=date('Y-m-d 15:00:00', time());
			$begin_time=date('Y-m-d 15:00:00', time()-86400);
		}

		$pagesize=50;
		$offset=0;

		while(true) {
			$command=Yii::app()->db->createCommand();
			$command->select('o.order_id,order_number,vipcard,phone,call_time,booking_time,location_start,location_end,distance,driver_id,income,l.created')->from('t_order o')->join('t_order_log l', 'o.order_id = l.order_id')->where('status in(1,4) and l.created between unix_timestamp(:begin_time) and unix_timestamp(:end_time) and o.driver_id = l.operator', array(
					':begin_time'=>$begin_time,
					':end_time'=>$end_time
			))->group('order_id')->limit($pagesize, $offset);

			$rows=$command->queryAll();

			if ($rows) {
				foreach($rows as $order) {
					echo $order['order_id']."\n";

					if (!$order['vipcard']) {

						$bonus=CustomerBonus::existsUnusedCustomerBonus($order['phone']);
						if ($bonus) {
							CustomerBonus::customerBonusUse($order['phone'], $bonus['bonus'], $order['order_id'], $order['booking_time']);
						}

						$bonus=CustomerBonus::getOrderBonus($order['phone'], $order['order_id']);

						if ($bonus&&$bonus['created']<$order['call_time']) {
							echo '---'.$bonus['bonus']."\n";
							$arrStaticBonus=BonusType::model()->getStaticBonus();

							if (in_array($bonus['bonus_type_id'], $arrStaticBonus))
								$bonus_sn=$bonus['bonus'];
							else
								$bonus_sn=$bonus['bonus'].$bonus['parity_bit'];

							EmployeeAccount::model()->deleteAll('order_id=:order_id and type=:type', array(
									':order_id'=>$order['order_id'],
									':type'=>EmployeeAccount::TYPE_BONUS_CASH
							));

							$account=new EmployeeAccount();
							$account->attributes=array(
									'type'=>EmployeeAccount::TYPE_BONUS_CASH,
									'user'=>$order['driver_id'],
									'order_id'=>$order['order_id'],
									'comment'=>'优惠券充值 号码：'.$bonus_sn, //$bonus['bonus'].$bonus['parity_bit'],
									'cast'=>$bonus['money'],
									'created'=>$order['created']
							);

							$account->addAccount();
							//print_r($bonus);die();


							//给发卡司机返点//司机发卡优惠券
							if ($bonus['bonus_type_id']==8) {
								$city_id=substr($bonus_sn, 0, 2);
								$driver_id=substr($bonus_sn, 2, 4);
								$cityPrefix=Dict::items("bonus_city");
								$driver=array();

								if (isset($cityPrefix[$city_id])) {
									$driver_id=$cityPrefix[$city_id].$driver_id;

									$model=Driver::model()->find('user=:driver_id', array(
											':driver_id'=>$driver_id
									));
									if ($model)
										$driver['driver_id']=$model->user;

									$return_type=EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN;
									$return_cast=20;
								}
							} else {
								$driver=DriverBonus::model()->getDriverID($bonus_sn); //$bonus['bonus'].$bonus['parity_bit']);
								$return_type=EmployeeAccount::TYPE_BONUS_RETUEN;
								$return_cast=5;
							}

							if ($driver) {
								EmployeeAccount::model()->deleteAll('order_id=:order_id and type=:type', array(
										':order_id'=>$order['order_id'],
										':type'=>$return_type
								));

								$account=new EmployeeAccount();
								$account->attributes=array(
										'type'=>$return_type, //EmployeeAccount::TYPE_BONUS_RETUEN,
										'user'=>$driver['driver_id'],
										'order_id'=>$order['order_id'],
										'comment'=>'优惠券返现 号码：'.$bonus_sn, //$bonus['bonus'].$bonus['parity_bit'],
										'cast'=>$return_cast, //5,
										'created'=>$order['created']
								);
								$account->addAccount();
							}
						}
					}
				}
				$offset+=$pagesize;
			} else {
				break;
			}
		}
	}

	/**
	 *
	 * 手动结算测试
	 * @param 订单流水号 $id
	 * php protected/yiic order testaccountsettle --id=382345
	 */
	public function actionTestAccountSettle($id=null) {
		$command=Yii::app()->db->createCommand();
		$command->select('o.order_id,order_number,vipcard,phone,call_time,booking_time,location_start,location_end,distance,driver_id,city_id,income,price,l.created')->from('t_order o')->join('t_order_log l', 'o.order_id = l.order_id')->where('status in(1,4) and o.order_id =:order_id and o.driver_id = l.operator', array(
				':order_id'=>$id
		))->group('order_id');

		$row=$command->queryRow();

		if ($row) {
			EmployeeAccount::model()->deleteAll('order_id=:order_id', array(
					':order_id'=>$row['order_id']
			));

			echo $row['order_id']."\n";
			//每订单扣除信息费
			self::order_infomatin_fee($row);
			//扣除每单保险费(仅北京司机),2013-04-23 日开始全国都收取保险费
			self::order_insurance($row);

			if ($row['vipcard']) {
				self::order_vip($row);
			} else {
				//司机优惠券返点
				//self::order_bonus($row, true);


				self::order_normal($row);
			}
		}
	}

	/**
	 *
	 * 手动订单结算优惠券
	 * @param int $order_id
	 * php protected/yiic order testbonus --id=382345
	 */
	public function actionTestBonus($id) {
		$command=Yii::app()->db->createCommand();
		$command->select('o.order_id,order_number,vipcard,phone,call_time,booking_time,location_start,location_end,distance,driver_id,income,source,l.created')->from('t_order o')->join('t_order_log l', 'o.order_id = l.order_id')->where('status in(1,4) and o.order_id =:order_id and o.driver_id = l.operator', array(
				':order_id'=>$id
		))->group('order_id');

		$order=$command->queryRow();

		if ($order) {
			echo $order['order_id']."\n";

			if (!$order['vipcard']) {
				self::order_bonus($order, true);
			}
		}
	}

	/**
	 *
	 * 普通订单的收入记录
	 * @param array $order
	 */
	private function order_bonus($order, $notify_sms=false) {
		//如果用户有绑定优惠券，金额返信息费
		$un_used_bonus=CustomerBonus::existsUnusedCustomerBonus($order['phone']);
		if ($un_used_bonus) {
			//有app用户优惠劵但用呼叫中心呼叫的
			if (($un_used_bonus['is_limited']==BonusType::BONUS_TYPE_LIMIT_APP || $un_used_bonus['is_limited']==BonusType::BONUS_TYPE_LIMIT_APP_FRESH)
					&& (in_array($order['source'], Order::$callcenter_sources) || in_array($order['source'], Order::$callcenter_input_sources))) {
				$sms_message="您昨天通过拨打400电话享受e代驾服务,您的账户中还有".$un_used_bonus['money']."元代金券,仅限通过e代驾客户端找代驾使用,免费下载>> http://wap.edaijia.cn ";
				Sms::SendSMS($order['phone'], $sms_message);
			} else {
				//优惠劵结账状态  默认是false不结账
				$status = FALSE;
				if(in_array($order['source'], Order::$callcenter_sources) || in_array($order['source'], Order::$callcenter_input_sources)){
					$status = TRUE;
				}else{
					//app呼叫  有呼入记录 返回true 否则false
					$status = CallHistory::model()->getIncomeCallHistory($order['phone'],$order['call_time']);
				}
				//app用户在订单生成之前两个小时之内有呼入记录
				if($status){
					//订单和优惠劵都存在使用优惠劵
					CustomerBonus::customerBonusUse($order['phone'], $un_used_bonus['bonus'], $order['order_id'], $order['booking_time']);
					//获取订单的优惠劵信息
					$bonus = CustomerBonus::getOrderBonus($order['phone'], $order['order_id']);

					if ($bonus) {
						echo '---'.$bonus['bonus']."\n";
						$arrStaticBonus=BonusType::model()->getStaticBonus();

						if (in_array($bonus['bonus_type_id'], $arrStaticBonus))
							$bonus_sn=$bonus['bonus'];
						else
							$bonus_sn=$bonus['bonus'].$bonus['parity_bit'];

						EmployeeAccount::model()->deleteAll('order_id=:order_id and type=:type', array(
								':order_id'=>$order['order_id'],
								':type'=>EmployeeAccount::TYPE_BONUS_CASH
						));

						$account=new EmployeeAccount();
						$account->attributes=array(
								'type'=>EmployeeAccount::TYPE_BONUS_CASH,
								'user'=>$order['driver_id'],
								'order_id'=>$order['order_id'],
								'comment'=>'优惠券充值 号码：'.$bonus_sn, //$bonus['bonus'].$bonus['parity_bit'],
								'cast'=>$bonus['money'],
								'created'=>$order['created']
						);

						$account->addAccount();
						//print_r($bonus);die();


						//给发卡司机返点//司机发卡优惠券
						if ($bonus['bonus_type_id']==8) {
							$city_id=substr($bonus_sn, 0, 2);
							$driver_id=substr($bonus_sn, 2, 4);
							$cityPrefix=Dict::items("bonus_city");
							$driver=array();

							if (isset($cityPrefix[$city_id])) {
								$driver_id=$cityPrefix[$city_id].$driver_id;

								$model=Driver::model()->find('user=:driver_id', array(
										':driver_id'=>$driver_id
								));
								if ($model)
									$driver['driver_id']=$model->user;

								$return_type=EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN;
								$return_cast=20;
							}
						} else {
							$driver=DriverBonus::model()->getDriverID($bonus_sn); //$bonus['bonus'].$bonus['parity_bit']);
							$return_type=EmployeeAccount::TYPE_BONUS_RETUEN;
							$return_cast=5;
						}

						if ($driver) {
							EmployeeAccount::model()->deleteAll('order_id=:order_id and type=:type', array(
									':order_id'=>$order['order_id'],
									':type'=>$return_type
							));

							$account=new EmployeeAccount();
							$account->attributes=array(
									'type'=>$return_type, //EmployeeAccount::TYPE_BONUS_RETUEN,
									'user'=>$driver['driver_id'],
									'order_id'=>$order['order_id'],
									'comment'=>'优惠券返现 号码：'.$bonus_sn, //$bonus['bonus'].$bonus['parity_bit'],
									'cast'=>$return_cast, //5,
									'created'=>$order['created']
							);
							$account->addAccount();
						}
						$bonus_name=BonusType::model()->getBonusName($bonus['bonus_type_id']);

						if ($notify_sms) {
							$cash=$order['income']-$bonus['money'];
							$sms_message=MessageText::getFormatContent(MessageText::CUSTOMER_BONUS_USED, date("Y-m-d", $order['created']), $order['income'], $bonus_name, $bonus_sn, $bonus['money'], $cash);
							Sms::SendSMS($order['phone'], $sms_message);
						}
					}
				}
			}
		}
	}

	/**
	 *
	 * php yiic.php order AccountOrderCast --date=2013-03-13
	 * @param unknown_type $date
	 */
	public function actionAccountOrderCast($date=null) {
		if (empty($date)) {
			$date=date('Y-m-d');
		}
		$pagesize=500;
		$offset=0;

		while(true) {
			$command=Yii::app()->db_finance->createCommand();
			$command->select('*')->from('t_employee_account')->where('type = :type and from_unixtime(created,"%Y-%m-%d") = :created', array(
					':type'=>EmployeeAccount::TYPE_ORDER_INFOMATION,
					':created'=>$date
			))->limit($pagesize, $offset);
			$rows=$command->queryAll();
			if ($rows) {
				foreach($rows as $item) {
					echo $item['order_id']."\n";
					//每订单扣的信息费更新到order表中
					self::update_order_cast($item);
				}
				$offset+=$pagesize;
			} else {
				break;
			}
		}
	}

	/**
	 * 修改order表cast信息
	 * Enter description here ...
	 * @param unknown_type $order
	 */
	private function update_order_cast($order) {
		//修改order表中cast字段
		Order::model()->updateByPk($order['order_id'], array(
				'cast'=>$order['cast']
		));
	}

	/**
	 * 计算订单的信息费
	 *
	 * @param array $order
	 */
	private function order_infomatin_fee($order) {
		if($order['city_id'] == 1){
			$cast = CityConfig::model()->calculatorCast($order); //Common::cast($order);
		}else{
			$cast=self::city_fee($order['driver_id'], $order['call_time']);
		}
		$account=new EmployeeAccount();
		$account->attributes=array(
				'type'=>EmployeeAccount::TYPE_ORDER_INFOMATION,
				'user'=>$order['driver_id'],
				'order_id'=>$order['order_id'],
				'comment'=>'信息费 单号：'.$order['order_number'],
				'cast'=>$cast,
				'created'=>$order['created']
		);
		$account->addAccount();

		// 订单信息费写回order表中cast字段
		Order::model()->findByPk($order['order_id'])->updateByPk($order['order_id'], array(
				'cast'=>$cast
		));
	}

	/**
	 *
	 * 计算各个城市的计费标准
	 * 标准城市：
	 * 扣除每单的信息费用
	 * 7点到22点：5元.
	 * 22点到23点：10元
	 * 23点到0点：15元
	 * 0点到6点：20元
	 *
	 * 重庆：
	 * 每单10元
	 *
	 * 杭州：
	 * 晚7点到22点：5元.
	 * 22点到早7点：10元
	 *
	 * @param int $driver_id
	 * @param datetime $calltime 订单呼叫时间
	 */
	private function city_fee($driver_id, $calltime) {
		$cast=10;
		//检查司机所在城市，重庆和杭州单独信息费标准
		$driver=Driver::model()->find('user=:user', array(':user'=>$driver_id) );

		if (!$driver) {
			return 0;
		}

		switch ( $driver->city_id ) {

			//北京
			case 1:
				$hour=date('H', $calltime);
				$minutes=date('i', $calltime);
				//换算为分钟后计算
				$min_sum=$hour*60+$minutes;

				if ($min_sum>=60*7 and $min_sum<60*22) {
					$cast=5;
				} elseif ($min_sum>=60*22 and $min_sum<60*23) {
					$cast=10;
				} elseif ($min_sum>=60*23 and $min_sum<60*24) {
					$cast=15;
				} elseif ($min_sum>=0 and $min_sum<60*9) {
					$cast=20;
				}
				break;

			//4杭州,8南京，10武汉，11西安，15济南，18郑州
			case 4 :
			case 8 :
			case 10 :
			case 11 :
            case 14 :
			case 15 :
			case 14 :
			case 18 :
				$hour=date('H', $calltime);
				$minutes=date('i', $calltime);
				//换算为分钟后计算
				$min_sum=$hour*60+$minutes;

				if ($min_sum>=60*7 and $min_sum<60*22) {
					$cast=5;
				} else {
					$cast=10;
				}
				break;

			//2 成都，7 重庆
			case 2 :
			case 7 :
				$cast=10;
				break;

			default :
				$hour=date('H', $calltime);
				$minutes=date('i', $calltime);
				//换算为分钟后计算
				$min_sum=$hour*60+$minutes;

				if ($min_sum>=60*7 and $min_sum<60*22) {
					$cast=5;
				} else {
					$cast=10;
				}
				break;
		}

		//得到司机费用的折扣，add by sunhongjing 2013-06-23
		//因为php是传值引用，这里想省点内存，算了代码简洁点儿，不折腾了。注掉吧
//		$driver = (object)array(
//								'driver_id'=>$driver->user,
//								'user'=>$driver->user,
//								'city_id'=>$driver->city_id,
//								'created'=>$driver->created,
//							);
		$driver_fee_discount = Common::driver_fee_discount($driver);

		return $cast*$driver_fee_discount;
	}


	/**
	 * 2013-04-23 日开始全国都收取保险费  --孟天学
	 * 扣除订单保险费用，每单2元
	 * @param object $order
	 */
	private function order_insurance($order, $cast=2) {

		$is_normal = 1;
		$ret = true;

		//begin----------新城市激励政策-----add by sunhongjing 2013-06-23---------//
		//2成都 ，8南京，10武汉，11西安，14天津，15济南，18郑州 ，新开城市，看司机情况，调用折扣方法，符合条件的不扣保险费
		$discount_city = array(2,8,10,11,14,15,18);
		if( in_array( $order['city_id'], $discount_city ) ){
			//取得司机信息
			$driver = Driver::model()->find('user=:user', array(':user'=>$order['driver_id']) );
			if (!$driver) {
				return $ret;
			}
			//得到司机费用的折扣，add by sunhongjing 2013-06-23
			$is_normal = Common::driver_fee_discount($driver,2);
		}
		//end

		if( $is_normal ){
			$account=new EmployeeAccount();
			$account->attributes=array(
					'type'=>EmployeeAccount::TYPE_ORDER_INSURANCE,
					'user'=>$order['driver_id'],
					'order_id'=>$order['order_id'],
					'comment'=>'保险费 单号：'.$order['order_number'],
					'cast'=>$cast,
					'created'=>$order['created']
			);
			$ret = $account->addAccount();
		}
		return $ret;
	}

	/**
	 *
	 * 普通订单的收入记录
	 * @param int $order_id
	 */
	private function order_normal($order) {
		$account=new EmployeeAccount();
		$account->attributes=array(
				'type'=>EmployeeAccount::TYPE_ORDER_CASH,
				'user'=>$order['driver_id'],
				'order_id'=>$order['order_id'],
				'comment'=>'现金收入 单号：'.$order['order_number'],
				'cast'=>$order['income'],
				'created'=>$order['created']
		);
		$account->addAccount();

		$model=Order::model()->findByPk($order['order_id']);
		if ($model->invoice) {
			$account=new EmployeeAccount();
			$account->attributes=array(
					'type'=>EmployeeAccount::TYPE_ORDER_INVOICE,
					'user'=>$order['driver_id'],
					'order_id'=>$order['order_id'],
					'comment'=>'发票扣税 单号：'.$order['order_number'],
					'cast'=>$order['income']*0.06,
					'created'=>$order['created']
			);
			$account->addAccount();
		}
	}

	/**
	 *
	 * VIP订单，扣除VIP帐户余额，增加司机收入记录，发短信通知VIP扣款金额及余额
	 * @param int $order_id
	 */
	private function order_vip($order) {
		$vip = Vip::model()->getPrimary($order['vipcard']);
		if (!$vip) {
			return;
		}

        $order_ext = OrderExt::model()->getPrimary($order['order_id']);
        $tip = 0;
        if($order_ext){
            $tip = $order_ext['total'];
        }
		$income = $order['income'] - $order['price'];
        $total = $income + $tip;

		if($income > 0){
			//VIP客户消费明细
			$attributes=array(
					'vipcard'=>$order['vipcard'],
					'order_id'=>$order['order_id'],
					'type'=>VipTrade::TYPE_ORDER,
					'amount'=>$total,
					'comment'=>'VIP消费 单号：'.$order['order_number'],
                    'balance'=> $vip->balance - $total,
					'created'=>$order['created']
			);
			$trade=new VipTrade();
			$amount=$trade->addTrade($attributes);

			//VIP订单划账给司机账户
			$account=new EmployeeAccount();
			$account->attributes=array(
					'type'=>EmployeeAccount::TYPE_ORDER_VIP,
					'user'=>$order['driver_id'],
					'order_id'=>$order['order_id'],
					'comment'=>'VIP订单收入 单号：'.$order['order_number'],
					'cast'=>$total,
					'created'=>$order['created']
			);
			$account->addAccount();

			//扣除发票税点
			//TODO：税点单独放到参数里
			$account=new EmployeeAccount();
			$account->attributes=array(
					'type'=>EmployeeAccount::TYPE_ORDER_INVOICE,
					'user'=>$order['driver_id'],
					'order_id'=>$order['order_id'],
					'comment'=>'VIP订单发票税点扣除 单号：'.$order['order_number'],
					'cast'=>($order['income'] + $tip) * 0.06,
					'created'=>$order['created']
			);
			$account->addAccount();

			//扣除vip帐户金额
			if ($amount!=null) {
				Vip::model()->updateBalance($vip->id, $amount);
			}
		}
	}
    /**
     * 生成结账单 迁库后优化该方法将以前的连表查询拆分
     * @param $settle_date 帐期  demo:20141218
     * author wq
     * php yiic.php order Settle --settle_date=2013-07
     */
    public function actionSettle($settle_date = 0) {
        if(empty($settle_date)){
            $settle_date = date('Y-m', strtotime("-1 month"));
        }else{
            $settle_date = $settle_date;
        }
        $date=new DateTime($settle_date);
        $table_name = 't_employee_account_'.$date->format('Ym');

        FinanceUtils::sendFinanceAlarm('开始生成结账单', '生成的账单日期为:'.$settle_date);//job开始运行发送邮件提醒
        $sql='delete from t_employee_account_settle where settle_date=:settle_date;';
        Yii::app()->db_finance->createCommand($sql)->execute(array(
            ':settle_date'=>$settle_date
        ));


        //-------------归档数据优化----------
        $eSql = 'SELECT ea.user,
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
				sum(cast)-sum(if(type=0,cast,0))  as total,left(from_unixtime(ea.created),7) settle_date
                FROM '. $table_name.' ea
                 GROUP BY ea.user ';
        $command2 = Yii::app()->db_finance->createCommand($eSql);
        $employeeInfos = $command2 -> queryAll();//查询出月份表里的数据

        $_settleInfos = array();//根据user查询司机的name和city_id信息保存于$_settleInfos中
        $driver_sql = "SELECT user,city_id,name FROM {{driver}} WHERE user=:user";
        $command1 = Yii::app()->db->createCommand($driver_sql);
        foreach($employeeInfos as $_employee){
            try{
                $_user = $_employee['user'];
                $driverInfo =$command1 ->bindParam(":user", $_user)->queryAll();
                if($driverInfo){
                    $_employee['name'] = $driverInfo[0]['name'];
                    $_employee['city_id'] = $driverInfo[0]['city_id'];
                    $_settleInfos[] = $_employee;
                }
            }catch (Exception $e){
                FinanceUtils::sendFinanceAlarm('生成账单出现数据异常', '司机工号:'.$_user.'出现异常');
            }
        }
        echo '---insert employee_account_settle COUNT:'.count($_settleInfos)."-----\n";
        $insert_sql = "INSERT INTO {{employee_account_settle}}(user,city_id,name,t0,t1,t2,t3,t4,t5,t6,t7,t8,t9,t10,total,settle_date) VALUES(:user,:city_id,:name,:t0,:t1,:t2,:t3,:t4,:t5,:t6,:t7,:t8,:t9,:t10,:total,:settle_date)";
        $command2 = Yii::app()->db_finance->createCommand($insert_sql);
        $num = 0;
        for($i = 0;$i < count($_settleInfos);$i++){
            $settle = $_settleInfos[$i];
            try{
                $command2 ->bindParam(":user", $settle['user']);
                $command2 ->bindParam(":city_id", $settle['city_id']);
                $command2 ->bindParam(":name", $settle['name']);
                $command2 ->bindParam(":t0", $settle['t0']);
                $command2 ->bindParam(":t1", $settle['t1']);
                $command2 ->bindParam(":t2", $settle['t2']);
                $command2 ->bindParam(":t3", $settle['t3']);
                $command2 ->bindParam(":t4", $settle['t4']);
                $command2 ->bindParam(":t5", $settle['t5']);
                $command2 ->bindParam(":t6", $settle['t6']);
                $command2 ->bindParam(":t7", $settle['t7']);
                $command2 ->bindParam(":t8", $settle['t8']);
                $command2 ->bindParam(":t9", $settle['t9']);
                $command2 ->bindParam(":t10", $settle['t10']);
                $command2 ->bindParam(":total", $settle['total']);
                $command2 ->bindParam(":settle_date", $settle['settle_date']);
                $row = $command2->execute();
                if($row){
                    $num++;
                }
            }catch (Exception $e){
                FinanceUtils::sendFinanceAlarm('生成账单插入employee_account_settle表出现异常', '司机工号:'.$settle['user'].'插入出现异常');
            }


        }
        FinanceUtils::sendFinanceAlarm('生成结账单结束.',  '需要插入：'.count($_settleInfos).'条数据，成功插入:'.$num.'条数据');//job结束运行发送邮件提醒
        echo '---insert success count:'.$num."---\n";
        echo '-TABLE:'.$table_name."\n";
    }


    /**
     * 生成结账单
     * @param $settle_date 帐期  demo:2012-08
     * author mengtianxue
     * php yiic.php order Settle --settle_date=2013-07
     */
    public function actionSettle2($settle_date = 0) {
        if(empty($settle_date)){
            $settle_date = date('Y-m', strtotime("-1 month"));
        }else{
            $settle_date = $settle_date;
        }
		$date=new DateTime($settle_date);
        $table_name = 't_employee_account_'.$date->format('Ym');

		$sql='delete from t_employee_account_settle where settle_date=:settle_date;';
		Yii::app()->db_finance->createCommand($sql)->execute(array(
				':settle_date'=>$settle_date
		));

		//归档数据
		$sql='insert into t_employee_account_settle
				select null,ea.user,d.city_id,d.name,
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
				sum(cast)-sum(if(type=0,cast,0))  as total,left(from_unixtime(ea.created),7) settle_date
				from '. $table_name.' ea, t_driver d
				WHERE ea.user = d.user
				group by ea.user';
		Yii::app()->db_finance->createCommand($sql)->execute();
        echo $table_name."\n";

	}

	/**
	 *
	 * 延迟报单扣除罚款,目前只有北京、上海罚款，从2014年开始全国开始罚款
	 * @param 日期 $date
     * php yiic.php order Fakuan
	 */
	public function actionFakuan($date=null) {
		if ($date) {
			$begin_time=date('Y-m-d 00:00:00', strtotime($date)-86400);
			$end_time=date('Y-m-d 00:00:00', strtotime($date));
		} else {
			$begin_time=date('Y-m-d 00:00:00', time()-86400);
			$end_time=date('Y-m-d 00:00:00', time());
		}


		$where_str = " and city_id in (1,3) ";
		$new_year = strtotime('2014-01-01 23:59:59');
		$now_date = time();
		if( $now_date > $new_year ){
			$where_str = "";
		}

		$sql="SELECT driver_id,CONCAT('延迟报单', GROUP_CONCAT(order_id)) as comment FROM t_order WHERE status = 0 {$where_str} and booking_time BETWEEN UNIX_TIMESTAMP('{$begin_time}') AND UNIX_TIMESTAMP('{$end_time}') group by driver_id";

		$fakuan_list = Order::getDbReadonlyConnection()->createCommand($sql)->queryAll();

        foreach($fakuan_list as $list){
            $driver_info = DriverStatus::model()->get($list['driver_id']);
            if($driver_info) {
                if($driver_info->mark == 0) {
                    $params = array(
                        'type' => EmployeeAccount::TYPE_FORFEIT,
                        'user' => $list['driver_id'],
                        'city_id' => $driver_info->city_id,
                        'channel' => 4,
                        'operator' => '系统扣费',
                        'order_id' => 0,
                        'comment' => $list['comment'], //$bonus['bonus'].$bonus['parity_bit'],
                        'cast' => 30,
                        'order_date' => time()
                    );
                    OrderSettlement::model()->insertAccount($params);
                    EdjLog::info($params['comment']."|".$list['driver_id']);
                }
            }
            else {
                EdjLog::info("get driver from redis error|".$list['driver_id']);
            }
        }
	}

	/**
	 *
	 * 延迟报单短信通知
	 * @param 日期 $date
     * php yiic.php order DriverNotifiy
	 */
	public function actionDriverNotifiy($date=null) {
            $begin_time=date('Y-m-d 00:00:00', time()-86400);
	    $end_time=date('Y-m-d 00:00:00', time());

            $params=array(
	        ':begin_time' => $begin_time,
	        ':end_time'   => $end_time
            );

            $message='%s师傅，截止目前为止，您还有%s单未报，请选择销单或报单，务必在15点前完成。如未查询到未报单的订单，请忽略此消息。';

	    $driver_orders = Order::getDbReadonlyConnection()->createCommand()
                ->select('driver_id, count(order_id) c')
                ->from('t_order')
                ->where('status = 0 and booking_time BETWEEN UNIX_TIMESTAMP(:begin_time) AND UNIX_TIMESTAMP(:end_time) ', $params)
                ->group('driver_id')
                ->queryAll();

            foreach($driver_orders as $do) {
                $driver_info = DriverStatus::model()->get($do['driver_id']);
                if($driver_info) {
                    if($driver_info->mark == 0) {
                        $i_phone = $driver_info->info['ext_phone']
                            ? $driver_info->info['ext_phone'] : $driver_info->phone;
                        $i_message = sprintf($message, $do['driver_id'], $do['c']);
                        EdjLog::info("未报单|".$do['driver_id'].'|'.$do['c']);
	                Sms::SendSMS($i_phone, $i_message);
                    }
		}
                else {
                    EdjLog::info("get driver from redis error|".$list['driver_id']);
                }
            }
	}

	/**
	 *
	 * 修改bug
	 * @param 日期 $date
	 */
	public function actionAccountRepair($date=null) {
		$order = new Order();
		if ($date) {
			$end_time=date('Y-m-d 15:00:00', strtotime($date));
			$begin_time=date('Y-m-d 15:00:00', strtotime($date)-86400);
		} else {
			$end_time=date('Y-m-d 15:00:00', time());
			$begin_time=date('Y-m-d 15:00:00', time()-86400);
		}


		$pagesize=500;
		$offset=0;

		while(true) {
			$command=Yii::app()->db->createCommand();
			$command->select('o.order_id,order_number,vipcard,phone,call_time,booking_time,location_start,location_end,distance,driver_id,city_id,income,price,l.created')->from('t_order o')->join('t_order_log l', 'o.order_id = l.order_id')->where('status in(1,4) and l.created between unix_timestamp(:begin_time) and unix_timestamp(:end_time) and l.operator =""', array(
					':begin_time'=>$begin_time,
					':end_time'=>$end_time
			))->group('order_id')->limit($pagesize, $offset);

			$rows=$command->queryAll();
			if ($rows) {
				foreach($rows as $item) {

					$order_params=array(
							':order_id'=>$item['order_id']
					);

					if (EmployeeAccount::model()->exists('order_id=:order_id', $order_params)) {
						EmployeeAccount::model()->deleteAll('order_id=:order_id', $order_params);
						echo 'delete '.$item['order_id']."\n";
					}

					echo $item['order_id']."\n";
					//每订单扣除信息费
					self::order_infomatin_fee($item);
					//扣除每单保险费(仅北京司机)，2013-04-23 日开始全国都收取保险费
					self::order_insurance($item);

					if ($item['vipcard']) {
						self::order_vip($item);
					} else {
						//司机优惠券返点
						//self::order_bonus($item, true);
						self::order_normal($item);
					}
				}
				$offset+=$pagesize;
			} else {
				break;
			}
		}
	}


	/**
	 *
	 * 生成每日15点前完成报单的订单对账单中优惠券返现
	 * @param 日期 $date
	 */
	public function actionAccountBonusRepair($date=null) {
		if ($date) {
			$end_time=date('Y-m-d 15:00:00', strtotime($date));
			$begin_time=date('Y-m-d 15:00:00', strtotime($date)-86400);
		} else {
			$end_time=date('Y-m-d 15:00:00', time());
			$begin_time=date('Y-m-d 15:00:00', time()-86400);
		}

		$pagesize=50;
		$offset=0;

		while(true) {
			$command=Yii::app()->db->createCommand();
			$command->select('o.order_id,order_number,vipcard,phone,call_time,booking_time,location_start,location_end,distance,driver_id,income,source,l.created')->from('t_order o')->join('t_order_log l', 'o.order_id = l.order_id')->where('status in(1,4) and l.created between unix_timestamp(:begin_time) and unix_timestamp(:end_time) and l.operator = ""', array(
					':begin_time'=>$begin_time,
					':end_time'=>$end_time
			))->group('order_id')->limit($pagesize, $offset);

			$rows=$command->queryAll();

			if ($rows) {
				foreach($rows as $item) {
					echo $item['order_id']."\n";

					if (!$item['vipcard']) {
						self::order_bonus($item, true);
					}
				}
				$offset+=$pagesize;
			} else {
				break;
			}
		}
	}

	//修改t_order_log 的operator
	public function actionOrderLogOperator($date = NULL){
		if ($date) {
			$end_time=date('Y-m-d 15:00:00', strtotime($date));
			$begin_time=date('Y-m-d 15:00:00', strtotime($date)-86400);
		} else {
			$end_time=date('Y-m-d 15:00:00', time());
			$begin_time=date('Y-m-d 15:00:00', time()-86400);
		}
		$command=Yii::app()->db->createCommand();
			$command->select('o.order_id,order_number,vipcard,phone,call_time,booking_time,location_start,location_end,distance,driver_id,city_id,income,price,l.created,l.id')->from('t_order o')->join('t_order_log l', 'o.order_id = l.order_id')->where('status in(1,4) and l.created between unix_timestamp(:begin_time) and unix_timestamp(:end_time) and l.operator =""', array(
					':begin_time'=>$begin_time,
					':end_time'=>$end_time
			))->group('order_id')->limit($pagesize, $offset);

			$rows=$command->queryAll();
			if ($rows) {
				foreach($rows as $item) {
					OrderLog::model()->updateByPk($item['id'], array('operator' => $item['driver_id']));
					echo $item['driver_id']."\n";
				}
			}
	}
     /**
     *
     * 城市补贴活动-司机返款
     * @param 日期 $date
     * php yiic.php order DriverCharge
     * add by zhangtongkai 2014-4-4
     */
    public function actionDriverCharge($date = NULL,$time=7) {
        echo "城市补贴活动-司机返款start"."\n";
        if ($date) {
            $end_time=date('Y-m-d '.$time.':00:00', strtotime($date));
            $begin_time=date('Y-m-d '.$time.':00:00', strtotime($date)-86400);
        } else {
            $end_time=date('Y-m-d '.$time.':00:00', time());
            $begin_time=date('Y-m-d '.$time.':00:00', time()-86400);
        }
        $activity_config = Yii::app()->params['activity'];
        $pagesize=50;
        $offset=0;
        $diver_count=array();$subsidy=0;$arrived=0;
        while(true) {
            $command=Order::getDbReadonlyConnection()->createCommand();
            $command->select('o.order_id,e.driver_subsidy_money,e.customer_subsidy_money,o.driver_id,o.phone,o.driver_phone,o.price,o.city_id,o.created')
            ->from('t_order o')->join('t_order_ext e', 'o.order_id = e.order_id')
            ->where('(e.driver_subsidy_money>0 or e.customer_subsidy_money>0 ) and o.created between unix_timestamp(:begin_time) and unix_timestamp(:end_time)', array(
                    ':begin_time'=>$begin_time,
                    ':end_time'=>$end_time
            ))->group('order_id')->limit($pagesize, $offset);

            $rows=$command->queryAll();
            if (!empty($rows) && count($rows)>0) {
                foreach($rows as $item) {
                    $city_id=$item['city_id'];
                    $subsidy = isset($activity_config[$city_id]['subsidy']) ? $activity_config[$city_id]['subsidy'] : array();
                    //城市没有补贴活动
                    if(!empty($subsidy)) {
                        //城市补贴活动已开启
                        if(isset($subsidy['turn_on']) && $subsidy['turn_on']) {
                            if(!empty($item['created'])){
                                $table_date=date('Ym', $item['created']);
                            }else{
                            	$table_date=date('Ym', time());
                            }
                            EmployeeAccount::$table_name = $table_date;
                            if($item['customer_subsidy_money']>0 && $item['price']>0){
                                $checked = EmployeeAccount::model()->find('order_id=:order_id and user=:user and city_id=:city_id and channel=:channel and cast=:cast ',array(':order_id' => $item['order_id'],':user' => $item['driver_id'],':city_id' => $item['city_id'],':channel' => 15,':cast' => $item['price']));
                                if(!$checked){
                                    $settle = array('comment' => '用户免单补偿', 'order_id'=>$item['order_id'],'user' => $item['driver_id'], 'city_id'=>$item['city_id'], 'channel'=>15, 'cast' =>$item['price']);
                                    OrderSettlement::model()->driverRecharge($settle, true);
                                    //用户免单补贴
                                    $subsidy=$item['price'];
                                    if(isset($diver_count[$item['driver_id']]['subsidy'])){
                                        $diver_count[$item['driver_id']]['subsidy']+=$subsidy;
                                    }else{
                                    	$diver_count[$item['driver_id']]['subsidy']=$subsidy;
                                    }
                                    $diver_count[$item['driver_id']]['phone']=$item['driver_phone'];
                                }
                            }
                            if($item['driver_subsidy_money']>0){
                                $checked = EmployeeAccount::model()->find('order_id=:order_id and user=:user and city_id=:city_id and channel=:channel and cast=:cast ',array(':order_id' => $item['order_id'],':user' => $item['driver_id'],':city_id' => $item['city_id'],':channel' => 15,':cast' => $item['driver_subsidy_money']));
                                if(!$checked){
                                    $settle = array('comment' => '准时到达奖励', 'order_id'=>$item['order_id'],'user' => $item['driver_id'], 'city_id'=>$item['city_id'], 'channel'=>15, 'cast' =>$item['driver_subsidy_money']);
                                    OrderSettlement::model()->driverRecharge($settle, true);
                                    //准时到达奖励
                                    $arrived=$item['driver_subsidy_money'];
                                    if(isset($diver_count[$item['driver_id']]['arrived'])){
                                        $diver_count[$item['driver_id']]['arrived']+=$arrived;
                                    }else{
                                    	$diver_count[$item['driver_id']]['arrived']=$arrived;
                                    }
                                    $diver_count[$item['driver_id']]['phone']=$item['driver_phone'];
                                }
                            }
                        }
                    }
                    //昨天准时到达奖励共X元,用户免单补贴共X元,您的信息费帐户共充入X元。
                }
                $offset+=$pagesize;
            } else {
                break;
            }
        }
        if(isset($diver_count)){
            foreach($diver_count as $k=>$v){
                if(isset($v['phone'])&&!empty($v['phone'])){
                    $arrived_txt='';$subsidy_txt='';$arrived_sum=0;$subsidy_sum=0;
                    if(isset($v['arrived']) and $v['arrived']>0){
                        $arrived_txt='准时到达奖励共'.$v['arrived'].'元,';
                        $arrived_sum=$v['arrived'];
                    }
                    if(isset($v['subsidy']) and $v['subsidy']>0){
                        $subsidy_txt='用户免单补贴共'.$v['subsidy'].'元,';
                        $subsidy_sum=$v['subsidy'];
                    }
                    $sum=$arrived_sum+$subsidy_sum;
                    if($sum>0){
                	$sms_message = $k.'师傅' . '您昨天'.$arrived_txt.$subsidy_txt.'信息费账户共充入'.$sum.'元。';
                    echo $sms_message."\n";
                    Sms::SendForPayment($v['phone'], $sms_message);
                    }
                }

            }
        }
        echo "城市补贴活动-司机返款end"."\n";
    }

	
	public function actionReloadSingleUser($phone){
		echo "user phone:".$phone."\n";
		$before=ROrderHistory::model()->getOrderList($phone);
		var_dump($before);
		
		ROrderHistory::model()->loadCustomerOrder($phone, '',true);
	
		$after=OrderHistory::model()->getOrderList($phone);
		var_dump($after);
		
	}

	
			

		
	/**
        *每周执行一次reload所有用户redis中的订单
        *
        **/
	public function actionReloadUserROrder($maxOrderId=0) {
                $starttime=date('y-m-d H:i:s',time());
                EdjLog::info("start to ReloadUserROrder==".$starttime);
                echo "start to ReloadUserROrder==".$starttime."\n";
                $this->reloadUserOrderList($maxOrderId);
                $endtime=date('y-m-d H:i:s',time());
                EdjLog::info("finish to ReloadUserROrder==".$endtime);
                echo "finish to ReloadUserROrder==".$endtime."\n";
        }


	public function actionReloadPreDayOrder($day=''){
		
		
		echo "day:".$day."\n";
		if(empty($day)) {
			$today=date("Y-m-d 00:00:00",strtotime("0 day"));
			$yesterday=$day=date("Y-m-d 00:00:00",strtotime("-1 day"));
		}else{
			$today=$day;
			$yesterday=$day=date("Y-m-d 00:00;00",strtotime($day)-24*60*60);
		}

		
		$sql="select distinct phone from t_order where status=2 and booking_time>UNIX_TIMESTAMP('".$yesterday."') and booking_time<UNIX_TIMESTAMP('".$today."')";

		echo "sql:".$sql."\n";
	        echo "today:".$today."\n";		
	        echo "yesterday:".$yesterday."\n";		
		
		
		$phoneArray=Order::getDbReadonlyConnection()->createCommand($sql)->queryAll();
		$i=0;	
		if(!empty($phoneArray)){
			foreach($phoneArray as $item){
				$i++;
				if($i % 1000 == 0) {
					echo "================".$i."\n";
				}
				$phone = $item['phone'];
				ROrderHistory::model()->loadCustomerOrder($phone, '',true);
			}
		}
		echo "finish to reload order,day:".$today."\n";
			
	}

	/**
	 * 司机即时到达活动补贴
	 *
	 * @param null $date
	 * @param int $runHour
	 */
	public function actionDriverSubsidy($date = null, $runHour = 7){
		echo "城市补贴活动-司机返款start"."\n";
		$activityCityList = ArriveInTimeActivity::model()->getCitiesForSettlement();
		if(empty($activityCityList)){
			echo 'no city open this activity';
			echo "\n";
			return;
		}
		if ($date) {
			$timeEnd	= strtotime($date);
			$timeStart	= strtotime($date) - 86400;//倒退一天
		} else {
			$timeEnd	= time();
			$timeStart	= time() - 86400;//倒退一天
		}
		$cityStr = implode(',', $activityCityList);
		$where = ' created between :timeStart and :timeEnd and status in (1, 4) and city_id in ('.$cityStr.')';
		$params = array(
			':timeStart'	=> $timeStart,
			':timeEnd'		=> $timeEnd,
		);
		$orderList = Order::getDbReadonlyConnection()->createCommand()
			->select('*')
			->from('{{order}}')
			->where($where, $params)
			->queryAll();
		if(empty($orderList)){
			echo sprintf('find no order from %s to %s', date('Y-m-d H:i:s', $timeStart), date('Y-m-d H:i:s', $timeEnd));
		}
		$unActiveList = array();
		$failList = array();
		$subsidyList = array();
		foreach($orderList as $order){
			$orderId 	= $order['order_id'];
			$status = OrderSettlement::model()->isArriveInTimeActivityTurnOn($order);
			if(!$status){
				$unActiveList[] = $orderId;
				continue;
			}
			$price 		= $order['price'];
			$income 	= $order['income'];
			$driverId	= $order['driver_id'];
			$cityId		= $order['city_id'];
			$orderExt = OrderExt::model()->getPrimary($orderId);
			if($orderExt){
				$checked = OrderSettlement::model()->checkOrderSettleByOrderID($order);
				if(!$checked){
					continue;
				}
				//是否已经补贴
				$employAccount =$this->getEmployeeAccountByOrder($order);
				if($employAccount){
					$subsidyList[] = $orderId;
					continue;
				}
				$customerSubsidyMoney = OrderSettlement::model()->check_order_ext(1, $order, $orderExt);
				$driverSubsidyMoney =  OrderSettlement::model()->check_order_ext(2, $order, $orderExt);
				$ret = false;
				//如果客户受补贴,同时是现金订单,则返回给司机付的现金部分
				if($customerSubsidyMoney > 0 && $price > 0){
					$amount = $price;
					$comment = '用户免单补偿';
					$ret = $this->rechargeDriver($comment, $orderId, $driverId, $cityId, $amount);
				}
				//司机应该享受的补贴
				if($driverSubsidyMoney > 0){
					$amount 	= $driverSubsidyMoney;
					$comment 	= '准时到达奖励';
					$ret = $this->rechargeDriver($comment, $orderId, $driverId, $cityId, $amount);
				}
				if(!$ret){
					EdjLog::info(sprintf('orderId:%s|customerSubsidyMoney:%s |driverSubsidyMoney:%s|income:%s', $orderId, $customerSubsidyMoney, $driverSubsidyMoney, $income));
					$failList[] = $orderId;
				}else{
					$subsidyList[] = $orderId;
				}
			}else{
				$unActiveList[] = $orderId;
			}
		}
		$format = 'from %s to %s , total find order count is %d, unActive count is %d, fail count is %d, subsidy count is %d';
		EdjLog::info(sprintf($format, date('Y-m-d H:i:s', $timeStart),
			date('Y-m-d H:i:s', $timeEnd), count($orderList), count($unActiveList), count($failList), count($subsidyList)));
		EdjLog::info('unActive list '.json_encode($unActiveList). ' fail list '.json_encode($failList). 'success list '.json_encode($subsidyList));
		EdjLog::info('=================end'.date('Y-m-d').'================');
	}

	
        private function reloadUserOrderList($maxOrderId=0){

		$ctime=date('y-m-d H:i:s',time());
                echo "=====maxOrderId:".$maxOrderId." and ctime is:".$ctime."\n";
		
                $phoneArray=$this->getCustomerMain($maxOrderId);
                if(!empty($phoneArray) && count($phoneArray)> 0 ) {
                        //var_dump($phoneArray);
                        foreach($phoneArray as $customer) {
                                if($customer['id'] >$maxOrderId ) {
                                        $maxOrderId=$customer['id'];
                                }//if
                                $phone=$customer['phone'];
                                if(isset($phone)){
                                        $redisCount=ROrderHistory::model()->getOrderCount($phone);
                                        $dbCount=$this->getCustomerMainCount($phone);
                                        //echo "\n |redisCount:".$redisCount."|dbCount:".$dbCount."\n";
                                        if($redisCount < $dbCount) {
                                                //echo "======= \n";
                                                ROrderHistory::model()->loadCustomerOrder($phone, '',true);
                                        }//if
                                }//if
                        }//foreach
                }else{
                        exit;
                }
                //echo "maxOrderId:".$maxOrderId."\n";
                $this->reloadUserOrderList($maxOrderId);
        }	



        private function getCustomerMain($startId=0) {
                $sql='select id,phone from t_customer_main where id>'.$startId.' order by id asc limit 1000';
                $phoneArray=Yii::app()->db_readonly->createCommand($sql)->queryAll();
                return $phoneArray;             
        }

        private function getCustomerMainCount($phone) {
                $sql="select count(1) as count from t_order where phone='".$phone."' and status=1";
                $orderCount=Order::getDbReadonlyConnection()->createCommand($sql)->queryAll();
                return $orderCount[0]['count'];
        }

	/**
	 *
	 *
	 * @param $comment
	 * @param $orderId
	 * @param $driverId
	 * @param $cityId
	 * @param $amount
	 */
	private function rechargeDriver($comment, $orderId, $driverId, $cityId, $amount){
		$settle = array(
			'comment' 	=> $comment,
			'order_id'	=> $orderId,
			'user' 		=> $driverId,
			'city_id'	=> $cityId,
			'channel'	=> 15,
			'cast' 		=> $amount,
		);
		return OrderSettlement::model()->driverRecharge($settle, true);
	}


	private   function getEmployeeAccountByOrder($order){
		$orderId = $order['order_id'];
		$orderTime = $order['created'];
		$currentMonth = date('Ym', $orderTime);//订单当月
		$nextMonth = date('Ym',strtotime('+1 month',$orderTime));//订单下一个月
		$currentList = $this->getEmployeeAccountListByOrderIdAndMonth($orderId,$currentMonth);
		$nextList = $this->getEmployeeAccountListByOrderIdAndMonth($orderId,$nextMonth);
		if(empty($currentList)){
			return $nextList;
		}
		if(empty($nextList)){
			return $currentList;
		}
		return array_merge($currentList, $nextList);
	}

	/**
	 * 根据月份和订单id返回流水表
	 *
	 * @param $orderId
	 * @param $month
	 */
	private  function getEmployeeAccountListByOrderIdAndMonth($orderId, $month){
		$where = 'order_id = :orderId and (comment like "%用户免单补偿%" or comment like "%准时到达奖励%") ';
		$params = array(
			':orderId'  => $orderId,
		);
		return Yii::app()->db_finance->createCommand()
			->select('*')
			->from('t_employee_account_'.$month)
			->where($where,$params)
			->order('id asc')
			->queryAll();
	}

	
}
