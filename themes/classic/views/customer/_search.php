<?php
/* @var $this QuestionnaireController */
/* @var $model Questionnaire */
/* @var $form CActiveForm */
?>

<div class="well">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<span class='span3'>
			<?php echo $form->label($model, 'batch_id');?>
			<?php echo $form->dropDownList($model, 'batch_id', $batchlist)?>
		</span>
		
		<span class="span3">
			<?php echo $form->label($model,'name'); ?>
			<?php echo $form->textField($model,'name',array('size'=>20,'maxlength'=>20)); ?>
		</span>
		<span class="span3">
			<?php echo $form->label($model,'phone'); ?>
			<?php echo $form->textField($model,'phone',array('size'=>20,'maxlength'=>20)); ?>
		</span>
		<span class="span3">
			<?php echo $form->label($model,'status'); ?>
			<?php echo $form->dropDownList($model, 'status', array(''=>'全部','0'=>'未回访','1'=>'成功回访','2'=>'不记得使用过','3'=>'不方便','4'=>'未接通','5'=>'空号'));?>
		</span>
	</div>
	<div class="row" style="display:none;">
		<span class="span3">
			<label for="Questionnaire_again_time">回访开始时间</label>
			<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'Questionnaire[again_time_s]', 
				//'model'=>$model,  //Model object
				'value'=>'', 
				'mode'=>'datetime',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh'
			));
			?>
		</span>
		<span class="span3">
			<label for="Questionnaire_again_time">回访结束时间</label>
			<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'Questionnaire[again_time_e]', 
				//'model'=>$model,  //Model object
				'value'=>'', 
				'mode'=>'datetime',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh'
			));
			?>
		</span>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton(' 搜索 '); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->