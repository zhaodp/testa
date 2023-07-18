<?php
/* @var $this ZhaopinController */
/* @var $model DriverZhaopin */
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
		<?php echo $form->label($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'mobile'); ?>
		<?php echo $form->textField($model,'mobile',array('size'=>11,'maxlength'=>11)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'city_id'); ?>
		<?php echo $form->textField($model,'city_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'district_id'); ?>
		<?php echo $form->textField($model,'district_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'work_type'); ?>
		<?php echo $form->textField($model,'work_type',array('size'=>1,'maxlength'=>1)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'gender'); ?>
		<?php echo $form->textField($model,'gender',array('size'=>1,'maxlength'=>1)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'age'); ?>
		<?php echo $form->textField($model,'age'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'id_card'); ?>
		<?php echo $form->textField($model,'id_card',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'domicile'); ?>
		<?php echo $form->textField($model,'domicile',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'assure'); ?>
		<?php echo $form->textField($model,'assure',array('size'=>1,'maxlength'=>1)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'marry'); ?>
		<?php echo $form->textField($model,'marry',array('size'=>1,'maxlength'=>1)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'political_status'); ?>
		<?php echo $form->textField($model,'political_status'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'edu'); ?>
		<?php echo $form->textField($model,'edu'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'pro'); ?>
		<?php echo $form->textField($model,'pro',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'driver_type'); ?>
		<?php echo $form->textField($model,'driver_type'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'driver_card'); ?>
		<?php echo $form->textField($model,'driver_card',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'driver_year'); ?>
		<?php echo $form->textField($model,'driver_year'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'driver_cars'); ?>
		<?php echo $form->textField($model,'driver_cars',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'contact'); ?>
		<?php echo $form->textField($model,'contact',array('size'=>50,'maxlength'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'contact_phone'); ?>
		<?php echo $form->textField($model,'contact_phone',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'contact_relate'); ?>
		<?php echo $form->textField($model,'contact_relate',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'experience'); ?>
		<?php echo $form->textArea($model,'experience',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'status'); ?>
		<?php echo $form->textField($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'recycle'); ?>
		<?php echo $form->textField($model,'recycle',array('size'=>1,'maxlength'=>1)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'recycle_reason'); ?>
		<?php echo $form->textField($model,'recycle_reason',array('size'=>60,'maxlength'=>255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ip'); ?>
		<?php echo $form->textField($model,'ip',array('size'=>15,'maxlength'=>15)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ttime'); ?>
		<?php echo $form->textField($model,'ttime'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'etime'); ?>
		<?php echo $form->textField($model,'etime'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'htime'); ?>
		<?php echo $form->textField($model,'htime'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'rtime'); ?>
		<?php echo $form->textField($model,'rtime'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'ctime'); ?>
		<?php echo $form->textField($model,'ctime'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->