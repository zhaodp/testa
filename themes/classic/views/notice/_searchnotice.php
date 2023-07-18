<div class="span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl('notice/index'),
	'method'=>'GET',
)); ?>
	<div class="span3">
		<?php echo $form->label($model,'title'); ?>
		<?php echo $form->textField($model, 'title',array('size'=>60,'maxlength'=>100));?>
	</div>

	<?php
		if ($is_city == 1){ 
	?>
	<div class='span3' style='display:none;'>
	<?php
		}else {
	?>
	<div class='span3'>
	<?php
		}
	?>
		<?php echo $form->label($model,'city_id')?>
		<?php echo $form->dropDownList($model,'city_id',Dict::items('city'));?>
	</div>
	
	<?php
		if ($is_city == 1){
			?>
		<div class='span3' style='display:none;'>
			<?php
		} else {
			?>
		<div class='span3'>	
			<?php 
		}
	?>
	
		<?php echo $form->label($model,'class')?>
		<?php echo $form->dropDownList($model,'class',array('0'=>'全部','1'=>'培训','2'=>'制度','3'=>'奖惩','4'=>'通知'))?>
	</div>
	
	<div class="span3 buttons">
		<label>&nbsp;</label>
		<?php echo CHtml::submitButton('搜索公告',array('class'=>'search-button btn-primary btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->