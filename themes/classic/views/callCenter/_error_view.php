<?php

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'callcenter-error-grid',
    'dataProvider'=>$errorData,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-striped',
    'columns'=>array(
        array (
            'name'=>'订单时间',
            'headerHtmlOptions'=>array (
                'width'=>'50px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["order_time"]'
        ),
        array (
            'name'=>'订单编号',
            'headerHtmlOptions'=>array (
                'width'=>'20px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["order_id"]'
        ),
        array (
            'name'=>'城市',
            'headerHtmlOptions'=>array (
                'width'=>'20px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'Dict::item("city", $data["city_id"])'
        ),

        array (
            'name'=>'司机信息',
            'headerHtmlOptions'=>array (
                'width'=>'20px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["driver_id"]'
        ),
        array (
            'name'=>'地址',
            'headerHtmlOptions'=>array (
                'width'=>'20%',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["location_start"]',
        ),
        array(
            'name'=>'备注',
            'headerHtmlOptions'=>array (
                'width'=>'45px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["mark"]',
        ),
        array(
            'name'=>'错误原因',
            'headerHtmlOptions'=>array (
                'width'=>'45px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'errorType_format')
        ),
        array(
            'name'=>'客服坐席',
            'headerHtmlOptions'=>array (
                'width'=>'45px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["agent_id"]',
        ),
        array(
            'name'=>'申报人',
            'headerHtmlOptions'=>array (
                'width'=>'45px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data["operator"]',
        ),
    ),
));




?>