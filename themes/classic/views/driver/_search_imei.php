<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<div class="row-fluid">			
		<div class="span3">		
		<?php echo $form->label($model,'imei'); ?>
		<?php echo $form->textField($model,'imei',array('size'=>60,'maxlength'=>255)); ?>
		</div>
		<div class="span3 buttons">
			<div class="btn-group">
			<?php echo CHtml::submitButton('查询'); ?>
			</div>
		</div>
	</div>		

	
<?php $this->endWidget(); ?>

</div><!-- search-form -->