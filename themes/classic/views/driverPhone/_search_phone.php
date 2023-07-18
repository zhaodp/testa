<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<div class="row-fluid">
		<div class="span3">		
			<?php echo $form->label($model,'driver_id'); ?>
			<?php echo $form->textField($model,'driver_id',array('size'=>60,'maxlength'=>255, 'value' => '')); ?>
		</div>
		<div class="span3">		
			<?php echo $form->label($model,'phone'); ?>
			<?php echo $form->textField($model,'phone',array('size'=>60,'maxlength'=>255, 'value' => '')); ?>
		</div>		
		<div class="span3">		
			<?php echo $form->label($model,'imei'); ?>
			<?php echo $form->textField($model,'imei',array('size'=>60,'maxlength'=>255)); ?>
		</div>
		<div class="span3">		
			<?php echo $form->label($model,'simcard'); ?>
			<?php echo $form->textField($model,'simcard',array('size'=>60,'maxlength'=>255)); ?>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span3">	
			<?php echo CHtml::submitButton('查询',array('class'=>'btn span10')); ?>
		</div>
	</div>		

	
<?php $this->endWidget(); ?>

</div><!-- search-form -->