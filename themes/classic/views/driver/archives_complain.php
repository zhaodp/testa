<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-12-2
 * Time: 上午10:56
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
            <a class="brand" href="#">投诉信息</a>
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
    'rowCssClassExpression'=>'"item_".$data->id." ".($data->attention?"attention":"")." ".($row%2>0?"odd":"even")',
    'columns'=>array (
        array (
            'name'=>'ID',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->id',
        ),
        array (
            'name'=>'投诉来源',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'value'=>'$data->source?CustomerComplain::$source[$data->source]:""',
        ),
        array (
            'name'=>'投诉详情',
            'headerHtmlOptions'=>array (
                'style'=>'width:200px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->detail'
        ),
        array (
            'name'=>'投诉时间',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->create_time'
        ),
        array (
            'name'=>'投诉类型',
            'headerHtmlOptions'=>array (
                'style'=>'width:50px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'getType')
        ),
        array (
            'name'=>'投诉人',
            'headerHtmlOptions'=>array (
                'style'=>'width:50px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>array($this,'complainUser'),
        ),
        array (
            'name'=>'预约电话',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>array($this,'customer_phone'),
        ),
        array (
            'name'=>'创建人',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->created'
        ),
        array (
            'name'=>'操作人',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>'$data->operator'
        ),
        /*
        array (
            'name'=>'司机',
            'headerHtmlOptions'=>array (
                'style'=>'width:40px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>array($this,'driverInfo'),
        ),
        */
        array (
            'name'=>'订单编号',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'orderIdAndNumber')
        ),
        array (
            'name'=>'处理状态',
            'headerHtmlOptions'=>array (
                'style'=>'width:60px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'processStatus')
        ),
        /*
        array (
            'header'=>'操作',
            'headerHtmlOptions'=>array (
                'style'=>'width:120px',
                'nowrap'=>'nowrap'
            ),
            'type'=>'raw',
            'value'=>array($this,'opt')
        ),
        */
    )
));
?>