<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerScriptFile(SP_URL_JS . 'highcharts.js', CClientScript::POS_END);

?>

<h2><?php echo $title; ?>订单趋势</h2>
<script type="text/javascript">
    $(document).ready(function () {

        <?php
         foreach($city_data as $num => $data){
            $categories = $data['categorie_list'];
            $order_ready = $data['order_ready'];
            $order_comfirm = $data['order_comfirm'];
            $order_complate = $data['order_complate'];
            $order_trends = $data['order_trends'];

         echo 'charta = new Highcharts.Chart({
            chart: {
                renderTo: "container_'.$num.'",
                type: "column"
            },

            title: {
                text: "'.$num.'"
            },

            xAxis: {
                categories:'.$categories.'
                   },
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: {
                    text: "预计订单数"
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: "bold",
                        color: (Highcharts.theme && Highcharts.theme.textColor) || "gray"
                    }
                }
            },

            tooltip: {
                formatter: function () {
                    return "<b>" + this.x + "</b><br/>" +
                        this.series.name + ": " + this.y + "<br/>" +
                        "Total: " + this.point.stackTotal;
                }
            },
            plotOptions: {
                column: {
                    stacking: "normal",
		    pointPadding: 0.2,
                    borderWidth: 0,
                    pointWidth: 30
                }
            },
            series: [
                {name: "未报单", data: '.$order_ready.', stack: "true"},
                {name: "销单", data: '.$order_comfirm.', stack: "true"},
                {name: "已报单", data: '.$order_complate.', stack: "true"},
                {name: "预计订单", data: '.$order_trends.', stack: "false"},
            ]
        });';

        }
        ?>

    });

</script>
<div class="span11">
    <?php echo $driver_rank_string; ?>
</div>
<div class="span11">
    <h4>
        <?php
        $cancel = $list['driver_cancel'] + $list['customer_cancel'] + $list['dispatch_cancel'] + $list['reject_no_dispatch'];
        $total_order = $list['ready'] + $list['comfirm'] + $list['complate'] + $cancel;
        if ($total_order == 0) {
            $total_order = 1;
        }
        ?>

        <?php echo $title; ?>总订单：<?php echo $total_order; ?> &nbsp;&nbsp;
        已报单：<span style="color:rgb(70, 136, 71);"><?php echo $list['complate']; ?></span>&nbsp;&nbsp;
        未报单：<span style="color: rgb(58, 135, 173);"><?php echo $list['ready']; ?></span> &nbsp;&nbsp;
        销单：<span style="color: rgb(185, 74, 72);"><?php echo $list['comfirm']; ?></span>&nbsp;&nbsp;
        销单率：
        <?php
        $comfirm = number_format(($list['comfirm'] / $total_order) * 100, 2);
        if ($comfirm > 25) {
            $ratio_css = 'color: rgb(185, 74, 72)';
        } else {
            $ratio_css = 'color:rgb(70, 136, 71)';
        }
        ?>
        <span style="<?php echo $ratio_css; ?>">
            <?php echo $comfirm . "%"; ?>
            </span>&nbsp;&nbsp;

        <?php
        if ($cancel > 0) {
            $cancel_ratio = number_format(($cancel / $total_order) * 100, 2);
        } else {
            $cancel_ratio = 0.00;
        }
        ?>

        拒单：<span style="color: rgb(185, 74, 72);"><?php echo $cancel; ?></span>&nbsp;&nbsp;
        拒单率：<?php echo $cancel_ratio . "%"; ?>&nbsp;&nbsp;
        预计订单：<span style="color: rgb(0, 128, 128);"><?php echo $list['trends']; ?></span>&nbsp;&nbsp;
        接单司机数：<span
            style="color:rgb(70, 136, 71);"><?php echo $list['has_order_driver_count']; ?></span>
    </h4>
</div>
<div class="span11">
    拒单详情：(
    司机拒绝订单: <?php echo $list['driver_cancel']; ?>
    &nbsp;&nbsp;比率：<?php echo number_format(($list['driver_cancel'] / $total_order) * 100, 2) . "%"; ?>&nbsp;&nbsp;&nbsp;&nbsp;
    用户取消订单: <?php echo $list['customer_cancel']; ?>
    &nbsp;&nbsp;比率：<?php echo number_format(($list['customer_cancel'] / $total_order) * 100, 2) . "%"; ?>&nbsp;&nbsp;&nbsp;&nbsp;
    [其中推送失败：<?php echo $list['push_failed_num']; ?>&nbsp;&nbsp;
	 比率：<?php echo number_format(($list['push_failed_num'] / $total_order) * 100, 2) . "%"; ?>&nbsp;&nbsp;&nbsp;
	 客户销单： <?php echo($list['customer_cancel']-$list['push_failed_num']); ?>&nbsp;&nbsp;
    	 比率：<?php $other = ($list['customer_cancel']-$list['push_failed_num']); echo number_format(( $other / $total_order) * 100, 2) . "%"; ?>&nbsp;&nbsp;&nbsp;]
    未派出订单: <?php echo $list['dispatch_cancel']; ?>
    &nbsp;&nbsp;比率：<?php echo number_format(($list['dispatch_cancel'] / $total_order) * 100, 2) . "%"; ?>&nbsp;&nbsp;&nbsp;&nbsp;
    司机拒绝未派出: <?php echo $list['reject_no_dispatch']; ?>
    &nbsp;&nbsp;比率：<?php echo number_format(($list['reject_no_dispatch'] / $total_order) * 100, 2) . "%"; ?>)
</div>

<div class="well span12">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'action' => Yii::app()->createUrl($this->route),
        'method' => 'get',
    )); ?>
    <div class="span3">
        <label>订单时间</label>
        <?php
        Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
        $this->widget('CJuiDateTimePicker', array(
            'name' => 'Order[call_time]',
            'value' => isset($_GET['Order']['call_time']) ? $_GET['Order']['call_time'] : '',
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
    </div>
    <?php $this->endWidget(); ?>
</div>
<?php
foreach ($city_data as $num => $data) {
    echo '<div id="container_' . $num . '" style="height: 400px; margin: 0 auto;" class="span12"></div>';
}
?>

