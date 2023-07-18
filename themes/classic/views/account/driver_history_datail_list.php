<?php
if(!isset($_GET['type'])){
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'order-grid',
	'dataProvider'=>$dataProvider, 
	'itemsCssClass'=>'table table-striped',
	'columns'=>array (
		array (
			'name'=>'预约时间', 
			'headerHtmlOptions'=>array (
				'width'=>'120px',
				'nowrap'=>'nowrap'
			), 
			'value'=>array($this,'orderidByBooking_time')),
		array (
			'name'=>'报单时间', 
			'headerHtmlOptions'=>array (
				'width'=>'120px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'date("Y-m-d H:i",$data["created"])'),
		array (
			'name'=>'交易类型', 
			'headerHtmlOptions'=>array (
				'width'=>'120px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'Dict::item("account_type",$data[\'type\'])'),
		array (
			'name'=>'收入/支出（元）',
			'headerHtmlOptions'=>array (
				'width'=>'150px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'sprintf("%0.2f",$data["cast"])'),
        array(
            'name' => '余额（元）',
            'headerHtmlOptions' => array(
                'width' => '150px',
                'nowrap' => 'nowrap'
            ),
            'value' => 'sprintf("%0.2f",$data["balance"])'),
		array (
			'name'=>'类型', 
			'value'=>'$data["comment"]'),
		
		)));	
}elseif(($_GET['type'] != 5 && $_GET['type'] != 4)){
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'order-grid',
	'dataProvider'=>$dataProvider, 
	'itemsCssClass'=>'table table-striped',
	'columns'=>array (
		array (
			'name'=>'预约时间', 
			'headerHtmlOptions'=>array (
				'width'=>'120px',
				'nowrap'=>'nowrap'
			), 
			'value'=>array($this,'orderidByBooking_time')),
		array (
			'name'=>'报单时间', 
			'headerHtmlOptions'=>array (
				'width'=>'120px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'date("Y-m-d H:i",$data["created"])'),
		array (
			'name'=>'交易类型', 
			'headerHtmlOptions'=>array (
				'width'=>'120px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'Dict::item("account_type",$data[\'type\'])'),
		array (
			'name'=>'收入/支出（元）',
			'headerHtmlOptions'=>array (
				'width'=>'150px',
				'nowrap'=>'nowrap'
			),
			'value'=>'sprintf("%0.2f",$data["cast"])'),
        array(
            'name' => '余额（元）',
            'headerHtmlOptions' => array(
                'width' => '150px',
                'nowrap' => 'nowrap'
            ),
            'value' => 'sprintf("%0.2f",$data["balance"])'),
		array (
			'name'=>'类型', 
			'value'=>'$data["comment"]'),
		
		)));
}else{
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'order-grid',
	'dataProvider'=>$dataProvider, 
	'itemsCssClass'=>'table table-striped',
	'columns'=>array (
		array (
			'name'=>'时间', 
			'headerHtmlOptions'=>array (
				'width'=>'120px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'date("Y-m-d H:i",$data["created"])'),
		array (
			'name'=>'交易类型', 
			'headerHtmlOptions'=>array (
				'width'=>'120px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'Dict::item("account_type",$data[\'type\'])'),
		array (
			'name'=>'收入/支出（元）',
			'headerHtmlOptions'=>array (
				'width'=>'150px',
				'nowrap'=>'nowrap'
			), 
			'value'=>'sprintf("%0.2f",$data["cast"])'),
        array(
            'name' => '余额（元）',
            'headerHtmlOptions' => array(
                'width' => '150px',
                'nowrap' => 'nowrap'
            ),
            'value' => 'sprintf("%0.2f",$data["balance"])'),
		array (
			'name'=>'类型', 
			'value'=>'$data["comment"]'),
		
		)));
}
