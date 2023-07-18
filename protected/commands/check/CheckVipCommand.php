<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 12/3/14
 * Time: 15:28
 */

class CheckVipCommand extends  LoggerExtCommand{

	public function actionBatchCheckTrade($fileName, $confirm = 0){
		$content 	= file_get_contents($fileName);
		$contentArr = preg_split('/[\r\n]+/', $content);
		$timeEnd = 1417599780;
		foreach($contentArr as $item){
			try {
				$arr = preg_split('/[:]+/', $item);
				$vipCard = trim($arr[0]);
				$timeStart = isset($arr[3]) ? trim($arr[3]) : 0;
				if(empty($timeStart)){
					echo 'time empty ----'.$item;
					echo "\n";
					continue;
				}
//				echo $vipCard . '-----' . $timeStart . "\n";
				$timeStart = 1417560300;
				$this->actionCheckTrade($timeStart, $timeEnd, $vipCard, $confirm);
			} catch (Exception $e) {
				EdjLog::info($e->getMessage());
				echo 'exception --- ' . $item;
			}
		}
	}

	public function actionCheckTrade($timeStart, $timeEnd,  $vipCard = 0, $confirm = 0){
		$filter = array();
		if(in_array($vipCard, $filter)){
			return;
		}
		//get vip
		$vip = Vip::model()->findByPk($vipCard);
		if(!$vip){
			echo 'find no vip ---'.$vipCard."\n";
			return;
		}
		//get trade
		$tradeList = $this->getVipTrade($timeStart, $timeEnd, $vipCard);
		if(empty($tradeList)){
			echo 'find no trade list --- '.$vipCard.'---'.$timeStart.'---'.$timeEnd."\n";
			return;
		}

		//update counter
		if($confirm){
			$failCount = $this->updateCounters($tradeList, $vipCard);
			echo 'update vip ---'.$vipCard.'--- fail count ---'.$failCount."\n";
		}
		//reload redis ????
	}

	private function updateCounters($tradeList, $vipcard){
		$failCount = 0;
		foreach($tradeList as $trade){
			$amount = isset($trade['amount']) ? $trade['amount'] : 0;
			if(0 == $amount){
				continue;
			}
			try{
				$ret = Vip::model()->updateBalance($vipcard, $amount);
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

	private function getVipTrade($timeStart, $timeEnd, $vipCard = 0){
		$criteria = new CDbCriteria();
//		$criteria->addBetweenCondition('created', $timeStart, $timeEnd);
		$criteria->addCondition('created > :timeStart and created < :timeEnd');
		$criteria->params = array(
			':timeStart'    => $timeStart,
			':timeEnd'      => $timeEnd,
		);
		if(!empty($vipCard)){
			$criteria->compare('vipcard', $vipCard);
		}
		return VipTrade::model()->findAll($criteria);
	}


	public function actionCheckOrderId($orderId){
		//1.get customer trans
		//2.get vip trade
	}

	private function getCustomerTrans($orderId){

	}

	public function actionUpdateMoney($vipcard, $cast, $confirm = 0){
		if($confirm){
			Vip::model()->updateBalance($vipcard, $cast);
		}
		$vip = Vip::model()->findByPk($vipcard);
		echo serialize($vip);
		echo "\n\n\n";
	}
} 