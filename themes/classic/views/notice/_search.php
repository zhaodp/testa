<div class="well span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl('notice/admin'),
	'method'=>'get',
)); ?>

	<div class="span3">
		<?php echo $form->label($model,'class'); ?>
		<?php echo $form->dropDownList($model,'class',array('0'=>'全部','1'=>'培训','2'=>'制度','3'=>'奖惩','4'=>'通知'))?>
	</div>
	<div class="span3">

		<?php echo $form->label($model,'city_id')?>
		<?php echo $form->dropDownList($model,'city_id',Dict::items('city'))?>
	</div>
	<div class="span3">

		<?php echo $form->label($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>100)); ?>
	</div>
	<div class="span3">
		
		<label>是否有效</label>
		<select name='Notice[is_valid]' id='Notice[is_valid]'>
			<option value='0'>全部</option>
			<Option value='1'>是</Option>
			<option value='2'>否</option>
		</select>
	</div>

	<div class="span3">
		<?php echo $form->label($model,'created'); ?>		
		<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'Notice[created]', 
				'model'=>$model,  //Model object
				'value'=>'', 
				'mode'=>'date',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh'
			));
			?>	
	</div>

	<div class="span3 buttons">
		<label>&nbsp;</label>
		<?php echo CHtml::submitButton('Search', array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->