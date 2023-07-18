<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 12/3/14
 * Time: 18:50
 */
Yii::import('application.models.schema.customer.*');
class CheckCustomerCommand extends LoggerExtCommand{

	public function actionBatchCheckCustomerTrans($fileName, $confirm = 0 ){
		$content 	= file_get_contents($fileName);
		$contentArr = preg_split('/[\r\n]+/', $content);
		foreach($contentArr as $item){
			$arr = preg_split('/[:]+/', $item);
			$userId = isset($item[0]) ? trim($item[0]) : 0;
			$oldID = isset($item[3]) ? trim($item[3]) : 0;
			$this->actionCheckCustomerTrans($oldID,$oldID,  $userId, $confirm);
		}
	}

	public function actionCheckCustomerTrans( $idOld, $idNew,  $userId = 0, $confirm = 0){
		$filter = array(2055943,1192795,541681,1926751);
		if(in_array($userId, $filter)){
			return;
		}
		$customerAccount = CarCustomerAccount::model()->getAmountList(array($userId));
		if(empty($customerAccount)){
			echo 'find no customer account ---- user id '.$userId;
			echo "\n";
			return ;
		}
		$transList = $this->getCustomerTransList($userId);
		if(empty($transList)){
			echo 'find no trans list --- user id '.$userId."\n";return;
		}
		if($confirm){
			$failCount = $this->updateCounters($transList, $userId);
			if($failCount == 0){
				echo 'update customer ---'.$userId.'--- fail count ---'.$failCount."\n";
			}
		}
	}

	private function getCustomerTransList($userId = 0){
		$criteria = new CDbCriteria();
//		$criteria->addCondition('id > :idOld and id < :idNew');
//		$criteria->params = array(
//			':idOld'    => $idOld,
//			':idNew'    =>$idNew,
//		);
		$criteria->addCondition('create_time > :dateStart and create_time < :dateEnd');
		$criteria->params = array(
			':dateStart'    => date('Y-m-d H:i:s', 1417560300),
			':dateEnd'      => date('Y-m-d H:i:s', 1417600920),
		);
		$criteria->compare('user_id', $userId);
		return CarCustomerTrans::model()->findAll($criteria);
	}

	private function updateCounters($transList, $userId){
		$failCount = 0;
		foreach($transList as $trans){
			$amount = isset($trans['amount']) ? $trans['amount'] : 0;
			if(0 == $amount){
				continue;
			}
			try{
				$ret = CarCustomerAccount::model()->updateCounters(array('amount' => $amount), 'user_id = :userId', array(':userId' => $userId));
				if(!$ret){
					$failCount += 1;
				}
			}catch (Exception $e){
				EdjLog::info($e->getMessage());
				$failCount += 1;
			}
		}
		return $failCount;
	}

	public function actionRepairPay($orderId){
		$pay_order = CarPayOrder::model()->find('order_id=:order_id',array(':order_id'=>$orderId));
		$user_phone=$pay_order->user_id;
		$amount=$pay_order->order_amount/100;
		$customer=  BCustomers::model()->getCustomerInfo(array('phone'=>$user_phone));
		if($customer['code']==0 && is_object($customer['data'])){
			$incomeResult=$this->incomeUserAccount($customer['data']->id,$amount,$pay_order->order_id);
		}else{
		}
	}

	public function actionCheckPayOrder(){
		$criteria = new CDbCriteria();
		$criteria->addBetweenCondition('create_time',('2014-12-03 06:00:00'), ('2014-12-03 18:02:00'));
		$criteria->compare('trans_status', BUpmpPayOrder::TRANS_STATUS_SECOND);
	}
} 