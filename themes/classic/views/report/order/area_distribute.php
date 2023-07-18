<?php
$this->pageTitle = '订单分布';
echo "<h1>".$this->pageTitle."</h1><br />";
Yii::app()->clientScript->registerScriptFile(SP_URL_JS.'highcharts.js',CClientScript::POS_END);
$i = 1;
foreach ($charts as $key=>$val) {
	$content = array_keys($val);
    $content_arr = explode("-" , $content[0]);
	foreach ($val[$content[0]] as $key1=>$val1) {
?>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
		
		// Build the chart
        chart = new Highcharts.Chart({
            chart: {
                renderTo: '<?php echo "container".$i?>',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
            	<?php 
            	    if ($key1 == 0) {
            	    	$text = $content_arr[1]."-成单数据";
            	    } else {
            	    	$text = $content_arr[1]."-销单数据";
            	    }
            	    echo "text: '".$text."'";
            	?>
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage}%</b><br />单数: {point.y}',
            	percentageDecimals: 1
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +'% ('+ this.y + ')';
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: '订单数分布占比',
                data: [
                    <?php
                        foreach ($val1 as $data_key=>$data_val) {
                    		echo "['".$data_key."' , ".$data_val."],";
                        }
                    ?>
                ]
            }]
        });
    });
    
});
</script>
<?php
        echo "<div style='width:100%;height:405px;'>";
        if ($i%2 == 0) {
        	echo "<div id='container".$i."' style='width:50%;float:right'></div></div><br />";
        }else {
        	echo "<div id='container".$i."' style='width:50%;float:left;'></div>";
        }
	    $i += 1;
    }
}
?> 