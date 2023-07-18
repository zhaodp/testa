<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'cancel-operate-grid',
    'dataProvider' => $dataProvider,
    'itemsCssClass' => 'table table-striped',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'ajaxUpdate' => false,
    'columns' => array(

        array(
            'name' => '处理订单区间',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
                ),
            'type' => 'raw',
            'value' => '$data["search_stime"]."<br/>".$data["search_etime"]'),

        array(
            'name' => '处理意见',
            'headerHtmlOptions' => array(
                'width' => '150px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data["mark"]'),

        array(
            'name' => '处理结果',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => array($this,'formatProcessResult')),

        array(
            'name' => '处理时间',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'date("Y-m-d",strtotime($data["create_time"]))'),

        array(
            'name' => '处理人',
            'headerHtmlOptions' => array(
                'width' => '20px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data["operator"]'),
        array(
            'name' => '查看',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'CHtml::link("销单明细", array("order/orderAccount", "driver_id"=>$data["driver_id"],"start_time"=>$data["search_stime"],"end_time"=>$data["search_etime"]),array("target" => "_blank"))'),
    ),
));


?>