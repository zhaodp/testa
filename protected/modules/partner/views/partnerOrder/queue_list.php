<h1>已下订单</h1>
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
			'value'=>'$data->flag==0 ? CHtml::link("取消", "javascript:cancelQueue($data->id)") : ""'
		),

		array (
			'name'=>'booking_time',
			'headerHtmlOptions'=>array (
				'width'=>'75px',
				'nowrap'=>'nowrap'
			),
			'value'=>'date("m-d H:i",strtotime($data->booking_time))'
		),
		array (
			'name'=>'city_id',
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'Dict::item("city", $data->city_id)'
		),
		array (
			'name'=>'name',
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
		),
        array (
            'name'=>'phone',
            'header' =>'联系电话',
            'headerHtmlOptions'=>array (
                'width'=>'70px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->contact_phone'
        ),
		array (
			'name'=>'address',
			'headerHtmlOptions'=>array (
				'width'=>'15%',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'number',
            'header'=>'预约人数',
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
		),
        array (
            'name'=>'dispatch_number',
            'header'=>'己派人数',
            'headerHtmlOptions'=>array (
                'width'=>'60px',
                'nowrap'=>'nowrap'
            ),
        ),
		array (
			'name'=>'comments',
			'headerHtmlOptions'=>array (
				'width'=>'10%',
			),
			'type'=>'raw'
		),
		array (
			'name'=>'created',
			'headerHtmlOptions'=>array (
				'width'=>'75px',
				'nowrap'=>'nowrap'
			),
			'value'=>'date("m-d H:i",strtotime($data->created))'
		),
		array (
			'name'=>'agent_id',
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
		),

        /*
		array (
			'name'=>'dispatch_agent',
			'headerHtmlOptions'=>array (
				'width'=>'60px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'dispatch_time',
			'headerHtmlOptions'=>array (
				'width'=>'75px',
				'nowrap'=>'nowrap'
			),
			'value'=>'($data->dispatch_time=="0000-00-00 00:00:00")?"":date("m-d H:i",strtotime($data->dispatch_time))'
		),
		array(
			'name'=>'派单',
			'headerHtmlOptions'=>array (
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>array($this,'queueDispatch'),
		),
		array(
			'name'=>'状态',
            'type'=>'raw',
			'headerHtmlOptions'=>array (
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value'=>array($this,'queueDispatchStatus'),
		),
        array(
            'name'=>'申报',
            'headerHtmlOptions'=>array (
                'width'=>'30px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'CHtml::link("错误", "",array("style" =>"display:inline-block;cursor:pointer;","mewidth"=>"400","data-target" => "","data-toggle"=>"modal","url"=>Yii::app()->createUrl("CallCenter/error",array("qid"=>$data->id))))',
        ),
        */
	),
));
?>
<script>

function cancelQueue(id){
    if(confirm("确认取消此订单？")){
        $.ajax({
            'url':'<?php echo Yii::app()->createUrl('business/default/cancelqueue');?>',
            'data':'id='+id,
            'type':'get',
            'success':function(data){
                alert('订单已经取消。');
                $.fn.yiiGridView.update('orderqueue-grid', {
                    data: $(this).serialize()
                });
            },
            'cache':false
        });
    }
}
</script>