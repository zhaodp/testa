<?php
/* @var $this ProblemsCollectController */
/* @var $model ProblemsCollect */

$this->breadcrumbs=array(
	'Problems Collects'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List ProblemsCollect', 'url'=>array('index')),
	array('label'=>'Create ProblemsCollect', 'url'=>array('create')),
	array('label'=>'Update ProblemsCollect', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ProblemsCollect', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ProblemsCollect', 'url'=>array('admin')),
);
?>

<h1>View ProblemsCollect #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'driver_id',
		'name',
		'phone',
		'title',
		'content',
		'status',
		'operator',
		'solve',
		'updated',
		'created',
	),
)); ?>
