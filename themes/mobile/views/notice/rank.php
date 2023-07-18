<?php
if($t == 30){
	$num = 1;
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'order_table',
		'itemsCssClass'=>'table table-striped',
		'dataProvider'=>$dataProvider,
		'pager'=>false,
		'template'=>'{items}',
		//'htmlOptions'=>array('class'=>''),
		'columns'=>array(
			array(
				'name'=>'序号',
				'headerHtmlOptions'=>array(
					'width'=>'10px',
					'nowrap'=>'nowrap'
				),
				'value' => '$row+1'
			),
			array(
				'name'=>'司机姓名', 
				'headerHtmlOptions'=>array (
					'width'=>'60px',
					'nowrap'=>'nowrap'
				),
				'value'=>'$data->name'
			),
			array (
				'name'=>'司机工号', 
				'headerHtmlOptions'=>array (
					'width'=>'60px',
					'nowrap'=>'nowrap'
				),
				'value'=>'$data->driver_id'
			), 
			array(
				'name'=>'出勤天数', 
				'headerHtmlOptions'=>array (
					'width'=>'60px',
					'nowrap'=>'nowrap'
				),
				'value'=>'$data->order_date'
			),
			array (
				'name'=>'总接单数', 
				'headerHtmlOptions'=>array (
					'width'=>'60px',
					'nowrap'=>'nowrap'
				), 
				'value'=>'$data->order_id'
			),
			array (
				'name'=>'呼叫中心派单量', 
				'headerHtmlOptions'=>array (
					'width'=>'60px',
					'nowrap'=>'nowrap'
				), 
				'value'=>'$data->distance'
			),
			array (
				'name'=>'客户直接呼叫量', 
				'headerHtmlOptions'=>array (
					'width'=>'60px',
					'nowrap'=>'nowrap'
				), 
				'value'=>'$data->charge'
			),
			array (
				'name'=>'收入', 
				'headerHtmlOptions'=>array (
					'width'=>'60px',
					'nowrap'=>'nowrap'
				), 
				'value'=>'$data->income'
			),
		)
	));
}else{
$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'order_table',
		'itemsCssClass'=>'table table-striped',
		'dataProvider'=>$dataProvider,
		//'htmlOptions'=>array('class'=>''),
		'columns'=>array(
			array(
				'name'=>'司机姓名', 
				'headerHtmlOptions'=>array (
					'width'=>'60px',
					'nowrap'=>'nowrap'
				),
				'value'=>'$data->name'
			),
			array (
				'name'=>'司机工号', 
				'headerHtmlOptions'=>array (
					'width'=>'60px',
					'nowrap'=>'nowrap'
				),
				'value'=>'$data->driver_id'
			), 
			array (
				'name'=>'总接单数', 
				'headerHtmlOptions'=>array (
					'width'=>'60px',
					'nowrap'=>'nowrap'
				), 
				'value'=>'$data->order_id'
			),
			array (
				'name'=>'呼叫中心派单量', 
				'headerHtmlOptions'=>array (
					'width'=>'60px',
					'nowrap'=>'nowrap'
				), 
				'value'=>'$data->distance'
			),
			array (
				'name'=>'客户直接呼叫量', 
				'headerHtmlOptions'=>array (
					'width'=>'60px',
					'nowrap'=>'nowrap'
				), 
				'value'=>'$data->charge'
			),
			array (
				'name'=>'收入', 
				'headerHtmlOptions'=>array (
					'width'=>'60px',
					'nowrap'=>'nowrap'
				), 
				'value'=>'$data->income'
			),
		)
	));
}