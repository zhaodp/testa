<?php
/* @var $this VipController */
/* @var $model Vip */

$this->breadcrumbs=array(
	'Vips'=>array('index'),
	'Manage',
);


$this->pageTitle = 'VIP 查询';
?>

<h1>VIP 查询</h1>
<hr/>
<?php 
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('vip-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="search-form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<div class="row-fluid">
<div class="span3">
	<?php echo $form->label($model,'id');?>
	<?php echo $form->textField($model,'id');?>
</div>
<div class="span3">
	<?php echo $form->label($model, 'phone');?>
	<?php echo $form->textField($model, 'phone');?>
</div>
</div>
<div class="row-fluid">
	<div class="span3">
	<?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
	</div>
</div>
</div>
<?php $this->endWidget(); ?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'vip-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-striped',
	'columns'=>array(
		array(
			'name'=>'id',
			'type' => 'raw',
			'value'=>'$data->id'
        ),
		'name',
        array(
			'name' => 'phone',
			'value' => 'Common::parseCustomerPhone($data->phone)'
		),
		array(
			'name' => '类型',
			'value' => 'Dict::item("vip_type",$data->type)'
		),
		array(
			'name' => '城市',
			'value' => 'Yii::app()->controller->getVipCity($data->city_id)'
		),
		array(
			'name' => '开卡时间',
			'value' => 'date("Y-m-d", $data->created)'
		),				
		'balance',
		'credit',	
		array(
			'name' => 'status',
			'value' => 'Yii::app()->controller->getStatus($data->status)'
		),
	),
)); 
?>