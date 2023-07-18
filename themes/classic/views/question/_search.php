<?php
/* @var $this QuestionController */
/* @var $model Question */
/* @var $form CActiveForm */
?>

<div class="search-form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<div class="row-fluid">
	<div class="span3">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="span3">
		<?php echo $form->label($model,'type'); ?>
		<?php echo $form->textField($model,'type',array('size'=>20,'maxlength'=>20)); ?>
	</div>

	<div class="span3">
		<?php echo $form->label($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>100)); ?>
	</div>

	<div class="span3">
		<?php echo $form->label($model,'question_type'); ?>
		<?php echo $form->textField($model,'question_type'); ?>
	</div>

<?php $this->endWidget(); ?>
</div>
<div class="row-fluid">
<?php echo CHtml::submitButton('搜索',array('class'=>'btn btn-success')); ?>
</div>
</div><!-- search-form -->