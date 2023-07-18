<?php
/**
 * Created by PhpStorm.
 * User: tuan
 * Date: 14-7-6
 * Time: 17:54
 */

class ThirdCallCenterSummary extends CACtiveRecord{


	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	public function findbyDateAndSource($date, $source){
		$criteria = new CDbCriteria;
		$criteria->compare('date', $date);
		$criteria->compare('source', $source);
		return self::model()->findAll($criteria);
	}


	public function tableName(){
		return '{{third_callcenter_summary}}';
	}

} 