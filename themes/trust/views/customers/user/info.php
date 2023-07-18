<?php $this->renderPartial('user/user_nav'); ?>
<?php
if (!empty($customer)) {
    ?>
    <div class="row-fluid">
        <table class="table table-bordered">
            <tr>
                <th>客户名称</th>
                <td><?php
                    $name = $customer->name;
                    if (!empty($name)) {
                        echo $name;
                    } else {
                        echo "未修改";
                    }
                    ?></td>

                <th>联系电话</th>
                <td><?php echo $customer->phone; ?></td>

                <th>帐户余额</th>
                <td>
                    <?php
                    if (!empty($customer_account)) {
                        echo $customer_account->amount;
                    } else {
                        echo 0;
                    }
                    ?>
                </td>

                <th>信誉度</th>
                <td>
                    <?php
                    if (!empty($customer_account)) {
                        echo $customer_account->credit;
                    } else {
                        echo 0;
                    }
                    ?>
                </td>
            </tr>

            <tr>
                <th>常用代驾地址</th>
                <td colspan="7"><?php echo $customer->address; ?></td>
            </tr>

        </table>

    </div>
<?php
}
?>
<div class="tabbable tabs-left" style="border-top:1px solid rgb(221, 221, 221); padding-top:10px;">
    <!-- Only required for left tabs -->
    <?php $this->renderPartial('user/left_nav', array('id' => $_GET['id'])); ?>

    <div class="tab-content">
        <?php
        $this->widget('zii.widgets.grid.CGridView', array(
            'id' => 'customer-main-grid',
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'table table-striped',
            'columns' => array(
                array(
                    'name' => '订单编号',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '"编号：".$data->order_id."<br/>"."单号：".$data->order_number'
                ),
                array(
                    'name' => '司机信息',
                    'headerHtmlOptions' => array(
                        'width' => '80px'
                    ),
                    'type' => 'raw',
                    'value' => '$data->driver."<br/>".$data->driver_id'
                ),
                array(
                    'name' => '客户信息',
                    'headerHtmlOptions' => array(
                        'style' => 'width:130px',
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data->name."<br/>".$data->phone'
                ),
                array(
                    'name' => '订单时间',
                    'headerHtmlOptions' => array(
                        'style' => 'width:120px',
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => 'date("Y-m-d H:i:s", $data->call_time)."<br/>".date("Y-m-d H:i:s",$data->booking_time)'
                ),
                array(
                    'name' => '起始地点',
                    'headerHtmlOptions' => array(
                        'style' => 'width:120px',
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data->location_start."<br/>".$data->location_end'
                ),
                array(
                    'name' => '收费',
                    'headerHtmlOptions' => array(
                        'style' => 'width:120px',
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '"总费用:".$data->income."<br/>"."实收:".$data->price."<br/>"."信息费：".$data->cast'
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
                    'header' => '订单描述',
                    'headerHtmlOptions' => array(
                        'width' => '62px',
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data->description',
                ),
                array(
                    'header' => '状态',
                    'headerHtmlOptions' => array(
                        'width' => '40px',
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => 'Order::model()->confirmOrder($data->status)'
                ),
            ),
        )); ?>
    </div>
</div>
