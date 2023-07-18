<?php

class UpdateAction extends CAction
{
	public function run(){
		$model=$this->controller->loadMainModel();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['CustomerMain']))
		{
			if ($_POST['CustomerMain']['bill_receive_mode']==CustomerMain::BILL_RECEIVE_MAIL && !$_POST['CustomerMain']['email']) {
				Yii::app()->user->setFlash('error', '账单接收方式为邮箱，邮箱不能为空');
			}
			$_POST['CustomerMain']['operator'] = Yii::app()->user->id;
			$model->attributes=$_POST['CustomerMain'];
			if (!empty($_POST['CustomerMain']['vip_card'])){
				$model->vip_main=1;
			}
			if (!Yii::app()->user->hasFlash('error')) {
				if($model->save())
					$this->controller->redirect($_POST['re']?$_POST['re']:Yii::app()->createUrl('/customer/main'));
			}
		}
		
		$this->controller->render('info/update',array(
			'model'=>$model,
		));
	}
	
	
	
}
