<?php
/* @var $this DriverCardController */
/* @var $model DriverCard */
/* @var $form CActiveForm */
?>

<div class="well span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<section>
	<div class="row">
		<div class="span3">
			<?php echo $form->label($model,'user'); ?>
			<?php echo $form->textField($model,'user',array('size'=>10,'maxlength'=>10)); ?>
		</div>
	
		<div class="span3">
			<?php echo $form->label($model,'name'); ?>
			<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		</div>
	
		<div class="span3">
			<?php echo $form->label($model,'id_card'); ?>
			<?php echo $form->textField($model,'id_card',array('size'=>20,'maxlength'=>20)); ?>
		</div>
	</div>
	
	<div class="row">
		<div class="span3">
			<?php echo $form->label($model,'city_id'); ?>
			<?php echo $form->dropDownList($model, 'city_id', Dict::items('city'));?>
		</div>	
		<div class="span3">
			<?php echo $form->label($model,'status'); ?>
			<?php echo $form->dropDownList($model, 'status', array(''=>"全部",'0'=>"未考试",'1'=>"已通过"));?>
		</div>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>
</section>
<?php $this->endWidget(); ?>


</div><!-- search-form -->