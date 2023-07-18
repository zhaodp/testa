<?php
Yii::import("application.controllers.DemoController");
Yii::import("application.models.demo.DemoUser");

class IndexAction extends CAction
{
	public function run()
	{
		$userName = DemoUser::model()->getName(1);

		$this->controller->renderText("用户名：". $userName);
	}
}