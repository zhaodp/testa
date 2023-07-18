<?php
/* @var $this ActivityReportController */
/* @var $model ActivityReport */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'activity-report-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'order_id'); ?>
		<?php echo $form->textField($model,'order_id'); ?>
		<?php echo $form->error($model,'order_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_id'); ?>
		<?php echo $form->textField($model,'driver_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'driver_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_name'); ?>
		<?php echo $form->textField($model,'driver_name',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'driver_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_phone'); ?>
		<?php echo $form->textField($model,'driver_phone',array('size'=>15,'maxlength'=>15)); ?>
		<?php echo $form->error($model,'driver_phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>15,'maxlength'=>15)); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'city_id'); ?>
		<?php echo $form->textField($model,'city_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'city_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->textField($model,'status'); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'total_order'); ?>
		<?php echo $form->textField($model,'total_order'); ?>
		<?php echo $form->error($model,'total_order'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'complate_count'); ?>
		<?php echo $form->textField($model,'complate_count'); ?>
		<?php echo $form->error($model,'complate_count'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'complate_p'); ?>
		<?php echo $form->textField($model,'complate_p'); ?>
		<?php echo $form->error($model,'complate_p'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'complate_driver_b'); ?>
		<?php echo $form->textField($model,'complate_driver_b'); ?>
		<?php echo $form->error($model,'complate_driver_b'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'complate_customer_b'); ?>
		<?php echo $form->textField($model,'complate_customer_b'); ?>
		<?php echo $form->error($model,'complate_customer_b'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'order_account'); ?>
		<?php echo $form->textField($model,'order_account',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'order_account'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_account'); ?>
		<?php echo $form->textField($model,'driver_account',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'driver_account'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'company_subsidy'); ?>
		<?php echo $form->textField($model,'company_subsidy',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'company_subsidy'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_subsidy'); ?>
		<?php echo $form->textField($model,'driver_subsidy',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'driver_subsidy'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'customer_subsidy'); ?>
		<?php echo $form->textField($model,'customer_subsidy',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'customer_subsidy'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'order_date'); ?>
		<?php echo $form->textField($model,'order_date'); ?>
		<?php echo $form->error($model,'order_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'day_date'); ?>
		<?php echo $form->textField($model,'day_date'); ?>
		<?php echo $form->error($model,'day_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'create_date'); ?>
		<?php echo $form->textField($model,'create_date'); ?>
		<?php echo $form->error($model,'create_date'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->