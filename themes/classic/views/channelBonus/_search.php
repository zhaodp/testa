<div class="well span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<div class="row span12">
		<div class="span3">
			<?php echo $form->label($model,'owner'); ?>
			<?php echo $form->textField($model,'owner',array('size'=>60,'maxlength'=>256)); ?>
		</div>
	
		<div class="span3">
			<?php echo $form->label($model,'channel_id'); ?>
			<?php echo $form->dropDownList($model,'channel_id', Dict::items('bonus_channel')); ?>
		</div>
	
		<div class="span3">
			<?php echo $form->label($model,'type_id'); ?>
			<?php echo $form->dropDownList($model,'type_id', BonusType::getBonusTypes()); ?>
		</div>
	
		<div class="span3">
			<label for="ChannelBonus_sn_start">Sn</label>
			<?php echo $form->textField($model,'sn_start'); ?>
		</div>
	</div>
	<div class="row span12">
		<div class="row span3">
			<?php echo CHtml::submitButton('Search'); ?>
		</div>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->