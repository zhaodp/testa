<?php
$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_exam_dialog', 
	// additional javascript options for the dialog plugin
	'options'=>array (
		'title'=>'上传司机头像', 
		'autoOpen'=>false, 
		'width'=>'400', 
		'height'=>'300', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){closedDialog("view_exam_dialog")}'))));
echo '<div id="view_exam_dialog"></div>';
echo '<iframe id="view_exam_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-zhaopin-grid',
	'dataProvider'=>$data,
	'itemsCssClass'=>'table table-striped',
	'pager'=>false,
	'template'=>'{items}',
	'columns'=>array(
		array(
			'name'=>'报名流水号',
			'type' => 'raw',
			'value'=>'$data->id',
        ),
        array (
			'name' => 'name',
			'type' => 'raw',
			'value' => '$data->name'
		),
        array(
			'name'=>'工号',
			'type' => 'raw',
			'value'=>'$data->driver_id',
        ),
        array(
			'name'=>'工作号码',
			'type' => 'raw',
			'value'=>'$data->driver_phone',
        ),
        'imei',
        'id_card',
		array(
			'name'=>'上传图片',
			'type'=>'raw',
			'value'=>array($this,'entryDisplay'),		
        ),
	),
)); 
?>
<script>
function closedDialog(id){
	$("#"+id).dialog("close");
	$.fn.yiiGridView.update('driver-exam-grid');
}
function openDialog(href){
	$("#view_exam_frame").attr("src",href);
	$("#view_exam_dialog").dialog("open");
	return false;
}
</script>