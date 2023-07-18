<div class="well span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<div class="row span12">
		<div class="span3">
			<?php echo $form->label($model,'channel'); ?>
			<?php echo $form->dropDownList($model,'channel', Dict::items('bonus_channel')); ?>
		</div>
	
		<div class="span3">
			<?php echo $form->label($model,'type'); ?>
			<?php echo $form->dropDownList($model,'type', Dict::items('bonus_type')); ?>
		</div>
	
		<div class="span3">
			<?php echo $form->label($model,'is_limited'); ?>
			<?php echo $form->dropDownList($model,'is_limited', Dict::items('bonus_type_limit')); ?>
		</div>
	</div>
	<div class="row span12">	
		<div class="span3">
			<?php echo $form->label($model,'name'); ?>
			<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>60)); ?>
		</div>
		
	</div>
	<div class="row span12">
		<div class="row span3">
			<?php echo CHtml::submitButton('Search'); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->