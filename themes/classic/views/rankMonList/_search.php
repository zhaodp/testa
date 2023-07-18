<?php
/* @var $this RankMonListController */
/* @var $model RankMonList */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'driver_id'); ?>
		<?php echo $form->textField($model,'driver_id',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'city_id'); ?>
		<?php echo $form->textField($model,'city_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'order_count'); ?>
		<?php echo $form->textField($model,'order_count'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'call_count'); ?>
		<?php echo $form->textField($model,'call_count'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'work_day_count'); ?>
		<?php echo $form->textField($model,'work_day_count'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'phone_count'); ?>
		<?php echo $form->textField($model,'phone_count'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'income'); ?>
		<?php echo $form->textField($model,'income'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'created'); ?>
		<?php echo $form->textField($model,'created'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->