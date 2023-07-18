<?php
/* @var $this ZhaopinController */
/* @var $model DriverZhaopin */

$this->breadcrumbs=array(
	'Driver Zhaopins'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List DriverZhaopin', 'url'=>array('index')),
	array('label'=>'Manage DriverZhaopin', 'url'=>array('admin')),
);
?>

<h1>Create DriverZhaopin</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>