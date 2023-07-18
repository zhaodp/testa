<?php
/* @var $this ActivityReportController */
/* @var $model ActivityReport */

$this->breadcrumbs = array(
    'Activity Reports' => array('index'),
    'Manage',
);

$this->menu = array(
    array('label' => 'List ActivityReport', 'url' => array('index')),
    array('label' => 'Create ActivityReport', 'url' => array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#activity-report-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>南京活动管理</h1>

<div class="search-form" style="display:block">
    <?php $this->renderPartial('_search', array(
        'model' => $model,
    )); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'activity-report-grid',
    'dataProvider' => $model->search(),
    'itemsCssClass' => 'table table-striped',
//	'filter'=>$model,
    'columns' => array(
        'day_date',
//		'id',
//		'order_id',
//		'driver_id',
//		'driver_name',
//		'driver_phone',
//		'phone',
        array(
            'name' => 'city_id',
            'type'=>'raw',
            'value'=> 'Dict::item("city", $data->city_id)'
        ),
//		'status',
        'total_order',
        'complate_count',
        'complate_p',
        'complate_driver_b',
        'complate_customer_b',
        'order_account',
        'driver_account',
        'company_subsidy',
        'driver_subsidy',
        'customer_subsidy',
//        'order_date',

//        array(
//            'class' => 'CButtonColumn',
//        ),
    ),
)); ?>
