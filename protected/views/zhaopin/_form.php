<?php
/* @var $this ZhaopinController */
/* @var $model DriverZhaopin */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-zhaopin-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'mobile'); ?>
		<?php echo $form->textField($model,'mobile',array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,'mobile'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'city_id'); ?>
		<?php echo $form->textField($model,'city_id'); ?>
		<?php echo $form->error($model,'city_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'district_id'); ?>
		<?php echo $form->textField($model,'district_id'); ?>
		<?php echo $form->error($model,'district_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'work_type'); ?>
		<?php echo $form->textField($model,'work_type',array('size'=>1,'maxlength'=>1)); ?>
		<?php echo $form->error($model,'work_type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'gender'); ?>
		<?php echo $form->textField($model,'gender',array('size'=>1,'maxlength'=>1)); ?>
		<?php echo $form->error($model,'gender'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'age'); ?>
		<?php echo $form->textField($model,'age'); ?>
		<?php echo $form->error($model,'age'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'id_card'); ?>
		<?php echo $form->textField($model,'id_card',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'id_card'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'domicile'); ?>
		<?php echo $form->textField($model,'domicile',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'domicile'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'assure'); ?>
		<?php echo $form->textField($model,'assure',array('size'=>1,'maxlength'=>1)); ?>
		<?php echo $form->error($model,'assure'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'marry'); ?>
		<?php echo $form->textField($model,'marry',array('size'=>1,'maxlength'=>1)); ?>
		<?php echo $form->error($model,'marry'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'political_status'); ?>
		<?php echo $form->textField($model,'political_status'); ?>
		<?php echo $form->error($model,'political_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'edu'); ?>
		<?php echo $form->textField($model,'edu'); ?>
		<?php echo $form->error($model,'edu'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'pro'); ?>
		<?php echo $form->textField($model,'pro',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'pro'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_type'); ?>
		<?php echo $form->textField($model,'driver_type'); ?>
		<?php echo $form->error($model,'driver_type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_card'); ?>
		<?php echo $form->textField($model,'driver_card',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'driver_card'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_year'); ?>
		<?php echo $form->textField($model,'driver_year'); ?>
		<?php echo $form->error($model,'driver_year'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_cars'); ?>
		<?php echo $form->textField($model,'driver_cars',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'driver_cars'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'contact'); ?>
		<?php echo $form->textField($model,'contact',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'contact'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'contact_phone'); ?>
		<?php echo $form->textField($model,'contact_phone',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'contact_phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'contact_relate'); ?>
		<?php echo $form->textField($model,'contact_relate',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'contact_relate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'experience'); ?>
		<?php echo $form->textArea($model,'experience',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'experience'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->textField($model,'status'); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'recyle'); ?>
		<?php echo $form->textField($model,'recyle',array('size'=>1,'maxlength'=>1)); ?>
		<?php echo $form->error($model,'recyle'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'recycle_reason'); ?>
		<?php echo $form->textField($model,'recycle_reason',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'recycle_reason'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'ip'); ?>
		<?php echo $form->textField($model,'ip',array('size'=>15,'maxlength'=>15)); ?>
		<?php echo $form->error($model,'ip'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'ttime'); ?>
		<?php echo $form->textField($model,'ttime'); ?>
		<?php echo $form->error($model,'ttime'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'etime'); ?>
		<?php echo $form->textField($model,'etime'); ?>
		<?php echo $form->error($model,'etime'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'htime'); ?>
		<?php echo $form->textField($model,'htime'); ?>
		<?php echo $form->error($model,'htime'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'rtime'); ?>
		<?php echo $form->textField($model,'rtime'); ?>
		<?php echo $form->error($model,'rtime'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'ctime'); ?>
		<?php echo $form->textField($model,'ctime'); ?>
		<?php echo $form->error($model,'ctime'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->