<h1>修改司机信息</h1>
<h3>修改  <?php echo $model->name; ?> 的信息</h3>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-zhaopin-form',
	'enableAjaxValidation'=>false,
	'enableClientValidation'=>false,
	'errorMessageCssClass'=>'alert alert-error'
)); ?>

<?php echo $form->errorSummary($model); ?>

<section id="basicinfo" class="basicinfo">
	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'name'); ?>	
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'driver_id'); ?>
		<?php echo $form->textField($model,'driver_id',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'driver_id'); ?>	
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'imei'); ?>
		<?php echo $form->textField($model,'imei',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'imei'); ?>	
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'driver_phone'); ?>
		<?php echo $form->textField($model,'driver_phone',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'driver_phone'); ?>	
	</div>		

 <?php echo CHtml::submitButton('修改报名表',array('class'=>'span3 btn-large btn-success btn-block')); ?>
</section>
<?php $this->endWidget(); ?>