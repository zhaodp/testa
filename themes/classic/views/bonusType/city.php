<?php 
$this->pageTitle = '司机联盟-城市排行榜';

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-bonus-city-list-grid',
	'itemsCssClass'=>'table table-striped',
	'dataProvider'=>$dataProviderCity,
	'columns'=>array(
		array (
			'name'=>'城市', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>'Dict::item("city", $data["city_id"]);'
		), 
		array (
			'name'=>'绑定总数', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'$data["bind_count_sum"]'
		),
		array (
			'name'=>'消费总数', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>'$data["used_count_sum"]'
		),
		array (
			'name'=>'收入总金额', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',			
			'value'=>'$data["bonus_sum"]'
		),				
	)
));
?>