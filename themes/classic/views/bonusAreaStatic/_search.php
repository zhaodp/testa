<div class="well span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<div class="row span12">
		<div class="span3">
			<?php echo $form->label($model,'bonus_type_id'); ?>
			<?php echo $form->dropDownList($model,'bonus_type_id', $this->getBonusList()); ?>
		</div>
		<div class="span3">
			<?php echo $form->label($model,'bonus_sn'); ?>
			<?php echo $form->textField($model,'bonus_sn',array('size'=>60,'maxlength'=>60)); ?>
		</div>
	</div>

		<div class="row span3">
			<?php echo CHtml::submitButton('Search'); ?>
		</div>
	

<?php $this->endWidget(); ?>

</div><!-- search-form -->
<?php echo CHtml::Button('下载当前数据到excel',array('class'=>'btn btn-success','id'=>'down_excel_btn')); ?>