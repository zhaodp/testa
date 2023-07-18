<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 12/3/14
 * Time: 10:45
 */

class TableCheckCommand extends  LoggerExtCommand{

	public function  actionCheckTable($table_source, $table_target, $db_source, $db_target, $primaryKey){
		$offset = 0;
		$limit = 1000;
		$list = $this->getListFromTableSource($table_source, $db_source, $offset, $limit, $primaryKey);
		if(empty($list)){
			EdjLog::info('find nothing '.'args');
		}
		try{
			$this->checkItem($limit, $table_target, $db_target, $primaryKey);
		}catch (Exception $e){

		}
	}
	public function getListFromTableSource($table_source, $db_source, $offset, $limit = 1000, $primaryKey){
		$db = $this->getSourceDb($db_source);
		$command = Yii::app()->$db->createCommand();
		$sql_format = 'select * from %s order by %s limit %s:%s';
		$sql = sprintf($sql_format, $table_source, $primaryKey, $offset, $limit);
		return $command->queryAll($sql);
	}

	public function checkItem($list, $table_target, $db_target, $primaryKey){
		$countTotal = count($list);
		$countFail  = 0;
		foreach($list as $item){
			$primaryValue = $item[$primaryKey];
			$targetItem = $this->getTargetItem($table_target, $db_target, $primaryKey, $primaryValue);
			$status = $this->compareArray($item, $targetItem);
			if(!$status){
				$countFail += 1;
			}
		}
		//TODO ... add log here
	}

	private function getTargetItem($table_target, $db_target, $primaryKey, $primaryValue){
		$db = $this->getTargetItem($table_target, $db_target, $primaryKey, $primaryValue);
		$command = Yii::app()->$db->createCommand();
		$sql_format = 'select * from %s where %s = %s';
		$sql = sprintf($sql_format, $table_target, $primaryKey, $primaryValue);
		return $command->queuryAll($sql);
	}

	private function getSourceDb($db_source){
		return Yii::app()->db_readonly;
	}

	private function getTargetDb($db_target){
		return Yii::app()->db_finance;
	}


	private function compareArray($item_source, $item_target){
		if(empty($item_target)){
			EdjLog::info('target no find'.json_encode($item_source));
			return false;
		}
		foreach($item_source as $k => $v){

		}
		return true;
	}

}