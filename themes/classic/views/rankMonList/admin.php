<?php
/* @var $this RankMonListController */
/* @var $model RankMonList */

$this->breadcrumbs=array(
	'Rank Mon Lists'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List RankMonList', 'url'=>array('index')),
	array('label'=>'Create RankMonList', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('rank-mon-list-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Rank Mon Lists</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'rank-mon-list-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'driver_id',
		'city_id',
		'order_count',
		'call_count',
		/*
		'work_day_count',
		'phone_count',
		'income',
		'created',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
