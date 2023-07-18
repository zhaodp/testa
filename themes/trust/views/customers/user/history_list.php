<?php $this->renderPartial('user/user_nav'); ?>
<script type='text/javascript'>
function updateStatus(id,order_id){
        $.ajax({
                'url':'<?php echo Yii::app()->createUrl('/customers/order_invoice');?>',
                'data':{'id':id,'order_id':order_id},
                'dataType':'json',
                'type':'get',
                'success':function(data){
                        $('#order_'+order_id).html(data.msg);
                        },
                'cache':false           
                });

}
</script>
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
        <?php $this->widget('zii.widgets.grid.CGridView', array(
            'id' => 'customer-main-grid',
            'dataProvider' => $dataProvider,
            'itemsCssClass' => 'table table-striped',
            'columns' => array(
                array(
                    'name' => '交易订单号',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => 'empty($data->trans_order_id) ? "---" : $data->trans_order_id'
                ),
                array(
                    'name' => '交易卡号',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => 'empty($data->trans_card) ? "---" : $data->trans_card'
                ),
                array(
                    'name' => '交易类型',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => 'CarCustomerTrans::$trans_type[$data->trans_type]'
                ),
                array(
                    'name' => '交易金额',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data->amount'
                ),
                array(
                    'name' => '余额',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data->balance'
                ),
                array(
                    'name' => '交易来源',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => 'CarCustomerTrans::$trans_source[$data->source]'
                ),
                array(
                    'name' => '交易时间',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data->create_time'
                ),
                array(
                    'name' => '备注',
                    'headerHtmlOptions' => array(
                        'nowrap' => 'nowrap'
                    ),
                    'type' => 'raw',
                    'value' => '$data->remark'
                ),
		array(
		    'name' => '是否已开发票',
		    'type' => 'raw',
		    'value' => '$data->source==CarCustomerTrans::TRANS_SOURCE_F?($data->invoiced==0?"<div id=order_".$data->trans_order_id.">".CHtml::link("未开发票，标记已开", "javascript:void(0);", array(
                        "onclick" => "updateStatus(".$data->id.",\'".$data->trans_order_id."\');"))."</div>":"已开发票"):""'
		),
            ),
        )); ?>
    </div>
</div>
