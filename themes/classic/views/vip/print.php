<style>
    h4 {
        height: 36px;
        line-height: 36px;
        text-indent: 12px;
        border: 1px solid rgb(212, 212, 212);
        border-radius: 4px;
        background-color: rgb(250, 250, 250) !important;
        background-image: linear-gradient(to bottom, rgb(255, 255, 255), rgb(242, 242, 242));
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.067) !important;

    }
</style>
<div class="row-fluid" style = "width:94%; margin-left:3%">
<div class="span12">
    <h1>VIP明细单</h1>
    <h3 style="font-size: 18px;"><?php echo 'VIP用户（' . $vip->id . '）' . '从 ' . date('Y-m-d', $data['start_time']) . ' 到 ' . date('Y-m-d', $data['end_time']) . '，' . $StatisticMessage . '账户余额:' . $vip->balance . '元'; ?></h3>

    <?php
    $data = $dataProviderRecharge->getData();
    if (!empty($data)) {
        ?>
        <h4>充值记录</h4>
        <?php
        $this->widget('zii.widgets.grid.CGridView', array(
            'id' => 'vip-grid',
            'dataProvider' => $dataProviderRecharge,
            'itemsCssClass' => 'table table-bordered table-striped',
            'columns' => array(
                array(
                    'name' => '日期',
                    'value' => 'date("Y-m-d", $data->created)'
                ),
                array(
                    'name' => '充值金额',
                    'value' => '($data->amount)'
                ),
                array(
                    'name' => '充值类型',
                    'value' => '($data->type == 0) ? "充值" : "充值卡充值"'
                ),
                array(
                    'name' => '余额',
                    'value' => '($data->balance == 0) ? "--" : $data->balance'
                ),
            ),
        ));
        ?>
        <h4>消费记录</h4>

    <?php
    }
    ?>

    <?php $this->widget('zii.widgets.grid.CGridView', array(
        'id' => 'vip-grid',
        'dataProvider' => $dataProvider,
        'itemsCssClass' => 'table  table-striped',
        'columns' => array(
            array(
                'name' => '结帐日期',
                'value' => 'date("Y-m-d", $data->created)'
            ),
            array(
                'name' => '订单编号',
                'value' => '$data->order_id'
            ),
            array(
                'name' => '司机工号',
                'value' => 'Yii::app()->controller->getDriverId($data->order_id, "driver_id")'
            ),
            array(
                'name' => '客户信息',
                'headerHtmlOptions' => array(
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => 'Yii::app()->controller->getDriverId($data->order_id, "phone")."<br/>".
            Yii::app()->controller->getDriverId($data->order_id, "name")'
            ),
            array(
                'name' => '出发/到达时间',
                'headerHtmlOptions' => array(
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '"预约：".Yii::app()->controller->getDriverId($data->order_id, "booking_time")."<br/>".
                        "出发：".Yii::app()->controller->getDriverId($data->order_id, "start_time")."<br/>".
                        "到达：".Yii::app()->controller->getDriverId($data->order_id, "end_time")'
            ),
            array(
                'name' => '出发/到达地点',
                'headerHtmlOptions' => array(
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => '"出发地：".Yii::app()->controller->getDriverId($data->order_id, "location_start")."<br/>".
                        "目的地：".Yii::app()->controller->getDriverId($data->order_id, "location_end")'
            ),

            array(
                'name' => '总里程',
                'value' => 'Yii::app()->controller->getDriverId($data->order_id, "distance")."公里"'
            ),

            array(
                'name' => '收费明细',
                'headerHtmlOptions' => array(
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => 'Order::model()->vipListPriceInfo($data->order_id)'
            ),

            array(
                'name' => '消费金额',
                'headerHtmlOptions' => array(
                    'nowrap' => 'nowrap',
                    'style' => 'text-align:right;'
                ),
                'htmlOptions' => array(
                    'style' => 'text-align:right;'
                ),
                'type' => 'raw',
                'value' => '$data->amount . "元"'
            ),
            array(
                'name' => '帐户余额',
                'headerHtmlOptions' => array(
                    'nowrap' => 'nowrap',
                    'style' => 'text-align:right;'
                ),
                'htmlOptions' => array(
                    'style' => 'text-align:right;'
                ),
                'type' => 'raw',
                'value' => '($data->balance == 0) ? "--" : $data->balance."元"'
            ),
            array(
                'name' => '备注',
                'headerHtmlOptions' => array(
                    'nowrap' => 'nowrap'
                ),
                'type' => 'raw',
                'value' => 'Yii::app()->controller->getDriverId($data->order_id, "contact_phone")."<br/>".Yii::app()->controller->getDriverId($data->order_id, "channel")'

            ),
        ),
    )); ?>
</div>
</div>