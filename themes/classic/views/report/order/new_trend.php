
<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerScriptFile(SP_URL_JS.'highcharts.js', CClientScript::POS_END);

$city = Dict::items('city');
$this->pageTitle = $city[$condition['city_id']].'订单趋势统计';
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
                    marginBottom: 35
                },
                title: {
                    text: '订单趋势统计',
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
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'top',
                    x: 10,
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


<div class="span11">
    <ul class="nav nav-pills">
        <?php
        $data_source_arr = OrderStat::$data_source;
        $default_source=$condition['data_source'];

        foreach ($data_source_arr as $k=>$v) {
            $light='';
            if($default_source==$k)
                $light='class="active"';
            echo "<li ".$light."><a href='".Yii::app()->createUrl('report/trend',array('data_source'=>$k))."'>".$v."</a></li>";
        }
        ?>
    </ul>

    <div class="search-form">
      <?php  $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
        )); ?>
        <div class="span11">
            <div class="thumbnail">
                <div class="row-fluid">
                    <div class="span2">
                        <label>开始日期</label>
                        <?php Yii::import('application.extensions.CJuiDateTimePicker.CJuiDateTimePicker');
                            $this->widget('CJuiDateTimePicker', array (
                                'id' => 'order_start_time',
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
                            ));?>
                    </div>
                    <div class="span2">
                        <label>结束日期</label>
                        <?php $this->widget('CJuiDateTimePicker', array (
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
                        ));?>
                    </div>
                    <div class="span2">
                        <label>城市</label>
                        <?php echo CHtml::dropDownList('city_id',$condition['city_id'], Dict::items('city'),array('style'=>'width:100px;')); ?>
                    </div>
                    <div class="span2">
                        <label>时间段</label>
                        <?php echo CHtml::dropDownList('time_part',$condition['time_part'],
                            array(
                                ''=>'全部',
                                '7'=>'7-22点',
                                '22'=>'22-23点',
                                '23'=>'23-24点',
                                '24'=>'24-7点'),
                            array('style'=>'width:100px;')); ?>
                    </div>
                    <div class="span2">
                        <label>距离范围</label>
                        <?php echo CHtml::dropDownList('distance_area',$condition['distance_area'],
                            array(
                                ''=>'全部',
                                '5'=>'5公里',
                                '10'=>'10公里',
                                '20'=>'20公里',
                                '30'=>'30公里',
                                '9999'=>'30公里以上'),
                            array('style'=>'width:100px;')); ?>
                    </div>

                </div>
                <div class="row-fluid">
                    <div class="span2">
                        <?php echo  CHtml::submitButton('查询当月',array('class'=>'btn btn-info span9','onclick'=>"changeDate('".$date_arr['current_month_frist_day']."', '".$date_arr['current_day']."')")); ?>
                    </div>
                    <div class="span2">
                        <?php echo  CHtml::submitButton('查询上月',array('class'=>'btn btn-info span9','onclick'=>"changeDate('".$date_arr['last_month_frist_day']."', '".$date_arr['last_month_last_day']."')")); ?>
                    </div>
                    <div class="span2">
                        <?php echo  CHtml::submitButton('查询上上月',array('class'=>'btn btn-info span9','onclick'=>"changeDate('".$date_arr['last_last_frist_day']."', '".$date_arr['last_last_last_day']."')")); ?>
                    </div>
                    <div class="span2">
                        <?php echo CHtml::hiddenField('data_source',$condition['data_source']) ?>
                        <?php echo  CHtml::submitButton('搜索',array('class'=>'btn btn-success span9')); ?>
                    </div>
                </div>
            </div>
    </div>
        <?php $this->endWidget();?>
    </div>
</div>

<div class="span11">
     <div id="container" style="min-width: 400px; height: 420px; margin: 0 auto;"></div>
</div>

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

