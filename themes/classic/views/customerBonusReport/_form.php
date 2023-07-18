<?php
/* @var $this CustomerBonusReportController */
/* @var $model CustomerBonusReport */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'customer-bonus-report-form',
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
		<?php echo $form->textField($model,'driver_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'driver_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'bonus_sn'); ?>
		<?php echo $form->textField($model,'bonus_sn',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'bonus_sn'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'bonus_count'); ?>
		<?php echo $form->textField($model,'bonus_count'); ?>
		<?php echo $form->error($model,'bonus_count'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'used_count'); ?>
		<?php echo $form->textField($model,'used_count'); ?>
		<?php echo $form->error($model,'used_count'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'amount'); ?>
		<?php echo $form->textField($model,'amount'); ?>
		<?php echo $form->error($model,'amount'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'report_time'); ?>
		<?php echo $form->textField($model,'report_time'); ?>
		<?php echo $form->error($model,'report_time'); ?>
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