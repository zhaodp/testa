<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 14/12/4
 * Time: 10:08
 */

class CheckDriverCommand extends LoggerExtCommand{

	public function actionBatchCharge($fileName, $confirm = 0){
		$content 	= file_get_contents($fileName);
		$contentArr = preg_split('/[\r\n]+/', $content);
		$timeStart = 1417560000;
		$timeEnd = 1417575600;
		foreach($contentArr as $item){
			$arr = preg_split('/[:]+/', $item);
			$driverId = isset($arr[0]) ? trim($arr[0]) : 0;
			$orderId  = isset($arr[1]) ? trim($arr[1]) : 0;
			$this->actionChargeEmployeeAccount($driverId, $orderId, $timeStart, $timeEnd, EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN, EmployeeAccount::TYPE_DRIVER_BONUS_RETUEN, $confirm);
		}
	}

	public function actionChargeEmployeeAccount($driverId, $orderId, $timeStart, $timeEnd, $type = -1, $channel = -1,  $confirm = 0){

		//1.get employee account
		$employeeAccountList = $this->getEmployeeAccountList($driverId, $orderId, $timeStart, $timeEnd ,$type, $channel);
		if(empty($employeeAccountList)){
			echo 'find no employee account, driverId ---- '.$driverId.'---- orderId ----'.$orderId."\n";
			return;
		}
		if($confirm){
			$failCount = $this->updateCounters($employeeAccountList, $driverId);
			echo 'update driver balance ---- fail count --- '.$failCount;
			echo "\n";
		}
		//update counters
	}

	private function updateCounters($employeeAccountList, $driverId){
		$failCount = 0;
		foreach($employeeAccountList as $item){
			$idFilter = array();
			if(in_array($item['id'], $idFilter)){
				return;
			}
			$cast = isset($item['cast']) ? $item['cast'] : 0 ;
			if(0 == $cast){
				continue;
			}
			$attr = array('balance' => $cast);
			try{
				$count = DriverBalance::model()->updateCounters($attr, 'driver_id = :driver_id', array(':driver_id' => $driverId));
				if(0 == $count){
					$failCount += 1;
				}else{
					EmployeeAccount::model()->ReloadDriverAmountRedis($driverId);
				}
			}catch (Exception $e){
				EdjLog::info($e->getMessage());
				$failCount += 1;
			}
		}
		return $failCount;
	}

	private function getEmployeeAccountList($driverId, $orderId, $timeStart, $timeEnd, $type = -1, $channel = -1){
		EmployeeAccount::$table_name = '201412';
		$criteria = new CDbCriteria();
		$criteria->addBetweenCondition('created', $timeStart, $timeEnd);
		$criteria->compare('user', $driverId);
		if(empty($orderId)){
			return array();
		}
		$criteria->compare('order_id', $orderId);
		if(-1 != $type){
			$criteria->compare('type', $type);
		}
		if(-1 != $channel){
			$criteria->compare('channel', $channel);
		}
		return EmployeeAccount::model()->findAll($criteria);
	}

	public function actionChargeFromFile($fileName){
		$content 	= file_get_contents($fileName);
		$contentArr = preg_split('/[\r\n]+/', $content);
		foreach($contentArr as $item){
			$arr = preg_split('/[,]+/', $item);
			$driverId = isset($arr[0]) ? trim($arr[0]) : 0;
			$cast  = isset($arr[1]) ? trim($arr[1]) : 0;
			$cast  = round($cast) * -1;
			echo $driverId." ----".$cast."\n";
//			$this->actionManul($driverId, $cast);
		}
	}

	public function actionManul($driverId, $cast){
		$attr = array('balance' => $cast);
		try{
			$count = 0;
//			$count = DriverBalance::model()->updateCounters($attr, 'driver_id = :driver_id', array(':driver_id' => $driverId));
			if(0 == $count){
				$format = 'fail charge ---- driver:%s , money:%s';
				EdjLog::info(sprintf($format, $driverId, $cast));
			}else{
				$format = 'success charge ---- driver:%s , money:%s';
				EdjLog::info(sprintf($format, $driverId, $cast));
				EmployeeAccount::model()->ReloadDriverAmountRedis($driverId);
			}
		}catch (Exception $e){
			EdjLog::info($e->getMessage());
		}
	}

	public function actionRefund($tableName, $driverId  = 0){
		$list = $this->actionGetDriverOrder($tableName, $driverId);
		$format = 'order_id|%s|driver_id|%s|cast|%s|comment|%s|';
		$newList = $this->filterList($list, $tableName);
		foreach($newList as $item){
			$str = sprintf($format, $item['order_id'], $item['user'], $item['cast'], $item['comment']);
			EdjLog::info($str);
		}
		EdjLog::info('--------------');
		EdjLog::info('new count:'.count($newList).'oldCount:'.count($list));
		$this->reSettle($newList);
	}


