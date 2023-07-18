<?php
/* @var $this BonusLibraryController */
/* @var $model BonusLibrary */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'bonus-library-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'bonus_sn'); ?>
		<?php echo $form->textField($model,'bonus_sn',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'bonus_sn'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'money'); ?>
		<?php echo $form->textField($model,'money',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'money'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'bonus_id'); ?>
		<?php echo $form->textField($model,'bonus_id'); ?>
		<?php echo $form->error($model,'bonus_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->textField($model,'type'); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sn_type'); ?>
		<?php echo $form->textField($model,'sn_type'); ?>
		<?php echo $form->error($model,'sn_type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'create_by'); ?>
		<?php echo $form->textField($model,'create_by',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'create_by'); ?>
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