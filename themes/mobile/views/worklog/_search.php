<div style="float:right;">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
	'htmlOptions'=>array('class'=>'form-search')
)); 
?>
<?php 
foreach($datelist as $item){
	echo CHtml::link($item['title'], array("worklog/index", "WorkLog"=>array("snap_time"=>$item['begin'],"type"=>$item['title'])),array('class'=>"btn"));
}
?>
<?php
	Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
	$this->widget('CJuiDateTimePicker', array (
		'name'=>'WorkLog[snap_time]', 
		'model'=>$model,  //Model object
		'value'=>'', 
		'mode'=>'date',  //use "time","date" or "datetime" (default)
		'options'=>array (
			'dateFormat'=>'yy-mm-dd'
		),  // jquery plugin options
		'language'=>'zh',
		'htmlOptions'=>array('style'=>'width:100px;margin:0px 2px 0px 2px;')
	));
?>
<?php echo CHtml::submitButton('查询',array('class'=>'btn btn-warning')); ?>
<?php $this->endWidget(); ?>
</div>