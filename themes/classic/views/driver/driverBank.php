<?php
/* @var $this DriverRecommandController */
/* @var $model DriverRecommand */

$this->breadcrumbs=array(
	'Driver Recommands'=>array('index'),
	'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#driver-recommand-grid').yiiGridView('update', {
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

<h2>财务扣款管理</h2>

<?php 
$url_import = Yii::app()->createUrl('/driver/driverBankImport');
$url_backimport = Yii::app()->createUrl('/driver/bankFeedbackImport');
echo CHtml::link('导入签约司机','javascript:;',array('onClick'=>'openDialog(\''.$url_import.'\')','class'=>'btn search-button')).'&nbsp;'; 
echo CHtml::link('正式生成划款司机名单',Yii::app()->createUrl('/driver/driverbankexport'),array('class' => 'btn search-botton')).'&nbsp;';
echo CHtml::link('导入银行返回信息','javascript:;',array('onClick'=>'openDialog(\''.$url_backimport.'\')','class'=>'btn search-button')).'&nbsp;';
echo CHtml::link('查看银行反馈信息',Yii::app()->createUrl('/driver/bankFeedbackList'),array('class'=>'btn button'));
echo CHtml::link('参考生成划款司机名单',Yii::app()->createUrl('/driver/driverbankexports'),array('class' => 'btn search-botton')).'&nbsp;';
?>
<div class="search-form">
    <?php
    $this->renderPartial('_search_driver_bank', array(
        'model' => $model));
    ?>
</div>
<!-- search-form -->


<?php
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-recommand-grid',
	'itemsCssClass'=>'table table-striped', 
	'dataProvider'=>$model->search(),
	'columns'=>array(
		'id',
		'sign_no',
		'pay_no',
		'pay_name',
		'fees_name',
		'driver_id',
		/*
		'amount',
		'remark',

		'created',

		array(
			'class'=>'CButtonColumn',
			'template'=>'{update} {delete}',
		),
				*/
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
</script>
