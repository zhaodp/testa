<div class="span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'bonus-type-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>60)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'money'); ?>
		<?php echo $form->textField($model,'money'); ?>
		<?php echo $form->error($model,'money'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'channel'); ?>
		<?php echo $form->dropDownList($model,'channel', Dict::items('bonus_channel')); ?>
		<?php echo $form->error($model,'channel'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->dropDownList($model,'type', Dict::items('bonus_type')); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'sn_type'); ?>
		<?php echo $form->dropDownList($model,'sn_type', Dict::items('bonus_sn_type')); ?>
		<?php echo $form->error($model,'sn_type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sn_start'); ?>
		<?php echo $form->textField($model,'sn_start',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'sn_start'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'sn_end'); ?>
		<?php echo $form->textField($model,'sn_end',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'sn_end'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'issued'); ?>
		<?php echo $form->textField($model,'issued',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'issued'); ?>
	</div>	

	<div class="row">
		<?php echo $form->labelEx($model,'end_date'); ?>
		<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'BonusType[end_date]',
				'model'=>$model,  //Model object
				'value'=>date('Y-m-d', $model->end_date),
				'mode'=>'date',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh',
			));
		?>
		<?php echo $form->error($model,'end_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'is_limited'); ?>
		<?php echo $form->dropDownList($model,'is_limited', Dict::items('bonus_type_limit')); ?>
		<?php echo $form->error($model,'is_limited'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'remark'); ?>
		<?php echo $form->textArea($model,'remark',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'remark'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? '新建' : '更新'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->