<?php
/* @var $this BOrderTrendController */
/* @var $model BOrderTrend */

?>

<h1>订单分析</h1>

<p></p>

<div class="search-form" style="margin-bottom: 120px;">
<?php $this->renderPartial('order/_time_order_search',array(
    'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php
$dataProvider = $model->search(null, 0);
$items = $dataProvider->getData();
?>

<div calss="chart span12" style="height:380px;">
<?php
$this->renderPartial('order/_time_order_chart',array(
    'items'=>$items,
));
?>
</div>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'border-trend-grid',
    'dataProvider'=>$dataProvider,
    'enableSorting'=>FALSE,
    'cssFile' => SP_URL_CSS . 'table.css',
    'itemsCssClass' => 'table  table-condensed',
    'pagerCssClass' => 'pagination text-center',
    'pager' => Yii::app()->params['formatGridPage'],
    'htmlOptions' => array('class'=>'span12','style'=>'margin-left:0;'),
    'enablePagination' => FALSE,
    'columns'=>array(
        'day',
        array(
            'name'=>'city_id',
            'value'=>'Dict::item("city", $data->city_id)'
        ),
        array(
            'header'=>'0',
            'value'=>'$data->twenty_four'
        ),
        'one',
        'two',
        'three',
        'four',
        'five',
        'six',
        'seven',
        'eight',
        'nine',
        'ten',
        'eleven',
        'twelve',
        'thirteen',
        'fourteen',
        'fifteen',
        'sixteen',
        'seventeen',
        'eighteen',
        'nineteen',
        'twenty',
        'twenty_one',
        'twenty_two',
        'twenty_three',
        /*
        'twenty_four',
        'update_time',
        array(
            'class'=>'CButtonColumn',
        ),
        */
    ),
)); ?>