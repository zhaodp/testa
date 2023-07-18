
<div style="margin-top:40px;">
    <pre class="well nav-tabs navbar" style="padding:0 20px 0 10px;">
        <a class="brand" href="javascript:;" target="_top">优惠劵绑定列表</a>
    </pre>
</div>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'bonus-type-bind-list-grid',
    'dataProvider' => $data,
    'showTableOnEmpty' => FALSE,
    'itemsCssClass' => 'table',
    'pagerCssClass' => 'pagination text-center',
    'pager' => Yii::app()->params['formatGridPage'],
    'enableSorting' => FALSE,
    'columns' => array(
        array(
            'name' => '订单流水号',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '($data->order_id == 0) ? "未使用" : $data->order_id'
        ),
        array(
            'name' => '优惠券名称',
            'headerHtmlOptions' => array(
                'width' => '120px',
                'nowrap' => 'nowrap'
            ),
            'value' => 'CustomerBonus::model()->getBonusCodeList($data->bonus_type_id)'
        ),
        array(
            'name' => '客户电话',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'Common::parseCustomerPhone($data->customer_phone)'
        ),
        array(
            'name' => '优惠码',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'Common::parseBonus($data->bonus_sn)'
        ),
        array(
            'name' => '用户限制',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'Dict::item("user_limited", BonusCode::model()->getFieldValue("$data->bonus_type_id", "user_limited"))'
        ),
        array(
            'name' => '使用限制',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'Dict::item("channel_limited", BonusCode::model()->getFieldValue("$data->bonus_type_id", "channel_limited"))'
        ),
        array(
            'name' => '金额',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => '$data->balance'
        ),
        array(
            'name' => '绑定时间',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data->created ? date("Y-m-d H:i:s", $data->created) : ""'
        ),
        array(
            'name' => '消费时间',
            'headerHtmlOptions' => array(
                'width' => '40px',
                'nowrap' => 'nowrap'
            ),
            'value' => '$data->used ? date("Y-m-d H:i:s", $data->used) : ""'
        ),
    ),
));

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'mydialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '订单信息',
        'autoOpen' => false,
        'width' => '750',
        'height' => '450',
        'modal' => true,
        'buttons' => array(
            'Close' => 'js:function(){$("#mydialog").dialog("close");}'
        ),
    ),
));
echo '<div id="dialogdiv"></div>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>