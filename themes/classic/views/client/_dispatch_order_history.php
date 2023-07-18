<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'orderqueue-grid',
    'dataProvider'=>$orderData,
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-striped',
    'template'=>'{items}',
    'columns'=>array(
        array (
            'name'=>'呼叫时间',
            'headerHtmlOptions'=>array (
                'width'=>'50px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'date("m-d H:i",$data->call_time)'
        ),
        array (
            'name'=>'城市',
            'headerHtmlOptions'=>array (
                'width'=>'20px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'Dict::item("city", $data->city_id)'
        ),
        array (
            'name'=>'客户名称',
            'headerHtmlOptions'=>array (
                'width'=>'30px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->name'
        ),
        array (
            'name'=>'司机信息',
            'headerHtmlOptions'=>array (
                'width'=>'5px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->driver_id'
        ),
        array (
            'name'=>'地址',
            'headerHtmlOptions'=>array (
                'width'=>'20%',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->location_start',
        ),
        array(
            'name'=>'状态',
            'headerHtmlOptions'=>array (
                'width'=>'45px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'($data->status==Order::ORDER_COMPLATE || $data->status==Order::ORDER_NOT_COMFIRM)?"报单":"销单"',
        ),
        array(
            'name'=>'申报',
            'headerHtmlOptions'=>array (
                'width'=>'30px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'CHtml::link("错误", "",array("style" =>"display:inline-block;cursor:pointer;","mewidth"=>"400","data-target" => "","data-toggle"=>"modal","url"=>Yii::app()->createUrl("CallCenter/error",array("oid"=>$data->order_id))))',
        ),
    ),
));

?>