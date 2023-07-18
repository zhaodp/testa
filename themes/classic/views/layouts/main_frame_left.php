<?php 
if(isset(Yii::app()->user->user_id)){
	$route = Yii::app()->getController()->getRoute();
	$params = Yii::app()->getController()->getActionParams();
	$this->beginContent('//layouts/menu_nav',array('route'=>$route,'params'=>$params));
	$this->endContent();
}
?>
