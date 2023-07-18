<?php
$this->renderPartial('thirdCallCenter/_uploadFile',
	array(
		'model' => $model,
		'sourceList'	=> $sourceList,
	)
);
?>
<label> <?php echo '查询以前时间的数据'  ?> </label>
<div class="well span12" style="margin-left:0;">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'action' => Yii::app()->createUrl($this->route),
		'method' => 'get',
	));
	?>

	<div class="span12">
		<?php Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker'); ?>
		<div class="span3">
			<label>开始时间</label>
			<?php  $this->widget('CJuiDateTimePicker', array(
				'name' => 'timeStart',
				'value' =>  $timeStart,
				'mode' => 'date', //use "time","date" or "datetime" (default)
				'options' => array(
					'dateFormat' => 'yy-mm-dd'
				), // jquery plugin options
				'language' => 'zh',
				'htmlOptions' => array('class' => "span9")
			));?>
		</div>
		<div class="span3">
			<label>结束时间</label>
			<?php  $this->widget('CJuiDateTimePicker', array(
				'name' => 'timeEnd',
				'value' => $timeEnd,
				'mode' => 'date', //use "time","date" or "datetime" (default)
				'options' => array(
					'dateFormat' => 'yy-mm-dd'
				), // jquery plugin options
				'language' => 'zh',
				'htmlOptions' => array('class' => "span9")
			));?>
		</div>
		<div class="span3">
			<?php echo $form->label($model, '&nbsp;'); ?>
			<?php echo CHtml::submitButton('Search', array('class' => 'btn')); ?>
		</div>

	</div>
	<?php $this->endWidget(); ?>
</div>


<div class="row-fluid">
	<b>
		<?php echo $summary ?>;
	</b>
</div>

<?php
$this->renderPartial('thirdCallCenter/_tableView',
	array(
		'dataProvider' => $dataProvider,
	)
);
?>


