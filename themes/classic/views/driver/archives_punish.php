<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-8-15
 * Time: 下午5:51
 * To change this template use File | Settings | File Templates.
 */

$this->layout = '//layouts/main_no_nav';
?>

<style>
    th {
        background-color: #D9EDF7;
    }

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
    <div class="navbar-inner">
        <a class="brand" href="#">处罚记录</a>
    </div>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'punish-grid',
    'dataProvider'=>$data,
    'cssFile'=>SP_URL_CSS . 'table.css',
    'itemsCssClass'=>'table table-bordered',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'columns'=>array(

        array(
            'name'=>'操作日期',
            'value' => '$data->create_time',
            'headerHtmlOptions' => array('width'=>'20%'),
        ),

        array(
            'name'=>'奖惩类型',
            'value' => '"惩罚"',
            'headerHtmlOptions' => array('width'=>'20%'),
        ),

        array(
            'name' => '操作',
            'value' => '$data->limit_time',
            'headerHtmlOptions' => array('width'=>'20%'),
        ),

        array(
            'name' => '操作说明',
            'value' => '$data->mark',
            'headerHtmlOptions' => array('width'=>'20%'),
        ),

        array(
            'name' => '操作人',
            'value' => '$data->operator',
            'headerHtmlOptions' => array('width'=>'20%'),
        )
    ),
));
?>
