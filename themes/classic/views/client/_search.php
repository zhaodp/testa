<?php
/* @var $this CallHistoryController */
/* @var $model CallHistory */
/* @var $form CActiveForm */
?>

<div class="well span12">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<div class="span12">
	<div class="row ">
		<div class="span3">
			<?php echo $form->label($model,'phone'); ?>
			<?php echo $form->textField($model,'phone',array('size'=>21,'maxlength'=>21)); ?>
		</div>		<div class="span3">
			<?php echo $form->label($model,'user'); ?>
			<input size="21" maxlength="21" name="CallHistory[user]" id="CallHistory_user" type="text">
		</div>
		<div class="span3">
			<?php echo $form->label($model,'cPhone'); ?>
			<input size="21" maxlength="21" name="CallHistory[cphone]" id="cphone" type="text">
		</div>
	</div>
	<div class="row ">
		<div class="span3">
			<?php echo $form->label($model,'s_time'); ?>
			<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'CallHistory[sdate]', 
				//'model'=>$model,  //Model object
				'value'=>'', 
				'mode'=>'datetime',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh'
			));
			?>
		</div>
		<div class="span3">
			<?php echo $form->label($model,'e_time'); ?>
			<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'CallHistory[edate]', 
				//'model'=>$model,  //Model object
				'value'=>'', 
				'mode'=>'datetime',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh'
			));
			?>
		</div>
		
		<div class="span3">
		<?php echo $form->label($model,'type'); ?>
		<select class="span12" name="CallHistory[type]" id="CallHistory_type">
			<option value="" selected="selected">全部</option>
			<option value="0" >呼入</option>
			<option value="1">呼出</option>
			<option value="2">未接</option>
		</select>
		</div>
	</div>
	<div class="row ">
		<?php echo CHtml::submitButton('Search',array('class'=>'btn span2')); ?>
	</div>
</div>
<?php $this->endWidget(); ?>

</div><!-- search-form -->