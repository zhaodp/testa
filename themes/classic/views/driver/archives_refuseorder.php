<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cuiluzhe
 * Date: 14-6-4
 * Time: 19:32
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
            <a class="brand" href="#">拒单明细</a>
        </div>
    </div>

<?php
$this->widget('zii.widgets.grid.CGridView', array (
    'id'=>'complain-grid',
    'dataProvider'=>$data,
    'ajaxUpdate' => false,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-bordered',
    'columns'=>array (
        array (
            'name'=>'订单id',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->order_id',
        ),
        array (
            'name'=>'司机编号',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->driver_id'
        ),
        array (
            'name'=>'拒单类型',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->fail_type==3?"司机主动拒绝":"订单推送超时弹回"'
        ),
        array (
            'name'=>'描述',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->description'
        ),
	 array (
            'name'=>'时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->created'
        ),
    )
));
?>
