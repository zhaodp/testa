<div class="well span6">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'comments-reply-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $model->comments; ?>
		<input size="20" maxlength="30" name="CommentsReply[comment_id]" id="CommentsReply_comment_id" type="hidden" value="<?php echo $model->comment_id; ?>" />	
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>5,'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>	

	<div class="row btn-lgr">
		<?php echo CHtml::submitButton($model->isNewRecord ? '保存' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->