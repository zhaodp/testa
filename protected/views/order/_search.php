<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'order_id'); ?>
		<?php echo $form->textField($model,'order_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'user_id'); ?>
		<?php echo $form->textField($model,'user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'driver'); ?>
		<?php echo $form->textField($model,'driver',array('size'=>30,'maxlength'=>30)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'driver_id'); ?>
		<?php echo $form->textField($model,'driver_id',array('size'=>10,'maxlength'=>10)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'driver_phone'); ?>
		<?php echo $form->textField($model,'driver_phone',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'imei'); ?>
		<?php echo $form->textField($model,'imei',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'call_time'); ?>
		<?php echo $form->textField($model,'call_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'order_date'); ?>
		<?php echo $form->textField($model,'order_date',array('size'=>8,'maxlength'=>8)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'booking_time'); ?>
		<?php echo $form->textField($model,'booking_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'reach_time'); ?>
		<?php echo $form->textField($model,'reach_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'reach_distance'); ?>
		<?php echo $form->textField($model,'reach_distance'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'start_time'); ?>
		<?php echo $form->textField($model,'start_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'end_time'); ?>
		<?php echo $form->textField($model,'end_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'distance'); ?>
		<?php echo $form->textField($model,'distance'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'charge'); ?>
		<?php echo $form->textField($model,'charge'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'location_start'); ?>
		<?php echo $form->textField($model,'location_start',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'location_end'); ?>
		<?php echo $form->textField($model,'location_end',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'income'); ?>
		<?php echo $form->textField($model,'income'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'cast'); ?>
		<?php echo $form->textField($model,'cast'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'description'); ?>
		<?php echo $form->textField($model,'description',array('size'=>60,'maxlength'=>512)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'status'); ?>
		<?php echo $form->textField($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'created'); ?>
		<?php echo $form->textField($model,'created'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->