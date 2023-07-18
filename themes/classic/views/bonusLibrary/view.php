<?php
/* @var $this BonusLibraryController */
/* @var $model BonusLibrary */

$this->breadcrumbs=array(
	'Bonus Libraries'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List BonusLibrary', 'url'=>array('index')),
	array('label'=>'Create BonusLibrary', 'url'=>array('create')),
	array('label'=>'Update BonusLibrary', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete BonusLibrary', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage BonusLibrary', 'url'=>array('admin')),
);
?>

<h1>View BonusLibrary #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'bonus_sn',
		'money',
		'bonus_id',
		'type',
		'sn_type',
		'create_by',
		'created',
	),
)); ?>
