<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 12/3/14
 * Time: 11:20
 */
Yii::import('application.models.schema.pay.*');
class DelDriverChargeCommand extends LoggerExtCommand {

	/**
	 * @param $dateStart
	 * @param $dateEnd
	 */
	public function actionBatchRun($dateStart, $dateEnd, $confirm = 0){
		$criteria = new CDbCriteria($dateStart, $dateEnd);
		$criteria->addBetweenCondition('create_time', $dateStart, $dateEnd);
		$driverPayOrderList = CarPayDriverOrder::model()->findAll($criteria);
		if(empty($driverPayOrderList)){
			echo 'find nothing';
			return;
		}
		foreach($driverPayOrderList as $driverOrder){
			echo json_encode($driverOrder);
			echo "\n\n";
			if($confirm){
				$this->actionDelDriverCharge(0, 0, $driverOrder);
			}
		}
	}

	public function actionDelDriverCharge($driverId, $orderId, $driverOrder = null){
		echo 'ok --- driverId ---'.$driverId.' ---- orderId ---'.$orderId;
		echo "\n\n";
		//1.get driver pay order
		if(is_null($driverOrder)){
			$driverOrder = $this->getDriverPayOrder($driverId, $orderId);
		}
		if(!$driverOrder){
			echo 'find nothing';
			echo "\n";return;
		}
		$orderId = $driverOrder['order_id'];
		$driverId = $driverOrder['user_id'];

		//2.get employee_account
		$employeeAccount = $this->getEmployeeAccount($driverId, $orderId);
		if(!$employeeAccount){
			echo 'find no employee account';
			echo "\n";return;
		}
		//3.get driver balance log
		$driverBalanceLog = $this->getDriverBalanceLog($driverId, $orderId);
		if(!$driverBalanceLog){
			echo 'find no driver balance log';
//			echo "\n";return;
		}
		//4.delete
		$employeeAccount->delete();
		if($driverBalanceLog){
			$driverBalanceLog->delete();
		}
		//5.recharge
		$this->recharge($driverOrder, $orderId, $driverId);
	}
	private function getDriverPayOrder($driverId, $orderId){
		$criteria = new CDbCriteria();
		$criteria->compare('user_id', $driverId);
		if(!empty($orderId)){
			$criteria->compare('order_id', $orderId);
		}
		//add between condition
		return CarPayDriverOrder::model()->find($criteria);
	}

	private function getEmployeeAccount($driverId, $orderId){
		EmployeeAccount::$table_name = '201412';
		$criteria = new CDbCriteria();
		$criteria->compare('user', $driverId);
		$criteria->compare('order_id', $orderId);
		return EmployeeAccount::model()->find($criteria);
	}

	private function getDriverBalanceLog($driverId, $orderId){
		$criteria = new CDbCriteria();
		$criteria->compare('driver_id', $driverId);
		$criteria->compare('order_id', $orderId);
		DriverBalanceLog::$table_name = '201412';
		return DriverBalanceLog::model()->find($criteria);
	}

	private function recharge($driverOrder, $orderId, $driverId){
		$amount=$driverOrder->order_amount/100;
		$driverModel= Driver::model()->getProfile($driverId);
		$ret = BUpmpPayOrder::model()->incomeDriverAccount($driverId, $amount, $orderId, $driverModel->city_id);
		if($ret){
			echo 'recharge ok . driver id ---- '.$driverId.'-----orderId'.$orderId;
			echo "\n";return;
		}else{
			echo 'recharge fail ---- '.$driverId.'-----orderId'.$orderId;
			echo "\n";return;
		}
	}
} 