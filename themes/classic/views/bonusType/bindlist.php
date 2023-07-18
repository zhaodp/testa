<?php
$this->pageTitle = '优惠券绑定列表';

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('bonus-type-bind-list-grid', {
		data: $(this).serialize()
	});
	return false;
});
");

?>

<h1><?php echo $this->pageTitle; ?></h1>

<?php echo CHtml::link('高级搜索','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search_bind_list',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<div class="row-fluid">
客户当天订单数(用于同一客户一天之内多个订单检查，呼叫中心派单可能是多人预约, 由于只有组长收到抵扣短信，所以可以不给组员补账,注:订单报单时间15点前昨15点至今天15点算一天,15点后至明天15点算一天)
</div>

<?php 

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
	'id'=>'bonus-type-bind-list-grid',
	'dataProvider'=>$dataProvider,
	'cssFile'=>SP_URL_CSS . 'table.css',
	'itemsCssClass'=>'table table-condensed',
	'rowCssClassExpression'=>array($this,'orderNumberCss'),
	//'filter'=>$model,
	'columns'=>array(
		array (
			'name'=>'bonus_type_id', 
			'headerHtmlOptions'=>array (
				'width'=>'120px',
				'nowrap'=>'nowrap'
			),
			'value' => array($this, 'getBonusTypeName')
		),
		
		array (
			'name'=>'customer_phone', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value' => 'CHtml::link(Common::parseCustomerPhone($data->customer_phone), array("bonusType/bindlist", "CustomerBonus[customer_phone]"=>$data->customer_phone))'
		),
		
		array (
			'name'=>'bonus_sn', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value' => array($this, "getBonusSn")
		),		
		
		array (
			'name'=>'order_id', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this, "getOrderStatus")
		),
		
		array (
			'name'=>'转账金额', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value' => array($this, "isReturnEmployAcount")
		),		
		array (
			'name'=>'客户当天订单数', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value' => array($this, "getOrderNumber")
		),
		
		array (
			'name'=>'created', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value'=>'$data->created ? date("Y-m-d H:i:s", $data->created) : ""' 
		),
		array (
			'name'=>'used', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value'=>'$data->used ? date("Y-m-d H:i:s", $data->used) : ""' 			
		),
		array (
			'name'=>'报单时间', 
			'headerHtmlOptions'=>array (
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value'=>array($this, 'getOrderCompleteTime') 			
		),
				

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
