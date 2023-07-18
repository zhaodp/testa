<?php $this->pageTitle = '消费记录';?>
<h1>消费记录</h1>
<b>充值记录</b>
<!--
交易时间
交易金额
交易类型
交易渠道
交易状态
交易单号
-->
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'vip-grid',
	'dataProvider'=>$dataProvider['consumption'],
	'cssFile'=>SP_URL_CSS . 'table.css',
	'itemsCssClass'=>'table  table-condensed',
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
	'columns'=>array(
		array(
			'name' => '交易时间',
			'value' => '$data->confirm_time',
		),
		array(
			'name' => '交易金额',
			'value' => '$data->amount',
		),
		array(
			'name' => '交易类型',
			'value'=> '$data->action_type'
		),
		array(
			'name' => '交易渠道',
			'value' => '$data->order_type',
		),
		array(
			'name'=> '交易状态',
			'value'=>'',
		),
		array(
			'name'=>'交易单号',
			'value'=>'',
		),	
	),
)); 
?>
<b>消费记录</b>
<!--
交易时间
交易金额
交易类型
订单编号
-->
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'vip-grid',
	'dataProvider'=>$dataProvider['recharge'],
	'cssFile'=>SP_URL_CSS . 'table.css',
	'itemsCssClass'=>'table  table-condensed',
	'pagerCssClass'=>'pagination text-center', 
	'pager'=>Yii::app()->params['formatGridPage'], 
	'columns'=>array(
		array(
			'name' => '交易时间',
			'value'=> '',
		),
		array(
			'name' => '交易金额',
			'value' => '',
		),
		array(
			'name' => '交易类型',
			'value' => '',
		),
		array(
			'name' => '订单编号',
			'value' => '',
		),
	),
)); 
?>