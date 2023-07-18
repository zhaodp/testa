<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-phone-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_id'); ?>
		<?php echo $form->textField($model,'driver_id',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'driver_id'); ?>
	</div>
	
	<div class="row">
		<span class='required'><?php echo $form->labelEx($model,'phone'); ?></span>
		<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'imei'); ?>
		<?php echo $form->textField($model,'imei',array('size'=>60,'maxlength'=>255,'readonly'=>'true')); ?>
		<?php echo $form->error($model,'imei'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'simcard'); ?>
		<?php echo $form->textField($model,'simcard',array('size'=>20,'maxlength'=>20,'readonly'=>'true')); ?>
		<?php echo $form->error($model,'simcard'); ?>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->