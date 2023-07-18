<?php
/* @var $this CustomerVisitBatchController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Customer Visit Batches',
);

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_exam_dialog', 
	// additional javascript options for the dialog plugin
	'options'=>array (
		'title'=>'批次管理', 
		'autoOpen'=>false, 
		'width'=>'680', 
		'height'=>'480', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){closedDialog("view_exam_dialog")}'))));
echo '<div id="view_exam_dialog"></div>';
echo '<iframe id="view_exam_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$click_view = <<<EOD
function(){
	$("#view_exam_frame").attr("src",$(this).attr("href"));
	$("#view_exam_dialog").dialog("open");
	return false;
}
EOD;

?>

<h1>批次管理</h1>
<div class="btn-group">
	<?php echo CHtml::link('问卷调查', array('customer/admin'),array('class'=>'btn'));?>
	<?php echo CHtml::link('问卷统计', array("customer/admincustomer"),array('class'=>"btn"));?>
	<?php echo CHtml::link('批次管理','#',array('class'=>'search-button btn-primary btn')); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'questionnaire-grid',
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
	'columns'=>array(
		array (
				'name'=>'id',
				'headerHtmlOptions'=>array (
						'width'=>'20px',
						'nowrap'=>'nowrap'
				),
				'value'=>'$data->id'
		),
		array (
				'name'=>'batch',
				'headerHtmlOptions'=>array (
						'width'=>'20px',
						'nowrap'=>'nowrap'
				),
				'value'=>'$data->batch'
		),
		array(
				'name'=>'城市',
				'headerHtmlOptions'=>array(
						'width'=>'20px',
						'nowrap'=>'nowrap',
				),
				'value'=>'Dict::item("city",$data->city_id)',
		),
		array(
				'name'=>'comment',
				'headerHtmlOptions'=>array(
						'width'=>'20px',
						'nowrap'=>'nowrap',
				),
		),
		array (
				'name'=>'type',
				'headerHtmlOptions'=>array (
						'width'=>'20px',
						'nowrap'=>'nowrap'
				),
				'value'=>'$data->type == 0 ? "调查中..." : "调查完毕"'
		),
		array(
				'header'=>'导入数据',
				'headerHtmlOptions'=>array(
						'width'=>'20px',
						'nowrap'=>'nowrap',
				),
				'type'=>'raw',
				'value'=>array($this,'CustomerImportHtml'),
		),
		array (
				'header'=>'操作',
				'class'=>'CButtonColumn',
				'template'=>'{viewed} {updated}',
				'buttons'=>array(
					'updated'=>array('label'=>'修改批次','url'=>'$this->grid->controller->createUrl("customerVisitBatch/update", array("id"=>$data->id))','click'=>$click_view),
					'viewed'=>array(
						'label'=>'查看统计','url'=>'$this->grid->controller->createUrl("customerVisitBatch/view",array("id"=>$data->id))','click'=>$click_view
					),
				),
		),
	),
)); ?>
<div class='btn-group'>
<?php echo CHtml::link('添加批次',array('customerVisitBatch/create'),array('onclick'=>'$("#view_exam_frame").attr("src",$(this).attr("href"));$("#view_exam_dialog").dialog("open"); return false;','class'=>'search-button btn-primary btn'));?>
</div>
<script language='javascript' type='text/javascript'>
function closedDialog(id){
	$("#"+id).dialog("close");
	$.fn.yiiGridView.update('driver-exam-grid');
	$("#view_exam_frame").attr("src","");
}
function closedDialogAjax(id){
	$("#"+id).dialog("close");
	$.fn.yiiGridView.update('driver-exam-grid');
	window.location.reload();
}
</script>
