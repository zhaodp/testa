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
	<table>
		<tr>
			<td><?php echo $form->labelEx($model,'type'); ?></td>
			<td><?php echo $form->dropDownList($model,'type', Dict::items('exam_type')); ?></td>
		</tr>
		<tr>
			<td><?php echo $form->labelEx($model,'city_id'); ?></td>
			<td><?php echo $form->dropDownList($model,'city_id', Dict::items('city')); ?></td>
		</tr>		
		<tr>
			<td><?php echo $form->labelEx($model,'title'); ?></td>
			<td><?php echo $form->textArea($model,'title',array('cols'=>50,'rows'=>2,'maxlength'=>255)); ?></td>
		</tr>

	<tr>
		<td><?php echo $form->labelEx($model,'a'); ?></td>
		<td><?php echo $form->textArea($model,'a',array('cols'=>50,'rows'=>2,'maxlength'=>255)); ?></td>
	</tr>

	<tr>
		<td><?php echo $form->labelEx($model,'b'); ?></td>
		<td><?php echo $form->textArea($model,'b',array('cols'=>50,'rows'=>2,'maxlength'=>255)); ?></td>
	</tr>

	<tr class="row">
		<td><?php echo $form->labelEx($model,'c'); ?></td>
		<td><?php echo $form->textArea($model,'c',array('cols'=>50,'rows'=>2,'maxlength'=>255)); ?></td>
	</tr>

	<tr>
		<td><?php echo $form->labelEx($model,'d'); ?></td>
		<td><?php echo $form->textArea($model,'d',array('cols'=>50,'rows'=>2,'maxlength'=>255)); ?></td>
	</tr>

	<tr>
		<td><?php echo $form->labelEx($model,'correct'); ?></td>
		<td><?php echo $form->checkBoxList($model, 'correct', array('A'=>'A','B'=>'B','C'=>'C','D'=>'D'),array('separator'=>' ','template'=>'{input} {label}'));?></td>
	</tr>
	</table>
	<div class="row"  style="display:none">
		<td><?php echo $form->labelEx($model,'status'); ?></td>
		<td><?php echo $form->dropDownList($model,'status',array('1'=>'正常','0'=>'屏蔽')); ?></td>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? ' 添加返回列表' : '保存返回列表',array('name'=>'submit_admin','id'=>'submit_admin')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

