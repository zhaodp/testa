<?php
/* @var $this DriverBatchController */
/* @var $model DriverBatch */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-batch-form',
    'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_batch'); ?>
		<?php echo $form->textField($model,'driver_batch',array('maxlength'=>10)); ?>
		<?php echo $form->error($model,'driver_batch'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'city_id');
			  echo $form->dropDownList($model,'city_id', Dict::items('city'),array('disabled'=>'true'));
		?>
		<?php echo $form->error($model,'city_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php 
			$batch_type = array(
				'0' => '-请选择-',
				'1'=>'司机招聘'
			);
			echo $form->dropDownList($model, 'type', $batch_type);
		?>
		<?php echo $form->error($model,'type'); ?>
	</div>
	<?php echo $form->hiddenField($model,'status'); ?>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->