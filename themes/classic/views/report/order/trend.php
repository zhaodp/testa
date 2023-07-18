
<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerScriptFile(SP_URL_JS.'highcharts.js', CClientScript::POS_END);

$city = Dict::items('city');
$this->pageTitle = $city[$condition['city_id']].'订单趋势统计';
?>
<h1><?php echo $this->pageTitle;  ?></h1>
<script type="text/javascript">
    $(function () {
        var chart;
        $(document).ready(function() {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'container',
                    type: 'line',
                    marginRight: 1,
                    marginBottom: 70
                },
                title: {
                    text:'<?php echo $this->pageTitle;  ?>',
                    x: -20 //center
                },
                subtitle: {
                    text: '<?php echo $chart['subtitle'];?>',
                    x: -20
                },
                xAxis: {
                    categories: [<?php echo $chart['date']?>]
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
                    shared: true,
                    crosshairs: true
                },
                legend: {
                    y: 10,
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
            if (GetDateDiff(start_time, end_time)>31) {
                alert('日期间隔不能超过31天');
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
    function changeDate(start, end) {
        jQuery('#order_start_time').val(start);
        jQuery('#end_time').val(end);
        $('.search-form form').submit();
    }
</script>

<?php $this->renderPartial('order/trend_search',array('condition'=>$condition,'date_arr'=>$date_arr));?>
<div class="span11">
    <div id="comments-grid" class="grid-view">
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
                    'value' => '$data["order_all_count"]'),
                array(
                    'name'=>'报单数',
                    'headerHtmlOptions'=>array(
                        'width'=>'80px',
                        'nowrap'=>'nowrap'
                    ),
                    'value' => '$data["order_complate_count"]'),
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
                    'name'=>'报单收入',
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
                    'value' => '$data["order_complate_count"] ? number_format($data["income_complate"]/$data["order_complate_count"],2) : "0.00"'),
                array(
                    'name'=>'公司收入占比',
                    'headerHtmlOptions'=>array(
                        'width'=>'80px',
                        'nowrap'=>'nowrap'
                    ),
                    'value' => '$data["income_complate"] ? sprintf("%.2f%%",($data["income_company"]/$data["income_complate"])*100) : "0%"'),

                array(
                    'name'=>'未报单',
                    'headerHtmlOptions'=>array(
                        'width'=>'80px',
                        'nowrap'=>'nowrap'
                    ),
                    'type'=>'raw',
                    'value' => array($this, 'NoEntry')
                ),
            ),
        ));
        Yii::app()->clientScript->registerScript('search', "
                $('.search-button').click(function(){
                    $('.search-form').toggle();
                    return false;
                });
                ");
        ?>
    </div>
</div>

