<?php
$this->pageTitle = '发票管理';
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('order-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>
<h1><?php echo $this->pageTitle;?></h1>
<hr class="divider"/>
<?php echo CHtml::link('高级搜索','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->
<?php
//title	content	contact address	zipcode	telephone	status	description	created
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'order-grid',
	'dataProvider'=>$model->search(),
	'itemsCssClass'=>'table table-striped',
	//	'summaryText'=>$dataProvider->itemCount,
	'rowCssClassExpression'=>array($this,'admin_invoiceRow'),
	'htmlOptions'=>array('style'=>'width:98%'),
	'columns'=>array (
		array (
			'name'=>'order_id',
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'CHtml::link($data->order->order_number, array("order/view", "id"=>$data->order_id), array("target"=>"_blank"))'
		),
		array (
			'header'=>'发票信息',
			'type'=>'raw',
			'value'=>array($this,'admin_invoiceContent')
		),
		array (
			'header'=>'收件人地址',
			'type'=>'raw',
			'value'=>array($this,'admin_invoiceContact')
		),
		array (
			'name'=>'金额',
			'value'=>'$data->order->income'
		),
		array(
			'header'=>'日期',
			'value'=>'date("Y-m-d H:i",$data->created)'
		),
		array(
			'header'=>'状态',
			'type'=>'raw',
			'value'=>'($data->status == 0) ? "未开" : "已开"'
		),
		array(
			'header'=>'操作',
			'class'=>'CButtonColumn',
			'template'=>'{confirmDone}',
			'buttons'=>array(
				'confirmDone'=>array(
					'label'=>'确认已开',
					'click'=>"function() {
								if(!confirm('确认该订单的发票已开？')) return false;
								var th=this;
								var afterConfirmDone=function(){};
								$.fn.yiiGridView.update('order-grid', {
									type:'POST',
									url:$(this).attr('href'),
									success:function(data) {
										$.fn.yiiGridView.update('order-grid');
										afterConfirmDone(th,true,data);
									},
									error:function(XHR) {
										return afterConfirmDone(th,false,XHR);
									}
								});
								return false;
							}",
					'url'=>'Yii::app()->controller->createUrl("operate", array("id"=>$data->order_id))',
					'imageUrl'=>false,
					'visible'=>'$data->status < 1',
				),
			),
		),
	)
));
