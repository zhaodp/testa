<?php
$this->breadcrumbs=array(
	'Orders'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Order', 'url'=>array('index')),
	array('label'=>'Create Order', 'url'=>array('create')),
	array('label'=>'Update Order', 'url'=>array('update', 'id'=>$model->order_id)),
	array('label'=>'Delete Order', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->order_id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Order', 'url'=>array('admin')),
);
?>

<h1>View Order #<?php echo $model->order_id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'order_id',
		'user_id',
		'name',
		'phone',
		'driver',
		'driver_id',
		'driver_phone',
		'imei',
		'call_time',
		'order_date',
		'booking_time',
		'reach_time',
		'reach_distance',
		'start_time',
		'end_time',
		'distance',
		'charge',
		'location_start',
		'location_end',
		'income',
		'cast',
		'description',
		'status',
		'created',
	),
)); ?>
