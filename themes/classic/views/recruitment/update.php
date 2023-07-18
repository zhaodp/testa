<?php
/* @var $this ZhaopinController */
/* @var $model DriverZhaopin */

$this->breadcrumbs=array(
	'Driver Zhaopins'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List DriverZhaopin', 'url'=>array('index')),
	array('label'=>'Create DriverZhaopin', 'url'=>array('create')),
	array('label'=>'View DriverZhaopin', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage DriverZhaopin', 'url'=>array('admin')),
);
?>
<h1>司机报名管理</h1>
<h2>编辑  <?php echo $model->name; ?> 的报名信息</h2>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>