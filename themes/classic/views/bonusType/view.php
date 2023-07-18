<?php
$this->pageTitle = '查看优惠券';

$this->menu=array(
	array('label'=>'优惠券列表', 'url'=>array('admin')),
);
?>

<h1><?php echo $this->pageTitle; echo $model->id; ?></h1>

<?php 
$model->channel = Dict::item("bonus_channel", $model->channel);
$model->type = Dict::item("bonus_type", $model->type);
$model->sn_type = Dict::item("bonus_sn_type", $model->sn_type);
$model->end_date = date('Y-m-d', $model->end_date);
$model->is_limited = Dict::item("bonus_type_limit", $model->is_limited);
$model->created = date('Y-m-d H:i:s', $model->created);
$model->updated = date('Y-m-d H:i:s', $model->updated);
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'money',
		'channel',
		'type',
		'sn_type',
		'sn_start',
		'sn_end',
		'issued',
		'end_date',
		'is_limited',
		'create_by',
		'created',
		'update_by',
		'updated',
		'remark'
	),
)); ?>
