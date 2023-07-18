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
        <a class="brand" href="#">代驾分记录</a>
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
                'name'=>'ID',
                'headerHtmlOptions'=>array (
                    'width'=>'60px',
                    'nowrap'=>'nowrap'
                ),
                'value' => '$data->id',
            ),
            
            array (
                'header'=>'投诉ID',
                'headerHtmlOptions'=>array (
                    'width'=>'80px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=> '$data->customer_complain_id',
            ),
            array (
                'header'=>'扣分原因',
                'headerHtmlOptions'=>array (
                    'width'=>'60px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data->deduct_reason',
            ),
            
            array (
                'header'=>'代驾分',
                'headerHtmlOptions'=>array (
                    'width'=>'220px',
                    'nowrap'=>'nowrap'
                ),
                'type'=>'raw',
                'value'=>'$data->driver_score',
            ),
            array (
                'header'=>'创建人',
                'headerHtmlOptions'=>array (
                    'width'=>'60px',
                    'nowrap'=>'nowrap'
                ),
                'value'=>'$data->operator',
            ),
            array (
                'header'=>'创建时间',
                'headerHtmlOptions'=>array (
                    'width'=>'60px',
                    'nowrap'=>'nowrap'
                ),
                'value'=>'$data->create_time'
            ),
            
        )
    )
);
?>

