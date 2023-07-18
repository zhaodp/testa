<?php
/* @var $this CustomerExamController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Customer Exams',
);

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_exam_dialog', 
	'options'=>array (
		'title'=>'考卷管理', 
		'autoOpen'=>false, 
		'width'=>'600', 
		'height'=>'420', 
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

<h1>答卷管理</h1>
<div class="btn-group">
	<?php echo CHtml::link('答卷管理', '#',array('class'=>"search-button btn-primary btn"));?>
	<?php echo CHtml::link('题库管理', array('question/index'),array('class'=>'btn'));?>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'questionnaire-grid',
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
	'columns'=>array(
		array(
			'name'=>'id',
			'headerHtmlOptions'=>array(
				'width'=>'20px',
				'nowrap'=>'nowrap',
			),
			'value'=>'$data->id',
		),
		array(
			'name'=>'exam_title',
			'headerHtmlOptions'=>array(
				'width'=>'20px',
				'nowrap'=>'nowrap',
			),
			'value'=>'$data->exam_title',
		),
		array(
			'name'=>'type',
			'headerHtmlOptions'=>array(
				'width'=>'20px',
				'nowrap'=>'nowrap',
			),
			'value'=>'$data->type == 1 ? "调查问卷" : "司机考卷"'
		),
		array(
			'name'=>'created',
			'headerHtmlOptions'=>array(
				'width'=>'20px',
				'nowrap'=>'nowrap',
			),
			'value'=>'$data->created',
		),
		array(
			'header'=>'操作',
			'class'=>'CButtonColumn',
			'template'=>'{view} {update}',
			'buttons'=>array(
				'view'=>array('url'=>'$this->grid->controller->createUrl("customerExam/view",array("id"=>$data->id))','click'=>$click_view),
			),
		),
	),
)); ?>

<div class='btn-group'>
	<?php echo CHtml::link('添加新答卷', array('customerExam/create'),array('class'=>'search-button btn-primary btn'))?>
	<!--<?php echo CHtml::link('自动生成打印司机考卷', array('customerExam/createExam'),array('onclick'=>'$("#view_exam_frame").attr("src",$(this).attr("href"));$("#view_exam_dialog").dialog("open"); return false;','class'=>'search-button btn-primary btn'))?>
--></div>
<script language='javascript' type='text/javascript'>
function closedDialog(id){
	$("#"+id).dialog("close");
	$.fn.yiiGridView.update('driver-exam-grid');
	$("#view_exam_frame").attr("src","");
}
</script>