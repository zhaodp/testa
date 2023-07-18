<?php
/* @var $this DriverTrainDataController */
/* @var $model DriverTrainData */
/* @var $form CActiveForm */
?>

<div class="well span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<div class="row-fluid">
		<div class="span3">
			<?php echo $form->label($model,'title'); ?>
			<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>150)); ?>
		</div>
	
		<div class="span3">
			<?php echo $form->label($model,'city_id'); ?>
			<?php echo $form->textField($model,'city_id'); ?>
		</div>
		
		<div class="span3">
			<label>&nbsp;</label>
			<?php echo CHtml::submitButton(' 搜索 ',array('class' => 'btn search-button btn-primary')); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->