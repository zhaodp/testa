
<h1>APILOG</h1>



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
			'name' => 'CODE',
			'type' => 'raw',
			'value' => 'CHtml::link($data["code"], "javascript:void(0);", array (
			"onclick"=>"{view($data[id])}"));'
		),
			array (
					'name' => 'TYPE',
					'type' => 'raw',
					'value' => 'CHtml::link($data["type"], "javascript:void(0);", array (
			"onclick"=>"{view($data[id])}"));'
			),
			array (
					'name' => 'FILE',
					'type' => 'raw',
					'value' => 'CHtml::link($data["file"], "javascript:void(0);", array (
			"onclick"=>"{view($data[id])}"));'
			),
			array (
					'name' => 'MESSAGE',
					'type' => 'raw',
					'value' => 'substr($data["message"],0,50)."..."'
			),
			array (
					'name' => 'CREATED',
					'type' => 'raw',
					'value' => '$data["created"]'
			),
	),
)); 


?>
<script>

function view(id){

	$(".ui-dialog-title").html("错误信息");
	url = '<?php echo Yii::app()->createUrl('/apiLog/view');?>&id='+id+'&type=1';
	$("#view_informexam_frame").attr("src",url);
	$("#mydialog").dialog("open");
}
</script>