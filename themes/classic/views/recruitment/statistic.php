<?php
$selCityId = isset($_REQUEST['city_id'])? $_REQUEST['city_id'] : Yii::app()->user->city;
?>
<head lang="zh-CN">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>e代驾</title>
    <link rel = "Shortcut Icon" href=img/favicon.ico>

    <style type="text/css">
    * {
      margin: 0;
      padding: 0:;
    }

    .highcharts-legend-item{
        visibility: hidden;
        z-index: -100;
    }
    .apply-control {
      padding: 20px 30px;
      width: 1305px;
    }

    .city-choose label {
      font-size: 14px;
      padding-right: 10px;
    }

    .city-choose .city-select {
      height: 30px;
      width: 170px;
      font-size: 14px;
      line-height: 30px;
      color: #555555;
      background: #FFF;
      vertical-align: middle;
      -webkit-border-radius: 4px;
      -moz-border-radius: 4px;
      border-radius: 4px;
    }

    .city-choose .query-result {
      margin-left: 10px;
      background: #77CC77;
      border: none;
      font-size: 14px;
      border-radius: 3px;
      padding: 3px 10px;
      height: 28px;
      width: 50px;
      color:#FFF;
    }

    .apply-plan {
      margin-top: 30px;
      width: 1305px;
      height: 80px;
      padding-top: 20px;
      background: #F6F8F9;
    }

    .plan-table {
      width: 90%;
    } 

    .plan-table th {
      font-size: 14px;
      line-height: 30px;
      height: 30px;
    }

    .plan-table td {
      font-size: 16px;
      text-align: center;
    }
    .chart-con {
      width: 1305px;
      min-height: 350px;
      padding-top: 30px;
      overflow: hidden;
    }

    .chart-con .right-chart {
/*      float: right;
      overflow: hidden;
*/
    }

    .chart-con .left-chart {
/*
      float: left;
      overflow: hidden;
*/
    }
</style>
</head>
<body>
<h1>司机招聘监控</h1>
  <div class="apply-control">
<?php $form = $this->beginWidget('CActiveForm',
    array('action'=>
$url.'&complete_year=' . $params['complete_year'] . '&funnel_month=' . $params['funnel_month'] .
'&funnel_year=' . $params['funnel_year']),
    array('htmlOptions' => array(
        'class' => 'city-choose',
        ))
);
?>
<?php
$city = Dict::items('city');
if(Yii::app()->user->city!=0){
    foreach($city as $k=>$v){
        if($k!==Yii::app()->user->city){
            unset($city[$k]);
        }
    }
    $city[-1] ='--选择城市--';
}
echo CHtml::label('城市选择','city'); 
echo CHtml::dropDownList('city_id',
    $selCityId,
    $city,
    array(
        'ajax' => array(
            'type'=>'POST', //request type
            'url'=>Yii::app()->createUrl('recruitment/district'),
            'update'=>'#district_id', //selector to update
            'data'=>array('city_id'=>'js:$("#city_id").val()', 'admin'=>'1')
        ))
    );
?>
&nbsp;&nbsp;
<input class="btn btn-success" type="submit" value="查询" />
<?php $this->endWidget(); ?>
    <div class="apply-plan">
      <table class="plan-table">
        <tr>
          <th>本月招聘计划</th>
          <th>本月完成率</th>
          <th>本月完成量</th>
          <th>本月剩余天数</th>
        </tr>
        <tr>
        <td><?php echo $current_month_kpi ?></td>
        <td><?php echo $current_month_achieve_rate ?>%</td>
        <td><?php echo $current_month_achieve ?></td>
        <td><?php echo $left_date ?></td>
        </tr>
      </table>
    </div>
    <div class="chart-con">
<div style="  float: left; overflow: hidden; width: 50%;">
<?php $form = $this->beginWidget('CActiveForm',array('action'=>
$url.'&city_id=' . $params['city_id'] . '&funnel_month=' . $params['funnel_month'] .
'&funnel_year=' . $params['funnel_year'])); ?>

<select name="complete_year" id="complete_year" style="width:80px;">
<?php for($i = date('Y'); $i >= 2000; $i--){?>
<option value="<?php echo $i?>" <?php if($i == $params['complete_year']) echo 'selected="selected"' ?>><?php echo $i?></option>
<?php }?>
</select>
<input id="complete_year" class="btn btn-success" type="submit" value="查询" />
<?php $this->endWidget(); ?>

      <div id="complete" class="left-chart">
      </div>
</div>

<div style="  float: left; overflow: hidden; width: 49%;">
<?php $form = $this->beginWidget('CActiveForm',array('action'=>
$url.'&city_id=' . $params['city_id'] . '&complete_year=' . $params['complete_year']));
?>
<select name="funnel_year" id="funnel_year" style="width:80px;">
<?php for($i = date('Y'); $i >= 2000; $i--){?>
<option value="<?php echo $i?>" <?php if($i == $params['funnel_year']) echo 'selected="selected"'?>><?php echo $i?></option>
<?php }?>
</select>
<select name="funnel_month" id="funnel_month" style="width:50px;">
<?php for($i = 1; $i <= 12; $i++){?>
<option value="<?php echo $i?>" <?php if($i == $params['funnel_month']) echo 'selected="selected"' ?>><?php echo $i?></option>
<?php }?>&nbsp;&nbsp;
<input class="btn btn-success" type="submit" value="查询" />
<?php if($process == 'new_process'){?>
平均签约时间：<?php echo $avg_entry_time?>
<?php }?>
<?php $this->endWidget() ?>


