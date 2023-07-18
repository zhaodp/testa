<?php
$this->pageTitle = '订单月报统计';
echo "<h1>".$this->pageTitle."</h1><br />";
?>
<?php
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
));
echo CHtml::label('城市','city_id');
echo CHtml::dropDownList('city_id', $condition['city_id'],Common::getOpenCity());
$this->endWidget();
?>
<?php
Yii::app()->clientScript->registerScriptFile(SP_URL_JS.'highcharts.js',CClientScript::POS_END);
$i = 1;
foreach ($dataProvider as $key=>$val) {
?>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: '<?php echo "container".$i?>',
                type: 'column'
            },
            title: {
                text: '<?php echo $key."月报统计"?>'
            },
            subtitle: {
                text: '单数'
            },
            xAxis: {
                categories: [<?php echo $val['month'];?>]
            },
            yAxis: {
                min: 0,
                title: {
                    text: '单'
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    }
                }
            },
            legend: {
                layout: 'vertical',
                backgroundColor: '#FFFFFF',
                align: 'left',
                verticalAlign: 'top',
                x: 100,
                y: 70,
                floating: true,
                shadow: false
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                        this.y +'单';
                }
            },
            plotOptions: {
                column: {
                	pointWidth: 30,
                	dataLabels: {
	                    enabled: true,
	                    
	                } 
                }, 
            },
            series: [{
                name: '报单单数',
                data: [<?php echo $val['order_complate_count'];?>]
            }]
        });
    });
    
});
</script>
<div id="<?php echo 'container'.$i;?>" style="min-width: 450px; height: 380px; margin: 0 auto;"></div>
<br />
<?php
    $i += 1;
}
?>
<script>
jQuery(document).ready(function(){
	jQuery('#city_id').change(function(){
		jQuery('#yw0').submit();
		//Window.location.href = '<?php echo Yii::app()->createUrl($this->route); ?>';
	});
})
</script>