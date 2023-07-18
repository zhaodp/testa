
<div class="form span4">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'admin-work-log-reply-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'content'); ?>
		<?php echo $form->textArea($model,'content',array('rows'=>6, 'cols'=>50, 'style'=>'width:90%;')); ?>
		<?php echo $form->error($model,'content'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '回复' : '保存', array('class'=>'btn btn-success')); ?>
	</div>

<?php $this->endWidget(); ?>

</div>