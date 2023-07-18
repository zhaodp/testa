<?php

//CGridView
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'driver-punish-grid',
    'dataProvider'=>$data,
    'itemsCssClass'=>'table table-striped',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'columns'=>array (
        array (
            'name'=>'处理日期',
            'headerHtmlOptions'=>array (
                'style'=>'width:100px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["create_time"]'
        ),
        array (
            'name'=>'处理结果',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["result"]'
        ),
        array (
            'name'=>'处理人',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["operator"]'
        ),
        array (
            'name'=>'备注',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["mark"]'
        ),
        array (
            'name'=>'屏蔽期限',
            'headerHtmlOptions'=>array (
                'style'=>'width:80px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data["limit_time"]'
        ),
//        array (
//            'name'=>'订单号',
//            'headerHtmlOptions'=>array (
//                'style'=>'width:80px',
//                'nowrap'=>'nowrap'
//            ),
//            'type'=>'raw',
//            'value'=>'$data["order_id"]'
//        ),

    )
));

?>