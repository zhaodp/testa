
<h1>YIILOG</h1>



<?php 

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'错误信息',
        'autoOpen'=>false,
		'width'=>'800',
		'height'=>'600',
		'modal'=>true,
		'buttons'=>array(
        	'Close'=>'js:function(){$("#mydialog").dialog("close");}'
		),
    ),
));
echo '<div id="dialogdiv"></div>';
echo '<iframe id="view_informexam_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-zhaopin-grid',
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
	'columns'=>array(
		
		array(
			'name'=>'ID',
			'type' => 'raw',
			'value' => 'CHtml::link($data["id"], "javascript:void(0);", array (
			"onclick"=>"{view($data[id])}"));'
        ),
		array (
			'name' => 'level',
			'type' => 'raw',
			'value' => 'CHtml::link($data["level"], "javascript:void(0);", array (
			"onclick"=>"{view($data[id])}"));'
		),
			array (
					'name' => 'category',
					'type' => 'raw',
					'value' => 'CHtml::link($data["category"], "javascript:void(0);", array (
			"onclick"=>"{view($data[id])}"));'
			),
			array (
					'name' => 'logtime',
					'type' => 'raw',
					'value' => 'CHtml::link($data["logtime"], "javascript:void(0);", array (
			"onclick"=>"{view($data[id])}"));'
			),
			array (
					'name' => 'MESSAGE',
					'type' => 'raw',
					'value' => 'substr($data["message"],0,100)."..."'
			),
			
	),
)); 


?>
<script>

function view(id){

	$(".ui-dialog-title").html("错误信息");
	url = '<?php echo Yii::app()->createUrl('/apiLog/view');?>&id='+id+'&type=2';
	$("#view_informexam_frame").attr("src",url);
	$("#mydialog").dialog("open");
}
</script>