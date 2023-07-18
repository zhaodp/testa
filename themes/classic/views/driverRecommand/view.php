<?php
/* @var $this DriverRecommandController */
/* @var $model DriverRecommand */

$this->breadcrumbs=array(
	'Driver Recommands'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List DriverRecommand', 'url'=>array('index')),
	array('label'=>'Create DriverRecommand', 'url'=>array('create')),
	array('label'=>'Update DriverRecommand', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete DriverRecommand', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage DriverRecommand', 'url'=>array('admin')),
);
?>

<h1>View DriverRecommand #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'driver_id',
		'type',
		'reason',
		'begin_time',
		'end_time',
		'created',
	),
)); ?>
