<?php
/* @var $this HolidayController */
/* @var $model Holiday */
/* @var $form CActiveForm */
?>

<div class="form span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'holiday-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'holiday'); ?>
		<?php
		Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
		$this->widget('CJuiDateTimePicker', array (
			'name'=>'Holiday[holiday]',
			'model'=>$model,
			'value'=>$model->holiday, 
			'mode'=>'date',  //use "time","date" or "datetime" (default)
			'options'=>array (
				'dateFormat'=>'yy-mm-dd'
			),  // jquery plugin options
			'language'=>'zh'
		));
	?>
	</div>

	<div class="row">
		<?php
			$selStatus=isset($model->status)?$model->status:0;
			$status = array('0'=>'屏蔽','1'=>'不屏蔽 '); 
			echo CHtml::label('状态选择','status'); 
			echo CHtml::dropDownList('Holiday[status]',
						$selStatus,
						$status,
				array()
			); 
		?>		
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

