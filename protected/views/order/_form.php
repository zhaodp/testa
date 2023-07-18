<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'order-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'order_number'); ?>
		<?php echo $form->textField($model,'order_number'); ?>
		<?php echo $form->error($model,'order_number'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'user_id'); ?>
		<?php echo $form->textField($model,'user_id'); ?>
		<?php echo $form->error($model,'user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'phone'); ?>
		<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver'); ?>
		<?php echo $form->textField($model,'driver',array('size'=>30,'maxlength'=>30)); ?>
		<?php echo $form->error($model,'driver'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_id'); ?>
		<?php echo $form->textField($model,'driver_id',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'driver_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'driver_phone'); ?>
		<?php echo $form->textField($model,'driver_phone',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'driver_phone'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'imei'); ?>
		<?php echo $form->textField($model,'imei',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'imei'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'call_time'); ?>
		<?php echo $form->textField($model,'call_time'); ?>
		<?php echo $form->error($model,'call_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'order_date'); ?>
		<?php echo $form->textField($model,'order_date',array('size'=>8,'maxlength'=>8)); ?>
		<?php echo $form->error($model,'order_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'booking_time'); ?>
		<?php echo $form->textField($model,'booking_time'); ?>
		<?php echo $form->error($model,'booking_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'reach_time'); ?>
		<?php echo $form->textField($model,'reach_time'); ?>
		<?php echo $form->error($model,'reach_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'reach_distance'); ?>
		<?php echo $form->textField($model,'reach_distance'); ?>
		<?php echo $form->error($model,'reach_distance'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'start_time'); ?>
		<?php echo $form->textField($model,'start_time'); ?>
		<?php echo $form->error($model,'start_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'end_time'); ?>
		<?php echo $form->textField($model,'end_time'); ?>
		<?php echo $form->error($model,'end_time'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'distance'); ?>
		<?php echo $form->textField($model,'distance'); ?>
		<?php echo $form->error($model,'distance'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'charge'); ?>
		<?php echo $form->textField($model,'charge'); ?>
		<?php echo $form->error($model,'charge'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'location_start'); ?>
		<?php echo $form->textField($model,'location_start',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'location_start'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'location_end'); ?>
		<?php echo $form->textField($model,'location_end',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'location_end'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'income'); ?>
		<?php echo $form->textField($model,'income'); ?>
		<?php echo $form->error($model,'income'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cast'); ?>
		<?php echo $form->textField($model,'cast'); ?>
		<?php echo $form->error($model,'cast'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textField($model,'description',array('size'=>60,'maxlength'=>512)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'cancel_desc'); ?>
		<?php echo $form->textField($model,'cancel_desc',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'cancel_desc'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->textField($model,'status'); ?>
		<?php echo $form->error($model,'status'); ?>
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