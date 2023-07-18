<?php
/* @var $this CityConfigController */
/* @var $model CityConfig */
$this->setPageTitle('司机财富管理');


?>
<?php

// echo '<div id="dialogdiv"></div>';
// echo '<iframe id="view_message_frame" width="100%" height="100%" style="border:0px"></iframe>';

// // $this->endWidget('zii.widgets.jui.CJuiDialog');
?>
<h1><?php echo $this->pageTitle; ?></h1>

<div class="search-form">
    <?php $this->renderPartial('wealthSearch', array(
        'model' => $model,
        'city_id'=>$city_id,
        'stat_month'=>$stat_month,
    )); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'score-driver-grid',
    'cssFile' => SP_URL_CSS . 'table.css',
    'dataProvider' => $dataProvider,
    'ajaxUpdate' => false,
    'pagerCssClass'=>'pagination text-center',
    'pager'=>Yii::app()->params['formatGridPage'],
    'itemsCssClass'=>'table table-striped',
//	'filter'=>$model,
    'columns' => array(

        array (
            'name'=>'司机',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->driver_id',
        ),
        array (
            'name'=>'总e币',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->total',
        ),
        array (
            'name'=>'五星',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->five_star_count',
        ),
        array (
            'name'=>'准时',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->reach_count',
        ),
        array (
            'name'=>'快速接单',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->receive_count',
        ),
        array (
            'name'=>'组长单',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->group_count',
        ),
        array (
            'name'=>'高峰在线(15分钟为单位)',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->hotline_count',
        ),
        array (
            'name'=>'远距离',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->long_distance_count',
        ),
        array (
            'name'=>'周全勤',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->week_count',
        ),
        array (
            'name'=>'销单',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->cancel_count',
        ),
        array (
            'name'=>'拒单',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->reject_count',
        ),
        array (
            'name'=>'恶劣天气',
            'headerHtmlOptions'=>array (
                'style'=>'width:10px',
                'nowrap'=>'nowrap'
            ), 'type'=>'raw',
            'value'=>'$data->reward_count',
        ),

        array('name'=>'报单数','value'=>array($this,'getOrderTotal')),
        array('name'=>'扣分值','value'=>array($this,'getScoreTotal')),
        // array('name'=>'到达率','value'=>array($this,'getOrderTotal')),
        // array('name'=>'接单时间','value'=>array($this,'getOrderTotal')),

    ),
)); ?>


