<div class="alert alert-info">
    <a href="#" name="sendpricebtn" id="sendpricebtn" onclick="sendprice('<?php echo $_REQUEST['phone']; ?>');">发送价格表</a>
</div>
<?php
	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'orderqueue-grid',
	'dataProvider'=>$dataProvider,
	'cssFile'=>SP_URL_CSS . 'table.css',
	'itemsCssClass'=>'table table-condensed',
	'rowCssClassExpression'=>array($this,'queueStatus'),
	//'filter'=>$model,
	'columns'=>array(
        array (
            'header'=>'取消',
            'headerHtmlOptions'=>array (
                'width'=>'30px',
                'nowrap'=>'nowrap'),
            'type'=>'raw',
            'value'=>array($this,'queueCancel')
        ),
        array (
			'name'=>'booking_time', 
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'value'=>'date("m-d H:i",strtotime($data->booking_time))'
		), 
		array (
			'name'=>'city_id', 
			'headerHtmlOptions'=>array (
				'width'=>'20px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'Dict::item("city", $data->city_id)'
		),
		array (
			'name'=>'name', 
			'headerHtmlOptions'=>array (
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'address', 
			'headerHtmlOptions'=>array (
				'width'=>'20%',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'number', 
			'headerHtmlOptions'=>array (
				'width'=>'5px',
				'nowrap'=>'nowrap'
			),
		),
        /*
		array (
			'name'=>'comments', 
			'headerHtmlOptions'=>array (
				'width'=>'10%',
			),
			'type'=>'raw'
		),
        */
		array (
			'name'=>'dispatch_time', 
			'headerHtmlOptions'=>array (
				'width'=>'10px',
				'nowrap'=>'nowrap'
			),
			'value'=>'($data->dispatch_time=="0000-00-00 00:00:00")?"":date("m-d H:i",strtotime($data->dispatch_time))'
		),		
		array(
			'name'=>'状态', 
			'headerHtmlOptions'=>array (
				'width'=>'45px',
				'nowrap'=>'nowrap'
			),
			'value'=>array($this,'queueDispatchStatus'),
		),
	),
));

?>