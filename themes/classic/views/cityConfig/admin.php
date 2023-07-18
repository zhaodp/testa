<?php
/* @var $this CityConfigController */
/* @var $model CityConfig */
$this->setPageTitle('城市管理');

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#city-config-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo $this->pageTitle;?></h1>

<div class="search-form">
    <?php $this->renderPartial('_search', array(
        'model' => $model,
    )); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'city-config-grid',
    'itemsCssClass' => 'table table-striped',
    'dataProvider' => $model->search(),
//	'filter'=>$model,
    'columns' => array(
        'id',
        'city_id',
        'city_name',
        'city_prifix',
        'bonus_prifix',
        'city_level',
        array(
            'name' => 'status',
            'value' => 'Dict::item("city_status", $data->status)'
        ),
        array(
            'name' => 'cast_id',
            'value' => 'Dict::item("city_cast", $data->cast_id)'
        ),
        array(
            'name' => 'fee_id',
            'value' => 'Dict::item("city_fee", $data->fee_id)' //'$data->fee_id',//
        ),
        'pay_money',
        'screen_money',
        'bonus_back_money',
        array(
            'name' => 'captital',
            'value' => 'Dict::item("city_captital", $data->captital)'
        ),
        'online_time',
        array(
            'header' => '操作',
            'class' => 'CButtonColumn',
            'template' => '{view} {update}'
        ),
    ),
)); ?>
