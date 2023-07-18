<?php
/* @var $this DriverBatchController */
/* @var $model DriverBatch */

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-batch-grid', {
		data: $(this).serialize()
	});
	return false;
});
");


$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_exam_dialog', 
	// additional javascript options for the dialog plugin
	'options'=>array (
		'title'=>'导入信息', 
		'autoOpen'=>false, 
		'width'=>'480', 
		'height'=>'380', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){closedDialog("view_exam_dialog")}'))));
echo '<div id="view_exam_dialog"></div>';
echo '<iframe id="view_exam_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

?>

<h1>司机批次管理</h1>
<?php echo CHtml::link('高级搜索','#',array('class'=>'search-button  btn-primary btn')); ?>
&nbsp;
<?php echo CHtml::link('添加批次',Yii::app()->createUrl('zhaopin/driverbatchcreate'),array('class'=>'btn'));?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_searchdriverbatch',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-batch-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-striped',
//	'filter'=>$model,
	'columns'=>array(
		'id',
		array(
			'name'=>'driver_batch',
			'headerHtmlOptions'=>array (
					'nowrap'=>'nowrap'
			),
			'value'=>array($this,'batch_info'),
		),
		array(
			'name'=>'城市',
			'headerHtmlOptions'=>array (
					'nowrap'=>'nowrap'
			),
			'value'=>'Dict::item("city", $data->city_id)'
		),
		array(
			'name'=>'类型',
			'headerHtmlOptions' => array(
				'nowrap' => 'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'driverBatchType'),
		),
		array(
			'name' => '总数量',
			'headerHtmlOptions' => array(
				'nowrap' => 'nowrap'
			),
			'type'=>'raw',
			'value' => '$data->entrycount == 0 ? "未分配" : $data->entrycount',
		),
		array(
			'name' => '已签约',
			'headerHtmlOptions' => array(
				'nowrap' => 'nowrap'
			),
			'type'=>'raw',
			'value' => '$data->entrynum',
		),
		array(
			'name' => '管理操作',
			'headerHtmlOptions' => array(
				'width' => '360',
				'nowrap' => 'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'driverBatchLink'),
		),
	),
)); ?>

<script type="text/javascript">
function closedDialog(id){
	$("#"+id).dialog("close");
	$.fn.yiiGridView.update('driver-batch-grid');
}
function openDialog(href){
	$("#view_exam_frame").attr("src",href);
	$("#view_exam_dialog").dialog("open");
	return false;
}
function entry(batch){
	 $.ajax({
			'url':'<?php echo Yii::app()->createUrl('/zhaopin/driverentry');?>',
			'data':'batch='+batch,
			'type':'get',
			'beforeSend':function(){
				$("#entry"+batch).button('loading');
			},
			'success':function(data){
				$("#Recycle"+batch).button("reset");
				if(data==0)
					alert("没有找到要签约的司机");
				else if(data == -1)
					alert("司机信息有问题");
				else
					alert("已有"+data+"个司机签约。");
				$.fn.yiiGridView.update('driver-batch-grid');
			},
			'cache':false
		});
}

function activation(batch){
	 $.ajax({
			'url':'<?php echo Yii::app()->createUrl('/zhaopin/batchactivation');?>',
			'data':'batch='+batch,
			'type':'get',
			'beforeSend':function(){
				$("#activation"+batch).button('loading');
			},
			'success':function(data){
				$("#activation"+batch).button("reset");
				if(data==0)
					alert("没有激活的司机！");
				else
					alert("已激活。");
				
			},
			'cache':false		
		});
}

function driverZhaopinRecycle(batch){
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/zhaopin/driverZhaopinRecycle');?>',
		'data':'batch='+batch,
		'type':'get',
		'beforeSend':function(){
			$("#Recycle"+batch).button('loading');
		},
		'success':function(data){
			alert(data);
			$("#Recycle"+batch).button("reset");
		},
		'cache':false		
	});
}

</script>
