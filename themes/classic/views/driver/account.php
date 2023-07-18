<?php
$this->pageTitle = '司机日记账流水';
?>

<h1>司机日记账流水</h1>
<hr class="divider"/>

<?php 
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('driver-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<div class="search-form">
<?php $this->renderPartial('_search_account',array(
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


$dataProvider = $model->search();
$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-grid',
	'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped ',
	'columns'=>array(
		array (
			'name'=>'city_id',
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'value'=>'Dict::item("city", $data->city_id)'
		), 
		array (
			'header'=>'工号', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'CHtml::link($data->user, array("driver/account", "Account"=>array("user"=>$data->user)))'
		), 
		array (
			'header'=>'姓名', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'value'=>'Driver::getProfile($data->user)->name'
		), 
		array (
			'name'=>'交易类型', 
			'value'=>array($this, 'getAccountType')
		),
		array (
			'name'=>'收/支金额（元）', 
			'value'=>'sprintf("%0.2f",$data->cast)'),
		array (
			'name'=>'comment'
		), 
		array (
			'name'=>'order_id',
			'type'=>'raw',
			'value'=>'$data->order_id ? CHtml::link($data->order_id, array("order/view","id"=>$data->order_id), array ("target"=>"_blank",
					"onclick"=>"{//orderDialogdivInit($data->order_id);}")) : ""'
		), 	
		array (
			'name'=>'created',
			'value'=>'date("Y-m-d H:i:s",$data->created)'
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
