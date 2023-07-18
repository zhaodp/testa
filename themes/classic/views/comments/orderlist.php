<?php
$this->pageTitle = '评论的相关订单';
?>
<h1><?php echo $this->pageTitle;?></h1>
<div class="span2"><?php echo CHtml::link('查看详情', array("order/admin", "Order"=>array("driver"=>$data_get['driver'], "phone" =>$data_get['phone'])),array('target'=>"_parent"));?></div>

<?php
//CGridView
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'comments-grid', 
	'dataProvider'=>$dataProvider, 
	'itemsCssClass'=>'table',
	'columns'=>array (
		array (
			'name'=>'order_number', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'$data->order_number'
		),
		array (
			'name'=>'driver', 
			'headerHtmlOptions'=>array (
				'width'=>'30px'
			),
			'type'=>'raw',
		), 
		array (
			'name'=>'name', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
		), 
		array (
			'name'=>'phone', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
			'type'=>'raw', 
			'value'=>'$data->phone'
		),
		array (
			'name'=>'call_time', 
			'headerHtmlOptions'=>array (
				'style'=>'width:72px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'date("m-d H:i",$data->call_time)'
		),
		array (
			'name'=>'booking_time', 
			'headerHtmlOptions'=>array (
				'style'=>'width:72px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'date("m-d H:i",$data->booking_time)'
		),		
		array (
			'name'=>'location_start', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			)
		),
		array (
			'name'=>'income', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			), 
			'htmlOptions'=>array (
				'style'=>'text-align:right'
			)
		), 
		array (
			'name'=>'cast', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			), 
			'htmlOptions'=>array (
				'style'=>'text-align:right'
			),
			'value'=>'EmployeeAccount::model()->getOrderfee($data);'
		),
		array (
			'name'=>'description', 
			'headerHtmlOptions'=>array (
				'nowrap'=>'nowrap'
			),
			'value'=>'($data->source == "0") ? "客户呼叫" : (($data->source == 1) ? "呼叫中心" : (($data->source == 2) ? "客户呼叫补单" : (($data->source == 3) ? "呼叫中心补单" : ""))) '
			
		),
	)
)
)
?>