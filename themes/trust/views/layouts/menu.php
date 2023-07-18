<div class="well" style="padding: 19px 2px;">
	<ul class="nav nav-list">
<?php
if(isset(Yii::app()->user->first_login) && Yii::app()->user->first_login ==0){
	$this->renderPartial('//layouts/menu_first_login',array('route'=>$route,'params'=>$params));
}else{
	if(isset(Yii::app()->user->id)){
        if(Yii::app()->user->type == AdminUserNew::USER_TYPE_DRIVER){
			$this->renderPartial('//layouts/menu_drivers',array('route'=>$route,'params'=>$params));
		}else{
			//$this->renderPartial('//layouts/menu_main',array('route'=>$route,'params'=>$params));
		}
	}else{
		$this->renderPartial('//layouts/menu_guest',array('route'=>$route,'params'=>$params));
	}
}
?>

	</ul>
</div>