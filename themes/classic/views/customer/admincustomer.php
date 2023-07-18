<?php
/* @var $this CustomerQuestionController */
/* @var $model CustomerQuestion */

$this->breadcrumbs=array(
	'Customer Questions'=>array('index'),
	'Manage',
);

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_exam_dialog', 
	// additional javascript options for the dialog plugin
	'options'=>array (
		'title'=>'问题详情统计', 
		'autoOpen'=>false, 
		'width'=>'600', 
		'height'=>'470', 
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

<h1>问题详情统计</h1>
<div class="btn-group">
	<?php echo CHtml::link('问卷调查',array("customer/admin"),array('class'=>'btn')); ?>
	<?php echo CHtml::link('问卷统计', array("customer/admincustomer"),array('class'=>'search-button btn-primary btn'));?>
	<?php echo CHtml::link('批次管理', array('customerVisitBatch/index'),array('class'=>'btn'));?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'customer-question-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-striped',
	'columns'=>array(
		'id',
		'title',
		array(
			'header'=>'操作',
			'class'=>'CButtonColumn',
			'template'=>'{view}',
			'buttons'=>array(
				'view' => array(
					'label'=> '查看',
					'url'=>'Yii::app()->controller->createUrl("customer/viewcustomer",array("id"=>$data->id))',
					'click'=>$click_view,
				),
            ),
		),
	),
)); ?>
<script type="text/javascript">
function closedDialog(id){
	$("#"+id).dialog("close");
	$.fn.yiiGridView.update('driver-exam-grid');
	$("#view_exam_frame").attr("src","");
}
</script>