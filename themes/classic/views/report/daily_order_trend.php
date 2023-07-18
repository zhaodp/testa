<?php
$this->pageTitle ='七日订单趋势';
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerScriptFile(SP_URL_JS.'highcharts.js', CClientScript::POS_END);

?>

<h1>七日订单趋势</h1>

<div class="container-fluid">
    <?php foreach($cityArr as $k=>$v) {  if($k>0){ ?>
    <script type="text/javascript">
        var chart;
        $(function () {
            chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'container<?php echo $k;?>',
                    type: 'line',//area line
                    marginRight: 20,
                    marginBottom: 50
                },
                title: {
                    text:'',
                    x: -20 //center
                },
                subtitle: {
                    text:'',
                    x: -20
                },
                xAxis: {
                    categories: <?php echo $xAxis; ?>,
                    tickmarkPlacement: 'on',
                    title: {
                        enabled: false
                    }
                },
                yAxis: {
                    title: {
                        text: '<?php echo $v; ?>历史订单数量'
                    },
                    plotLines: [
                        {
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }
                    ]
                },
                tooltip: {
                    shared: true,
                    crosshairs: true,
                    formatter: function() {

                        var s = '<b>'+ this.x +'</b>';
                        var total=cancel=all_cancel=0;
                        $.each(this.points, function(i, point) {
                            s += '<br/>'+ point.series.name +': '+
                                point.y ;
                            if(point.series.name=='总订单'){
                                total=point.y;
                            }
                            if(point.series.name=='销单'){
                                cancel=point.y;
                            }
                            if(point.series.name=='取消'){
                                all_cancel=point.y;
                            }
                        });

                        if(total>0 && cancel>0){
                            s+='<br/>销单率：'+Math.round(cancel/total*100)+'%';
                        }
                        if(total>0 && all_cancel>0){
                            s+='<br/>取消率：'+Math.round(all_cancel/total*100)+'%';
                        }

                        return s;


                    },
                    backgroundColor: '#FCFFC5',
                    borderColor: '#DDD',
                    borderRadius: 10,
                    borderWidth: 1
                },
                legend: {
//                    layout: 'vertical',
//                    align: 'bottom',
//                    verticalAlign: 'middle',
                    y: 10,
                    borderWidth: 0
                },

                series: [
                    {
                        name: '总订单',
                        data: <?php echo '['.implode(",",$chartData[$k]['order_count']).']'; ?>
                    },
                    {
                        name: '呼叫中心',
                        data: <?php echo '['.implode(",",$chartData[$k]['callcenter_order_count']).']'; ?>
                    },
                    {
                        name: 'APP',
                        data: <?php echo '['.implode(",",$chartData[$k]['app_order_count']).']'; ?>
                    },
                    {
                        name: '报单',
                        data: <?php echo '['.implode(",",$chartData[$k]['complete_order']).']'; ?>
                    },
                    {
                        name: '销单',
                        data: <?php echo '['.implode(",",$chartData[$k]['cancel_order']).']'; ?>
                    },
                    {
                        name: '取消',
                        data: <?php echo '['.implode(",",$chartData[$k]['all_cancel']).']'; ?>
                    }



                ]
            });
        });
    </script>

    <div class="row-fluid">
        <div class="span8">
            <div  class="span11" style="text-align: center;"><?php if(isset($driverRank[$k])) echo $driverRank[$k]; ?></div>
            <div id="container<?php echo $k;?>" style="min-width: 310px; height: 320px; margin: 0 auto" class="span12">
            </div>
        </div>
        <div class="span4">
            <label style="color: red;">（计算周期当日7:00到次日7:00, 数据10分钟更新）</label>

            <table class="table table-bordered" style="font-size:12px; height:300px;">
                <tbody>
                <tr>
                    <td rowspan="3" class="text-info" style="width:20px;"><h5>订<br/>单<br/>来<br/>源</h5></td>
                    <td style="width:90px;">呼叫中心订单</td>
                    <td style="width:50px;"><?php echo $tableData[$k]['callcenter_order_count']; ?></td>
                    <td style="width:80px;">APP订单</td>
                    <td style="width:50px;"><?php echo $tableData[$k]['app_order_count']; ?></td>
                </tr>
                <tr>
                    <td>手机订单</td>
                    <td><?php echo $tableData[$k]['mobile_order_count']; ?></td>
                    <td>固话订单 </td>
                    <td><?php echo $tableData[$k]['tel_order_count']; ?></td>
                </tr>
                <tr>
                    <td>新用户订单</td>
                    <td><?php echo $tableData[$k]['new_user_order']; ?></td>
                    <td>老用户订单 </td>
                    <td><?php echo $tableData[$k]['old_user_order']; ?></td>
                </tr>
                <tr>
                    <td  rowspan="3"  class="text-warning"><h5>运<br/>营<br/>统<br/>计</h5></td>
                    <td>已接单司机</td>
                    <td><?php echo $tableData[$k]['have_order_driver']; ?></td>
                    <td>已上线司机</td>
                    <td><?php echo $tableData[$k]['online_driver']; ?></td>
                </tr>

                <tr>
                    <td>上线未接单司机</td>
                    <td><?php echo $tableData[$k]['online_no_order']; ?></td>
                    <td>代驾中司机</td>
                    <td><?php echo $tableData[$k]['on_service_driver']; ?></td>
                </tr>
                <tr>
                    <td>今日报单</td>
                    <td><?php echo $tableData[$k]['complete_order']; ?></td>
                    <td>今日消单</td>
                    <td><?php echo $tableData[$k]['cancel_order']; ?></td>
                </tr>
                <tr>
                    <td rowspan="3" class="text-success"><h5>在线</h5></td>
                    <td >服务中司机</td>
                    <td><?php echo $tableData[$k]['service_driver']; ?></td>
                    <td>空闲司机</td>
                    <td><?php echo $tableData[$k]['idle_drivers']; ?></td>
                </tr>


                </tbody>
            </table>
        </div>
    </div>
    <br/>
  <?php  }} ?>
</div>


