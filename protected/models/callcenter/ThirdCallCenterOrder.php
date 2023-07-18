<?php
/**
 * Created by PhpStorm. 用来表示第三方呼叫中心产生的订单
 * User: tuan
 * Date: 14-7-6
 * Time: 16:15
 */

class ThirdCallCenterOrder extends  CActiveRecord{

	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return '{{third_callcenter_order}}';
	}


	public function isNotExist($orderId){
		$model = self::model()->findByPk($orderId);
		return $model == null;
	}

	public function attributeLabels(){
		return array(

		);
	}

	public function getListByTime($timeStart, $timeEnd){
		$criteria = new CDbCriteria;
		$criteria->addBetweenCondition('callTime', $timeStart, $timeEnd);
		//$criteria->order = 'asc';
		return  new CActiveDataProvider($this, array(
			'criteria'	=>$criteria,
			'pagination'=>array(
				'pageSize'=>50,
			),
		));
	}



} 