<?php

Yii::app()->clientScript->registerScriptFile(SP_URL_JS.'highcharts.js',CClientScript::POS_END);
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('notice-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>每日用户状况趋势</h1>
<div class="btn-group">
	<?php echo CHtml::link('高级搜索','#',array('class'=>'search-button btn-primary btn')); ?>
</div>
<div class="search-form" style="display:none">
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
<section>
		
		<label>日期</label>		
		<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'active_customer[start_date]', 
				//'model'=>$model,  //Model object
				'value'=>'', 
				'mode'=>'date',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh'
			));
			?>	
		<?php
			Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
			$this->widget('CJuiDateTimePicker', array (
				'name'=>'active_customer[end_date]', 
				//'model'=>$model,  //Model object
				'value'=>'', 
				'mode'=>'date',  //use "time","date" or "datetime" (default)
				'options'=>array (
					'dateFormat'=>'yy-mm-dd'
				),  // jquery plugin options
				'language'=>'zh'
			));
			?>	
		<?php echo CHtml::submitButton('Search'); ?>
</section>
<?php $this->endWidget(); ?>

</div><!-- search-form -->

<?php

$freshs = "";
$repeats = "";
$actives = "";
$fresh_actives = "";
$repeat_actives = "";
$timeLine = "";

foreach ($dataProvider as $key => $data){
	$actives .= (isset($data['active'])?$data['active']:0) . ",";
	$fresh_actives .= (isset($data['fresh_active'])?$data['fresh_active']:0) . ",";
	$repeat_actives .= (isset($data['repeat_active'])?$data['repeat_active']:0) . ",";
	$freshs .= $data['refresh'] . ",";
	$repeats .= $data['rerepeat'] . ",";
	$timeLine .= date('m-d', strtotime($data['current_date'])) . ",";
}


$timeLine = "'" . str_replace(",", "','", $timeLine) . "'";
?>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'area',
                marginRight: 130,
                marginBottom: 25
            },
            title: {
                text: '',
                x: -20 //center
            },
            subtitle: {
                text: '',
                x: -20
            },
            xAxis: {
                categories: [<?php echo $timeLine; ?>]
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
    					states: {
    						hover: {
    							enabled: true
    						}
    					}
    				}
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
                name: '活跃用户',
                data: [<?php echo $actives; ?>]
            },{
                name: '老用户访问',
                data: [<?php echo $repeat_actives; ?>]
            }, {
                name: '老用户订单',
                data: [<?php echo $repeats; ?>]
            },{
                name: '新用户订单',
                data: [<?php echo $freshs; ?>]
            }, {
                name: '新用户访问',
                data: [<?php echo $fresh_actives; ?>]
            }]
        });
    });
    
});
</script>



<div id="container" style="min-width: 400px; height: 420px; margin: 0 auto;"></div>
	
