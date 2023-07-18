<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-12-2
 * Time: 下午1:23
 * To change this template use File | Settings | File Templates.
 */
$this->layout = '//layouts/main_no_nav';
?>
<style>
    .navbar .navbar-inner{
        background-color: #FAFAFA!important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.067)!important;
        background-image: -moz-linear-gradient(top, #FFF, #F2F2F2);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#FFF), to(#F2F2F2));
        background-image: -webkit-linear-gradient(top, #FFF, #F2F2F2);
        background-image: -o-linear-gradient(top, #FFF, #F2F2F2);
        background-image: linear-gradient(to bottom, #FFF, #F2F2F2);
    }
</style>
<div class="navbar">
    <div class="navbar-inner" >
        <a class="brand" href="#">财富统计</a>
    </div>
</div>
<?php

$this->widget('zii.widgets.grid.CGridView', array (
        'id'=>'comments-grid',
        'dataProvider'=>$data,
        'itemsCssClass'=>'table table-bordered',
        'pagerCssClass'=>'pagination text-center',
        'pager'=>Yii::app()->params['formatGridPage'],
        // 'rowCssClassExpression'=>'($data->id%2 == 0)?"alert-error":""',
        'columns'=>array (
            array(
                //'class' => 'CCheckBoxColumn',
                //'selectableRows' => 2,
                'name'=>'日期',
                'headerHtmlOptions'=>array (
                    'width'=>'20px',
                    'nowrap'=>'nowrap'
                ),
                'value' => 'date("Y-m-d",strtotime($data->stat_day))',
            ),
            
            array (
                'header'=>'五星(+10e)',
                'headerHtmlOptions'=>array (
                    'width'=>'20px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=> '$data->five_star_count',
            ),
            array (
                'header'=>'准时(+2e)',
                'headerHtmlOptions'=>array (
                    'width'=>'20px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data->reach_count',
            ),
            
            array (
                'header'=>'快速接单(+3e)',
                'headerHtmlOptions'=>array (
                    'width'=>'20px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data->receive_count',
            ),
            array (
                'header'=>'组长单(+15e)',
                'headerHtmlOptions'=>array (
                    'width'=>'20px',
                    'nowrap'=>'nowrap'
                ),
                'value'=>'$data->group_count',
            ),
            array (
                'header'=>'高峰在线(15分钟为单位,+1e)',
                'headerHtmlOptions'=>array (
                    'width'=>'20px',
                    'nowrap'=>'nowrap'
                ),
                'value'=>'$data->hotline_count'
            ),
            array (
                'header'=>'远距离(+5e)',
                'headerHtmlOptions'=>array (
                    'width'=>'20px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data->long_distance_count'
            ),
            array (
                'header'=>'周全勤(+80e)',
                'headerHtmlOptions'=>array (
                    'width'=>'20px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data->week_count'
            ),
            array (
                'header'=>'销单(-20e)',
                'headerHtmlOptions'=>array (
                    'width'=>'60px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data->cancel_count'
            ),
            array (
                'header'=>'拒单(-20e)',
                'headerHtmlOptions'=>array (
                    'width'=>'60px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data->reject_count'
            ),
            array (
                'header'=>'恶劣天气(+2e)',
                'headerHtmlOptions'=>array (
                    'width'=>'60px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data->reward_count'
            ),
            array (
                'header'=>'填写日间订单信息(+5e)',
                'headerHtmlOptions'=>array (
                    'width'=>'60px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data->day_order_count'
            ),
            array (
                'header'=>'奖惩总和',
                'headerHtmlOptions'=>array (
                    'width'=>'60px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data->reward_punish_count'
            ),
            array (
                'header'=>'e币',
                'headerHtmlOptions'=>array (
                    'width'=>'60px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data->total_wealth'
            ),
            
        )
    )
);
?>

