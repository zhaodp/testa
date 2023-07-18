<?php
/* @var $this DriverComplaintController */
/* @var $model DriverComplaint */
/* @var $form CActiveForm */
?>

<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'order_id'); ?>
		<?php echo $form->textField($model,'order_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'driver_user'); ?>
		<?php echo $form->textField($model,'driver_user',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'customer_name'); ?>
		<?php echo $form->textField($model,'customer_name',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'city'); ?>
		<?php echo $form->textField($model,'city'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'customer_phone'); ?>
		<?php echo $form->textField($model,'customer_phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'order_type'); ?>
		<?php echo $form->textField($model,'order_type'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'complaint_type'); ?>
		<?php echo $form->textField($model,'complaint_type'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'complaint_content'); ?>
		<?php echo $form->textArea($model,'complaint_content',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'driver_time'); ?>
		<?php echo $form->textField($model,'driver_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'complaint_status'); ?>
		<?php echo $form->textField($model,'complaint_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'create_time'); ?>
		<?php echo $form->textField($model,'create_time'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->