<?php
Yii::app()->clientScript->registerScriptFile(SP_URL_JS . 'highcharts.js', CClientScript::POS_END);
$monthArr = array();
$sumPrice = array();
$orderCount = array();
$avePrice = array();
$aveCost = array();
krsort($items);
foreach ($items as $item) {
    if(!$item->month){
        continue;
    }
    $monthArr[] = $item->month;
    $sumPrice[] = $item->vip_cost_sum_month ? $item->vip_cost_sum_month : 0;
    $orderCount[] = $item->vip_order_count_month ? $item->vip_order_count_month : 0;
    $avePrice[] = ($item->vip_order_count_month > 0 && ($ave = ($item->vip_cost_sum_month / $item->vip_order_count_month))) ? substr($ave, 0, strpos($ave, ".") + 3) : 0;
    $aveCost[] = ($item->vip_count_month > 0 && ($ave = ($item->vip_cost_sum_month / $item->vip_count_month))) ? substr($ave, 0, strpos($ave, ".") + 3) : 0;
}
unset($items);
?>
<script type="text/javascript">
    $(function() {
        var chart;
        $(document).ready(function() {
            chart = new Highcharts.Chart({
                credits: {
                    enabled: false
                },
                chart: {
                    renderTo: 'vip_report',
                    type: 'line'
                },
                title: {
                    text: '全部订单趋势统计'
                },
                xAxis: {
                    categories: [<?php echo '"' . implode('","', $monthArr) . '"' ?>]
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: ''
                    }
                },
                tooltip: {
                    shared: true,
                    crosshairs: true
                },
                series: [{name: 'vip消费总额',
                        data: [<?php echo implode(',', $sumPrice); ?>]}, {name: 'vip订单数量',
                        data: [<?php echo implode(',', $orderCount); ?>]}, {name: '客单价',
                        data: [<?php echo implode(',', $avePrice); ?>]}, {name: '月均消费',
                        data: [<?php echo implode(',', $aveCost); ?>]
                    }]
            });
        });

    });
</script>
<div id="vip_report" style="min-width: 450px; height: 380px; margin: 0 auto;" class="span12"></div>

