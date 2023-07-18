<?php

//CGridView
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'complain-order-grid',
    'ajaxUpdate' => false,
    'dataProvider'=>$ordermodel,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-striped',
    'columns'=>array (
        array (
            'name'=>'订单ID',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->order_id'
        ),
        array (
            'name'=>'司机信息',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap',
                'align'=>'center'
            ),
            'type'=>'raw',
            'value'=>array($this,'formatDriverInfo'),
        ),
        array (
            'name'=>'客户信息',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'orderPhone'),
        ),
        array (
            'name'=>'订单时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'orderTime'),
        ),
        array (
            'name'=>'起始地点',
            'headerHtmlOptions'=>array (
                'style'=>'width:180px',
                'nowrap'=>'nowrap'
            ),
            'type' => 'raw',
            'value' => array($this,'OrderAddr')
        ),
        array (
            'name'=>'收费',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'type' => 'raw',
            'value' => array($this, 'orderFee')
        ),
        array (
            'name'=>'description',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'($data->source == "0") ? "客户呼叫" : (($data->source == 1) ? "呼叫中心" : (($data->source == 2) ? "客户呼叫补单" : (($data->source == 3) ? "呼叫中心补单" : ""))) '

        ),
        array (
            'header'=>'司机状态',
            'headerHtmlOptions'=>array (
                'width'=>'50px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'driverState')
        ),
        array (
            'header'=>'销单',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'orderCancel')
        ),
        array (
            'header'=>'状态',
            'headerHtmlOptions'=>array (
                'width'=>'40px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'confirmOrderCacnel')
        ),
        array (
            'name'=>'操作',
            'headerHtmlOptions'=>array (
                'width'=>'110px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'operateOrder'),

        ),
    )

));

?>
