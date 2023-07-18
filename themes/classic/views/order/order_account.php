<?php
$this->pageTitle = '报单销单明细对账单';
?>
<div style = "width:96%; margin-left:2%;">
<h1><?php echo $this->pageTitle; ?></h1>
<h4>
    <?php
    $call_time = !empty($call_time) ? $call_time : ' 开始 ';
    $booking_time = !empty($booking_time) ? $booking_time : ' 现在,';
    if(!empty($order_info)){
        $order_count = !empty($order_info['order_count']) ? $order_info['order_count'] : 1;
        $single = number_format((($order_info['order_single'] / $order_count)*100),2);

    echo $order_info['driver']."（". $order_info['driver_id']."）师傅,从". $call_time."到". $booking_time,
        '总接 '. $order_info['order_count'].' 单，报 '. $order_info['order_declaration'].' 单，销 '. $order_info['order_single']." 单,销单比率：". $single ."%。";
    }

    ?>

</h4>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'order-grid',
    'dataProvider' => $dataProvider,
	'emptyText'=>'没有找到数据.',
    'cssFile' => SP_URL_CSS . 'table.css',
    'pagerCssClass' => 'pagination text-center',
    'pager' => Yii::app()->params['formatGridPage'],
    'itemsCssClass' => 'table table-condensed',
    'rowCssClassExpression' => array($this, 'orderStatus'),
    'htmlOptions' => array('class' => 'row-fluid'),
    'columns' => array(
        array(
            'name' => '订单编号',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->order_id'
        ),
        array(
            'name' => '司机信息',
            'headerHtmlOptions' => array(
                'width' => '80px'
            ),
            'type' => 'raw',
            'value' => '$data->driver_id',
        ),
        array(
            'name' => '客户信息',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'preg_replace("/(1\d{1,2})\d\d(\d{0,3})/", "\$1****\$3", $data->phone)',
        ),
        array(
            'name' => '订单时间',
            'headerHtmlOptions' => array(
                'style' => 'width:120px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => array($this, 'orderTime')
        ),
        array(
            'name' => '起始地点',
            'headerHtmlOptions' => array(
                'style' => 'width:120px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => array($this, 'OrderAddr')
        ),
        array(
            'name' => '收费',
            'headerHtmlOptions' => array(
                'style' => 'width:120px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => array($this, 'orderFee')
        ),

        array(
            'name' => '订单来源',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '($data->source == "0") ? "客户呼叫" : (($data->source == 1) ? "呼叫中心" : (($data->source == 2) ? "客户呼叫补单" : (($data->source == 3) ? "呼叫中心补单" : ""))) '

        ),

		array(
            'name' => '可疑等级',
            'headerHtmlOptions' => array(
				'style' => 'width:60px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' =>  array($this, 'getAlertLevel'),

        ),
        array(
            'header' => '销单',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => array($this, 'orderCancel')
        ),
    )
));
?>
</div>