<?php
/* @var $this VipController */
/* @var $model VipCard */
/* @var $form CActiveForm */
?>

<div class="well span12" style="border:0px">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<div class="row span12">
		<div class="span3">
			<?php echo $form->label($model,'id'); ?>
			<?php echo $form->textField($model,'id'); ?>
		</div>
	
		<div class="span3">
			<?php echo $form->label($model,'money'); ?>
			<?php echo $form->textField($model,'money',array('size'=>50,'maxlength'=>50)); ?>
		</div>
	
		<div class="span3">
			<?php echo $form->label($model,'status'); ?>
			<?php echo $form->dropDownList($model,
							'status',
							array(
								'' =>'全部',
								'0'=>'未售出',
								'1'=>'未激活',
								'2'=>'已激活',	
							),
				array('class'=>"span12")
			);?>
		</div>
	</div>
	<div class="row span12">
			<?php echo CHtml::submitButton('Search'); ?>
		</div>
<?php $this->endWidget(); ?>

</div><!-- search-form -->