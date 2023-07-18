<?php
class CallLogCommand extends LoggerExtCommand{

	public function actionRun($date='', $limit=1000){
		echo('开始用户输入时间:'.$date);
		echo "\n";
		$date = empty($date) ?  date('Y-m-d', strtotime('-1 days')) : $date;
		//昨天这个时候
		$timeStart 	= strtotime($date.' 0:0:0');
		$timeEnd 	= strtotime($date.' 23:59:59');

		echo('查询时间区间为'.$timeStart.'到'.$timeEnd);
		echo "\n";
		$count 	= AppCallRecord::model()->getCountByTime($timeStart, $timeEnd);
		$limit 	= 1000;
		$offset = 0;
		$begin = time();
		//多余了
		while($offset < $count + $limit){
			$records = AppCallRecord::model()->getByOffsetAndLimit($timeStart, $timeEnd, $offset, $limit);
			$this->updateDate($records);
			$offset  += $limit;

			//每处理一次请求 sleep 1s
			sleep(1);
		}
		$end = time();
		echo('执行花费时间 '.($end - $begin). ' offset = '.$offset. ' limit = '.$limit.' 总数为:'.$count);
		echo "\n";
		echo '---------------------------------';
		echo "\n";

	}

	private function updateDate($records){
		if(empty($records)){
			return ;
		}
		foreach($records as $item){
			if($item->order_id > 0){
				continue;
			}
			$ret = $this->getOrderInfo($item->call_time, $item->driverID);
			if(is_null($ret)){
				continue;
			}
			$item->order_status 	= $ret['status'];
			$item->order_id 		= $ret['orderId'];
			$item->order_time 		= $ret['start_time'];
			$item->city_id			= $ret['city_id'];
			$item->order_cast		= $ret['income'];
			//
			$item->phone = $ret['phone'];

			if(empty($item->device)){
				$item->device= 'null';
			}
			if(empty($item->longitude)){
				$item->longitude= 'null';
			}
			if(empty($item->latitude)){
				$item->latitude= 'null';
			}
			if(!$item->save(false)){
				echo 'error'.var_dump($item);
				echo "\n";
				echo json_encode($item->getErrors());
			}
		}
	}

	private function getOrderInfo($callTime, $driverId){
		$start 	= $callTime - 2*60;
		$end	= $callTime + 2*60;

		$order	= Order::model()->getByAppCallTimeAndDriverId($start, $end, $driverId);
		if(!is_null($order)){
			$ret = array(
				'orderId'		=> $order->order_id,
				'status'		=> $order->status,
				'start_time'    => $order->booking_time,
				'city_id'       => $order->city_id,
				'income'  		=> $order->income,
				'phone'			=> $order->phone,
			);
			return $ret;
		}
	}



}
