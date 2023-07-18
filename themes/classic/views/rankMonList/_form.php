<?php
/* @var $this RankMonListController */
/* @var $model RankMonList */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'rank-mon-list-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_id'); ?>
		<?php echo $form->textField($model,'driver_id',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'driver_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'city_id'); ?>
		<?php echo $form->textField($model,'city_id'); ?>
		<?php echo $form->error($model,'city_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'order_count'); ?>
		<?php echo $form->textField($model,'order_count'); ?>
		<?php echo $form->error($model,'order_count'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'call_count'); ?>
		<?php echo $form->textField($model,'call_count'); ?>
		<?php echo $form->error($model,'call_count'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'work_day_count'); ?>
		<?php echo $form->textField($model,'work_day_count'); ?>
		<?php echo $form->error($model,'work_day_count'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'phone_count'); ?>
		<?php echo $form->textField($model,'phone_count'); ?>
		<?php echo $form->error($model,'phone_count'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'income'); ?>
		<?php echo $form->textField($model,'income'); ?>
		<?php echo $form->error($model,'income'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'created'); ?>
		<?php echo $form->textField($model,'created'); ?>
		<?php echo $form->error($model,'created'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->