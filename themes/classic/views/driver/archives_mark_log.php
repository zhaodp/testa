<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ztyzjn
 * Date: 13-8-15
 * Time: 下午5:48
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
    <div class="navbar-inner" >
        <a class="brand" href="#">状态变化日志</a>
    </div>
</div>

<div>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'driver-grid',
    'dataProvider'=>$data,
    'cssFile'=>SP_URL_CSS . 'table.css',
    'itemsCssClass'=>'table table-bordered',
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    //'htmlOptions'=>array('class'=>'row span11'),
    'columns'=>array(
        array(
            'name'=>'日志类型',
            'value'=>'Dict::item("driver_log_status", $data->type)',
        ),
        array (
            'name'=>'记录',
            'type'=>'raw',
            'value'=>array($this,'driver_mark_reason')
        ),
        array(
            'name' => '操作人',
            'value' => '$data->operator',
        ),

        array(
            'name'=>'操作时间',
            'value'=>'date("Y-m-d H:i",$data->created)'),
    )));
?>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery('strong').css('color', '#316AAF');
    });
</script>