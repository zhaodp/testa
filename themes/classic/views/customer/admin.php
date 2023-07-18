<?php
/* @var $this QuestionnaireController */
/* @var $model Questionnaire */

$this->breadcrumbs=array(
	'Questionnaires'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('questionnaire-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_exam_dialog', 
	// additional javascript options for the dialog plugin
	'options'=>array (
		'title'=>'问卷详情', 
		'autoOpen'=>false, 
		'width'=>'780', 
		'height'=>'580', 
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

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'update_exam_dialog', 
	// additional javascript options for the dialog plugin
	'options'=>array (
		'title'=>'问卷调查信息', 
		'autoOpen'=>false, 
		'width'=>'780', 
		'height'=>'580', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function() {closedDialog("update_exam_dialog")}'))));
echo '<div id="update_exam_dialog"></div>';
echo '<iframe id="update_exam_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<h1>问卷调查</h1>
<div class="btn-group">
	<?php echo CHtml::link('高级搜索','#',array('class'=>'search-button btn-primary btn')); ?>
	<?php echo CHtml::link('问卷统计', array("customer/admincustomer"),array('class'=>"btn"));?>
	<?php echo CHtml::link('批次管理', array('customerVisitBatch/index'),array('class'=>'btn'));?>
</div>

<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
	'batchlist' =>$batchlist
)); ?>
</div><!-- search-form -->

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
				'name'=>'name',
				'headerHtmlOptions'=>array (
						'width'=>'20px',
						'nowrap'=>'nowrap'
				),
				'value'=>'$data->name'
		),
		array (
				'name'=>'phone',
				'headerHtmlOptions'=>array (
						'width'=>'20px',
						'nowrap'=>'nowrap'
				),
				'value'=>'Common::parseCustomerPhone($data->phone)'
		),
		array (
				'name'=>'created',
				'headerHtmlOptions'=>array (
						'width'=>'20px',
						'nowrap'=>'nowrap'
				),
				'value'=>'(($data->created == "0000-00-00 00:00:00") ? "" : $data->created)'
		),
		array (
				'name'=>'visit_time',
				'headerHtmlOptions'=>array (
						'width'=>'20px',
						'nowrap'=>'nowrap'
				),
				'value'=>'($data->visit_time == "0000-00-00 00:00:00" && $data->status >= 2)?"拒绝访问":(($data->visit_time == "0000-00-00 00:00:00") ? "" : $data->visit_time)'
		),
		array (
				'name'=>'status',
				'headerHtmlOptions'=>array (
						'width'=>'20px',
						'nowrap'=>'nowrap'
				),
				'value'=>array($this,'CustomerStatus')
		),
		/*
		'created',
		*/
		array(
			'name'=>'回访',
			'headerHtmlOptions'=>array (
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'CustomerStatusHtml')
		),
		array(
			'header'=>'操作',
            'class'=>'CButtonColumn',
            'template'=>'{view}',
            'buttons'=>array(		
                         'view'=> array(
                                'label'=>'查看',
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
function closedDialogAjax(id){
	$("#"+id).dialog("close");
	$.fn.yiiGridView.update('driver-exam-grid');
	window.location.reload();
}
function openDialog(id){
	$("#update_exam_frame").attr("src",id);
	$("#update_exam_dialog").dialog("open");
}
</script>
