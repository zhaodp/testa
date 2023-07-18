<?php
/* @var $this ZhaopinController */
/* @var $model DriverZhaopin */

$this->breadcrumbs=array(
	'Driver Zhaopins'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List DriverZhaopin', 'url'=>array('index')),
	array('label'=>'Create DriverZhaopin', 'url'=>array('create')),
	array('label'=>'Update DriverZhaopin', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete DriverZhaopin', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage DriverZhaopin', 'url'=>array('admin')),
);
?>

<h1>View DriverZhaopin #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'mobile',
		'city_id',
		'district_id',
		'work_type',
		'gender',
		'age',
		'id_card',
		'domicile',
		'assure',
		'marry',
		'political_status',
		'edu',
		'pro',
		'driver_type',
		'driver_card',
		'driver_year',
		'driver_cars',
		'contact',
		'contact_phone',
		'contact_relate',
		'experience',
		'status',
		'recyle',
		'recycle_reason',
		'ip',
		'ttime',
		'etime',
		'htime',
		'rtime',
		'ctime',
	),
)); ?>
