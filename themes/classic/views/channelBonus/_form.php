<div class="row span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'channel-bonus-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'owner'); ?>
		<?php echo $form->textField($model,'owner',array('size'=>60,'maxlength'=>256, 'readonly'=>'readonly')); ?>
		<?php echo $form->error($model,'owner'); ?>
		<?php 
			if ($employee) echo $employee->name;
		?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'channel_id'); ?>
		<?php echo $form->dropDownList($model,'channel_id', Dict::items('bonus_channel')); ?>
		<?php echo $form->error($model,'channel_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type_id'); ?>
		<?php echo $form->dropDownList($model,'type_id', BonusType::getBonusTypes()); ?>
		<?php echo $form->error($model,'type_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sn_start'); ?>
		<?php echo $form->textField($model,'sn_start'); ?>
		<?php echo $form->error($model,'sn_start'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sn_end'); ?>
		<?php echo $form->textField($model,'sn_end'); ?>
		<?php echo $form->error($model,'sn_end'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '创建' : '更新'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->