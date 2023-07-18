<?php

/**
 * 用户写一些小的command,免得到处写了
 *
 * User: tuan
 * Date: 14-9-1
 * Time: 10:20
 */
Yii::import('application.models.pay.*');
Yii::import('application.models.schema.pay.*');
Yii::import('application.vendors.upmp.*');
class CrumbsCommand extends LoggerExtCommand
{

	public function actionCommonSettle($orderId, $debug = 1)
	{
		//1.get order type
		//2.settle
		$order = Order::model()->getOrderById($orderId);
		$orderSnapshoot = OrderSnapshoot::model()->getLatestSnapshoot($orderId);
		$params = array();
		if ($orderSnapshoot) {
			$params = $orderSnapshoot->attributes;
		}
		$financeOrderType = FinanceUtils::getOrderType($order);
		if ($debug) {
			echo $orderId . "-----" . $financeOrderType . "------ \n";
			return;
		}
		if(FinanceConstants::ORDER_TYPE_UNIT == $financeOrderType){
			echo "wash order --- end"."\n";
			return;
		}
		$tableName = date('Ym', $order['created']);
		EmployeeAccount::$table_name = $tableName;
		$criteria = new CDbCriteria();
		$criteria->compare('order_id', $orderId);
		$list = EmployeeAccount::model()->findAll($criteria);
		$tableName = date('Ym', time());
		EmployeeAccount::$table_name = $tableName;
		$list2 = EmployeeAccount::model()->findAll($criteria);
		echo 'size 1 ---- '.count($list).'size 2 ----'.count($list2);
		echo "\n";
		if (empty($list) && empty($list2)) {
			if (FinanceUtils::isParamSettle($financeOrderType)) {
                $params['subsidy_hour'] = 1;
                OrderSettlement::model()->submitSettle($orderId, $params);
			} else {
				if (FinanceUtils::isRemoteOrder($order, OrderExt::model()->getPrimary($orderId))) {
					OrderSettlement::model()->remoteOrderSettle($orderId);
				} else {
					OrderSettlement::model()->orderSettle($orderId);
				}
			}
		}
	}

	public function actionCreateNewVipCard($startSn, $count, $money, $confirm = 0){
		for($i = 0; $i < $count; ++$i){
			$params = array();
			$id= $startSn + $i;
			if(VipCard::model()->findByPk($id)){
				echo 'exist --- '.$id."\n";
				continue;
			}
			$params['id'] = $id;
			$params['pass'] = rand(100000, 999999);
			$params['money'] = $money;
			$params['status'] = 1;
			$params['saled_by'] = '系统默认激活';
			$params['atime'] = time();
			$vip_card = new VipCard;
			$vip_card->attributes = $params;
			if($confirm){
				$vip_card->insert();
				$format = 'insert ok : %s ---- %s';
				EdjLog::info(sprintf($format, $id, $params['pass']));
			}
		}
	}

	/**
	 * 检查司机银联充值情况
	 * Crumbs CheckDriverUpmp --driverId='SH78819' --dateStart='2014-11-10' --dateEnd='2014-11-13'
	 * @param $driverId
	 * @param $dateStart
	 * @param $dateEnd
	 */
	public function actionCheckDriverUpmp($driverId, $dateStart, $dateEnd){
		$criteria = new CDbCriteria();
		$criteria->compare('user_id', $driverId);

		$criteria->addBetweenCondition('trans_time', date('YmdHis', strtotime($dateStart)), date('YmdHis',strtotime($dateEnd)));
		echo 'stat --- '.$dateStart.' --- end ---'.$dateEnd;
		$list = CarPayDriverOrder::model()->findAll($criteria);
		$count = count($list);
		echo "\n"." upmp count".$count;
		echo "\n";
		if(0 < $count){
			$format = '司机:%s ---- 充值单号: %s, ---- 银联扣款:%s, ---- 入账:%s ---- 对账文件:%s';
			$upmpPay = new UpmpPay();
			foreach($list as $payOrder){
				$trans_type = $payOrder->trans_type;    //交易类型
				$order_time = $payOrder->trans_time;   //商户交易开始日期时间
				$order_no   = $payOrder->order_id;     //商户订单号
				$result=$upmpPay->query0($trans_type, $order_time, $order_no);//核对银联是否成功
				$excelOrder = ExcelOrder::model()->findByPk($order_no);
				$excel = 'fail';
				if($excelOrder){
					$excel = 'ok';
				}
				if($result){
					$status = BUpmpPayOrder::model()->checkPaySuccess($order_no, $payOrder, true);
					if($status){
						echo sprintf($format, $driverId, $order_no, 'ok', 'ok', $excel);
						echo "\n";
					}else{
						echo sprintf($format, $driverId, $order_no, 'ok', 'fail', $excel);
						echo "\n";
					}
				}else{
					echo sprintf($format, $driverId, $order_no, 'fail', 'fail', $excel);
					echo "\n";
				}
			}
		}
	}

