<?php

class IndexAction extends CAction
{
	public function run(){
		$model = new CustomerMain('search');
		$model->unsetAttributes();

		if (Yii::app()->user->city != 0) {
			//$_GET['CustomerMain']['city_id'] = Yii::app()->user->city;
			$model->city_id=$this->controller->getCity($model->city_id);
		}
		
		if (isset($_GET['CustomerMain'])) {
			//$_GET['CustomerMain']['operator'] = Yii::app()->user->id;
			$model->attributes = $_GET['CustomerMain'];
		}
		$this->controller->render('info/admin', array(
			'model' => $model,
		)); 		
	}
	
	
	
}
