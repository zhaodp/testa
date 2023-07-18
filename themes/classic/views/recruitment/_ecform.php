<?php
/* @var $this DriverExamController */
/* @var $model DriverExam */
/* @var $form CActiveForm */
?>

<div class="form span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'driver-exam-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>
	<div class="row">
		<div class="span4">
			<?php echo $form->labelEx($model,'type'); ?>
			<?php echo $form->dropDownList($model,'type', Dict::items('exam_type')); ?>
		</div>
		<div class="span3">
			<?php echo $form->labelEx($model,'city_id'); ?>
			<?php echo $form->dropDownList($model,'city_id', Dict::items('city')); ?>
		</div>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textArea($model,'title',array('cols'=>50,'rows'=>2,'maxlength'=>255,'style'=>'width:400px')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'a'); ?>
		<?php echo $form->textArea($model,'a',array('cols'=>50,'rows'=>2,'maxlength'=>255,'style'=>'width:400px')); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'b'); ?>
		<?php echo $form->textArea($model,'b',array('cols'=>50,'rows'=>2,'maxlength'=>255,'style'=>'width:400px')); ?>
		<?php echo $form->error($model,'b'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'c'); ?>
		<?php echo $form->textArea($model,'c',array('cols'=>50,'rows'=>2,'maxlength'=>255,'style'=>'width:400px')); ?>
		<?php echo $form->error($model,'c'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'d'); ?>
		<?php echo $form->textArea($model,'d',array('cols'=>50,'rows'=>2,'maxlength'=>255,'style'=>'width:400px')); ?>
		<?php echo $form->error($model,'d'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'correct'); ?>
		<?php echo $form->checkBoxList($model, 'correct', array('A'=>'A','B'=>'B','C'=>'C','D'=>'D'),array('separator'=>' ','template'=>'<div class="span1">{input} {label}</div>','labelOptions'=>array('style'=>'display:inline;')));?>
	</div>

	<div class="row"  style="display:none">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->dropDownList($model,'status',array('1'=>'正常','0'=>'屏蔽')); ?>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? ' 添加返回列表' : '保存返回列表',array('name'=>'submit_admin','id'=>'submit_admin')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

