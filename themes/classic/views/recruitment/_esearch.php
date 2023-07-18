<?php
/* @var $this DriverExamController */
/* @var $model DriverExam */
/* @var $form CActiveForm */
?>

<div class="well span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<div class="row">
		<div class="span3">
			<?php echo $form->label($model,'title'); ?>
			<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		</div>
		<div class="span3">
			<?php echo $form->label($model,'city_id'); ?>
			<?php echo $form->dropDownList($model,'city_id',Dict::items('city')); ?>
		</div>
		<div class="span3">
			<?php echo $form->label($model,'type'); ?>
			<?php echo $form->dropDownList($model,'type', Dict::items('exam_type')); ?>
		</div>
		<div class="span3">
			<?php echo $form->label($model,'status'); ?>
			<?php echo $form->dropDownList($model,'status',array(''=>'全部','1'=>'正常','0'=>'屏蔽')); ?>
		</div>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->