	public function actionBatchSettle($fileName = '', $debug = 1){
		if (empty($fileName)) {
			return;
		}
		$fileContent = file($fileName);
		foreach ($fileContent as $line => $content) {
			$orderId = trim($content);
			$this->actionCommonSettle($orderId, $debug);
		}
	}


	public function actionSettle($orderId){
		if(empty($orderId)){
			return 0;
		}
		$order  = Order::model()->getOrderById($orderId);
		if($order){
			$tableName = date('Ym', $order['created']);
			EmployeeAccount::$table_name = $tableName;
			$criteria = new CDbCriteria();
			$criteria->compare('order_id', $orderId);
			$list = EmployeeAccount::model()->findAll($criteria);
			if(empty($list)){
				OrderSettlement::model()->orderSettle($orderId);
			}else{
				echo $orderId.'have employee  account list'."\n";
			}
		}
	}

	public function actionBatchDisable($fileName)
	{
		if (empty($fileName)) {
			return;
		}
		$fileContent = file($fileName);
		foreach ($fileContent as $line => $content) {
			$bonusSn = trim($content);
			$this->actionBonusDisable($bonusSn);
		}
	}

	public function actionBonusDisable($bonusSn)
	{
		$now = date('Y-m-d H:i:s');
		//update t_bonus_library
		$bonusLibraryCriteria = new CDbCriteria();
		$bonusLibraryCriteria->compare('bonus_sn', $bonusSn);
		$bonusLibrary = BonusLibrary::model()->find($bonusLibraryCriteria);
		echo json_encode($bonusLibrary->attributes);
		echo "\n";
		#echo serialize($bonusLibrary->getDbConnection());
		if ($bonusLibrary) {
			try {
				$bonusLibrary->effective_date = $now;
				$bonusLibrary->binding_deadline = $now;
				$bonusLibrary->end_date = $now;
				if ($bonusLibrary->save()) {
					echo "library update ok! $bonusSn" . "\n";
				} else {
					echo json_encode($bonusLibrary->getErrors());
				}
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
		//update t_customer_bonus
		$bonusCriteria = new CDbCriteria();
		$bonusCriteria->compare('bonus_sn', $bonusSn);
		$bonusCriteria->addCondition('order_id <= 0 or used = 0');
		$bindList = CustomerBonus::model()->findAll($bonusCriteria);
		if ($bindList) {
			try {
				foreach ($bindList as $bonus) {
					$bonus->end_date = $now;
					$bonus->used = time();
					if ($bonus->save()) {
						echo "customer bonus update ok! $bonusSn" . "\n";
					} else {
						echo json_encode($bonusLibrary->getErrors());
					}
				}
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
	}
	/**
	 * 生成固定优惠码
	 *
	 * @param $bonusSn
	 */
	public function actionReInsertFixBonus($bonusSn){
		$bonusLibrary = BonusLibrary::model()->getBonusByBonus_sn($bonusSn, 1);
		if($bonusLibrary){
			echo 'this bonus sn is existed';
			return ;
		}
		$criteria = new CDbCriteria();
		$criteria->compare('sn_start', $bonusSn);
		$criteria->compare('sn_end', $bonusSn);
		$model = BonusCode::model()->find($criteria);
		if(!$model){
			echo 'find no bonus code,please check !!';
			return;
		}
		$bonusType = $model['sn_type'];
		if(1 != $bonusType){
			echo '这个方法只支持固定码的优惠券'."\n";
			return;
		}
		$bonusCodeId  = $model['id'];
		$couponArr = $codeRules = array();
		$couponArr['bonus_id'] = $bonusCodeId;
		$couponArr['money'] = $model['money']; //优惠券金额
		$couponArr['sn_type'] = $model['sn_type']; //类型
		$couponArr['channel'] = $model['channel']; //渠道
		$couponArr['effective_date'] = $model['effective_date']; //生效日期
		$couponArr['binding_deadline'] = $model['binding_deadline']; //绑定截止日期
		if (!empty($model['end_date'])) {
			$couponArr['end_date'] = $model['end_date'];
		}
		$couponArr['bonus_sn'] = $model['issued']; //固定优惠码
		BonusLibrary::model()->doFixedCouponInsert($couponArr);
	}


	/**
	 * 根据{{driver}}表里面的user更新对应新客码{{bonus_library}}的城市id,
	 *
	 * @param int $offset
	 * @param int $limit
	 */
	public function actionUpdateCityIdInBalanceLibrary($offset = 0, $limit = 100)
	{
		$format = 'driver:%s --- driver_city:%s --- bonus_code:%s --- bonus_city:%s --- update_status:%s';
		for ($offset; $offset < 39000; $offset += $limit) {
			$driverList = Yii::app()->db_readonly->createCommand()->select('user,city_id')
				->from('{{driver}}')
				->where('id > 0')
				->order('id asc')
				->offset($offset)
				->limit($limit)
				->queryAll();
			foreach ($driverList as $item) {
				$driverId = $item['user'];
				$driverCityId = $item['city_id'];
				if (empty($driverId)) {
					continue;
				}
				//get new customer bonus sn
				$code = substr($driverId, 0, 2);
				$code_num = substr($driverId, 2);

				$bonusCity = Dict::items('bonus_city');
				$bonusCode = array_flip($bonusCity);
				$city = $bonusCode[$code];
				$bonusSn = $city . $code_num;
				$checkedBonus = $this->getBonusInfo($bonusSn);
				if ($checkedBonus) {
					$bonusCity = $checkedBonus->city_id;
					if ($bonusCity > 0) {
						$msg = sprintf($format, $driverId, $driverCityId, $bonusSn, $city, 'exist' . $bonusCity);
						echo $msg . "\n";
					} else {
						$checkedBonus->city_id = $city;
						$status = $checkedBonus->update();
						if ($status > 0) {
							$msg = sprintf($format, $driverId, $driverCityId, $bonusSn, $city, 'success');
							echo $msg . "\n";
						} else {
							$msg = sprintf($format, $driverId, $driverCityId, $bonusSn, $city, 'fail');
							echo $msg . "\n";
						}
					}
				}
			}
			echo 'offset --- '.$offset. "\n";
			sleep(1);
		}


	}

	/**
	 * 减少用户余额
	 *
	 * @param $phone
	 * @param $ok
	 * @param int $check
	 */
	public function actionUserWithdrawMoney($phone, $check = 0, $ok = 0){
		if(empty($phone)){
			echo 'phone cannot be null'."\n";
			echo 'halted!!!'."\n";
			return;
		}
		$wanted = $check;
		$format = '欲转出客户%s金额%s,客户实际余额%s,实际减少为%s,客户账户余额为%s';
		$customerInfo=CustomerMain::model()->forceGetCustomerInfo($phone);
		$id = $customerInfo->id;
		$user_account = BCustomers::model()->getAccount(array('user_id' => $id));
		$balance = 0;
    	if($user_account['code'] === 0){
			$user_account_data = $user_account['data'];
			$balance = $user_account_data->amount;
		}
		if($balance == 0){
			echo 'have no balance '."\n";
			return;
		}
		if($check > $balance){
			$check = $balance;
		}
		if($check <= 0){
			$check = $balance;
		}
		echo sprintf($format,$customerInfo->name,$wanted,$balance,$check,$balance-$check);
		echo "\n";
		if(9494 != $ok){
			return;
		}
		//更新用户余额
		$attributes = array(
			'user_id' => $id,
			'trans_order_id' => 0,
			'trans_type' => 11,
			'amount' => $check * -1,
			'balance' => $balance -  $check,
			'source' => 11,
			'remark' => "客户提现,刘团望根据丁润华邮件操作",
		);
		$add_trans = BCustomers::model()->addCustomerTrade($attributes);
		if ($add_trans['code'] === 0) {
			$customerParams = array(
				'user_id' => $id,
				'amount' => abs($check) * -1,
			);
			$return = BCustomers::model()->updateAccount($customerParams);
			if($return['code'] == 0){
				EdjLog::info('subtraction user balance ok'.json_encode($attributes));
				$code = 0;
				$message = '减掉 客户余额成功';
			}else{
				EdjLog::info('subtraction user balance fail'.json_encode($attributes));
				$code = 1;
				$message = '减掉 客户余额失败';
			}
			echo $message;
			echo "\n";
		}
		echo 'run end!!!'."\n";

	}

	/**
	 * 根据bonus id 修改 绑定使用时间
	 *
	 * @param $bonusId
	 * @param int $bind
	 * @param $end
	 * @param int $ok
	 */
	public function actionChangeBonusEndTime($bonusId, $bind = 0 ,$end = 0 , $effective = 0, $snType = -1, $ok = 0){

		if(empty($bind) && empty($end) && empty($effective) && (-1 == $snType)){
			return ;
		}
		//update bonus code
		echo '------------------update bonus code -------------------'."\n";
		$bonusCode = BonusCode::model()->getBonusCode($bonusId);
		if(!empty($bind)){
			$bonusCode->binding_deadline = $bind;
		}
		if(!empty($end)){
			$bonusCode->end_date = $end;
		}
		if(!empty($effective)){
			$bonusCode->effective_date = $effective;
		}
		if( -1 != $snType){
			$bonusCode->sn_type =$snType;
		}
		echo 'update bonus_code bonus name is '.$bonusCode->name;
		echo "\n";
		if($ok){
			$ret = $bonusCode->save();
			if($ret){
				echo 'update ok'.'the new model is '.serialize($ret);
			}else{
				echo 'update fail'.'the error is '.json_encode($bonusCode->getErrors());
				return;
			}
		}
		echo "\n";
		//update bonus library
		echo '------------------update bonus library -------------------'."\n";
		$bonusLibraryList = $this->getBonus($bonusId);
		if(!$bonusLibraryList){
			echo 'bonus library is empty';
			return;
		}
		$libraryCount = count($bonusLibraryList);
		$updatedCount = 0 ;
		foreach($bonusLibraryList as $item){
			if(!empty($bind)){
				$item->binding_deadline = $bind;
			}
			if(!empty($end)){
				$item->end_date = $end;
			}
			if(!empty($effective)){
				$item->effective_date = $effective;
			}
			if(-1 != $snType){
				$item->sn_type = $snType;
			}
			if($ok){
				$ret = $item->save();
				if($ret){
					echo 'update ok'.'the new model is '.serialize($ret);
					$updatedCount += 1;
				}else{
					echo 'update fail'.'the error is '.json_encode($item->getErrors());
					continue;
				}
			}
		}
		echo 'need update bonus library count is '.$libraryCount;
		echo ' updated count is '.$updatedCount;
		echo "\n";
		// update customerBonus
		echo '------------------update customerBonus -------------------'."\n";
		$customerBonusList = $this->getCustomerBonusList($bonusId);
		$updatedCount = 0 ;
		$customerBonusCount = count($customerBonusList);
		foreach($customerBonusList as $item){
			if(!empty($end)){
				$item->end_date = $end;
			}
			if(-1 != $snType){
				$item->sn_type = $snType;
			}
			if($ok){
				$ret = $item->save();

				if($ret){
					echo 'update ok'.'the new model is '.serialize($ret);
					$updatedCount += 1;
				}else{
					echo 'update fail'.'the error is '.json_encode($item->getErrors());
					continue;
				}
			}
		}

		echo 'need update customer bonus count is '.$customerBonusCount;
		echo ' updated count is '.$updatedCount;
		echo "\n";

		echo '==============end==============='."\n";
	}

	/**
	 * 更新优惠券发送短信的文案
	 *
	 * @param string $fileName 文件绝对路径名,内容分每行为优惠券id 和短信内容
	 */
	public function actionUpdateBonusMessage($fileName = ''){
		if(empty($fileName)){
			echo 'file name cannot be empty ';
			echo "\n";
			return;
		}
		$fileContent = file($fileName);
		echo 'have ' . count($fileContent) . ' to update';
		echo "\n";
		foreach($fileContent as $line => $content){
			echo $content;
			echo "\n";
			$arr = explode("\040", $content, 2);
			$id  = trim($arr[0]);
			$message = trim($arr[1]);
			if(!is_numeric($id) || empty($message)){
				echo 'line error !!! '. $line + 1;
				echo "\n";
				continue;
			}
			$this->updateBonusMessage($id, $message);
		}

	}

	private function updateBonusMessage($id, $message){
		$bonus = BonusCode::model()->findByPk($id);
		if($bonus){
			$bonus->sms = 'test';
			if($bonus->save()){
				echo 'update ok, id is '.$id;
				echo "\n";
			}else{
				echo 'update error, id is '.$id;
				echo "\n";
				var_dump($bonus->getErrors());
			}
		}
	}

	private  function getBonus($bonus_id)
	{
		$criteria = new CDbCriteria();
		$criteria->compare('bonus_id',$bonus_id,false);
		$codeList = BonusLibrary::model()->findAll($criteria);
		return $codeList;
	}

	private function getCustomerBonusList($bonusTypeId){
		$criteria = new CDbCriteria();
		$criteria->compare('bonus_type_id', $bonusTypeId, false);
		$customerBonusList = CustomerBonus::model()->findAll($criteria);
		return $customerBonusList;
	}

	private function getBonusInfo($bonusSn)
	{
		$criteria = new CDbCriteria;
		$criteria->compare('bonus_sn', $bonusSn, false);
		return BonusLibrary::model()->find($criteria);
	}
} 