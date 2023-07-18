<?php
Yii::app()->clientScript->registerScriptFile(SP_URL_JS . 'highcharts.js', CClientScript::POS_END);
$weekthArr = array();
$aveCost = array();
$weekOrderPrice = array();
$weekOrderCount = array();
krsort($items);
foreach ($items as $item) {
    if (!$item->weekth) {
        continue;
    }
    $weekthArr[] = date('y.m.d', $item->start_time) . '-' . date('y.m.d', $item->end_time - 1);
    $aveCost[] = $item->ave_cost;
    $weekOrderPrice[] = $item->week_order_price;
    $weekOrderCount[] = $item->week_order_count;
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
                    categories: [<?php echo '"' . implode('","', $weekthArr) . '"' ?>],
                    labels: {
                        rotation: 25,
                        style: {color: 'gray'},
                        formatter: function() {
                            $xx = this.value;
                            $x1 = $xx.substr(6, 2);
                            $x2 = $xx.substr(-2, 2);
                            return $x1 + '-' + $x2;
                        }
                    }
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
                series: [{name: '本周订单数量',
                        data: [<?php echo implode(',', $weekOrderCount); ?>]}, {name: '本周消费',
                        data: [<?php echo implode(',', $weekOrderPrice); ?>]}, {name: '平均周消费',
                        data: [<?php echo implode(',', $aveCost); ?>]
                    }]
            });
        });

    });
</script>
<div id="vip_report" style="min-width: 450px; height: 380px; margin: 0 auto;"></div>