	private  function actionGetDriverOrder($tableName, $driverId = 0)
	{
		EmployeeAccount::$table_name = $tableName;
		$criteria = new CDbCriteria();
		$criteria->compare('comment', '准时到达奖励', true);
//		$criteria->compare('comment', '用户免单补偿', true, 'OR');
		$criteria->addBetweenCondition('created', strtotime('2014-12-04 12:01:00'), strtotime('2014-12-04 18:00:00'));
		$criteria->order = 'order_id asc';
		if(!empty($driverId)){
			$criteria->compare('user', $driverId);
		}
		return EmployeeAccount::model()->findAll($criteria);
	}

	private function filterList($list, $tableName){
		$ret = array();
		foreach($list as $item){
			$orderId = $item['order_id'];
			$status = $this->checkOrderDate($orderId, $tableName);
			if(!$status){
				$ret[] = $item;
			}
		}
		return $ret;
	}

	private function checkOrderDate($orderId, $tableName){
		$order =Order::model()->getOrdersById($orderId);
		if($order){
			$orderMonth = date('Ym', $order['created']);
			if($orderMonth == $tableName){
				return true;
			}
		}
		return false;
	}

	private function reSettle($list){
		foreach($list as $item){
			$driverId = $item['user'];
			$orderId  = $item['order_id'];
			$comment  = $item['comment'].'错误充值冲账';
			$cityId   = $item['city_id'];
			$amount   = $item['cast'] * -1;
			try{
				$ret = $this->rechargeDriver($comment, $orderId, $driverId, $cityId, $amount);
				EdjLog::info('result ---- '.serialize($ret));
			}catch (Exception $e){
				EdjLog::info('exception ----'. json_encode($item));
			}
		}
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
		$filter = array(
		);
		if(in_array($driverId, $filter)){
			EdjLog::info('filter ---- '.$driverId.'orderID'.$orderId);
			return;
		}
		$settle = array(
			'comment' 	=> $comment,
			'order_id'	=> $orderId,
			'user' 		=> $driverId,
			'city_id'	=> $cityId,
			'channel'	=> 15,
			'cast' 		=> $amount,
		);
		EdjLog::info('settle ----'. json_encode($settle));
	    #POrderSettlement::model()->driverRecharge($settle, true);
		EmployeeAccount::model()->ReloadDriverAmountRedis($driverId);
	}


	public function actionBatchCheckDriver($cityId){
		$criteria = new CDbCriteria();
		$criteria->compare('city_id', $cityId);
		$criteria->select = 'user';
		$driverList = Driver::model()->findAll($criteria);
		echo 'driver count ---- '.count($driverList);
		echo "\n";
		$timeStart = strtotime('2014-12-03 07:00:00');
		$timeEnd   = time();
		foreach($driverList as $driver ){
			$driverId = $driver['user'];
			$this->actionCheckDriverBalance($driverId, $timeStart, $timeEnd);
		}
	}

	public function actionCheckDriverBalance($driverId, $timeStart, $timeEnd){
		EmployeeAccount::$table_name = '201412';
		$criteria = new CDbCriteria();
		$criteria->compare('user', $driverId);
		$criteria->addBetweenCondition('created', $timeStart, $timeEnd);
		$list = EmployeeAccount::model()->findAll($criteria);
		$oldBalance = $this->getOldBalance($driverId);
		$format = 'driver_id|%s|oldBalance|%s|newBalance|%s|(newBalance - oldBalance)| %s';
		$tmp = $this->checkBalance($oldBalance, $list);
		$newBalance = $this->getNewBalance($driverId);
		if(($newBalance - $tmp) < 1){
			EdjLog::info('success ---- driver_id |'.$driverId);
		}else{
			$log = sprintf('fail ---- '.$format, $driverId, $tmp,$newBalance, ($newBalance - $tmp));
			EdjLog::log($log);
		}

	}

	private function getOldBalance($driverId){
		$sql_format = 'select balance from t_driver_balance where driver_id = "%s"';
		$sql = sprintf($sql_format, $driverId);
		$balance = Yii::app()->db->createCommand($sql)->queryScalar();
		return $balance;
	}

	private function getNewBalance($driverId){
		$sql_format = 'select balance from t_driver_balance where driver_id = "%s"';
		$sql = sprintf($sql_format, $driverId);
		$balance = Yii::app()->db_finance->createCommand($sql)->queryScalar();
		return $balance;
	}

	private function checkBalance($oldBalance, $list){
		$tmp = $oldBalance;
		foreach($list as $item){
			$type = $item['type'];
			if(0 == $type){
				continue;
			}
			$cast = $item['cast'];
			$tmp  += $cast;
		}
		return $tmp;
	}
}