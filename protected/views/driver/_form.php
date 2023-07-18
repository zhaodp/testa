<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'city_id'); ?>
		<?php echo $form->textField($model,'city_id'); ?>
		<?php echo $form->error($model,'city_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'user'); ?>
		<?php echo $form->textField($model,'user',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'user'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'picture'); ?>
		<?php echo $form->textField($model,'picture',array('size'=>60,'maxlength'=>1024)); ?>
		<?php echo $form->error($model,'picture'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'imei'); ?>
		<?php echo $form->textField($model,'imei',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'imei'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'domicile'); ?>
		<?php echo $form->textField($model,'domicile',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'domicile'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'id_card'); ?>
		<?php echo $form->textField($model,'id_card',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'id_card'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'car_card'); ?>
		<?php echo $form->textField($model,'car_card',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'car_card'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'year'); ?>
		<?php echo $form->textField($model,'year'); ?>
		<?php echo $form->error($model,'year'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'level'); ?>
		<?php echo $form->textField($model,'level'); ?>
		<?php echo $form->error($model,'level'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'price'); ?>
		<?php echo $form->textField($model,'price'); ?>
		<?php echo $form->error($model,'price'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'price_detail'); ?>
		<?php echo $form->textField($model,'price_detail',array('size'=>60,'maxlength'=>1024)); ?>
		<?php echo $form->error($model,'price_detail'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'mark'); ?>
		<?php echo $form->textField($model,'mark'); ?>
		<?php echo $form->error($model,'mark'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'state'); ?>
		<?php echo $form->textField($model,'state'); ?>
		<?php echo $form->error($model,'state'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'ext_phone'); ?>
		<?php echo $form->textField($model,'ext_phone',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'ext_phone'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->