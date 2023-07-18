<?php
$this->pageTitle = '司机日记账流水历史';
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
	$.fn.yiiGridView.update('driver_account_history', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="search-form">
<?php $this->renderPartial('_search_account_history',array(
	'model'=>$model,
)); ?>
</div>
<?php //CGridView
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id'=>'mydialog',
    // additional javascript options for the dialog plugin
    'options'=>array(
        'title'=>'订单信息',
        'autoOpen'=>false,
		'width'=>'750',
		'height'=>'450',
		'modal'=>true,
		'buttons'=>array(
        	'Close'=>'js:function(){$("#mydialog").dialog("close");}'
		),
    ),
));
echo '<div id="dialogdiv"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver_account_history',
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped ',
	'columns'=>array(
		array (
			'name'=>'城市',
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'value'=>'Dict::item("city", $data["city_id"])'
		), 
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
			'name'=>'交易类型', 
			'value'=>array($this, 'getAccountHistoryBonusType')
		),
		array (
			'name'=>'收/支金额（元）', 
			'value'=>'sprintf("%0.2f",$data["cast"])'),
		array (
			'name'=>'备注',
			'value'=>'$data["comment"]'
		), 
		array (
			'name'=>'订单流水号',
			'type'=>'raw',
			'value'=>array($this, 'getAccountHistoryOrderLink')
		), 	
		array (
			'name'=>'报单日期',
			'value'=>'date("Y-m-d H:i:s",$data["created"])'
		) 
	),
)); ?>


<script>
function orderDialogdivInit(orderId){
	$('#dialogdiv').html("<img src='<?php echo SP_URL_IMG;?>loading.gif' />");
	$.ajax({
		'url':'<?php echo Yii::app()->createUrl('/order/view');?>',
		'data':'id='+orderId,
		'type':'get',
		'success':function(data){
			$('#dialogdiv').html(data);
		},
		'cache':false		
	});
	jQuery("#mydialog").dialog("open");
	return false;
}
</script>
