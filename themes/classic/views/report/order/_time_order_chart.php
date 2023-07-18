<?php
Yii::app()->clientScript->registerScriptFile(SP_URL_JS . 'highcharts.js', CClientScript::POS_END);
$dayArr = array();
$dayData = array();
krsort($items);
if(!empty($items)){
    foreach ($items as $item) {
        if(!$item->day){
            continue;
        }
        $outKey = array('one','two','three','four','five','six','seven','eight','nine',
            'ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen','twenty',
            'twenty_one','twenty_two','twenty_three');
        $i = 1;
        $dayItem[0] = $item->twenty_four;
        foreach ($item->attributes as $attKey => $attItem){
            if(in_array($attKey, $outKey)){
                $dayItem[$i] = $attItem;
                $i++;
            }
        }
        $dayData[] = $dayItem;
        $dayArr[] = $item->day;
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
                        text: '订单数量'
                    },
                    xAxis: {
                        categories: ['0点','1点','2点','3点','4点','5点','6点','7点','8点','9点','10点','11点','12点','13点','14点','15点','16点','17点','18点','19点','20点','21点','22点','23点']
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
                    series: [{name: '<?php echo date('Y年m月d日',strtotime($dayArr[0])); ?>',
                            data: [<?php echo implode(',', $dayData[0]); ?>]
                            <?php if(isset($dayData[1])){ ?>},{name: '<?php echo date('Y年m月d日',strtotime($dayArr[1])); ?>',
                            data: [<?php echo implode(',', $dayData[1]); ?>]<?php } ?>
                        }]
                });
            });

        });
    </script>
    <div id="vip_report" style="min-width: 450px; height: 380px; margin: 0 auto;" class="span12"></div>
<?php } ?>