<?php
$city = Dict::items('city');
Yii::app()->clientScript->registerScriptFile(SP_URL_JS.'highcharts.js',CClientScript::POS_END);
?>

<h1>司机在线情况详情</h1>

<hr/>

<form class="form-inline" action="" method="post" onsubmit="return formSubmit()">
    <div class="row-fluid">
	    <div class="span2">
	        <div><?php echo CHtml::label('选择城市','city_id');?></div>
	        <?php echo CHtml::dropDownList('city_id', $city_id, $city, array('style'=>'width:100px;')); ?>
	    </div>
        <div class="span4">
            <div><?php echo CHtml::label('选择时间', '');?></div>
            <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                'attribute'=>'visit_time',
                'language'=>'zh_cn',
                'name'=>"date_start",
                'options'=>array(
                    'showAnim'=>'fold',
                    'showOn'=>'both',
                    //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
                    'buttonImageOnly'=>true,
                    'maxDate'=>'new Date()',
                    'minDate'=>'2013-03-01',
                    'dateFormat'=>'yy-mm-dd',
                    'changeYear'=>true,
                    'changeMonth'=> true,
                ),
                'value' => $date_start,
                'skin' => 'classic',
                'htmlOptions'=>array(
                    'style'=>'width:100px',
                ),
            ));
            ?>
            --
            <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker',array(
                'attribute'=>'visit_time',
                'language'=>'zh_cn',
                'name'=>"date_end",
                'options'=>array(
                    'showAnim'=>'fold',
                    'showOn'=>'both',
                    //'buttonImage'=>Yii::app()->request->baseUrl.'/images/calendar.gif',
                    'buttonImageOnly'=>true,
                    'maxDate'=>'new Date()',
                    'minDate'=>'2013-03-01',  //数据是从3月1日开始统计的，
                    'dateFormat'=>'yy-mm-dd',
                    'changeYear'=>true,
                    'changeMonth'=> true,
                ),
                'value' => $date_end,
                'htmlOptions'=>array(
                    'style'=>'width:100px',
                ),
            ));
            ?>
        </div>
    </div>
    <div class="row-fluid" style="margin-top: 10px">
        <div class="span3">
            <?php  echo CHtml::submitButton('查询', array('class'=>'btn btn-primary')); ?>
        </div>
    </div>
</form>

<hr/>

<div id="container"></div>

<hr/>

<?php

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'driver-bonus',
	'itemsCssClass'=>'table table-striped',
	'pagerCssClass'=>'pagination text-center',
	'dataProvider'=>$dataProvider,
	'columns'=>array(

		'date'=>array(
			'header' => '日期',
			'name' => 'date',
		),
		'free'=>array(
			'header' => '空闲司机数',
			'name' => 'free',
		),
		'busy'=>array(
			'header' => '峰值期上线人数',
			'name' => 'busy',
		),
		'free_proportion' => array(
			'header' => '空闲比例',
			'name' => 'free_proportion',
            'value' => '$data["free_proportion"]."%"'
		),
		'online' =>array(
			'header' => '上线司机数',
			'name' => 'online',
		),
		'online_proportion' => array(
			'header' => '上线率',
			'name' => 'online_proportion',
            'value' => '$data["online_proportion"]."%"'
		),
		'accept' => array(
			'header' => '接单司机数',
			'name' => 'accept',
		),
		'accept_proportion' => array(
			'header' => '接单率',
			'name' => 'accept_proportion',
            'value' => '$data["accept_proportion"]."%"'
		),
		'notonline' => array(
			'header' => '未上线司机数',
			'name' => 'notonline',
		),
	)
));

?>

<script type="text/javascript">
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
                text: '<?php echo $city[$city_id];?>司机在线情况详情',
                x: -20 //center
            },
            subtitle: {
                text: '   ',
                x: -20
            },
            xAxis: {
                categories: <?php echo json_encode($simple_date_line);?>
            },
            yAxis: {
                title: {
                    text: '司机数'
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
                        this.x +': '+ this.y +'';
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
            series: <?php echo json_encode($chars_data);?>
        });

    });

    Date.prototype.dateDiff = function(interval,endTime)
    {
        switch (interval)
        {
            //計算秒差
            case "s":
                return parseInt((endTime-this)/1000);

            //計算分差
            case "n":
                return parseInt((endTime-this)/60000);

            //計算時差
            case "h":
                return parseInt((endTime-this)/3600000);

            //計算日差
            case "d":
                return parseInt((endTime-this)/86400000);

            //計算週差
            case "w":
                return parseInt((endTime-this)/(86400000*7));

            //計算月差
            case "m":
                return (endTime.getMonth()+1)+((endTime.getFullYear()-this.getFullYear())*12)-(this.getMonth()+1);

            //計算年差
            case "y":
                return endTime.getFullYear()-this.getFullYear();

            //輸入有誤
            default:
                return undefined;
        }
    }

    window.onload = function() {
        jQuery('.ui-datepicker-trigger').remove();
    }

    function formSubmit() {
        var date_start = new Date(jQuery('#date_start').val());
        var date_end = new Date(jQuery('#date_end').val());
        if (!date_start || !date_end) {
            alert('请输入查询时间');
            return false;
        }
        var date_diff = date_start.dateDiff("d", date_end);
        if (date_diff <= 0) {
            alert('终止日期不得大于起始日期');
            return false;
        }
        if (date_diff > 31) {
            alert('时间不得相差30天以上');
            return false;
        }
    }

</script>