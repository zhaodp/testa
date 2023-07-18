<style>
    h4{
        height: 36px;
        line-height: 36px;
        text-indent: 12px;
        border: 1px solid rgb(212, 212, 212);
        border-radius:4px;
        background-color: rgb(250, 250, 250) !important;
        background-image: linear-gradient(to bottom, rgb(255, 255, 255), rgb(242, 242, 242));
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.067) !important;

    }
</style>
<h1>e代驾VIP明细单</h1>

<div class="row-fluid">
    <div class="span12 well">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'driver-admin-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'method' => 'get',
            'errorMessageCssClass' => 'alert alert-error'
        )); ?>
        <div class="span3">
            <label for="start_time">开始时间</label>
            <?php
            $start_time = isset($data['start_time']) ? date('Y-m-d', $data['start_time']) : date('Y-m-d', strtotime("-1 month"));
            //$start_time = isset($data['start_time']) ? $data['start_time'] : date('Y-m-d',strtotime("-1 day"));
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'VipTrade[start_time]',
                //		'model'=>$model,  //Model object
                'value' => $start_time,
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
            ));
            ?>
        </div>
        <div class="span3">
            <label for="end_time">结束时间</label>
            <?php
            $end_time = isset($data['end_time']) ? date('Y-m-d', $data['end_time']) : date('Y-m-d', time());
            //$end_time = isset($data['end_time']) ? $data['end_time'] : date('Y-m-d',time());
            Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
            $this->widget('CJuiDateTimePicker', array(
                'name' => 'VipTrade[end_time]',
                //		'model'=>$model,  //Model object
                'value' => $end_time,
                'mode' => 'date', //use "time","date" or "datetime" (default)
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ), // jquery plugin options
                'language' => 'zh',
            ));
            ?>
        </div>
        <div class="span3">
            <label>&nbsp;</label>
            <?php echo CHtml::submitButton('搜索', array('class' => 'btn')); ?>
            <?php echo CHtml::button('打印预览', array('class' => 'btn', 'id' => 'print')); ?>
            <?php echo CHtml::button('导出列表', array('class' => 'btn', 'id' => 'export_account')); ?>
        </div>
    </div>
    <?php $this->endWidget(); ?>
</div>

<h4>帐户信息</h4>
<table class="table table-bordered">
    <tr>
        <th width="150">VIP卡号：</th>
        <td width="150">
            <?php
            echo $vip->id;
            ?></td>
        <th rowspan="3" width="150">客户绑定电话：</th>
        <td rowspan="3" width="260">
            <?php
            echo Yii::app()->controller->showPhoneNumber($vip->attributes) . "<br>";
            //echo $vip->phone."<br/>";
            foreach ($vipPhone as $list) {
                echo Yii::app()->controller->showPhoneNumber($list) . "<br>";
                //echo $list['phone']."<br />";
            }

            ?>
        </td>
    </tr>
    <tr>
        <th>客户姓名：</th>
        <td><?php echo $vip->name; ?></td>
    </tr>
    <tr>
        <th>账户余额：</th>
        <td><?php echo $vip->balance; ?></td>
    </tr>
</table>

<h4>充值记录</h4>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'vip-grids',
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
            'value' => '(VipTrade::$trans_type[$data->type])'
        ),
        array(
            'name' => '充值备注',
            'value' => '$data->comment'
        ),
        array(
            'name' => '余额',
            'value' => '($data->balance == 0) ? "--" : $data->balance'
        ),
    ),
));
?>

<h4>消费记录</h4>
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

<script>
    $('#print').click(function () {
        start_time = $("#VipTrade_start_time").val();
        end_time = $("#VipTrade_end_time").val();
        if (start_time == '') {
            alert('选择开始时间！');
        } else if (end_time == '') {
            alert('选择结束时间！');
        } else {
            href = '<?php echo Yii::app()->createUrl('vip/print',array('id'=>$_GET['id']));?>' + '&start_time=' + start_time + '&end_time=' + end_time;
            window.location.href = href;
        }
    });

    $('#export_account').click(function () {
        start_time = $("#VipTrade_start_time").val();
        end_time = $("#VipTrade_end_time").val();
        if (start_time == '') {
            alert('选择开始时间！');
        } else if (end_time == '') {
            alert('选择结束时间！');
        } else {
            href = '<?php echo Yii::app()->createUrl('vip/exportAccount',array('id'=>$_GET['id']));?>' + '&start_time=' + start_time + '&end_time=' + end_time;
            window.location.href = href;
        }
    });




</script>