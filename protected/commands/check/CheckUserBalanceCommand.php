<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 14/12/5
 * Time: 09:53
 */
Yii::import('application.models.schema.customer.*');
class CheckUserBalanceCommand extends  LoggerExtCommand{

	public function actionBatchCheckNormal($dateStart){
		$db = Yii::app()->db_readonly;
		$sql_format = 'select user_id from t_customer_account order by id desc' ;
		$list = $db->createCommand($sql_format)->queryAll();
		foreach($list as $user){
			try{
				$this->actionCheck($user['user_id'], false, $dateStart);
			}catch (Exception $e){
				EdjLog::info('exception --- '.json_encode($user));
				continue;
			}
		}
	}

	public function actionBatchCheckVip($dateStart){
		$db = Yii::app()->db_readonly;
		$sql = 'select id from t_vip ';
		$vipList = $db->createCommand($sql)->queryAll();
		foreach($vipList as $vip){
			try{
			$this->actionCheck($vip['id'], true, $dateStart);
			}catch (Exception $e){
				EdjLog::info('exception --- '.json_encode($vip));
				continue;
			}
		}
	}

	public function actionCheck($userId, $isVip,  $dateStart){
		$dateEnd = date('Y-m-d H:i:s');
		//1.get old new balance
		$oldBalance  = $this->getOldBalance($userId, $isVip);
		//2.get new trans list
		$transList = $this->getTransList($userId, $isVip,  $dateStart, $dateEnd);
		if(empty($transList)){
			return;
		}
		//check status
		$status = $this->checkStatus($oldBalance, $userId, $isVip, $transList);
		$format = 'result ---- user_id|%s|status|%s|';
		if($status){
			EdjLog::info(sprintf($format, $userId, 'success'));
		}else{
			EdjLog::info(sprintf($format, $userId, 'fail'));
		}
	}

	private function getOldBalance($userId, $isVip){
		$db = Yii::app()->db_readonly;
		if(!$isVip){
			$sql_format = 'select * from t_customer_account where user_id = %s';
			$sql = sprintf($sql_format , $userId);
			$customerAccount = $db->createCommand($sql)->queryRow();
			return isset($customerAccount['amount']) ? $customerAccount['amount'] : 0;
		}else{
			$sql_format = 'select * from t_vip where id = %s';
			$sql = sprintf($sql_format, $userId);
			$vip = $db->createCommand($sql)->queryRow();
			return isset($vip['balance']) ? $vip['balance'] :0;
		}

	}
	private function getNewBalance($userId, $isVip){
		if(!$isVip){
			$criteria = new CDbCriteria();
			$criteria->compare('user_id', $userId);
			$customerAccount = CarCustomerAccount::model()->find($criteria);
			return isset($customerAccount['amount']) ? $customerAccount['amount'] : 0;
		}else{
			$vip = Vip::model()->getPrimary($userId);
			return isset($vip['balance']) ? $vip['balance'] :0;
		}
	}

	private function getTransList($userId, $isVip, $dateStart, $dateEnd)
	{
		try {
			if (!$isVip) {
				$criteria = new CDbCriteria();
				$criteria->addBetweenCondition('create_time', $dateStart, $dateEnd);
				$criteria->compare('user_id', $userId);
				return CarCustomerTrans::model()->findAll($criteria);
			} else {
				$criteria = new CDbCriteria();
				$criteria->addBetweenCondition('created', strtotime($dateStart), strtotime($dateEnd));
				$criteria->compare('vipcard', $userId);
				return VipTrade::model()->findAll($criteria);
			}
		} catch (Exception $e) {
			EdjLog::error('exception --- '.$userId);
		}
	}

	private function checkStatus($oldBalance, $userId, $isVip, $transList){
		$balance = $oldBalance;
		$transBalance = 0;
		foreach($transList as $trans){
			$cast = isset($trans['amount']) ? $trans['amount'] : 0;
			$balance += $cast;
			$transBalance = isset($trans['balance']) ? $trans['balance'] : 0;
		}
		$newBalance = $this->getNewBalance($userId, $isVip);
		$format = 'check-status ---- userId|%s|oldBalance|%s|newBalance|%s|isVip|%s|excepted_balance|%s|';
		EdjLog::info(sprintf($format, $userId, $oldBalance, $newBalance, $isVip, $balance));
		return  $balance == $newBalance;
	}
}