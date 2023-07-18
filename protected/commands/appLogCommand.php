<?php
class AppLogCommand extends CConsoleCommand {
	/**
	 * 批量把数据按月统计插库
	 * Enter description here ...
	 * @param unknown_type $time
	 * php yiic.php AppLog Source --time=2012-12-12
	 */
	public function actionSource($time){
		$connection = Yii::app()->dbstat_readonly;
		$connection_db = Yii::app()->dbstat;
		$str = "";//记录macaddress
		$status = "";//记录status
		$date = strtotime($time);
		$dateDay = date('t',$date);
		$dateYM = date('Y-m-',$date);
		$table_date = date('Ym',$date);
		for($i = 1; $i<=$dateDay; $i++)
		{
			if($i<10){
				$startTime = $dateYM.$i;
				$endTime = $dateYM.'0'.$i.' 23:59:59';
			}else{
				$startTime = $dateYM.$i;
				$endTime = $dateYM.$i.' 23:59:59';
			}
			$table_name = 't_api_log_'.$table_date;
			$source = $connection->createCommand()
			->select('COUNT(1) as count , macaddress, method, created, source')
			->from($table_name)
			->where("macaddress !=  '' and created between :startTime and :endTime")
			->group('macaddress, method')
			->queryAll(true,array(":startTime"=>$startTime,":endTime"=>$endTime));
			if(count($source) > 0){
				foreach($source as $source)
				{
					//检查当前信息是否存在
					$table_log_name = 't_api_log_count_'.$table_date;
					$sql = 'SELECT *
								FROM '.$table_log_name.' 
								WHERE macaddress = :macaddress
								AND created = :created';
					$commandExistence = $connection->createCommand($sql);
					$commandExistence->bindParam(":macaddress",$source['macaddress']);
					$commandExistence->bindParam(":created",$source['created']);
					$isExistence = $commandExistence->queryRow();
					
					//不存在，保存
					if(!$isExistence){
						if($str != $source['macaddress']){
							$str = $source['macaddress'];
							$status = $this->isCustomerMacaddress($source);
						}
						$sql = 'INSERT INTO '.$table_log_name.'(method, macaddress, source, status, count_day, created) VALUES(:method, :macaddress, :source, :status, :count_day, :created)';
						$command = $connection_db->createCommand($sql);
						$command->bindParam(":method", $source['method']);
						$command->bindParam(":macaddress", $source['macaddress']);
						$command->bindParam(":source", $source['source']);
						$command->bindParam(":status", $status);
						$command->bindParam(":count_day", $source['count']);
						$command->bindParam(":created", $source['created']);
						$command->execute();
						$command->reset();
						echo $source['method']."\n";
					}
					echo $source['method']."  Existence\n";
				}
			}
		}
	}

	//查询macaddress是否存在
	public static function isCustomerMacaddress($data){
		$connection = Yii::app()->dbstat;
		$sql = "SELECT count(1) as count FROM t_customer_macaddress where macaddress = :macaddress";
		$command = $connection->createCommand($sql);
		$command->bindParam(":macaddress",$data['macaddress']);
		$isnot = $command->queryRow();
		if($isnot["count"]>0){
			return 1;
		}else{
			if(isset($data)){
				$sql = 'INSERT INTO t_customer_macaddress(macaddress, source, created) VALUES(:macaddress,  :source, :created)';
				$command_m = $connection->createCommand($sql);
				$command_m->bindParam(":source", $data['source']);
				$command_m->bindParam(":macaddress", $data['macaddress']);
				$command_m->bindParam(":created", $data['created']);
				$command_m->execute();
				$command_m->reset();
			}
			return 0;
		}
	}

	
	
	//把数据统计保存到t_daily_report_customer_active
	public function actionDottedLine($date){
		$connection = Yii::app()->dbstat;
		$date = strtotime($date);
		$dateDay = date('t',$date);
		$dateYM = date('Y-m-',$date);
		$table_date = date('Ym',$date);
		for($i = 1; $i<=$dateDay; $i++)
		{
			if($i<10){
				$startTime = $dateYM.$i;
				$endTime = $dateYM.'0'.$i.' 23:59:59';
			}else{
				$startTime = $dateYM.$i;
				$endTime = $dateYM.$i.' 23:59:59';
			}
				
			$table_name = 't_api_log_count_'.$table_date;
			//老用户
			$sql = 'SELECT count(DISTINCT macaddress) as count FROM '.$table_name.' where status = 1 and
	created between :startTime and :endTime';
			$command = $connection->createCommand($sql);
			$command->bindParam(":startTime",$startTime);
			$command->bindParam(":endTime",$endTime);
			$repeat_active = $command->queryRow();

			//新用户
			$sql = 'SELECT count(DISTINCT macaddress) as count FROM '.$table_name.' where status = 0 and
	created between :startTime and :endTime';
			$command = $connection->createCommand($sql);
			$command->bindParam(":startTime",$startTime);
			$command->bindParam(":endTime",$endTime);
			$fresh_actives = $command->queryRow();
			//活跃用户
			$actives=$repeat_active['count']+$fresh_actives['count'];

			if($actives > 0)
			{
				$insert_sql = "delete from t_daily_report_customer_active where `current_date`=:startTime;
				INSERT INTO t_daily_report_customer_active(`fresh`, `repeat`, `active_customer`,`current_date`) VALUES(:fresh_actives,:repeat_active,:actives,:startTime)";
				$command = $connection->createCommand($insert_sql);
				$command->bindParam(":fresh_actives",$fresh_actives['count']);
				$command->bindParam(":repeat_active",$repeat_active['count']);
				$command->bindParam(":actives",$actives);
				$command->bindParam(":startTime",$startTime);
				$command->execute();
				echo $startTime."\n";
			}
		}
	}
}