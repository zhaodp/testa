<?php
/* @var $this MenuController */
/* @var $model Menu */

$this->breadcrumbs=array(
	'Menus'=>array('admin'),
	'Create',
);

?>

<h1><?php echo $model->parentid == 0 ?'创建一级菜单':'创建子菜单'; ?></h1>

<?php
    $form= $model->parentid==0?'_form':'_form_sub';
    $action_info = !isset($action_info) ? array() : $action_info;
    echo $this->renderPartial($form, array('model'=>$model,'parents'=>$parents,'action_info'=>$action_info));
?>