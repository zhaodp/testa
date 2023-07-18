<?php
$this->widget('zii.widgets.grid.CGridView', array (
	'id'=>'orderqueue-grid', 
	'dataProvider'=>$dataProvider, 
	'cssFile'=>SP_URL_CSS.'table.css', 
	'itemsCssClass'=>'table table-condensed', 
	'columns'=>array (
		array (
			'name'=>'name', 
			'headerHtmlOptions'=>array (
				'width'=>'30px', 
				'nowrap'=>'nowrap')), 
		array (
			'name'=>'phone', 
			'headerHtmlOptions'=>array (
				'width'=>'30px', 
				'nowrap'=>'nowrap')), 
		array (
			'name'=>'vipcard', 
			'headerHtmlOptions'=>array (
				'width'=>'30px', 
				'nowrap'=>'nowrap')), 
		array (
			'name'=>'call_time', 
			'headerHtmlOptions'=>array (
				'width'=>'50px', 
				'nowrap'=>'nowrap'), 
			'value'=>'date("m-d H:i",$data->call_time)'), 
		
		array (
			'name'=>'booking_time', 
			'headerHtmlOptions'=>array (
				'width'=>'50px', 
				'nowrap'=>'nowrap'), 
			'value'=>'date("m-d H:i",$data->booking_time)'), 
		array (
			'name'=>'location_start', 
			'headerHtmlOptions'=>array (
				'width'=>'20%', 
				'nowrap'=>'nowrap')), 
		array (
			'name'=>'status', 
			'headerHtmlOptions'=>array (
				'width'=>'5px', 
				'nowrap'=>'nowrap')))));

?>