<?php

	$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'orderqueue-grid',
	'dataProvider'=>$model->search(),
	'cssFile'=>SP_URL_CSS . 'table.css',
	'itemsCssClass'=>'table table-condensed',
	'rowCssClassExpression'=>array($this,'queueStatus'),
	//'filter'=>$model,
	'columns'=>array(
		array (
			'name'=>'booking_time',
			'header'=>'预约时间',
			'headerHtmlOptions'=>array (
				'width'=>'50px',
				'nowrap'=>'nowrap'
			),
			'value'=>'date("m-d H:i",strtotime($data->booking_time))'
		), 
		array (
			'name'=>'city_id',
			'header'=>'城市',
			'headerHtmlOptions'=>array (
				'width'=>'20px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value'=>'Dict::item("city", $data->city_id)'
		),
		array (
			'name'=>'name',
			'header'=>'客户姓名',
			'headerHtmlOptions'=>array (
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
            'type'=>'raw',
            'value'=>'($data->is_vip)?"<span class=\"vip\" title=\"vip\"></span>".$data->name:$data->name',
		),
		array (
			'name'=>'address',
			'header'=>'地址',
			'headerHtmlOptions'=>array (
				'width'=>'20%',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'number',
			'header'=>'预约人数',
			'headerHtmlOptions'=>array (
				'width'=>'5px',
				'nowrap'=>'nowrap'
			),
		),
		array (
			'name'=>'comments',
			'header'=>'备注',
			'headerHtmlOptions'=>array (
				'width'=>'10%',
			),
			'type'=>'raw'
		),
		array (
			'name'=>'dispatch_time',
			'header'=>'派单时间',
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
        array(
            'name'=>'申报',
            'headerHtmlOptions'=>array (
                'width'=>'30px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'CHtml::link("错误", "",array("style" =>"display:inline-block;cursor:pointer;","mewidth"=>"400","data-target" => "","data-toggle"=>"modal","url"=>Yii::app()->createUrl("CallCenter/error",array("qid"=>$data->id))))."<br /><a href=\"javascript:void(0);\" onclick=\"recordCallTimes($data->id);\">催单</a>"',
        )

	),
));

?>


