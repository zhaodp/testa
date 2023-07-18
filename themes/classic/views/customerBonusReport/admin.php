<?php
/* @var $this CustomerBonusReportController */
/* @var $model CustomerBonusReport */
Yii::app()->clientScript->registerScriptFile(SP_URL_JS . 'highcharts.js', CClientScript::POS_END);
$this->breadcrumbs = array(
    'Customer Bonus Reports' => array('index'),
    'Manage',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#customer-bonus-report-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
});
");

$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'mydialog',
    // additional javascript options for the dialog plugin
    'options' => array(
        'title' => '订单信息',
        'autoOpen' => false,
        'width' => '750',
        'height' => '450',
        'modal' => true,
        'buttons' => array(
            'OK' => 'js:function(){$(this).dialog("close");}',
        ),
    ),
));

echo '<div id="dialogdiv"></div>';
echo '<iframe id="create_complaint_frame" width="100%" height="100%" style="border:0px"></iframe>';
$this->endWidget('zii.widgets.jui.CJuiDialog');
?>

<h1>优惠劵绑定、消费一览表</h1>


<div class="row-fluid">
    <div id="container" style="height: 400px;" class="well span12"></div>
</div>
<script type="text/javascript">
    $(function () {
        var chart;
        $(document).ready(function () {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'container',
                    type: 'line'
                },

                title: {
                    text: '<?php echo $line['city_name']; ?>优惠劵'
                },
                xAxis: {
                    categories: [<?php echo $line['date']; ?>]
                },
                yAxis: {
                    allowDecimals: false,
                    min: 0,
                    title: {
                        text: ''
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
                    formatter: function () {
                        return '<b>' + this.series.name + ': </b>' + this.y + '<br/>';
                    }
                },
                plotOptions: {
                    column: {
                        stacking: 'normal'
                    }
                },
                series: [
                    {name: '绑定', data: [<?php echo $line['bonus']; ?>]},
                    {name: '已使用', data: [<?php echo $line['used']; ?>]},
                ]
            });
        });

    });

</script>

<div class="well search-form">
    <?php $this->renderPartial('_search', array(
        'model' => $model,
        'params' => $params,
    )); ?>

    <?php
    if (!empty($customerReport)) {
        echo "<h4>在选择时间内，" . $line['city_name'] . "绑定" . number_format($customerReport['bonus_count']) . '个，消费' . number_format($customerReport['used_count']) . '个，返现' . number_format($customerReport['amount'], 2) . '元。</h4>';
    }
    ?>


</div>
<!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'customer-bonus-report-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'table',
    'pagerCssClass' => 'pagination text-right',
    'pager' => Yii::app()->params['formatGridPage'],
    'columns' => array(
        array(
            'name' => '排行',
            'value' => '$this->grid->dataProvider->getPagination()->getOffset() + ($row + 1)'
        ),
        'name',
        'driver_id',
        array(
            'name' => 'bonus_sn',
//            'type' => 'raw',
//            'value' => 'CHtml::link($data->bonus_sn, "javascript:void(0);", array("onclick"=>"{orderDialogdivsInit(\'$data->bonus_sn\');}"))'
        ),
        array(
            'name' => 'bonus_count',
            'type' => 'raw',
            'value' => 'CHtml::link($data->bonus_count, "javascript:void(0);", array("onClick"=>"{bonusInit(\'$data->bonus_sn\',1);}"))'
        ),
        array(
            'name' => 'used_count',
            'type' => 'raw',
            'value' => 'CHtml::link($data->used_count, "javascript:void(0);", array("onClick"=>"{usedInit(\'$data->bonus_sn\');}"))'
        ),
        'amount',
        /*
        'report_time',
        'created',

        array(
            'class' => 'CButtonColumn',
        ),
        */
    ),
)); ?>

<script type="text/javascript">
    function bonusInit(bonus,type){
        $(".ui-dialog-title").html("优惠劵绑定");
        $('#dialogdiv').html('');
        $("#create_complaint_frame").height('100%');
        var report_time = $("#CustomerBonusReport_report_time").val();
        var created = $("#CustomerBonusReport_created").val();
        var url = '<?php echo Yii::app()->createUrl('/CustomerBonusReport/view');?>&bonus_sn=' + bonus + '&report_time=' + report_time + '&created=' + created + '&type=' + type;
        $("#create_complaint_frame").attr("src", url);
        $("#mydialog").dialog("open");
    }

    function usedInit(bonus_sn) {
        $(".ui-dialog-title").html("优惠劵绑定");
        $('#dialogdiv').html('');
        $("#create_complaint_frame").height('100%');
        var report_time = $("#CustomerBonusReport_report_time").val();
        var created = $("#CustomerBonusReport_created").val();
        var url = '<?php echo Yii::app()->createUrl('/CustomerBonusReport/UsedView');?>&bonus_sn=' + bonus_sn + '&report_time=' + report_time + '&created=' + created;
        $("#create_complaint_frame").attr("src", url);
        $("#mydialog").dialog("open");
    }
</script>