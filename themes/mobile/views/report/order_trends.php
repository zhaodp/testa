<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerScriptFile(SP_URL_JS.'highcharts.js', CClientScript::POS_END);

?>

<h4>当日订单趋势</h4>

<div class="span11">
    <?php echo $driver_rank_string; ?>
</div>
<div class="span11">
    <h5>
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
    </h5>
</div>
<div class="span11">
    拒单详情：(
    司机拒绝订单: <?php echo $list['driver_cancel']; ?>
    &nbsp;&nbsp;比率：<?php echo number_format(($list['driver_cancel'] / $total_order) * 100, 2) . "%"; ?>&nbsp;&nbsp;&nbsp;&nbsp;
    用户取消订单: <?php echo $list['customer_cancel']; ?>
    &nbsp;&nbsp;比率：<?php echo number_format(($list['customer_cancel'] / $total_order) * 100, 2) . "%"; ?>&nbsp;&nbsp;&nbsp;&nbsp;
    未派出订单: <?php echo $list['dispatch_cancel']; ?>
    &nbsp;&nbsp;比率：<?php echo number_format(($list['dispatch_cancel'] / $total_order) * 100, 2) . "%"; ?>&nbsp;&nbsp;&nbsp;&nbsp;
    司机拒绝未派出: <?php echo $list['reject_no_dispatch']; ?>
    &nbsp;&nbsp;比率：<?php echo number_format(($list['reject_no_dispatch'] / $total_order) * 100, 2) . "%"; ?>)
</div>


