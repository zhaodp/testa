<?php
/* @var $this MenuController */
/* @var $model Menu */

$this->breadcrumbs=array(
	'Menus'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);


?>
<h1>编辑菜单</h1>

<?php
    $form= $model->parentid==0?'_form':'_form_sub';
    $action_info =  !isset($action_info) ? array() : $action_info;
    echo $this->renderPartial($form, array('model'=>$model,'parents'=>$parents,'action_info'=>$action_info));
?>