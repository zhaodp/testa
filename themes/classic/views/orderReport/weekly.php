<?php
$this->pageTitle = '订单周报统计';
echo "<h1>".$this->pageTitle."</h1><br />";
?>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'line',
                marginRight: 130,
                marginBottom: 25
            },
            title: {
                text: '订单周报统计',
                x: -20 //center
            },
            subtitle: {
                text: '单数',
                x: -20
            },
            xAxis: {
                categories: [<?php echo $chart['date']?>]
            },
            yAxis: {
                title: {
                    text: '单数'
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
                        this.y +'单';
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                borderWidth: 0
            },
            series: [{
                name: '全部报单',
                data: [<?php echo $chart['order_count']?>]
            }, {
                name: '7-22点',
                data: [<?php echo $chart['chart_seven']?>]
            }, {
                name: '22-23点',
                data: [<?php echo $chart['chart_twentytwo']?>]
            }, {
                name: '23-24点',
                data: [<?php echo $chart['chart_twentythree']?>]
            }, {
                name: '24-7点',
                data: [<?php echo $chart['chart_twentyfour']?>]
            }]
        });
    });
    
});
		</script>
<div id="container" style="min-width: 450px; height: 350px; margin: 0 auto;"></div>
<br />
<?php
echo "<div class='btn-group'>";
echo CHtml::link('高级搜索', array("#"),array('class'=>"search-button btn-primary btn"));
echo CHtml::link('上周数据', array("orderReport/weekly", "date"=>$condition['date']),array('class'=>"btn"));
echo '</div>';

echo "<div class='search-form' style='margin-top:10px;display:none;'>";
echo '<div class="span12">';
$city = Dict::items('city');
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
));
Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
echo '开始日期：';
$this->widget('CJuiDateTimePicker', array (
    'id' => 'condition_start_time',
	'name'=>'start_time', 
	'value'=>$condition['start_time'], 
	'mode'=>'date',
	'options'=>array (
	    'width' => '60',
		'dateFormat'=>'yy-mm-dd'
	),
	'htmlOptions'=>array(
         'style'=>'width:100px;'
     ),
	'language'=>'zh'
));
echo "&nbsp;&nbsp;";
echo '结束日期：';
$this->widget('CJuiDateTimePicker', array (
    'id' => 'condition_end_time',
	'name'=>'end_time', 
	'value'=>$condition['end_time'], 
	'mode'=>'date',
	'options'=>array (
		'dateFormat'=>'yy-mm-dd'
	), 
	'htmlOptions'=>array(
         'style'=>'width:100px;'
     ),
	'language'=>'zh'
));
echo "&nbsp;&nbsp;";
echo '时间段：';
echo "<select id='condition_time_part' name='time_part' style='width:80px;'>";
echo "<option value=''>全部</option>";
echo $condition['time_part'] == 7 ? "<option value='7' selected>7-22点</option>" : "<option value='7'>7-22点</option>";
echo $condition['time_part'] == 22 ? "<option value='22' selected>22-23点</option>" : "<option value='22'>22-23点</option>";
echo $condition['time_part'] == 23 ? "<option value='23' selected>23-24点</option>" : "<option value='23'>23-24点</option>";
echo $condition['time_part'] == 24 ? "<option value='24' selected>24-7点</option>" : "<option value='24'>24-7点</option>";
echo "</select>";
echo "&nbsp;&nbsp;";
$city = Dict::items('city');
echo '城市：';
echo "<select id='condition_city_id' name='city_id' style='width:80px;'>";
foreach ($city as $k=>$v)
{
	echo $condition['city_id'] == $k ? "<option value='".$k."' selected>".$v."</option>" : "<option value='".$k."' >".$v."</option>";
}
echo "</select>&nbsp;&nbsp;";
echo '渠道：';
echo "<select id='condition_source' name='source' style='width:80px;'>";
echo "<option value=0>全部</option>";
echo "</select>&nbsp;&nbsp;";
echo CHtml::submitButton('Search');
$this->endWidget();
echo '</div>';
echo '</div>';


$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'weekly-grid',
    'dataProvider'=>$dataProvider,
	'itemsCssClass'=>'table table-striped',
    //'filter'=>$model,
    'columns'=>array(
		 array(
			'name'=>'日期',
			'headerHtmlOptions'=>array(
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value' => 'date("Y-m-d" , strtotime($data["date"]))." 星期".$data["week_cn"]'),
		array(
			'name'=>'城市',
			'headerHtmlOptions'=>array(
				'width'=>'20px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["city"]'),
		 array(
			'name'=>'渠道',
			'headerHtmlOptions'=>array(
				'width'=>'40px',
				'nowrap'=>'nowrap'
			),
			'value' => '0'),
		array(
			'name'=>'订单总数',
			'headerHtmlOptions'=>array(
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["all_count"]'),
		array(
			'name'=>'同比增长',
			'headerHtmlOptions'=>array(
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["all_count"]?sprintf("%.2f%%",(($data["all_count"] - $data["last_all_count"])/$data["all_count"])*100):"0%"'),
		array(
			'name'=>'报单单数',
			'headerHtmlOptions'=>array(
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["complate_count"]'),
		array(
			'name'=>'同比增长',
			'headerHtmlOptions'=>array(
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["complate_count"]?sprintf("%.2f%%",(($data["complate_count"] - $data["last_complate_count"])/$data["complate_count"])*100):"0%"'),
		array(
			'name'=>'总收入',
			'headerHtmlOptions'=>array(
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["income_complate"]'),
		array(
			'name'=>'同比增长',
			'headerHtmlOptions'=>array(
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["income_complate"]?sprintf("%.2f%%",(($data["income_complate"] - $data["last_income_complate"])/$data["income_complate"])*100):"0%"'),
		array(
			'name'=>'公司收入',
			'headerHtmlOptions'=>array(
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["income_company"] ? $data["income_company"] : 0'),
		array(
			'name'=>'同比增长',
			'headerHtmlOptions'=>array(
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'value' => '$data["income_company"]?sprintf("%.2f%%",(($data["income_company"] - $data["last_income_company"])/$data["income_company"])*100):"0%"'),	
		array(
			'name'=>'未完成单数',
			'headerHtmlOptions'=>array(
				'width'=>'30px',
				'nowrap'=>'nowrap'
			),
			'type'=>'raw',
			'value' => 'CHtml::link($data["ready_count"], array("order/admin", "Order"=>array("location_start"=>$data["city_id"], "status" =>0, "call_time"=>date("Y-m-d", strtotime($data["date"])) . " 07:00", "booking_time"=>date("Y-m-d", strtotime($data["date"]) + 86400) . " 07:00")))'),
     ),
));

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('weekly-grid', {
		data: $(this).serialize()
	});
	
	return false;
});
");
//sprintf("%.2f%%",(($data["all_count"] - $data["last_all_count"])/$data["last_all_count"])*100);
?>