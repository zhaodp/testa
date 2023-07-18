<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerScriptFile(SP_URL_JS.'highcharts.js',CClientScript::POS_END);
?>

<h2>司机在线状态趋势(<?php echo date('Y-m-d',strtotime($model->begin_date));?>)</h2>
<div class="btn-group">
	<div class="search-form">
	<?php $this->renderPartial('_search',array(
		'model'=>$model,'datelist'=>$datelist
	)); 
	?>
	</div><!-- search-form -->
</div>

<?php
	$js_template = 
<<<EOD
	var chart%s = new Highcharts.Chart({
        chart: {
            renderTo: 'container%s',
            type: 'area',
            marginBottom: 50
        },
        title: {
            text: '',
            x: -20 //center
        },
        subtitle: {
            text: '%s',
            x: -20
        },
        xAxis: {
            categories: [%s]
        },
        yAxis: {
            title: {
                text: '人数'
            },
            plotLines: [{
                value: 0,
                width: 1,
                color: '#808080'
            }]
        },
        tooltip: {
            formatter: function() {
				return '<b>'+ this.series.name + this.y + '</b><br/>' + this.x;
            }
        },
		plotOptions: {
			area: {
				marker: {
					enabled: false,
					symbol: 'circle',
					radius: 2,
					states: {hover: {enabled: true}}
				}
			}
		},            
        legend: {
            layout: 'horizontal',
            align: 'center',
            x: 0,
            y: 0,
            borderWidth: 0
        },
        series: [{
            name: '空闲',
            data: [%s]
        }, {
            name: '服务中',
            data: [%s]
        }, {
            name: '下班',
            data: [%s]
        }]
    });
    
    series = chart%s.series[2];
    if (series.visible) {
        series.hide();
    };
EOD;
?>

<script type="text/javascript">
$(function () {
    $(document).ready(function() {
    <?php
	foreach($city_worklog as $worklog){
		$id = $worklog['city_id'];
		$title = $worklog['city_name'];
		$timeLine=$worklog['data']['timeLine'];
		$freeDriver=$worklog['data']['freeDriver'];
		$busyDriver=$worklog['data']['busyDriver'];
		$offlineDriver = $worklog['data']['offlineDriver'];
		
		$js =sprintf($js_template,$id,$id,$title,$timeLine,$freeDriver,$busyDriver,$offlineDriver,$id);
		echo $js."\n";
	}
    ?>
    });
});
</script>

<div id="container1" style="height: 250px; margin: 0 auto;" class="span12"></div>
<div id="container3" style="height: 250px; margin: 0 auto;" class="span12"></div>
<div id="container4" style="height: 250px; margin: 0 auto;" class="span12"></div>
<div id="container5" style="height: 250px; margin: 0 auto;" class="span12"></div>
<div id="container6" style="height: 250px; margin: 0 auto;" class="span12"></div>
<div id="container7" style="height: 250px; margin: 0 auto;" class="span12"></div>
<div id="container2" style="height: 250px; margin: 0 auto;" class="span12"></div>

	
