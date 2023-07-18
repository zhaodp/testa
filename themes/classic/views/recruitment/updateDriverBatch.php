<?php
/* @var $this DriverBatchController */
/* @var $model DriverBatch */

$this->breadcrumbs=array(
	'Driver Batches'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List DriverBatch', 'url'=>array('index')),
	array('label'=>'Create DriverBatch', 'url'=>array('create')),
	array('label'=>'View DriverBatch', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage DriverBatch', 'url'=>array('admin')),
);
?>

<h1>Update DriverBatch <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>