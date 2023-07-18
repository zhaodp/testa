<?php
$this->pageTitle = '司机台账历史';
?>

<h1><?php echo $this->pageTitle;?></h1>
<hr class="divider"/>

<?php 
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver_history_monthly', {
		data: $(this).serialize()
	});
	return false;
});
");

$this->beginWidget('zii.widgets.jui.CJuiDialog', array (
	'id'=>'view_driver_dialog', 
	// additional javascript options for the dialog plugin
	'options'=>array (
		'title'=>'查看司机信息', 
		'autoOpen'=>false, 
		'width'=>'780', 
		'height'=>'580', 
		'modal'=>true, 
		'buttons'=>array (
			'关闭'=>'js:function(){$("#view_driver_dialog").dialog("close");}'))));
echo '<div id="view_driver_dialog"></div>';
echo '<iframe id="view_driver_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$click_view = <<<EOD
function(){
	$("#view_driver_frame").attr("src",$(this).attr("href"));
	$("#view_driver_dialog").dialog("open");
	return false;
}
EOD;
?>
<div class="search-form">
<?php $this->renderPartial('_search_sum_history',array(
	'model'=>$model,
)); ?>
</div>
<?php //CGridView
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver_history_monthly',
	'dataProvider'=>$dataProvider,
	'cssFile'=>SP_URL_CSS . 'table.css',
	'itemsCssClass'=>'table table-condensed',
	'columns'=>array(
		array (
			'header'=>'工号', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'$data["user"]'
		), 
		array (
			'header'=>'姓名', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'value'=>'$data["name"]'
		), 
		array (
			'name'=>Dict::item("account_type", 0),
			'value'=>'$data["t0"]'
		), 
		array (
			'name'=>Dict::item("account_type", 1),
			'value'=>'$data["t1"]'
		), 
		array (
			'name'=>Dict::item("account_type", 2),
			'value'=>'$data["t2"]'
		), 
		array (
			'name'=>Dict::item("account_type", 3),
			'value'=>'$data["t3"]'
		), 
		array (
			'name'=>Dict::item("account_type", 4),
			'value'=>'$data["t4"]'
		), 
		array (
			'name'=>Dict::item("account_type", 5),
			'value'=>'$data["t5"]'
		), 
		array (
			'name'=>Dict::item("account_type", 6),
			'value'=>'$data["t6"]'
		), 
		array (
			'name'=>Dict::item("account_type", 7),
			'value'=>'$data["t7"] + $data["t10"]'
		), 
		array (
			'name'=>Dict::item("account_type", 8),
			'value'=>'$data["t8"] + $data["t9"]'
		), 
		array(
			'header'=>'账户余额',
			'value'=>'sprintf("%0.2f",$data["total"])'
		),
		array (
			'class'=>'CButtonColumn', 
			'template'=>'{view}', 
			'buttons'=>array (
				'view'=>array(
					'label'=>'查看',
					'url'=>'$this->grid->controller->createUrl("driver/archives",array("id"=>$data["user"]));',
                                        'options'=>array('target'=>'_blank'),
					)))		
	),
)); ?>
