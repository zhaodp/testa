<?php

class CreateAction extends CAction
{
	public function run(){
		$model=new CustomerMain;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['CustomerMain']))
		{
			$_POST['CustomerMain']['create_time'] = date('Y-m-d H:i:s', time());
			$_POST['CustomerMain']['operator'] = Yii::app()->user->id;
			$model->attributes=$_POST['CustomerMain'];
			if($model->save())
				$this->controller->redirect(array('customer/main'));
		}

		$this->controller->render('info/create',array(
			'model'=>$model,
		));	
	}
	
	
	
}
