<?php
/* @var $this ActivityReportController */
/* @var $model ActivityReport */

$this->breadcrumbs=array(
	'Activity Reports'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List ActivityReport', 'url'=>array('index')),
	array('label'=>'Create ActivityReport', 'url'=>array('create')),
	array('label'=>'Update ActivityReport', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ActivityReport', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ActivityReport', 'url'=>array('admin')),
);
?>

<h1>View ActivityReport #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'order_id',
		'driver_id',
		'driver_name',
		'driver_phone',
		'phone',
		'city_id',
		'status',
		'total_order',
		'complate_count',
		'complate_p',
		'complate_driver_b',
		'complate_customer_b',
		'order_account',
		'driver_account',
		'company_subsidy',
		'driver_subsidy',
		'customer_subsidy',
		'order_date',
		'day_date',
		'create_date',
	),
)); ?>
