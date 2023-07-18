<?php
/* @var $this SmsContentController */
/* @var $model SmsContent */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'sms-content-form',
	'enableAjaxValidation'=>false,
)); ?>


	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->textArea($model,'content',array('class'=>'span12','style'=>'width:500px;height:60px;')); ?>
			<?php echo $form->error($model,'content'); ?>
	</div>
	
	<?php echo $form->hiddenField($model,'comments_id'); ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton('发送'); ?>
	</div>
<span id='showmessage'>您已经输入<span id='shownum'>0</span>个字。</span>
<?php $this->endWidget(); ?>

</div><!-- form -->
<script lange='javascropt'>
$(function(){
	$("#SmsContent_content").keyup(function(){
			num = $("#SmsContent_content").val().length;
			
				$("#showmessage").html("您已经输入"+(num)+"个字");
			
			
	});	
});
</script>