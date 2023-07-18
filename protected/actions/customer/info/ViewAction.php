<?php
/**
 * 查看个人信息类
 * Enter description here ...
 * @author zengzhihai
 *
 */
class ViewAction extends CAction
{
	public function run(){
		$Thisviews=array();
		$model=$this->controller->loadMainModel();
		$Thisviews['city_name']=$this->controller->getCity($model->city_id);
		$this->controller->renderPartial('info/view',array(
			'model'=>$model,
			'views'=>$Thisviews,
		));
	}
	
	
	
}