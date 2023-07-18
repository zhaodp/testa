<?php
/* @var $this VipController */
/* @var $model Vip */

$this->breadcrumbs=array(
	'Vips'=>array('index'),
	$model->name,
);
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('admin-user-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

?>
<div class="row-fluid">
<div class="span8">
<h1>VIP卡充值  卡号<?php echo $model->vipcard; ?></h1>
</div>
<div class="span2"><?php echo CHtml::link("编辑主卡", Yii::app()->controller->createUrl("vip/update",array("id"=>$model->vipcard)), array('target'=>'_main')); ?></div>
</div>

<?php
if ($charge)
{ 
?>
<div class="row-fluid">
<div class="span6">
<span class="alert alert-success">充值成功!</span>
</div>
</div>
<?php
} 
?>
<?php
if ($error)
{
?>
<div class="row-fluid">
<div class="span6 alert alert-error">
<?php echo $error; ?>
</div>
</div>
<?php
}
?>
<div class="row">
<div class="span4">
账户余额:
</div>
<div class="span4">
<?php echo $model->remain; ?>
</div>
</div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'vip-charge-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error',
	'method'=>'post'
)); ?>

<div class="row">
<div class="span4">
充值金额:
</div>
<div class="span4">
<input type="text" name="amount" id="amount" value="0.00" />
<input type="hidden" name="id" id="id" value="<?php echo $model->vipcard; ?>" />
</div>
</div>
<div class="row">
<div class="span4">
充值时间:
</div>
<div class="span4">
<?php echo date("Y-m-d", time());?>
</div>
</div>

<div class="row">
<div class="span4">
操作人:
</div>
<div class="span4">
<?php echo Yii::app()->user->getId(); ?>
</div>
</div>
<div class="row-fluid">
<div class="span4">
</div>
<div class="span6">
<?php echo CHtml::submitButton("确定充值", array('class'=>'span3 btn-large btn-success btn-block'));?>
</div>
</div>
<?php $this->endWidget(); ?>
<h2>充值记录</h2>
<div class="row-fluid">
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'vip-charge-grid',
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array (
			'name' => 'created',
			'value' => 'date("Y-m-d", $data->created)'
		),
		'amount',
	),
)); ?>
</div>





