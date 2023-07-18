<?php
$city = Dict::items('city');
$this->pageTitle = $city[$condition['city_id']].'周订单趋势统计';
echo "<h1>".$this->pageTitle."</h1>";
Yii::app()->clientScript->registerScriptFile(SP_URL_JS.'highcharts.js',CClientScript::POS_END);
?>

<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'line',
                marginRight: 0,
                marginBottom: 60
            },
            title: {
                text: '',
                x: 0 //center
            },
            subtitle: {
                text: '<?php echo $chart['subtitle'];?>',
                x: -20
            },
            xAxis: {
				categories: <?php echo json_encode($chart['date']);?>
            },
            yAxis: {
                title: {
                    text: '<?php echo $chart['subtitle'];?>'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        this.y +'';
                }
            },
            legend: {
                y: 0,
                borderWidth: 1
            },
            series: [{
                name: '全部报单',
                data: <?php echo json_encode($chart['order_count']);?>
            }, {
                name: '7-22点',
                data: <?php echo json_encode($chart['chart_seven']);?>
            }, {
                name: '22-23点',
                data: <?php echo json_encode($chart['chart_twentytwo']);?>
            }, {
                name: '23-24点',
                data: <?php echo json_encode($chart['chart_twentythree']);?>
            }, {
                name: '24-7点',
                data: <?php echo json_encode($chart['chart_twentyfour']);?>
            }]
        });
		
	jQuery('[name="data_source"]').change(function(){
		formSubmit();
	});
	
	jQuery('.search-form form').submit(function(){
		var start_time = jQuery('#order_start_time').val();
		var end_time = jQuery('#end_time').val();
		if (end_time<start_time) {
			alert('结束日期不能早于开始日期');
			return false;
		}
		if (GetDateDiff(start_time, end_time)>180) {
			alert('日期间隔不能超过180天');
			return false;
		}
		if (GetDateDiff(start_time, end_time)<29) {
			alert('日期间隔不能小于30天');
			return false;
		}
	});
	
	function formSubmit() {
		$('.search-form form').submit();
	}
	//计算两个日期相差几天
	function GetDateDiff(startTime, endTime) {  
        startTime = startTime.replace(/\-/g, "/");  
        endTime = endTime.replace(/\-/g, "/");   
        var sTime = new Date(startTime);      //开始时间  
        var eTime = new Date(endTime);  //结束时间  
        var divNum = 1;  
		divNum = 1000 * 3600 * 24; 
		return parseInt((eTime.getTime() - sTime.getTime()) / parseInt(divNum));  
    } 

    });	
});
</script>

<div class="row-fluid">
	<div class="span10">
		<div id="container" style="height: 400px; margin: 0 auto;"></div>
	</div>
	<div class="span2">
		<?php $this->renderPartial('order/weekly_search',array('condition'=>$condition)); ?>
	</div>
</div>

<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'trend-grid',
    'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
    //'filter'=>$model,
    'columns'=>array(
		 array(
			'name'=>'日期',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => 'date("Y-m-d" , strtotime($data["date"]))'),
		array(
			'name'=>'城市',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["city"]'),
		array(
			'name'=>'订单总数',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["all_count"]'),
		array(
			'name'=>'报单数',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["complate_count"]'),
		array(
			'name'=>'上周同期',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["last_complate_count"]'),
		array(
			'name'=>'同比增长',
			'headerHtmlOptions'=>array(
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["complate_count"]?sprintf("%.2f%%",(($data["complate_count"] - $data["last_complate_count"])/$data["complate_count"])*100):"0%"'),
		array(
			'name'=>'人工',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["callcenter_count"]'),
		array(
			'name'=>'客户端',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["app_count"]'),
		array(
			'name'=>'总收入',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["income_complate"]'),
		array(
			'name'=>'公司收入',
			'headerHtmlOptions'=>array(
				'width'=>'80px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["income_company"]'),
		array(
				'name'=>'客单价',
				'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
				),
				'value' => '$data["complate_count"]>0 ? number_format($data["income_complate"]/$data["complate_count"],2):0'),
		array(
				'name'=>'上周同期',
				'headerHtmlOptions'=>array(
						'width'=>'80px',
						'nowrap'=>'nowrap'
				),
				'value' => '$data["last_complate_count"] > 0 ? number_format($data["last_income_complate"]/$data["last_complate_count"],2):0'),
	),
));
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
");
