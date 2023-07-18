<?php
$this->pageTitle = '浏览渠道优惠券';

$this->menu=array(
	array('label'=>'管理渠道优惠券', 'url'=>array('admin')),
);
?>

<h1><?php echo $this->pageTitle.' #'; echo $model->id; ?></h1>

<?php 
$model->created = date('Y-m-d H:i:s', $model->created);
$model->updated = date('Y-m-d H:i:s', $model->updated);
$model->channel_id = Dict::item("bonus_channel", $model->channel_id);
$model->type_id = BonusType::getBonusType($model->type_id)->name;
$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'owner',
		'channel_id',
		'type_id',
		'sn_start',
		'sn_end',
		'create_by',
		'created',
		'update_by',
		'updated',
	),
)); ?>
