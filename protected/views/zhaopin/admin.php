<?php
/* @var $this ZhaopinController */
/* @var $model DriverZhaopin */

$this->breadcrumbs=array(
	'Driver Zhaopins'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List DriverZhaopin', 'url'=>array('index')),
	array('label'=>'Create DriverZhaopin', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-zhaopin-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Driver Zhaopins</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-zhaopin-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'mobile',
		'city_id',
		'district_id',
		'work_type',
		/*
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
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
