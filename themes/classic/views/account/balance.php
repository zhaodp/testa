<?php
/**
 * Created by JetBrains PhpStorm.
 * author: mtx
 * Date: 13-7-9
 * Time: 上午7:27
 */
$this->breadcrumbs = array(
    'Driver Balances' => array('index'),
    'Manage',
);

$this->menu = array(
    array('label' => 'List DriverBalance', 'url' => array('index')),
    array('label' => 'Create DriverBalance', 'url' => array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
    $('.search-form').toggle();
    return false;
});
$('.search-form form').submit(function(){
    $('#driver-balance-grid').yiiGridView('update', {
        data: $(this).serialize()
    });
    return false;
});
");
?>

    <h1>司机信息费余额查询</h1>

    <div class="search-form" style="display:">
        <?php $this->renderPartial('_search_balance', array(
            'model' => $model,
        )); ?>
    </div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'driver-balance-grid',
    'itemsCssClass' => 'table',
    'dataProvider' => $model->search(),
    'columns' => array(
        array(
            'name' => 'city_id',
            'headerHtmlOptions' => array(
                'nowrap' => 'nowrap'
            ),
            'type' => 'raw',
            'value' => 'Dict::item("city",$data->city_id)'
        ),
        'driver_id',
        'name',
        'balance',
//        array(
//            'class' => 'CButtonColumn',
//        ),
    ),
)); ?>