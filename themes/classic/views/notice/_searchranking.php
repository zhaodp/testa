<?php
/* @var $this RankDayListController */
/* @var $model RankDayList */
/* @var $form CActiveForm */
?>

<div class="span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="span3">
		<?php echo $form->label($model,'city_id'); ?>
		<?php echo $form->dropDownList($model, 'city_id', Dict::items('city'))?>
	</div>
	
	<div class="span3">
		<label>排行类型</label>
		<?php echo $form->dropDownList($model, 'current_day', array('0'=>'日排行','1'=>'月排行'));?>
	</div>

	<div class="span3 buttons">
		<label>&nbsp;</label>
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->