<div id="<?php echo $process?>" class="right-chart">
      </div>
</div>
    </div>
  </div>
</body>

<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>
<div style="clear:both"></div>
<script>
$(function () {
    $('#complete').highcharts({
        chart: {
            zoomType: 'xy'
        },
        title: {
            text: '任务完成情况',
                align: 'left'
        },
        subtitle: {
            text: null
        },
        xAxis: [{
            categories: ['1月', '2月', '3月', '4月', '5月', '6月',
                '7月', '8月', '9月', '10月', '11月', '12月'],
                crosshair: true
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
            },
                title: {
                    text: '',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                }
        }
            /*
        { // Secondary yAxis
            min: 0,
                title: {
                    text: '',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                },
                    labels: {
                        format: '',
                            style: {
                                color: Highcharts.getOptions().colors[0]
                            }
                    },
                        opposite: true
        }
             */
        ],
        tooltip: {
            shared: true
        },
        legend: {
            layout: 'vertical',
                align: 'left',
                x: 460,
                verticalAlign: 'top',
                y: -10,
                floating: true,
                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
        },
        series: [{
            name: '招聘完成',
                type: 'column',
                data: [
                <?php echo implode(',', $achieve_str) ?>
                // {y: 49.9, color: 'red'},
                // 71.5, 106.4, 129.2, 144.0, 
                // {y:176.0, color:'red'}, 
                // 135.6, 148.5, 216.4, 194.1, 95.6, 54.4
                ],
                color: '#77CC77',
                //colors: ['red', 'gree', 'red', 'gree','red', 'gree','red', 'gree','red', 'gree','red', 'gree'],
                tooltip: {
                    valueSuffix: ''
                }

        }, {
            name: '招聘计划',
                type: 'spline',
                data: [<?php echo implode(',', $kpi) ?>],
                tooltip: {
                    valueSuffix: ''
                }
        }]
    });
});
<?php if($process == 'process'){?>
$(function () {
    $('#process').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: '招聘流程分析',
                align: 'left',
        },
        subtitle: {
            text: null
        },
        xAxis: {
            categories: [
                '报名成功：<?php echo $funnel_data['apply'] ?>',
                //'参加考试：1234',
                '通过考试：<?php echo $funnel_data['pass_exam'] ?>',
                //'通知面试：,
                '参加面试：<?php echo $funnel_data['attend_interview'] ?>',
                '面试通过：<?php echo $funnel_data['pass_interview'] ?>',
                '路考通过：<?php echo $funnel_data['pass_road_exam'] ?>',
                '签约：<?php echo $funnel_data['sign'] ?>',
                '激活：<?php echo $funnel_data['active'] ?>',
                '活跃：<?php echo $funnel_data['lively'] ?>'
                ],
                title: {
                    text: null
                }
        },
            yAxis: {
                min: 0,
                max: 100,
                    title: {
                        text: '百分比',
                            align: 'high'
                    },
                    labels: {
                        overflow: 'justify'
                    }
            },
                tooltip: {
                    valueSuffix: '%'
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                    legend: {
                        layout: 'vertical',
                            align: 'right',
                            verticalAlign: 'bottom',
                            x: -40,
                            y: 100,
                            floating: true,
                            borderWidth: 1,
                            backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
            shadow: true
                    },
                    credits: {
                        enabled: false
                    },
                    series: [{
                        name: '完成度',
                            valueSuffix: ' %',
                            data: [
                            <?php echo implode(',', $funnel_data_str); ?>
                            ],
                            color:'#77CC77'
                    }]
    });
});
<?php }?>
<?php if($process == 'new_process'){?>
$(function () {
    $('#new_process').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: '招聘流程分析',
                align: 'left',
        },
        subtitle: {
            text: null
        },
        xAxis: {
            categories: [
                '报名成功：<?php echo $funnel_data['apply'] ?>',
                '预约成功：<?php echo $funnel_data['date'] ?>',
                //'参加考试：<?php //echo $funnel_data['attend_exam'] ?>',
                '路考通过：<?php echo $funnel_data['road_pass'] ?>',
                '签约：<?php echo $funnel_data['sign'] ?>',
                '上线考核通过：<?php echo $funnel_data['online_exam_pass']?>',
                '签收装备：<?php echo $funnel_data['received_equip']?>',
                '激活：<?php echo $funnel_data['active'] ?>',
                '活跃：<?php echo $funnel_data['lively'] ?>'
                ],
                title: {
                    text: null
                }
        },
            yAxis: {
                min: 0,
                max: 100,
                    title: {
                        text: '百分比',
                            align: 'high'
                    },
                    labels: {
                        overflow: 'justify'
                    }
            },
                tooltip: {
                    valueSuffix: '%'
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                    legend: {
                        layout: 'vertical',
                            align: 'right',
                            verticalAlign: 'bottom',
                            x: -40,
                            y: 100,
                            floating: true,
                            borderWidth: 1,
                            backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
            shadow: true
                    },
                    credits: {
                        enabled: false
                    },
                    series: [{
                        name: '完成度',
                            valueSuffix: ' %',
                            data: [
                            <?php echo implode(',', $funnel_data_str); ?>
                            ],
                            color:'#77CC77'
                    }]
    });
});
<?php }?>

</script>
