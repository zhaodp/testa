<?php
/* @var $this DriverBatchController */
/* @var $model DriverBatch */

$this->breadcrumbs=array(
	'Driver Batches'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List DriverBatch', 'url'=>array('index')),
	array('label'=>'Manage DriverBatch', 'url'=>array('admin')),
);
?>

<h1>Create DriverBatch</h1>

<?php echo $this->renderPartial('_formdriverbatch', array('model'=>$model)); ?>