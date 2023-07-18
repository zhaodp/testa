<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerScriptFile(SP_URL_JS.'highcharts.js', CClientScript::POS_END);

$categories = $order_ready = $order_cancel = $order_complate = $order_not_comfirm = $order_comfirm ='';

$last_city = -1;
foreach($order as $item) {
	if ($last_city==-1||$last_city!=$item['city_id']) {
		$categories .= '"'. Dict::item('city', $item['city_id']).'",';
		$last_city = $item['city_id'];
	}

	switch ($item['status']){
		case Order::ORDER_READY:
			$order_ready .= sprintf('%s,',$item['sum']);
			break;
		case Order::ORDER_CANCEL:
			$order_cancel .= $item['sum'].',';
			break;
		case Order::ORDER_COMPLATE:
			$order_complate .= $item['sum'].',';
			break;
		case Order::ORDER_COMFIRM:
			$order_comfirm .= $item['sum'].',';
			break;
		case Order::ORDER_NOT_COMFIRM:
			$order_not_comfirm .= $item['sum'].',';
			break;
	}
}
$categories = rtrim($categories, ',');
?>

<h2>当日订单趋势</h2>

<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'column'
            },
    
            title: {
                text: '订单趋势'
            },
    
            xAxis: {
                categories: [<?php echo $categories;?>]
            },
            yAxis: {
            	allowDecimals: false,
                min: 0,
                title: {
                    text: '预计订单数'
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    }
                }
            },            
    
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'<br/>'+
                        'Total: '+ this.point.stackTotal;
                }
            },
            plotOptions: {
                column: {
                    stacking: 'normal'
                }
            },
            series: [
				{name: '未报单',data: [<?php echo $order_ready;?>],stack: 'true'},
				{name: '销单',data: [<?php echo $order_comfirm;?>],stack: 'false'}, 
				{name: '已报单',data: [<?php echo $order_complate;?>],stack: 'true'}, 
            ]
        });
    });
    
});

</script>

<div id="container" style="height: 400px; margin: 0 auto;" class="span12"></div>


