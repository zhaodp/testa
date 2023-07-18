<?php
class IDsequence extends CActiveRecord{
	public static function model($className = __CLASS__){
		return parent::model($className);
	}
	public function tableName(){
		return "IDsequence";
	}
	
	public function nextId($tableName){
		$model = self::model();
		$transaction = $model->dbConnection->beginTransaction();
		try{
			$seq = $model->findByPk($tableName);
			$id=$seq->nextid;
			$newId = $id + 1;
			$seq->nextid = $newId;
			$seq->save();
			$transaction->commit();
			return $id;
		}catch(Exception $e){
			$transaction->rollBack();
		}
	}
}
